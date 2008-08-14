<?php

/**
 * Joss framework & content management system.
 *
 * Created 1.2.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Alphabetic glossary (sth like sitemap).
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPGlossary extends JPlugin {

	public $cached = FALSE;

	public $type = Texy::CONTENT_BLOCK;

	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}

	public function process() {
		$config = JConfig::getInstance();
		$texy = new Texy(); // really! JTexy is not neccessary (and causes infinite cycles)

		$list = Html::el('dir')->id('glossary');

		$dir = JDoc::$paths['text'];
		$d = dir($dir);

		// languages
		$l = '';
		if (JLang::moreVersionsExist()) { // multiple language versions
			$l = basename($dir);
			$l = (strlen($l) == 2)? "$l/" : "$config[language]/";
		}

		while (FALSE !== ($f = $d->read())) {
			if (!is_dir("$dir/$f") && $f{0} != '_' && $f{0} != '.') {
				$entry = $l . substr($f, 0, -5);
				$file = new JFile("$dir/$f");
				$texy->process($file->content);
				$list->add(
					Html::el('li')->setText("$entry: ")->add(
					Html::el('a')->href(JRouter::url($entry))->setText($texy->headingModule->title)
				));
			}
		}
		$d->close();

		return $list;
	}

}
