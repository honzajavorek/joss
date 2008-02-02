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
 * Document controller.
 * 
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
final class JDoc extends NObject {
	
	const PATH_TEXT = '/web/content/';
	const PATH_HEAD = '/web/content/head/';
	const PATH_FOOT = '/web/content/foot/';

	const EXT = '.texy';
	const EXT_LINK = '.link';
	
	const PAGE_ERROR = '_404';
	const PAGE_DEFAULT = '_main';
	
	/**
	 * Paths to files.
	 *
	 * @var array
	 */
	public static $paths = array();
	
	/**
     * Static class - cannot be instantiated.
     */
	public function __construct() {
		self::$paths = array(
			'head' => JOSS_APP_DIR . self::PATH_HEAD,
			'text' => JOSS_APP_DIR . self::PATH_TEXT,
			'foot' => JOSS_APP_DIR . self::PATH_FOOT
		);
		$this->render($this->router());
	}

	/**
	 * URL manager.
	 * 
	 * @return string Document identifier.
	 */
	private function router() {
		$config = JConfig::getInstance();
		$get = new JInput('get');
		if ($config['mod_rewrite']) {
			$rewrite = array(
				'doc' => 'string'
			);

			// main parts
			$dir = dirname($_SERVER['PHP_SELF']);
			$dir = ($dir == '/')? substr($_SERVER['REQUEST_URI'], 1) : str_replace($dir, '', $_SERVER['REQUEST_URI']);
			$params = explode('/', trim($dir, '/'));
			$i = 0;
			foreach ($rewrite as $part => $type) {
				if (strpos($params[$i], '?') !== FALSE) { // query
					break;
				}
				$get->set($part, $type, $params[$i]);
				$i++;
			}
			
			// query
			$q = $params[count($params) - 1];
			$q = explode('&', preg_replace('~(index\\.\\w{3,4})?\?~iu', '', $q));
			foreach ($q as $var) {
				$var = explode('=', $var);
				$var[1] = (!isset($var[1]))? '' : $var[1];
				$get->set(urldecode($var[0]), 'string', urldecode($var[1]));
			}
		}

		if (!$get->export('doc', 'bool')) {
			$get->set('doc', 'string', 'index');
		}
		return strtolower($get->export('doc', 'string'));
	}
	
	/**
	 * Includes source file.
	 *
	 * @param string $path Path to file.
	 * @param string $doc Filename without an extension.
	 * @return string $default Source.
	 */
	private function loadSource($path, $doc, $default = self::PAGE_DEFAULT) {
		// initializations
		$source = '';
		$order = array(
			$doc . self::EXT,
			$doc . self::EXT_LINK,
		);
		if ($default) {
			$order = array_merge($order, array(
				$default . self::EXT,
				$default . self::EXT_LINK			
			));
		}
		
		// searching file
		foreach ($order as $item) {
			$f = new JFile($path . $item);
			if ($f->exists()) {
				$source .= $f->content;
				break;
			}
		}
		
		// not found
		if (trim($source) == '') {
			if ($path == self::$paths['text']) { // if text, includes 404 error page
				header("HTTP/1.0 404 Not Found");
				$f = new JFile(self::$paths['text'] . self::PAGE_ERROR . self::EXT_LINK);
				if ($f->exists()) { // link
					$source .= $f->content;
				} else {
					$f = new JFile(self::$paths['text'] . self::PAGE_ERROR . self::EXT);
					$source .= $f->content;
				}
			} else { // if head or foot
				echo 1;
				//throw new Exception("Files *.texy or links *.link corresponding to '$doc' are empty or don't exist.");
			}
		}
		
		return $source . "\n\n\n";
	}
	
	/**
	 * Renders the page.
	 *
	 * @param string $identifier
	 */
	private function render($identifier) {
		$source = $this->loadSource(self::$paths['head'], $identifier)
			. $this->loadSource(self::$paths['text'], $identifier, NULL)
			. $this->loadSource(self::$paths['foot'], $identifier);

		header('Content-type: text/html; charset=utf-8');
		$content = new JFormatter();
		print $content->process($source);
		
		// fix IE
		if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
		    $s = " \t\r\n";
		    for ($i = 2e3; $i; $i--) echo $s{rand(0, 3)};
		}
	}
	
	/**
	 * Composes URL according to mod_rewrite settings.
	 *
	 * @param string $identifier Page identifier.
	 */
	public static function url($identifier) {
		$config = JConfig::getInstance();
		$identifier = (empty($identifier) || $identifier == 'index')? NULL : urlencode((string)$identifier);
		return ($config['mod_rewrite'] || !$identifier)?
			JOSS_URL_ROOT . '/' . $identifier
			: JOSS_URL_ROOT . '/index.php?doc=' . $identifier;
	}
	
}
