<?php

/**
 * Joss framework & content management system.
 *
 * Created 7.6.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Manages language versions.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JLang extends NObject {

	const DIRECTORY_LANG = 'lang/';

	private $existing = array();
	private $default;
	private $current;

	public function __construct() {
		$config = JConfig::getInstance();
		$this->default = $this->current = $config['language'];
		$this->searchForLanguages();
		$this->applyBrowserSettings();
	}

	/**
	 * Takes care of user's browser settings.
	 *
	 * See http://diskuse.jakpsatweb.cz/index.php?action=vthread&forum=9&topic=10613.
	 */
	private function applyBrowserSettings() {
		if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// parse settings
			preg_match_all('~([a-z-]+) *(?:; *q=([0-9.]+))?~', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']), $matches);
			$accept = array_combine($matches[1], $matches[2]);
				
			// sets default values, sets type to float
			foreach ($accept as $key => $val) {
				$accept[$key] = ($val == '')? 1.0 : (float)$val;
			}
			arsort($accept);

			// performs change of current setting
			$accept = array_keys($accept);
			foreach ($accept as $language) {
				$l = substr($language, 0, 2);
				if ($this->languageExists($l)) {
					$this->current = $l;
					break;
				}
			}
		}
	}

	/**
	 * Searches for all language versions.
	 */
	private function searchForLanguages() {
		$this->existing[] = $this->default;

		if (self::moreVersionsExist()) {
			$d = dir(JOSS_APP_DIR . JDoc::PATH . self::DIRECTORY_LANG);
			while (false !== ($entry = $d->read())) {
				if (preg_match('~[a-z]{2}~i', $entry)) {
					$this->existing[] = $entry;
				}
			}
			$d->close();
		}
	}

	/**
	 * Detection of presention of other language versions.
	 */
	public static function moreVersionsExist() {
		return is_dir(JOSS_APP_DIR . JDoc::PATH . self::DIRECTORY_LANG);
	}
	
	/**
	 * Detection of presention of a specified language version.
	 */
	public function languageExists($l) {
		if (empty($l)) {
			return false;
		}
		return in_array($l, $this->existing);
	}
	
	/**
	 * Rewrites include paths according to current language settings.
	 *
	 * @param array $paths
	 * @return array
	 */
	public function changePaths(array $paths) {
		if ($this->current == $this->default) { // default language setting, no changes
			return $paths;
		}
		foreach ($paths as $index => $value) { // else... changing paths
			$paths[$index] = str_replace(
				JOSS_APP_DIR . JDoc::PATH,
				JOSS_APP_DIR . JDoc::PATH . self::DIRECTORY_LANG . "$this->current/",
				$value
			);
		}
		return $paths;
	}

	public function setLanguage($lang) {
		$this->current = $lang;
	}
	
	public function getLanguage() {
		return $this->current;
	}

}
