<?php

/**
 * Joss framework & content management system.
 *
 * Created 29.6.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Renders common Texy! page.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPage extends JDoc {

	public function __construct($identifier) {
		$source = $this->loadTexySource(self::$paths['head'], $identifier)
		. $this->loadTexySource(self::$paths['text'], $identifier, NULL)
		. $this->loadTexySource(self::$paths['foot'], $identifier);

		header('Content-type: text/html; charset=utf-8');
		$content = new JFormatter();
		print $content->process($source);
		$this->fixInternetExplorer();
	}

	/**
	 * Includes Texy! source file.
	 *
	 * @param string $path Path to file.
	 * @param string $doc Filename without an extension.
	 * @return string $default Source.
	 */
	private function loadTexySource($path, $doc, $default = self::PAGE_DEFAULT) {
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
				throw new FileNotFoundException("Files *.texy or links *.link corresponding to '$doc' are empty or don't exist.");
			}
		}

		return $source . "\n\n\n";
	}
	
	/**
	 * No decoding needed.
	 *
	 * @param string $id
	 * @return string
	 */
	static public function resolveName($id) {
		return $id;
	}

}
