<?php

/**
 * Joss framework & content management system.
 *
 * Created 8.6.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * URL manager.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
final class JRouter extends Object {

	private $paths;

	/**
	 * Rewriting rules.
	 *
	 * @var array
	 */
	private static $rewrite = array('doc', 'item');

	/**
	 * Language manager.
	 *
	 * @var JLang
	 */
	private $lang;

	public function __construct(&$paths) {
		$this->paths = &$paths;

		// language versions
		$this->lang = new JLang();
		if (JLang::moreVersionsExist()) {
			// adding a language to the beginning of array
			self::$rewrite = array_merge(array('lang'), self::$rewrite);
		}
	}

	private function rewrite() {
		// main parts
		$dir = dirname($_SERVER['PHP_SELF']);
		$dir = ($dir == '/')? substr($_SERVER['REQUEST_URI'], 1) : str_replace($dir, '', $_SERVER['REQUEST_URI']);
		$params = explode('/', trim($dir, '/'));

		$i = 0;
		foreach (self::$rewrite as $part) {
			if (!array_key_exists($i, $params)) { // nothing
				break;
			}
			if (strpos($params[$i], '?') !== FALSE) { // query
				break;
			}
			$_GET[$part] = $params[$i];
			$i++;
		}

		// query
		$q = $params[count($params) - 1];
		$q = explode('&', preg_replace('~(index\\.\\w{3,4})?\?~iu', '', $q));
		foreach ($q as $var) {
			$var = explode('=', $var);
			$var[1] = (!isset($var[1]))? '' : $var[1];
			$_GET[urldecode($var[0])] = urldecode($var[1]);
		}
	}

	/**
	 * Returns routed document identifier.
	 *
	 * @return string Document identifier.
	 */
	public function getIdentifier() {
		$config = JConfig::getInstance();
		if ($config['mod_rewrite']) {
			$this->rewrite();
		}

		// defaults
		if (empty($_GET['doc'])) { // default 'page'
		    $_GET['doc'] = 'index';
		}
		if (empty($_GET['lang']) || !$this->lang->languageExists($_GET['lang'])) { // default 'language'
			if (JLang::moreVersionsExist()) {
				header('HTTP/1.1' . (!empty($_GET['lang']))? '301 Moved Permanently' : '404 Not Found');
				header('Location: ' . JOSS_URL_ROOT . '/' . $this->lang->getLanguage() . '/');
				exit();
			} else {
			    $_GET['lang'] = $this->lang->getLanguage(); // from now, everywhere globally accessible
			}
		}

		// languages
		$this->lang->setLanguage((string)$_GET['lang']);
		$this->paths = $this->lang->changePaths($this->paths);

		// return
		return strtolower($_GET['doc']);
	}

	/**
	 * Composes URL according to mod_rewrite settings.
	 *
	 * @param string $identifier Page identifier.
	 */
	public static function url($identifier) {
		$identifier = trim((string)$identifier, '/');
		$config = JConfig::getInstance();

		if (empty($identifier) || $identifier == 'index') {
			$identifier = NULL;
		} else {
			if (substr($identifier, -5, 5) == 'index') {
				$identifier = substr($identifier, 0, -5);
			}
		}

		return ($config['mod_rewrite'] || !$identifier)?
		JOSS_URL_ROOT . '/' . $identifier
		: JOSS_URL_ROOT . '/index.php?doc=' . $identifier;
	}

	/**
	 * Returns page identifier of the URL.
	 *
	 * @param string $url
	 * @return string
	 */
	public function id($url) {
		$url = trim(preg_replace('~^' . JOSS_URL_ROOT . '~i', '', $url), '/');

		// classic url
		$matches = array();
		if (preg_match('~doc=([^\\?\\=\\&]*)~i', $url, $matches)) {
			return $matches[1];
		}

		// rewrited url
		$parts = explode('/', $url);
		$i = 0;
		foreach (self::$rewrite as $part) {
			if ($part == 'doc') {
				break;
			}
			$i++;
		}

		return $parts[$i];
	}

}
