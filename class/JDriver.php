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
 * Part of application which represents special behavior of HTML page.
 *
 * Represents generated content of web page (instead of Texy! files).
 * Uses templates etc.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
abstract class JDriver extends JDoc {

	/**
	 * Decodes name, {{some-words}} are converted to JDSomeWords.
	 *
	 * @param string $id
	 * @return string
	 */
	static public function resolveName($id) {
		$id = explode('-', $id);
		foreach ($id as &$part) {
			$part = ucfirst($part);
		}
		$id = 'JD' . implode('', $id);
		return $id;
	}

}
