<?php

/**
 * Joss framework & content management system.
 *
 * Created 31.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * HTML head automatic generator.
 * 
 * Arguments:
 * 	none
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPHead extends JPlugin {
	
	public $delayed = TRUE;

	public $type = Texy::CONTENT_MARKUP;
	
	/**
	 * Default configuration.
	 *
	 * @var array
	 */
	private $config = array(
		'title' => NULL,
		'language' => 'en',
		'xhtml' => true,
		'allowrobots' => false,
		'author' => NULL,
		'copyright' => NULL,
		'keywords' => NULL,
		'description' => NULL
	);
	
	/**
	 * Local reconfiguration.
	 *
	 * @var array
	 */
	private $reconfig = array();
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	/**
	 * Loads global configuration and applies local changes.
	 */
	private function reconfigure() {
		$global = JConfig::getInstance();
		foreach ($global as $key => $value) {
			if (array_key_exists($key, $this->config)) {
				$this->config[$key] = $value;
			}
		}
		try {
			if (class_exists('JPConfig')) {
				$this->config = array_merge($this->config, JPConfig::getData());
			}
		} catch (Exception $e) {
			// class JPConfig doesn't exist
		}
	}
	
	/**
	 * Provides robots.txt setting.
	 *
	 * @return string
	 */
	private function getRobots() {
		return (empty($this->config['allowrobots']) || !$this->config['allowrobots'])?
			'noindex, nofollow' : 'all';
	}
	
	/**
	 * Provides meta tags.
	 *
	 * @return string
	 */
	private function getMeta() {
		// http-equiv
		$metaContent = NHtml::el('meta');
		$metaContent->attrs['http-equiv'] = 'content-type';
		$metaContent->content('text/html; charset=utf-8');
		
		$metaLanguage = NHtml::el('meta');
		$metaLanguage->attrs['http-equiv'] = 'content-language';
		$metaLanguage->content($this->config['language']);
		
		$metaLanguage = NHtml::el('meta');
		$metaLanguage->attrs['http-equiv'] = 'imagetoolbar';
		$metaLanguage->content('no');
		
		// all
		$output = '';
		$meta = array(
			$metaContent,
			$metaLanguage,
			NHtml::el('meta')->name('robots')->content($this->getRobots()),
			NHtml::el('meta')->name('author')->content(str_replace('@', ' at ', $this->config['author'])),
			NHtml::el('meta')->name('generator')->content('Joss + Texy2!'),
			NHtml::el('meta')->name('copyright')->content(preg_replace(array('~\(c\)|&copy;|&#169;~iu', '~(©\\s*)?(\d{4})(-\d{4})?(\\s*©)?~iu'), array('©', '\\1\\2-' . date('Y') . '\\4'), $this->config['copyright'])),
			NHtml::el('meta')->name('keywords')->content(str_replace(', ', ',', $this->config['keywords'])),
			NHtml::el('meta')->name('description')->content($this->config['description'])
		);
		
		// rendering
		foreach ($meta as $tag) {
			if ($tag->content) {
				$output .= "\t" . $tag->__toString() . "\n";
			}
		}
		return $output;
	}
	
	/**
	 * Searches CSS file according to identifier.
	 *
	 * @param string $id
	 * @param bool $main Search for a default style?
	 * @param bool $ie Internet Explorer styles.
	 */
	private function getCssFile($id, $main = true, $ie = false) {
		$dir = JOSS_APP_DIR . '/web/css';
		$ie = ($ie)? '.ie' : '';
		$file = NULL;

		if (is_dir($dir)) {
			$file = "$id$ie.css";
			$f = new JFile("$dir/$file");
			if (!$f->exists()) { // specialized style doesn't exist
				$file = NULL;
				if ($main) {
					$file = "_main$ie.css";
					$f = new JFile("$dir/$file");
					if (!$f->exists()) { // main style doesn't exist
						$file = NULL;
					}
				}
			}
		}
		
		return $file;
	}
	
	/**
	 * Provides CSS files.
	 *
	 * @param string Page identifier.
	 * @return string
	 */
	private function getStyles($id) {
		$output = '';
		$dir = JOSS_APP_DIR . '/web/css';
		$url = JOSS_URL_ROOT . '/web/css';
				
		if (is_dir($dir)) {
			$css = array();
			
			$file = $this->getCssFile($id);
			if ($file) {
				$css[] = NHtml::el('link')->rel('stylesheet')->type('text/css')->href("$url/$file?" . filemtime("$dir/$file"))->media('screen,projection,tv');
			}

			// internet explorer
			$file = $this->getCssFile($id, true, true);
			if ($file) {
				$comment = NHtml::el('link')->rel('stylesheet')->type('text/css')->href("$url/$file?" . filemtime("$dir/$file"))->media('screen,projection,tv');
				$css[] = '<!--[if lte IE 7]>' . $comment->__toString() . '<![endif]-->';
			}

			// media (http://www.w3.org/TR/REC-CSS2/media.html)
			$media = array('all', 'aural', 'braille', 'embossed', 'handheld', 'print', 'projection', 'screen', 'tty', 'tv');
			foreach ($media as $item) {
				$file = $this->getCssFile("_$item", false, false);
				if ($file) {
					$css[] = NHtml::el('link')->rel('stylesheet')->type('text/css')->href("$url/$file?" . filemtime("$dir/$file"))->media($item);
				}
			}
			
			// rendering
			foreach ($css as $tag) {
				if (is_object($tag)) {
					$output .= "\t" . $tag->__toString() . "\n";
				} else {
					$output .= "\t" . $tag . "\n";
				}
			}
		}
		return $output;
	}
	
	/**
	 * Provides JavaScript files.
	 *
	 * @return string
	 */
	private function getScripts() {
		$output = '';
		$dir = JOSS_APP_DIR . '/web/js';
		$url = JOSS_URL_ROOT . '/web/js';
		
		if (is_dir($dir)) {
			$d = dir($dir);
			while (FALSE !== ($entry = $d->read())) {
				if (substr($entry, -3, 3) == '.js') {
					$tag = NHtml::el('script')->type('text/javascript')->src("$url/$entry?" . filemtime("$dir/$entry"));
					$output .= "\t" . $tag->__toString() . "\n";
				}
			}
		}
		
		return $output;
	}
	
	/**
	 * Provides XML feeds.
	 *
	 * @return string
	 */
	private function getFeeds() {
		$output = '';
		$dir = JOSS_APP_DIR . '/web/rss';
		$url = JOSS_URL_ROOT . '/web/rss';
		
		if (is_dir($dir)) {
			$d = dir($dir);
			while (FALSE !== ($entry = $d->read())) {
				if (is_dir("$dir/$entry")) {
					continue; // skip subdirectories
				}
				try {
					
					$rss = new JFile("$dir/$entry", TRUE, 7 * 24);
					$xml = $rss->content;
					$title = $xml->channel->title . ' (RSS ' . $xml['version'] . ')';

					$link = NHtml::el('link')->rel('alternate')->type('application/rss+xml')->title($title)->href($rss->getAddress());
					$output .= "\t" . $link->__toString() . "\n";
					
				} catch (Exception $e) {
					$config = JConfig::getInstance();
					if ($config['debug']) {
						NDebug::exceptionHandler($e);
					}
				}
			}
		}
		
		return $output;
	}
	
	public function process() {
		if (!$this->tpl instanceof JTemplate) {
			throw new JException('JPHead.tpl template file not found.');
		}
		$this->reconfigure();
		$output = array();

		// doctype
		$output['doctype'] = ($this->config['xhtml'])? '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n"
		: '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . "\n";
		
		$output['html'] = ($this->config['xhtml'])? '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $this->config['language'] . "\">\n"
		: "<html>\n";
		
		// meta
		$output['meta'] = $this->getMeta();
		
		// page identifier
		$get = new JInput('get');
		$id = $get->export('doc', 'string');
		
		// css
		$output['css'] = $this->getStyles($id);
		
		// js
		$output['js'] = $this->getScripts();
		
		// rss
		$output['rss'] = $this->getFeeds();
		
		// icon
		$output['icon'] = '';
		$f = new JFile(JOSS_APP_DIR . '/favicon.ico');
		if ($f->exists()) {
			$icon = NHtml::el('link')->rel('shortcut icon')->type('image/x-icon')->href(JOSS_URL_ROOT . '/favicon.ico?' . filemtime(JOSS_APP_DIR . '/favicon.ico'));
			$output['icon'] = "\t" . $icon->__toString() . "\n";
		}
		
		// title
		$title = NHtml::el('title')->setText((string)$this->texy->headingModule->title . ' - ' . $this->config['title']);
		$output['title'] = "\t" . $title->__toString() . "\n";
		
		// fetch
		foreach ($output as $key => $value) {
			$this->tpl->set($key, $value, FALSE);
		}
		return trim($this->tpl->fetch());
		
	}
	
}
