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
 * Includes another local source file.
 *
 * Arguments:
 * 	[0] ... *.texy file to be included
 * 
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPInclude extends JPlugin {
	
	public $type = Texy::CONTENT_BLOCK;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	public function process() {
		$f = new JFile(JOSS_APP_DIR . '/web/content/' . $this->args[0] . '.texy');
		if ($f->exists()) {
			
			$content = new JFormatter();
			return '<div class="included-' . Texy::webalize($this->args[0]) . '">'
				. $content->process($f->content)
				. '</div>';
		}
		return '';
	}
	
}
