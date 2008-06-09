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
 * Applies local configuration.
 * 
 * Arguments:
 * 	[0] ... identifier
 *  [1, 2, 3, ] ... value - all arguments >= 1 are put together into only one string
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPConfig extends JPlugin {
	
	public $delayed = TRUE;

	public $type = Texy::CONTENT_MARKUP;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	/**
	 * Settings allowed to be changed.
	 *
	 * @var array
	 */
	private static $changeable = array(
		'title', 'language', 'allowrobots', 'author', 'copyright', 'keywords', 'description'
	);
	
	/**
	 * Changes in configuration.
	 *
	 * @var array
	 */
	private static $data = array();
	
	/**
	 * Provides changes in configuration.
	 *
	 * @return array
	 */
	public static function getData() {
		return self::$data;
	}
	
	public function process() {
		$setting = (string)$this->args[0];
		if (empty($setting)) {
			throw new LogicException('Cannot set something without a name.');
		}
		if (!in_array($setting, self::$changeable)) {
			throw new LogicException("Setting '$setting' cannot be changed!");
		}
		
		unset($this->args[0]);
		self::$data[$setting] = implode(', ', $this->args);
		
		return '';
	}
	
}
