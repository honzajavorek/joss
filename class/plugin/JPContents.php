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
 * Table of contents.
 * 
 * Arguments:
 * 	[0] ... process H1? (bool, default is TRUE)
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPContents extends JPlugin {
	
	public $delayed = TRUE;

	public $type = Texy::CONTENT_BLOCK;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	public function process() {
		$skipH1 = (isset($this->args[0]))? !(bool)$this->args[0] : TRUE;
		$get = new JInput('get');
		$self = $get->export('doc', 'string');	
		
		$toc = NHtml::el('ul')->id('TOC');
		foreach ($this->texy->headingModule->TOC as $item) {
			if ($skipH1 && $item['level'] == 1) { // skip H1
				continue;
			}			
			$link = NHtml::el('a')->href(JRouter::url($self) . '#' . $item['el']->id)->setText($item['title']);
			$toc->add(NHtml::el('li')->add($link));
		}

		return $toc;
	}
	
}
