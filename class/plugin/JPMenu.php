<?php

/**
 * Joss framework & content management system.
 *
 * Created 31.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://work.javorek.net/joss
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Menu link.
 * 
 * Arguments:
 * 	[0] ... relative URL (page identifier)
 * 	[1] ... anchor text (optional)
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision: 4 $ ($Date: 2008-02-01 04:34:45 +0100 $, $Author: jan.javorek $)
 */
class JPMenu extends JPlugin {
	
	public $cached = FALSE;
	
	public $type = Texy::CONTENT_BLOCK;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	public function process() {
		$get = new JInput('get');
		
		$class = ($this->args[0] == $get->export('doc', 'string'))? 'active' : NULL;
		$link = JDoc::url($this->args[0]);
		$text = (($this->args[1])? $this->args[1] : $this->args[0]);
		
		$li = NHtml::el('li')->class($class);
		$a = NHtml::el('a')->href($link)->setText($text);
		$li->add($a);

		return $li;
	}
	
}
