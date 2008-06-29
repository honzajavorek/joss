<?php

/**
 * Joss framework & content management system.
 *
 * Created 30.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Configurator. Singleton to be called for access to global settings.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
final class JConfig extends Object implements ArrayAccess, Countable, IteratorAggregate {

	const CONFIG_FILE = 'config.xml';

	/**
	 * Initialization detection.
	 *
	 * @var bool
	 */
	private static $initialized = FALSE;

	/**
	 * PHP settings.
	 *
	 * @var array
	 */
	private $directives = array(
		'display_errors'	=>		0,
		'register_globals'	=>		0,
		'allow_url_fopen'	=>		1,
		'magic_quotes_gpc'	=>		0,
		'magic_quotes_runtime'	=>	0,
		'magic_quotes_sybase'	=>	0,
		'short_open_tag'	=>		1,
		'default_charset'	=> 		'utf-8'
		);

		/**
		 * Writable directories.
		 *
		 * @var array
		 */
		private $writable = array(
		'',
		'cache'
		);

		/**
		 * Configuration data.
		 *
		 * Access to global configuration values: e.g. Conf::$data['something']...
		 *
		 * @var array
		 */
		private $data = array();

		private function __construct() {
			if (!self::$initialized) {
				$this->init(self::CONFIG_FILE);
				self::$initialized = TRUE;
			}
		}

		public function __clone() {
			throw new LogicException('Cannot clone! I\'m singleton!');
		}

		/**
		 * Supports INI and XML files.
		 */
		private function loadConfigFile($file) {
			$f = new JFile($file);
			$type = $f->type;

			if (!empty($type)) {
				switch ($type) {
					case 'ini':
						return $f->content;

					case 'xml':
						$xml = (array)$f->content;
						
						// comments
						unset($xml['comment']);
						
						// boolean corrections
						foreach ($xml as $key => $value) {
							if (empty($value) || $value == 'false' || $value == '0') {
								$xml[$key] = false;
							} elseif ($value == 'true') {
								$xml[$key] = true;
							}
						}
						
						return $xml;

					default:
						exit("Configuration file '$file' must be static INI or XML file.");
						break;
				}
					
			}
		}

		/**
		 * Loads configuration file and it's contents saves into a data array.
		 *
		 * @param string $file Configuration ini file.
		 */
		private function init($file) {
			try {
				$this->data = $this->loadConfigFile(JOSS_APP_DIR . '/config/' . $file);
			} catch (Exception $e) {
				exit("Configuration file '$file' doesn't exist.");
			}

			// php settings
			$this->directives['display_errors'] = (int)$this->data['debug'];
			foreach ($this->directives as $directive => $value) {
				ini_set($directive, $value);
			}

			// error handling
			if ($this->data['debug']) {
				Debug::enable(E_ALL | E_NOTICE);
			} else {
				JError::setErrorTemplate(JOSS_APP_DIR . '/config/tpl/error.tpl');
				JError::register(E_ALL | E_NOTICE);
			}

			// conditions check
			$this->checkConditions();

			// files
			$this->makeRobotsFile($this->data['allowrobots']);
			$this->makeSitemapFile($this->data['language'], $this->data['allowrobots']);
			$this->setUpHtaccessFile();
		}

		/**
		 * Gets the boolean value of a configuration option.
		 *
		 * @param string Configuration option name.
		 * @return bool
		 */
		private function getDirective($directive) {
			$val = strtolower(ini_get($directive));
			return $val === 'on' || $val === 'TRUE'
			|| $val === 'yes' || $val % 256;
		}

		/**
		 * Automatic check of conditions.
		 */
		private function checkConditions() {
			$errors = array();
			// apache mod_rewrite
			if ($this->data['mod_rewrite'] && function_exists('apache_get_modules')) {
				$this->data['mod_rewrite'] = in_array('mod_rewrite', apache_get_modules());
			}

			// writability
			if (!$errors) {
				foreach ($this->writable as $dir) {
					$dir = JOSS_APP_DIR . '/' . $dir;
					if (!is_writable($dir)) {
						$errors[] = "Directory '$dir' is not writable.";
					}
				}
			}

			// check of essential files
			if (empty($errors)) {
				if ((!@is_file(JDoc::$paths['head'] . JDoc::PAGE_DEFAULT . JDoc::EXT)
				&& !@is_file(JDoc::$paths['head'] . JDoc::PAGE_DEFAULT . JDoc::EXT_LINK))
					
				|| (!@is_file(JDoc::$paths['foot'] . JDoc::PAGE_DEFAULT . JDoc::EXT)
				&& !@is_file(JDoc::$paths['foot'] . JDoc::PAGE_DEFAULT . JDoc::EXT_LINK))
					
				|| (!@is_file(JDoc::$paths['text'] . JDoc::PAGE_ERROR . JDoc::EXT)
				&& !@is_file(JDoc::$paths['text'] . JDoc::PAGE_ERROR . JDoc::EXT_LINK))
					
				|| (!@is_file(JDoc::$paths['text'] . 'index' . JDoc::EXT)
				&& !@is_file(JDoc::$paths['text'] . 'index' . JDoc::EXT_LINK))) {
					$errors[] = 'Files \'' . JDoc::PAGE_ERROR . '.texy\', \'index.texy\', and \'' . JDoc::PAGE_DEFAULT . '.texy\' for both headers and footers (or links to them) must exist in resources.';
				}
			}

			if (!empty($errors)) {
				JError::printError(JOSS_APP_DIR . '/config/tpl/error.tpl', $errors);
				exit;
			}
		}

		/**
		 * Manages robots.txt.
		 *
		 * @param bool $allowIndexing allow/disallow
		 * @return void
		 */
		private function makeRobotsFile($allowIndexing = TRUE) {
			$f = new JFile(JOSS_APP_DIR . '/robots.txt');
			if ($f->exists()) {
				return;
			}
			if ($allowIndexing) {
				$f->content = '';
			} else {
				$f->content = "User-agent: *\nDisallow: /";
			}
		}

		/**
		 * Manages sitemap.xml.
		 *
		 * @param bool $allowIndexing allow/disallow
		 * @return void
		 */
		private function makeSitemapFile($defaultLanguage, $allowIndexing = TRUE) {
			$f = new JFile(JOSS_APP_DIR . '/sitemap.xml');
			if ($allowIndexing) {
				$map = new JSitemap($defaultLanguage, 60 * 60 * 24 * 7); // every week
				$map->generate($f);
			} elseif ($f->exists()) {
				$f->unlink();
			}
		}

		/**
		 * Manages .htaccess file.
		 */
		private function setUpHtaccessFile() {
			if (is_file(JOSS_APP_DIR . '/.htaccess')) {
				return;
			}
			$tpl = new JTemplate(JOSS_APP_DIR . '/config/tpl/htaccess.tpl');

			// variables
			$tpl->set('root', JOSS_URL_ROOT, FALSE);

			$www = '';
			$hostPrefix = substr($_SERVER['HTTP_HOST'], 0, 4);
			if (JOSS_URL_ROOT == '' && $hostPrefix == 'www.') {
				$www = "\n## www\nRewriteCond %{HTTP_HOST} ^" . substr($_SERVER['HTTP_HOST'], 4)
				. "\nRewriteRule (.*) http://" . $_SERVER['HTTP_HOST'] . "/$1 [R=301,QSA,L]\n";
			}
			$tpl->set('www', $www, FALSE);

			$php = '';
			foreach ($this->directives as $directive => $value) {
				$php .= "php_flag $directive $value\n";
			}
			$tpl->set('php', $php, FALSE);

			$tpl->set('rewrite', (bool)$this->data['mod_rewrite'], FALSE);

			// fetch
			$file = new JFile(JOSS_APP_DIR . '/.htaccess');
			$file->content = $tpl->fetch();
		}



		public function offsetExists($offset) {
			return array_key_exists($offset, $this->data);
		}

		public function offsetGet($offset) {
			return (isset($this->data[$offset]))? $this->data[$offset] : '';
		}

		public function offsetSet($offset, $value) {
			throw new LogicException("Readonly properties.");
		}

		public function offsetUnset($offset) {
			throw new LogicException("Readonly properties.");
		}

		public function getIterator() {
			return new ArrayIterator($this->data);
		}

		public function count() {
			return count($this->data);
		}



		private static $_instance = FALSE;

		public static function getInstance() {
			if (!self::$_instance instanceof self) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

}
