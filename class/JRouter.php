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
final class JRouter extends NObject {

	private $paths;
	private $config;
	private $get;

	/**
	 * Rewriting rules.
	 *
	 * @var array
	 */
	private $rewrite = array('doc' => 'string');

	/**
	 * Language manager.
	 *
	 * @var JLang
	 */
	private $lang;

	public function __construct(&$paths, $contentRoot) {
		$this->paths = &$paths;
		$this->config = JConfig::getInstance();
		$this->get = new JInput('get');

		// language versions
		$this->lang = new JLang($contentRoot);
		if ($this->lang->otherVersionsExist()) {
			// adding a language to the beginning of array
			$this->rewrite = array_merge(array('lang' => 'string'), $this->rewrite);
		}
	}

	private function rewrite() {
		// main parts
		$dir = dirname($_SERVER['PHP_SELF']);
		$dir = ($dir == '/')? substr($_SERVER['REQUEST_URI'], 1) : str_replace($dir, '', $_SERVER['REQUEST_URI']);
		$params = explode('/', trim($dir, '/'));

		$i = 0;
		foreach ($this->rewrite as $part => $type) {
			if (!array_key_exists($i, $params)) { // nothing
				break;
			}
			if (strpos($params[$i], '?') !== FALSE) { // query
				break;
			}
			$this->get->set($part, $type, $params[$i]);
			$i++;
		}

		// query
		$q = $params[count($params) - 1];
		$q = explode('&', preg_replace('~(index\\.\\w{3,4})?\?~iu', '', $q));
		foreach ($q as $var) {
			$var = explode('=', $var);
			$var[1] = (!isset($var[1]))? '' : $var[1];
			$this->get->set(urldecode($var[0]), 'string', urldecode($var[1]));
		}
	}

	/**
	 * Returns routed document identifier.
	 *
	 * @return string Document identifier.
	 */
	public function getIdentifier() {
		if ($this->config['mod_rewrite']) {
			$this->rewrite();
		}

		// defaults
		if (!$this->get->export('doc', 'bool')) { // default 'page'
			$this->get->set('doc', 'string', 'index');
		}
		if (!$this->lang->languageExists($this->get->export('lang', 'string'))) { // default 'language'
			if ($this->lang->otherVersionsExist()) {
				header('HTTP/1.1' . ($this->get->export('lang', 'bool'))? '301 Moved Permanently' : '404 Not Found');
				header('Location: ' . JOSS_URL_ROOT . '/' . $this->lang->getLanguage() . '/');
				exit();
			} else {
				$this->get->set('lang', 'string', $this->lang->getLanguage());
			}
		}

		// languages
		$this->lang->setLanguage($this->get->export('lang', 'string'));
		$this->paths = $this->lang->changePaths($this->paths);

		return strtolower($this->get->export('doc', 'string'));
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

}
