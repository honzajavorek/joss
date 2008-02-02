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
 * Access to server variables.
 * 
 * Arguments:
 * 	[0] ... index in $_SERVER array
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision: 4 $ ($Date: 2008-02-01 04:34:45 +0100 $, $Author: jan.javorek $)
 */
class JPServer extends JPlugin {

	public $cached = FALSE;
	
	public $type = Texy::CONTENT_TEXTUAL;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	public function process() {
		$index = strtoupper($this->args[0]);
		if (empty($index)) {
			return '';
		}
		return (isset($_SERVER[$index]))? (string)$_SERVER[$index] : '';
	}
	
}
