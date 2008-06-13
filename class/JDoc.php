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
 * Document controller.
 * 
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
final class JDoc extends NObject {
	
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
	 * Constructor.
	 */
	public function __construct() {
		$contentRoot = JOSS_APP_DIR . self::PATH;
		self::$paths = array(
			'head' => $contentRoot . self::DIRECTORY_HEAD,
			'text' => $contentRoot,
			'foot' => $contentRoot . self::DIRECTORY_FOOT
		);
		
		$router = new JRouter(self::$paths);	
		$this->render($router->getIdentifier());
	}
	
	/**
	 * Includes source file.
	 *
	 * @param string $path Path to file.
	 * @param string $doc Filename without an extension.
	 * @return string $default Source.
	 */
	private function loadSource($path, $doc, $default = self::PAGE_DEFAULT) {
		// initializations
		$source = '';
		$order = array(
			$doc . self::EXT,
			$doc . self::EXT_LINK,
		);
		if ($default) {
			$order = array_merge($order, array(
				$default . self::EXT,
				$default . self::EXT_LINK			
			));
		}
		
		// searching file
		foreach ($order as $item) {
			$f = new JFile($path . $item);
			if ($f->exists()) {
				$source .= $f->content;
				break;
			}
		}
		
		// not found
		if (trim($source) == '') {
			if ($path == self::$paths['text']) { // if text, includes 404 error page
				header("HTTP/1.0 404 Not Found");
				$f = new JFile(self::$paths['text'] . self::PAGE_ERROR . self::EXT_LINK);
				if ($f->exists()) { // link
					$source .= $f->content;
				} else {
					$f = new JFile(self::$paths['text'] . self::PAGE_ERROR . self::EXT);
					$source .= $f->content;
				}
			} else { // if head or foot
				echo 1;
				//throw new Exception("Files *.texy or links *.link corresponding to '$doc' are empty or don't exist.");
			}
		}
		
		return $source . "\n\n\n";
	}
	
	/**
	 * Renders the page.
	 *
	 * @param string $identifier
	 */
	private function render($identifier) {
		// source
		$source = $this->loadSource(self::$paths['head'], $identifier)
			. $this->loadSource(self::$paths['text'], $identifier, NULL)
			. $this->loadSource(self::$paths['foot'], $identifier);

		header('Content-type: text/html; charset=utf-8');
		$content = new JFormatter();
		print $content->process($source);
		
		// fix IE
		if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
		    $s = " \t\r\n";
		    for ($i = 2e3; $i; $i--) echo $s{rand(0, 3)};
		}
	}
	
}
