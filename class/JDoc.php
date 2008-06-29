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
	 * Document factory.
	 */
	final static public function render() {
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
		$get = new JInput('get');
		$action = JAction::resolveName($get->export('action', 'string'));
		$driver = JDriver::resolveName($identifier);

		// factory
		if (class_exists($action)) {
			new $action($identifier);
		} elseif(class_exists($driver)) {
			new $driver($identifier);
		} else {
			new JPage($identifier);
		}
	}

	/**
	 * Decodes name.
	 *
	 * @param string $cmd
	 * @return string
	 */
	abstract static public function resolveName($id);

	protected function fixInternetExplorer() {
		// fix IE
		if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
			$s = " \t\r\n";
			for ($i = 2e3; $i; $i--) echo $s{rand(0, 3)};
		}
	}

}
