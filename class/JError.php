<?php

/**
 * Joss framework & content management system.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Exception which overrides all native PHP errors.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class PhpErrorException extends Exception
{
}



/**
 * PHP error handling.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JError extends Object {

	/**
	 * Template file for HTML errors.
	 *
	 * @var string
	 */
	private static $template = '';
	
	/**
	 * Handles uncaught exceptions.
	 * 
	 * @param Exc $exception
	 */
	static public function handleException(Exception $exception) {
		self::restore();
		if (self::$template) {
			self::printError(self::$template, (array)$exception->getMessage());
		} else {
			print '<pre>' . $exception->__toString() . '<pre>';
		}
		exit;
	}
	
	/**
	 * Throws exceptions instead of PHP errors.
	 */
	static public function handleError($code, $message, $file, $line, $context) {
        $message = strip_tags($message);
		throw new PhpErrorException("$message (in '" . basename($file) . "' at line $line).");
	}
	

	/**
	 * Sets err/exc handlers.
	 */
	static public function register($level = NULL) {
		if ($level !== NULL) error_reporting($level);
		set_error_handler(array(__CLASS__, 'handleError'), $level);
		set_exception_handler(array(__CLASS__, 'handleException'));
	}
	
	/**
	 * Restores err/exc handlers.
	 */
	static public function restore() {
		restore_error_handler();
		restore_exception_handler();
	}
	
	static public function setErrorTemplate($file) {
		if (!is_file($file)) exit("HTML template file '$file' for errors doesn't exist.");
		self::$template = $file;
	}
	
	/**
	 * Prints a nice HTML error.
	 *
	 * @param string $file Template file.
	 * @param array $errors Array of error messages.
	 */
	static public function printError($file, $errors) {
		header("HTTP/1.0 500 Internal Server Error");
		if (!is_file($file)) exit("HTML template file '$file' for errors doesn't exist.");

		try {
			$tpl = new JTemplate($file);
			$tpl->set('errors', $errors);
	
			print $tpl->fetch();
		} catch (Exception $e) {
			print $e;
		}
	}
    
}
