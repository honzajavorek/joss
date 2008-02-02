<?php

/**
 * Joss framework & content management system.
 *
 * Created 26.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://work.javorek.net/joss
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Registering JAutoLoad as an autoload handler.
 *
 * @param string $class
 */
function __autoload($class) { JAutoload::load($class); }

/**
 * Autoload.
 * 
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision: 4 $ ($Date: 2008-02-01 04:34:45 +0100 $, $Author: jan.javorek $)
 */
final class JAutoLoad {
	
	/**
     * Static class - cannot be instantiated.
     */
    final public function __construct() {
        throw new LogicException("Cannot instantiate static class '" . get_class($this) . "'.");
    }
    
	/**
     * Static class - cannot be cloned.
     */
	public function __clone() {
		throw new LogicException("Cannot clone static class '" . get_class($this) . "'.");
	}

	/**
	 * Function to scan directory with it's subdirectories for classes.
	 * 
	 * @param string $dir
	 * @param string $class
	 * @return bool
	 */
	private static function scanDir($dir, $class) {
		if(is_dir($dir)){
		    if($dh = opendir($dir)){
		    	if (is_file("$dir/$class.php")) { // file found
						include_once "$dir/$class.php";
						return TRUE;
			    }
			    while(($file = readdir($dh)) !== FALSE){
			    	// subdirectories
			    	if (is_dir("$dir/$file")
			    		&& $file{0} != '.'
			    		&& self::scanDir("$dir/$file", $class)
			    	) {
			    			return TRUE;
			    	}
			    }
		    }
		}
		return FALSE;
	}
	
	/**
	 * Finds and includes the requested class.
	 *
	 * @param string $class
	 */
	public static function load($class) {
		if (!self::scanDir(JOSS_CLASS_DIR, $class) || !class_exists($class)) {
			throw new Exception("Class '$class' doesn't exist.");
		}
	}
	
}
