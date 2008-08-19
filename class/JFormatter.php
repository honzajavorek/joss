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
 * Document source formatter.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
final class JFormatter extends Object {

	const ALL_AS_SOURCE = FALSE;

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
		$this->texy = new JTexy();
	}

	/**
	 * Escapes all special characters of PCRE regular expressions.
	 *
	 * @param string $s
	 * @return string
	 */
	private function escapePattern($s) {
		return preg_replace('~([\\$\\.\\[\\]\\|\\(\\)\\?\\*\\+\\{\\}\\^\\\])~', '\\\\\1', $s);
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
		//print_r($matches);exit;

		$i = 0;
		$patterns = array();
		$replacements = array();

		foreach ($matches[1] as $cmd) {
			// setting a pattern
			$pattern = '~' . $this->escapePattern($matches[0][$i]) . '~iu';
			if (in_array($pattern, $patterns)) {
				continue; // no duplicate occurances!
			}
			$patterns[$i] = $pattern;

			// processing the plugin
			try {
				$replacements[$i] = '';
				if (class_exists($cmd)) {
					$args = explode(',', str_replace(', ', ',', $matches[3][$i]));
					$plugin = new $cmd((array)$args, $this->texy);

					if (!$plugin instanceof JPlugin) {
						throw new InvalidStateException("Class doesn't seem to be a plugin.");
					}
					$tmp = $plugin->process();
					if ($tmp instanceof Html) {
						$tmp = $tmp->__toString();
					} elseif (is_object($tmp)) {
						throw new InvalidStateException("Delayed or not cached plugins like '$cmd' must not use TexyHtml objects.");
					}
					$replacements[$i] = (string)$tmp;
				}
			} catch (InvalidStateException $e) {
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
		return $this->processBlindedPlugins($html, '~<!--D--{{([^:]+)(:\\s([^}]+)?)?}}-->~iu');
	}

	/**
	 * Searches for commented calls of not cached plugins.
	 *
	 * @param string HTML
	 * @return string
	 */
	private function processNotCachedPlugins($html) {
		return $this->processBlindedPlugins($html, '~<!--{{([^:]+)(:\\s([^}]+)?)?}}-->~iu');
	}

	/**
	 * Makes the very first editations of source. Replaces special constants.
	 *
	 * @param string Source
	 * @return string
	 */
	private function preProcessor($src) {
		// replacements
		$src = str_replace(array(

		'§§ ROOT §§',
		'§§ LANGUAGE §§'
		
		), array(

		JOSS_URL_ROOT . '/',
		$_GET['lang']

		), $src);

		return $src;
	}

	/**
	 * Makes some additional editations of HTML.
	 *
	 * @param string HTML
	 * @return string
	 */
	private function postProcessor($html) {
		// replacements
		$html = preg_replace(array(

		'~(?<!-){{([^}:]+)(:[^}]+)?}}(?!-)~', // to hide buggy plugins
		'~<!-- by Texy[^!]*! -->~iu',
		'~<body([^>]*)>~iu',
		'~</html>~iu',
		'~</body>~iu'
		
		), array(

		'<!-- [plugin error]: \\1 -->',
		'',
		'<body id="body-' . $_GET['doc'] . '"\\1>',
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
		return $this->postProcessor(trim($this->texy->process($this->preProcessor($this->text))));
	}

	/**
	 * Provides formatted output.
	 *
	 * @param string $text
	 * @return string
	 */
	public function process($text = '') {
		$this->text = (string)$text;
		$config = JConfig::getInstance();

		if ($config['cached']) {
			$cache = new JCache($this->text, array($this, 'format'), 300 * 24);
			$html = $this->processNotCachedPlugins($cache->process());
		} else {
			$html = $this->format();
		}

		return (self::ALL_AS_SOURCE)? '<pre>' . htmlspecialchars($html) . '</pre>' : $html;
	}

}
