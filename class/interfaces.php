<?php

/**
 * Joss framework & content management system.
 *
 * Created 3.7.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Classes which have a name and are identifiable by this name.
 * @package    Joss
 */
interface JInamed {
	
	/**
	 * Decodes class name, e.g. `some-words` are converted to `JSomeWords`.
	 *
	 * @param string $id
	 * @return string
	 */
	static public function resolveName($id);
	
}
