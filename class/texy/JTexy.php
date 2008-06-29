<?php

/**
 * Joss framework & content management system.
 *
 * Created 13.6.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Special 'edition' of Texy! for Joss.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JTexy extends Texy {

	/**
	 * If cached.
	 *
	 * @var bool
	 */
	private $cached;

	function __construct() {
		parent::__construct();
		$config = JConfig::getInstance();
		$cached = (bool)$config['cached'];

		$this->setOutputMode(($config['xhtml'])? Texy::XHTML1_TRANSITIONAL : Texy::HTML4_STRICT);
		Html::$xhtml = $config['xhtml'];
//		$this->htmlOutputModule->removeOptional = FALSE;

		$this->imageModule->root = JOSS_URL_ROOT . '/web/file/';
		$this->imageModule->leftClass  = 'float-left';
		$this->imageModule->rightClass = 'float-right';
		$this->imageModule->defaultAlt = '';
		$this->headingModule->generateID = TRUE;

		$this->addHandler('phrase', array($this, 'phraseHandler'));
		$this->addHandler('script', array($this, 'scriptHandler'));
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
			$link->URL = JRouter::url($link->URL);
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
	 * @return Html|string|FALSE
	 */
	public function scriptHandler($invocation, $cmd, $args, $raw) {
		try {
			// plugin name
			$cmd = JPlugin::resolveName($cmd);

			if (class_exists($cmd)) {
				$plugin = new $cmd((array)$args, $this);

				// conditions
				if (!$plugin instanceof JPlugin) {
					throw new InvalidStateException("Class doesn't seem to be a plugin.");
				}
				if (empty($plugin->type)) {
					throw new InvalidStateException("Plugin's type is not set.");
				}

				// processing
				if (($this->cached && !$plugin->cached) || $plugin->delayed) {
					// plugin's output mustn't be cached OR plugin should be processed after Texy!
					$string = '<!--' . (($plugin->delayed)? 'D--' : '') . '{{' . $cmd . ': ' . $raw . '}}-->';
				} else {
					$string = $plugin->process();
					if (is_object($string)) {
						$string = $string->__toString();
					}
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

}
