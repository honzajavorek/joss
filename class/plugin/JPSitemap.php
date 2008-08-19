<?php

/**
 * Joss framework & content management system.
 *
 * Created 14.8.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Generates hierarchical sitemap of the web.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPSitemap extends JPlugin {
	
	public $cached = FALSE;

	public $type = Texy::CONTENT_BLOCK;

	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	private function getLevel(SimpleXMLElement $level) {
		$lang = (JLang::moreVersionsExist())? "/$_GET[lang]" : '';
		$dir = Html::el('dir');
		
		foreach ($level->item as $item) {
			$url = (empty($item['url']))? Texy::webalize((string)$item) : $item['url'];
			$url = JRouter::url($url);
			$li = Html::el('li')->add(
				Html::el('a')->href(JOSS_URL_ROOT . "$lang$url")->setText(trim((string)$item))
			);
			if (isset($item->menu)) {
				$li->add($this->getLevel($item->menu));
			}

			$dir->add($li);
		}
		return $dir;
	}
	
	public function process() {
		$list = NULL;
		
		$xml = new JFile(JOSS_APP_DIR . '/config/navigation.xml');
		$nav = $xml->content;
		
		foreach ($nav->language as $language) {
			if ($language['name'] == $_GET['lang']) {
				$list = $this->getLevel($language->menu);
				break;
			}
		}
		
		if ($list instanceof Html) {
			$list->id('sitemap');
			return $list;
		}
		return NULL; // shouldn't happen
	}
	
}
