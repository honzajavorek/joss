<?php

/**
 * Joss framework & content management system.
 *
 * Created 1.2.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://work.javorek.net/joss
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Glossary, sth like website map.
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
		$texy = new Texy();
		
		$list = NHtml::el('dir')->id('glossary');
		
		$dir = JOSS_APP_DIR . '/web/content';
		$d = dir($dir);
		while (FALSE !== ($f = $d->read())) {
			if (!is_dir("$dir/$f") && $f{0} != '_' && $f{0} != '.') {
				$entry = substr($f, 0, -5);
				$file = new JFile("$dir/$f");
				$texy->process($file->content);
				$list->add(
					NHtml::el('li')->setText("$entry: ")->add(
						NHtml::el('a')->href(JDoc::url($entry))->setText($texy->headingModule->title)
					));
			}
		}
		$d->close();
		
		return $list;
	}
	
}
