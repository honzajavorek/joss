<?php

/**
 * Joss framework & content management system.
 *
 * Created 26.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Encapsulates access to local/remote files.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JFile extends NObject {

	/**
	 * Data loaded from the file.
	 *
	 * @var mixed
	 */
	private $content = NULL;

	/**
	 * Path to the file.
	 *
	 * @var string
	 */
	public $file;

	/**
	 * Type of data.
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * Type of location (local/remote).
	 *
	 * @var bool
	 */
	public $local = FALSE;

	/**
	 * Target file, if provided by link.
	 *
	 * @var JFile
	 */
	private $link = NULL;

	/**
	 * Caching settings.
	 *
	 * @var bool
	 */
	private $cached = FALSE;

	/**
	 * Character set encoding.
	 *
	 * @var string
	 */
	private $encoding = 'UTF-8';

	/**
	 * Cache expiration in hours.
	 *
	 * @var float
	 */
	private $expires = 0;

	/**
	 * Supported groups of extensions.
	 *
	 * @var array
	 */
	private static $extGroups = array(
		'txt' => array('', 'txt', 'texy', 'php', 'html', 'htm', 'tpl', 'css'),
		'xml' => array('xml', 'rss')
	);

	/**
	 * Supported MIME types.
	 *
	 * @var array
	 */
	private static $mimeGroups = array(
		'txt' => array('', 'text/plain', 'text/html', 'application/xhtml+xml', 'text/css', 'text/javascript'),
		'xml' => array('text/xml', 'application/xml', 'application/atom+xml', 'application/rss+xml')
	);

	/**
	 * Initialization indicator.
	 *
	 * @var bool
	 */
	private static $initialized = FALSE;

	/**
	 * Constructor.
	 *
	 * @param string $file Path to the file.
	 * @param bool $cached If should be cached.
	 * @param float $expires In hours.
	 */
	public function __construct($file, $cached = FALSE, $expires = 24) {
		if (!is_numeric($this->expires)) {
			throw new InvalidArgumentException("Expiration time must be numeric.");
		}

		$this->init();
		$this->cached = $cached;
		$this->expires = $expires;
		$this->checkStreamWrapper($file); // sets $this->local
		$this->file = $file;

		// file type
		$this->type = $this->checkType();
	}

	/**
	 * File creator.
	 *
	 * @param mixed $content
	 */
	public function setContent($content) {
		$file = $this->file;
		// locality
		if (!$this->local) {
			throw new JException("File '$file' is not local, cannot be changed!");
		}
		// data serialization
		if (is_object($content) || is_array($content)) {
			$content = serialize($content);
		}
		// saving changes
		$file = str_replace('safe://', '', $file);
		if (file_put_contents($file, $content) === FALSE) {
			throw new JException("File '$file' cannot be created or changed.");
		}
		@chmod($file, 0777);
		$this->content = NULL;
	}

	/**
	 * Content handler.
	 *
	 * @return mixed
	 */
	public function getContent() {
		if (!$this->exists()) {
			throw new JException("File '$this->file' does not exist.");
		}
		if (!$this->content) {
			if ($this->cached) { // cache
				$cache = new JCache($this->file, array($this, 'loadContent'), $this->expires);
				$this->content = $cache->process();
			} else { // not cached
				$this->content = $this->loadContent();
			}
		}
		return $this->content;
	}

	/**
	 * Returns valid URL address to a file.
	 *
	 * @return string $address
	 */
	public function getAddress() {
		if ($this->local) {
			$address = str_replace(array(
				'safe://',
			JOSS_APP_DIR
			), array(
				'',
			JOSS_URL_ROOT
			), $this->file);
		} else {
			$address = $this->file;
		}
		return $address;
	}

	/**
	 * If linked, provides address to the target file.
	 *
	 * @return bool
	 */
	public function isLinked() {
		return (is_object($this->link))? TRUE : FALSE;
	}

	/**
	 * Exists the file?
	 *
	 * @return bool
	 */
	public function exists() {
		if (stripos($this->file, 'https://') !== FALSE) { // HTTPS

			return TRUE; // TODO check the existency

		} elseif (stripos($this->file, 'http://') !== FALSE) { // HTTP URL
				
			$url = str_replace('http://', '', $this->file);
			if (strstr($url, '/')) {
				$url = explode('/', $url, 2);
				$url[1] = '/' . $url[1];
			} else {
				$url = array($url, '/');
			}
			$fh = fsockopen($url[0], 80);
			if ($fh) {
				fputs($fh, 'GET ' . $url[1] . " HTTP/1.1\nHost:" . $url[0] . "\n\n");
				if (fread($fh, 22) == 'HTTP/1.1 404 Not Found') {
					return FALSE;
				}
				else {
					return TRUE;
				}
			} else {
				return FALSE;
			}

		} elseif ($this->local) { // local file

			return @is_file(str_replace('safe://', '', $this->file));
				
		} else { // others (ftp, ...)

			throw new JException('Unable to decide if the file exists.');
				
		}
	}

	/**
	 * Removes the file.
	 */
	public function unlink() {
		$file = $this->file;
		if (!$this->local) {
			throw new JException("File '$file' is not local, cannot be changed!");
		}
		if (!$this->exists($file)) {
			throw new JException("File '$file' does not exist.");
		}
		if (!unlink($file)) {
			throw new JException("File '$file' cannot be deleted.");
		}
	}

	/**
	 * Initializes atomic operations with files.
	 */
	private function init() {
		if (!self::$initialized) {
			if (!NSafeStream::register()) {
				throw new JException('Unable to register protocol for atomic operations.');
			}
			self::$initialized = TRUE;
		}
	}

	/**
	 * Checks an presence of stream wrapper, adds safe:// for local files.
	 *
	 * @param string $file
	 */
	private function checkStreamWrapper(&$file) {
		$wrappers = stream_get_wrappers();
		$found = FALSE;
		foreach ($wrappers as $wrapper) {
			if (stripos($file, $wrapper . '://') !== FALSE) {
				$found = TRUE;
				if ($wrapper == 'safe') {
					$this->local = TRUE;
				} else { // remote file
					$val = strtolower(ini_get('allow_url_fopen'));
					if (!($val === 'on' || $val === 'TRUE' || $val === 'yes' || $val % 256)) {
						throw new JException('Unable to work with remote files, \'allow_furl_open\' is turned off.');
					}
				}
				break;
			}
		}
		if (!$found) { // local file
			$this->local = TRUE;
			$file = "safe://$file"; // safe atomic operations
		}
	}

	/**
	 * Resolves a type of requested file.
	 *
	 * @return string File type.
	 */
	private function checkType() {
		if (!$this->exists()) {
			return NULL;
		}

		if ($this->local) { // local file
			$info = pathinfo($this->file);
			$type = (isset($info['extension']))? $info['extension'] : '';
			$groups = self::$extGroups;
		} else { // remote file
			list($type, $encoding) = $this->getContentType($this->file);
			$groups = self::$mimeGroups;
			$this->encoding = strtoupper($encoding); // encoding
		}

		// link check
		if ($type == 'link') {
			$this->link = $this->followLink($this->file);
			$type = $this->type;
		}

		// extension groups
		foreach ($groups as $group => $types) {
			if (in_array($type, $types)) {
				$type = $group;
				break;
			}
		}

		return $type;
	}

	/**
	 * Forces UTF-8 for remote files.
	 *
	 * @param string $s
	 */
	private function checkEncoding(&$s) {
		if ($this->encoding != 'UTF-8') {
			$s = iconv($this->encoding, 'UTF-8//TRANSLIT', $s);
		}
	}

	/**
	 * Returns MIME type of remote file and it's encoding charset.
	 *
	 * Inspired by http://nadeausoftware.com/articles/2007/06/php_tip_how_get_web_page_content_type.
	 *
	 * @param string $link
	 * @return array First is MIME, second charset.
	 */
	private function getContentType($link) {
		$fp = fopen($link, 'r');
		$meta = stream_get_meta_data($fp);
		$meta = array_reverse($meta['wrapper_data']); // array reverse because we want the last Content-type
		foreach ($meta as $header) {
			$matches = array();
			preg_match('~content-type:\\s+([^;]+)(;\\s+charset=([a-z0-9_-]+))?~i', $header, $matches);
			if (!empty($matches)) {
				break;
			}
		}
		fclose($fp);

		return array($matches[1], (isset($matches[3]))? $matches[3] : 'UTF-8');
	}

	/**
	 * Follows the link and loads target's properties.
	 *
	 * @param string $f Link file to follow.
	 * @return JFile
	 */
	private function followLink($f) {
		$link = trim(file_get_contents($f));
		if (empty($link)) {
			throw new JException("Unable to follow link '$file'.");
		}
		$link = preg_replace('~^(\\./|(\\.{2}/)|/)~', JOSS_APP_DIR . '/\\2', $link);
		$file = new JFile($link, FALSE, NULL);

		$this->local = $file->local;
		$this->file = $file->file;
		$this->type = $file->type;
		$this->content = NULL;
		return $file;
	}

	/**
	 * Internal general way how to get the content.
	 *
	 * This is NOT a STANDARD WAY how to use JFile. This function is public to
	 * allow Cache object load content for cache files. For access to content use
	 * overloading such as $jfile->content or directly $jfile->getContent().
	 *
	 * @return mixed
	 */
	public function loadContent() {
		if ($this->isLinked()) {
			return $this->link->getContent();
		}

		$loader = 'load' . ucfirst($this->type);

		// calling a loader
		if (!method_exists($this, $loader)) {
			throw new DomainException("File type '{$this->type}' is not supported.");
		}
		return $this->$loader($this->file);
	}

	/**
	 * INI file loader.
	 *
	 * @link http://en.wikipedia.org/wiki/INI_file
	 * @param string $file
	 * @return array
	 */
	private function loadIni($file) {
		return parse_ini_file($file, TRUE);
	}

	/**
	 * Cache file loader.
	 *
	 * @param string $file
	 * @return mixed
	 */
	private function loadCache($file) {
		return JCache::load($this->loadTxt($file));
	}

	/**
	 * RSS file loader.
	 *
	 * @link http://en.wikipedia.org/wiki/Rss
	 * @param string $file
	 * @return SimpleXMLElement
	 */
	private function loadRss($file) {
		return $this->loadXml($file);
	}

	/**
	 * XML file loader.
	 *
	 * @link http://en.wikipedia.org/wiki/Xml
	 * @param string $file
	 * @return SimpleXMLElement
	 */
	private function loadXml($file) {
		$xml = @simplexml_load_file(str_replace('safe://', '', $file));
		if ($xml === FALSE) {
			throw new JException("File '$file' is not valid XML.");
		}
		return $xml;
	}

	/**
	 * Common text file loader.
	 *
	 * @link http://en.wikipedia.org/wiki/Text_file
	 * @param string $file
	 * @return string
	 */
	private function loadTxt($file) {
		$string = @file_get_contents($file);
		if ($string === FALSE) {
			throw new JException("Unable to parse the file '$file'.");
		}
		$this->checkEncoding($string);
		return $string;
	}

}
