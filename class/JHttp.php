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
 * Takes care about $_GET, $_POST, etc.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
final class JHttp extends Object {

	/**
	 * Initialization.
	 *
	 * @var bool
	 */
	static protected $initialized = FALSE;

	/**
	 * Static initialization.
	 */
	static function init() {
		if (!self::$initialized) {
			// tidy up (destroys useless and dangerous global vars)
			$unset = array('HTTP_ENV_VARS', '_ENV', '_REQUEST', 'GLOBALS');
			foreach ($unset as $globalArray) {
				unset($$globalArray);
			}

			// remove fuckin' quotes
			self::removeMagicQuotes();

			self::$initialized = TRUE;
		}
	}

	/**
	 * Removes the magic_quotes.
	 *
	 * Affects all $GLOBALS which can provide text data.
	 *
	 * @copyright Jakub Vrána, http://php.vrana.cz
	 * @link http://php.vrana.cz/vypnuti-magic_quotes_gpc.php
	 */
	static private function removeMagicQuotes() {
		if (get_magic_quotes_gpc()) {
			$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST, &$_FILES);
			while (list($key, $val) = each($process)) {
				foreach ($val as $k => $v) {
					unset($process[$key][$k]);
					if (is_array($v)) {
						$process[$key][($key < 5 ? $k : stripslashes($k))] = $v;
						$process[] =& $process[$key][($key < 5 ? $k : stripslashes($k))];
					}
					else {
						$process[$key][stripslashes($k)] = stripslashes($v);
					}
				}
			}
		}
	}

	/**
	 * Returns all HTTP headers.
	 * 
	 * If URL given, returns HTTP headers of this URL. Otherwise, returns
	 * headers of current response.
	 *
	 * @param string $url
	 * @return array
	 */
	static public function getHttpHeaders($url = NULL) {
		if (!empty($url)) {
			return get_headers($url, 1);
		}
		
		if(function_exists("apache_request_headers")) {
			if($headers = apache_request_headers()) {
				return $headers;
			}
		}

		$headers = array();
		foreach(array_keys($_SERVER) as $skey) {
			if(substr($skey, 0, 5) == "HTTP_") {
				$headername = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($skey, 0, 5)))));
				$headers[$headername] = $_SERVER[$skey];
			}
		}

		return $headers;
	}
	
	static public function getMetaTags($url) {
		return get_meta_tags($url);
	}

}
