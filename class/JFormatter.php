<?php

/**
 * Joss framework & content management system.
 *
 * Created 31.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://work.javorek.net/joss
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Document source formatter.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
final class JFormatter extends NObject {

	const ALL_AS_SOURCE = FALSE;
	
	/**
	 * Configuration object.
	 *
	 * @var Config
	 */
	private $config;
	
	/**
	 * Texy! object.
	 *
	 * @var Texy
	 */
	private $texy;
	
	/**
	 * Formatted text.
	 *
	 * @var string
	 */
	private $text;
	
	public function __construct() {
		$this->config = JConfig::getInstance();
		$this->texy = new Texy();

		// settings
		$this->texy->htmlOutputModule->xhtml = NHtml::$xhtml = $this->config['xhtml']; // http://texy.info/cs/api-htmloutput-module
		$this->texy->htmlOutputModule->removeOptional = FALSE;
		$this->texy->imageModule->root = JOSS_URL_ROOT . '/web/file/';
		$this->texy->imageModule->leftClass  = 'float-left';
		$this->texy->imageModule->rightClass = 'float-right';
		$this->texy->imageModule->defaultAlt = '';
		$this->texy->headingModule->generateID = TRUE;

		$this->texy->addHandler('phrase', array($this, 'phraseHandler'));
		$this->texy->addHandler('script', array($this, 'scriptHandler'));
	}
	
	/**
	 * Extension of links.
	 *
	 * @param TexyHandlerInvocation  handler invocation
	 * @param string
	 * @param string
	 * @param TexyModifier
	 * @param TexyLink
	 * @return TexyHtml|string|FALSE
	 */
	public function phraseHandler($invocation, $phrase, $content, $modifier, $link) {
		if (!$link) return $invocation->proceed();

		if (Texy::isRelative($link->URL)) {
			$link->URL = ($link->URL == 'index')? '' : $link->URL;
			$link->URL = JDoc::url($link->URL);

		} elseif (substr($link->URL, 0, 5) === 'file:') {
			$link->URL = JOSS_URL_ROOT . '/web/file/' . substr($link->URL, 5);
		} elseif (substr($link->URL, 0, 9) === 'download:') {
			$link->URL = JOSS_URL_ROOT . '/web/file/download.php?item=' . urlencode(substr($link->URL, 9));
		}

		return $invocation->proceed();
	}

	/**
	 * Plugin handler.
	 *
	 * @param TexyHandlerInvocation  handler invocation
	 * @param string  command
	 * @param array   arguments
	 * @param string  arguments in raw format
	 * @return NHtml|string|FALSE
	 */
	public function scriptHandler($invocation, $cmd, $args, $raw) {
		try {
			// plugin name
			$cmd = $this->decodePluginName($cmd);
			
			if (class_exists($cmd)) {
				$plugin = new $cmd((array)$args, $this->texy);
				
				// conditions
				if (!$plugin instanceof JPlugin) {
					throw new JException("Class doesn't seem to be a plugin.");
				}
				if (empty($plugin->type)) {
						throw new JException("Plugin's type is not set.");
				}
				
				// processing
				if (($this->config['cached'] && !$plugin->cached) || $plugin->delayed) {
					// plugin's output mustn't be cached OR plugin should be processed after Texy!
					$string = '<!--' . (($plugin->delayed)? 'D--' : '') . '{{' . $cmd . ': ' . $raw . '}}-->';
				} else {
					$string = $plugin->process();
				}
				
				return $invocation->texy->protect($string, $plugin->type);
			}
		} catch (Exception $e) {
			// unknown identifier or error
			$config = JConfig::getInstance();
			if ($config['debug']) {
				throw $e;
			}
			return $invocation->proceed();
		}
	}
	
	/**
	 * Decodes plugin name, {{some_words}} are converted to JPSomeWords.
	 *
	 * @param string $cmd
	 * @return string
	 */
	private function decodePluginName($cmd) {
		$cmd = explode('_', $cmd);
		foreach ($cmd as &$part) {
			$part = ucfirst($part);
		}
		$cmd = 'JP' . implode('', $cmd);
		return $cmd;
	}
	
	/**
	 * Searches for commented calls of plugins.
	 *
	 * @param string HTML
	 * @param string comment regexp pattern
	 * @return string
	 */
	private function processBlindedPlugins($html, $commentPattern) {
		$matches = array();
		preg_match_all($commentPattern, $html, $matches);
		// reversed evaluating (JPHead etc. should be the last)
		foreach ($matches as $i => $match) {
			$matches[$i] = array_reverse($match);
		}
		// print_r($matches);

		$i = 0;
		$patterns = array();
		$replacements = array();

		foreach ($matches[1] as $cmd) {
			// setting a pattern
			$pattern = '~' . $matches[0][$i] . '~iu';
			if (in_array($pattern, $patterns)) {
				continue; // no duplicate occurances!
			}
			$patterns[$i] = $pattern;

			// processing the plugin
			try {
				$replacements[$i] = '';
				if (class_exists($cmd)) {
					$args = explode(',', str_replace(', ', ',', $matches[2][$i]));
					$plugin = new $cmd((array)$args, $this->texy);
					if (!$plugin instanceof JPlugin) {
						throw new JException("Class doesn't seem to be a plugin.");
					}
					$tmp = $plugin->process();
					if ($tmp instanceof NHtml) {
						$tmp = $tmp->__toString();
					} elseif (is_object($tmp)) {
						throw new JException("Delayed or not cached plugins like '$cmd' must not use TexyHtml objects.");
					}
					$replacements[$i] = (string)$tmp;
				}
			} catch (JException $e) {
				// unknown identifier or error
				$config = JConfig::getInstance();
				if ($config['debug']) {
					throw $e;
				}
				$replacements[$i] = '';
			}
			$i++;
		}

		// print_r($patterns); print_r($replacements);
		$html = preg_replace($patterns, $replacements, $html);

		return $html;
	}
	
	/**
	 * Searches for commented calls of delayed plugins.
	 *
	 * @param string HTML
	 * @return string
	 */
	private function processDelayedPlugins($html) {
		return $this->processBlindedPlugins($html, '~<!--D--{{([^:]+):\\s([^}]+)?}}-->~iu');
	}

	/**
	 * Searches for commented calls of not cached plugins.
	 *
	 * @param string HTML
	 * @return string
	 */
	private function processNotCachedPlugins($html) {
		return $this->processBlindedPlugins($html, '~<!--{{([^:]+):\\s([^}]+)?}}-->~iu');
	}
	
	/**
	 * Makes some additional editations of HTML.
	 *
	 * @param string HTML
	 * @return string
	 */
	private function postProcessor($html) {
		$get = new JInput('get');
		
		// replacements
		$html = preg_replace(array(

		'~<!-- by Texy[^!]*! -->~iu',
		'~<body([^>]*)>~iu',
		'~</html>~iu',
		'~</body>~iu'
		
		), array(

		'',
		'<body id="body-' . $get->export('doc', 'string') . '"\\1>',
		'',
		"</body>\n</html>"
		
		), $html);

		return $this->processDelayedPlugins($html);
	}
	
	/**
	 * Formatting trigger.
	 * 
	 * This is NOT a STANDARD WAY how to use JFormatter. This function is public to
	 * allow Cache object load content for cache files. For access to content use
	 * $jformatter->process().
	 */
	public function format() {
		return $this->postProcessor(trim($this->texy->process($this->text)));
	}
	
	/**
	 * Provides formatted output.
	 *
	 * @param string $text
	 * @return string
	 */
	public function process($text = '') {
		$this->text = (string)$text;
		
		if ($this->config['cached']) {
			$cache = new JCache($this->text, array($this, 'format'), 300 * 24);
			$html = $this->processNotCachedPlugins($cache->process());
		} else {
			$html = $this->format();
		}

		return (self::ALL_AS_SOURCE)? '<pre>' . htmlspecialchars($html) . '</pre>' : $html;
	}

}
