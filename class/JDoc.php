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
 * Document factory.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
abstract class JDoc extends Object {

	const PATH = '/web/content/';
	const DIRECTORY_HEAD = 'head/';
	const DIRECTORY_FOOT = 'foot/';

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
	 * Selects proper document class to handle the request.
	 */
	final static public function factory() {
		// paths
		$contentRoot = JOSS_APP_DIR . self::PATH;
		self::$paths = array(
			'head' => $contentRoot . self::DIRECTORY_HEAD,
			'text' => $contentRoot,
			'foot' => $contentRoot . self::DIRECTORY_FOOT
		);

		// routing
		$router = new JRouter(self::$paths);
		$identifier = $router->getIdentifier();

		// preparations
		$driver = JDriver::resolveName($identifier);

		// factory
		try {
			if(class_exists($driver)) {
				new $driver($identifier);
			} else {
				new JPage($identifier);
			}
		} catch (TemplateNotFoundException $e) {
			new JPage($identifier);
		}
	}

	protected function fixInternetExplorer() {
		// fix IE
		if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
			$s = " \t\r\n";
			for ($i = 2e3; $i; $i--) echo $s{rand(0, 3)};
		}
	}
	
	/**
	 * Sends common headers. Prepared to be overriden.
	 */
	protected function sendCommonHeaders() {
		if (!headers_sent()) {
			header('Content-Type: text/html; charset=utf-8');
		}
	}

}
