<?php

/**
 * Joss framework & content management system.
 *
 * Created 9.6.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Inter-language link.
 * 
 * Arguments:
 * 	[0] ... language two-letter abbreviation
 * 	[1] ... anchor text (optional) 
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPLanguage extends JPlugin {
	
	public $cached = FALSE;
	
	public $type = Texy::CONTENT_BLOCK;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	public function process() {
		$class = ($this->args[0] == $_GET['lang'])? 'active' : NULL;
		$link = JRouter::url($this->args[0]);
		$text = ((!empty($this->args[1]))? $this->args[1] : $this->args[0]);
		
		$li = Html::el('li')->class($class);
		if (!$class) {
			$menu = Html::el('a')->id('lang-' . $this->args[0])->href($link)->setText($text)->add(Html::el('span'));
		} else {
			$menu = Html::el('strong')->id('lang-' . $this->args[0])->setText($text)->add(Html::el('span'));
		}
		$li->add($menu);

		return $li;
	}
	
}
