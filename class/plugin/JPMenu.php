<?php

/**
 * Joss framework & content management system.
 *
 * Created 31.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Menu provider.
 *
 * Arguments:
 * 	[0] ... level of desired menu (default is the highest, 0)
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPMenu extends JPlugin {

	public $cached = TRUE;

	public $type = Texy::CONTENT_BLOCK;

	static private $navigation = NULL;

	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
		if (!self::$navigation) {
			$this->loadNavigationSettings();
		}
	}

	private function loadNavigationSettings() {
		$xml = new JFile(JOSS_APP_DIR . '/config/navigation.xml');
		self::$navigation = $xml->content;
	}

	private function findPage($id, SimpleXMLElement $menu) {
		$path = array();
		foreach ($menu->item as $item) {
			$url = (empty($item['url']))? Texy::webalize((string)$item) : trim($item['url']);
			if (isset($item->menu)) { // submenu
				$path = $this->findPage($id, $item->menu);
				if (!empty($path)) {
					$path[] = $url;
				}
				return $path;
			} elseif ($id == $url) { // single
				$path[] = $url;
				return $path;
			}
		}
		return $path; // empty
	}

	private function drawMenu($level, SimpleXMLElement $menu, array $path) {
		if (
			!(empty($path) && $level == 0) // 404 etc.
			&& (!isset($path[$level])) // exists?
		) {
			return Html::el(); // empty
		}
		
		// looking for desired level
		for ($i = 0; $i < $level; $i++) {
			foreach ($menu->item as $item) {
				if (in_array((string)$item['url'], $path) && $item->menu) {
					$menu = $item->menu;
				}
			}
		}

		// language
		$lang = '';
		if (JLang::moreVersionsExist()) {
			$lang = $_GET['lang'] . '/';
		}

		// creating items
		$html = Html::el('menu')->class("menu-level-$level");
		foreach ($menu->item as $item) {
			$originalUrl = (empty($item['url']))? Texy::webalize((string)$item) : trim($item['url']);
			$url = $lang . $originalUrl;

			$class = (isset($path[$level]) && $originalUrl == $path[$level])? 'active' : NULL;
			$link = JRouter::url($url);

			$text = (string)$item;
			$text = trim(((!empty($text))? $text : $url));

			$li = Html::el('li')->class($class);
			if (!$class) {
				$filling = Html::el('a')->href($link)->setText($text);
			} else {
				$filling = Html::el('strong')->setText($text);
			}
			$html->add($li->add($filling));
		}
		return $html;
	}

	public function process() {
		$xml = self::$navigation;

		$path = array();
		$menu = NULL;
		foreach ($xml->language as $language) {
			if ($language['name'] == $_GET['lang']) {
				$menu = $language->menu;
				$path = array_reverse($this->findPage($_GET['doc'], $menu));
				break;
			}
		}
		if (!$menu) {
			throw new InvalidStateException('Menu in desired language or menu item not found.');
		}

		$level = (empty($this->args[0]))? 0 : $this->args[0];
		return $this->drawMenu($level, $menu, $path);
	}

}
