<?php

/**
 * Joss framework & content management system.
 *
 * Created 4.2.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Generic header.
 * 
 * Arguments:
 * 	[0] ... subtitle
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPHeader extends JPlugin {
	
	public $type = Texy::CONTENT_BLOCK;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	public function process() {
		$config = JConfig::getInstance();
		
		$header = NHtml::el('strong');
		$a = NHtml::el('a')->href(JOSS_URL_ROOT . '/')->id('logo');

		$link = NHtml::el('strong')->add(NHtml::el('span')->class('hidden')->setText($config['title']));
		
		$header->add($a->add($link));
		$header->add(NHtml::el('span')->setText(', ')->class('hidden'));
		$header->add(NHtml::el('span')->id('subtitle')->add(NHtml::el('em')->class('hidden')->setText($this->args[0], TRUE)));
		
		return NHtml::el('p')->id('header')->add($header);
	}
	
}
