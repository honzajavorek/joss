<?php

/**
 * Joss framework & content management system.
 *
 * Created 29.6.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Part of application which represents functionality of forms, AJAX etc.
 *
 * Can be used separately (...../?action=id-of-action) to provide AJAX responses
 * or with drivers (see JDriver) to perform functionality of web forms.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
abstract class JAction extends JDoc {

	final public function __construct($id) {
		if (!empty($id)) {
			$this->__constructByUrl($id);
		} else {
			$this->__constructByDriver();
		}
	}
	
	protected function __constructByUrl($id) {
		throw new InvalidStateException('This action cannot be instantiated via URL.');
	}
	
	protected function __constructByDriver() {
		throw new InvalidStateException('This action cannot be instantiated by driver.');
	}
	
	/**
	 * Decodes name, {{some-words}} are converted to JASomeWords.
	 *
	 * @param string $id
	 * @return string
	 */
	static public function resolveName($id) {
		$id = explode('-', $id);
		foreach ($id as &$part) {
			$part = ucfirst($part);
		}
		$id = 'JA' . implode('', $id);
		return $id;
	}

}
