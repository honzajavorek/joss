<?php

/**
 * Joss framework & content management system.
 *
 * Created 29.12.2007.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Input class to encapsulate POST, GET, etc.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
final class JInput extends NObject {

	private static $input = array();

	/**
	 * Initialization.
	 *
	 * @var bool
	 */
	static protected $initialized = FALSE;

	/**
	 * Current input method.
	 *
	 * @var string
	 */
	private $method;

	/**
	 * Current input resource pointer.
	 *
	 * @var array
	 */
	private $resource;

	function __construct($method) {
		// initialization
		if (!self::$initialized) {
			$this->removeMagicQuotes();
				
			self::$input = array(
				'get' => $_GET,
				'post' => $_POST,
				'cookie' => $_COOKIE,
				'request' => $_REQUEST,
				'files' => $_FILES
			);
			unset($_GET, $_POST, $_COOKIE, $_REQUEST, $_FILES);
				
			self::$initialized = TRUE;
		}

		// saving the method name
		$this->method = $method;

		// setting pointer
		$superglobal = '_' . strtoupper($method);
		if (array_key_exists($method, self::$input)) { // protected area
			$this->resource = &self::$input[$method];
		} elseif (isset($$superglobal)) { // superglobal variables
			$this->resource = &$$superglobal;
		} else {
			throw new Exc("There is no input resource similar to expression '$method'.");
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
	private function removeMagicQuotes() {
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
	 * Validates variables.
	 *
	 * @param array $requirements
	 * @return array Bad items.
	 */
	function validate(array $requirements) {
		$bad = array();
		foreach ($requirements as $name => $pattern) {
			$isset = array_key_exists($name, $this->resource);
			if ($isset) {
				$match = preg_match($pattern, $this->resource[$name]);
			}
			if (!$isset || !$match) {
				$bad[] = $name;
			}
		}
		return $bad;
	}

	/**
	 * Export a variable.
	 *
	 * @param string $name
	 * @param string $type
	 * @return mixed Variable.
	 */
	function export($name, $type) {
		// exists a special handler? (something like overloading)
		$specialHandlerName = 'export' . ucfirst($this->method);
		if (method_exists($this, $specialHandlerName)) {
			$this->$specialHandlerName($name, $type);
			return;
		}

		// common handling
		$resource = $this->resource;
		if (array_key_exists($name, $resource)) {
			if ($type == 'array' || $type == 'object') {
				return unserialize($resource[$name]);
			}
			settype($resource[$name], $type);
			return trim($resource[$name]);
		} else return NULL;
	}

	/**
	 * Set a variable.
	 *
	 * @param string $name
	 * @param string $type
	 * @param string $value
	 */
	function set($name, $type, $value) {
		// exists a special handler? (something like overloading)
		$specialHandlerName = 'set' . ucfirst($this->method);
		if (method_exists($this, $specialHandlerName)) {
			$this->$specialHandlerName($name, $type, $value);
			return;
		}

		// common handling
		if ($type == 'array' || $type == 'object') {
			settype($value, $type);
			$value = serialize($value);
		} else {
			settype($value, $type);
		}

		$this->resource[$name] = $value;
	}

	/**
	 * Unset a variable.
	 *
	 * @param $name
	 */
	function remove($name) {
		// exists a special handler? (something like overloading)
		$specialHandlerName = 'remove' . ucfirst($this->method);
		if (method_exists($this, $specialHandlerName)) {
			$this->$specialHandlerName($name);
			return;
		}

		// common handling
		unset($this->resource[$name]);
	}



	/**
	 * Special handler for setting cookies.
	 *
	 * @param string $name
	 * @param string $type
	 * @param mixed $value
	 */
	private function setCookie($name, $type, $value) {
		switch ($type) {
			case 'object':
			case 'array':
				throw Exc('Objects and arrays cannot be set as cookies. Serializing is not recommended because of some security issues.');
				break;

			case 'bool':
				settype($value, 'int'); // cookies have problems with boolean values
				break;

			default:
				settype($value, $type);
				break;
		}
		setcookie($name, $value, NULL, '/');
	}

	/**
	 * Special handler for unsetting cookies.
	 *
	 * @param string $name
	 */
	private function removeCookie($name) {
		setcookie($name, '', time() - (128 * 3600), '/');
	}
	
}
