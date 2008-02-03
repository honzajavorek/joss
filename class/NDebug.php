<?php

/**
 * Nette Framework
 *
 * Copyright (c) 2004, 2008 David Grudl aka -dgx- (http://www.dgx.cz)
 *
 * This source file is subject to the "Nette license" that is bundled
 * with this package in the file license.txt.
 *
 * For more information please see http://nettephp.com/
 *
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @license    http://nettephp.com/license  Nette license
 * @link       http://nettephp.com/
 * @category   Nette
 * @package    Nette
 */

// namespace Nette;


/**
 * Debug static class.
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @package    Nette
 * @version    $Revision: 8 $ $Date: 2008-02-01 02:29:10 +0100 (pĂˇ, 01 II 2008) $
 */
final class NDebug
{
    /** @var bool  Is output HTML page or textual terminal? */
    public static $html;

    /** @var bool  */
    private static $enabled;

    /** @var array  */
    public static $keysToHide = array('password', 'passwd', 'pass', 'pwd', 'creditcard', 'credit card', 'cc', 'pin');



    /**
     * Static class - cannot be instantiated.
     */
    final public function __construct()
    {
        throw new LogicException("Cannot instantiate static class " . get_class($this));
    }



    /**
     * Static class constructor.
     */
    public static function constructStatic()
    {
        self::$html = PHP_SAPI !== 'cli';

        if (!defined('E_RECOVERABLE_ERROR')) {
            define('E_RECOVERABLE_ERROR', 4096);
        }
    }



    /**
     * Dumps information about a variable in readable format.
     *
     * @param  mixed  variable to dump.
     * @param  bool   return output instead of printing it?
     * @return string
     */
    public static function dump($var, $return = FALSE)
    {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        if (self::$html) {
            $output = htmlspecialchars($output, ENT_NOQUOTES);
            $output = preg_replace('#\]=&gt;\n\ +([a-z]+)#i', '] => <span>$1</span>', $output);
            $output = preg_replace('#^([a-z]+)#i', '<span>$1</span>', $output);
            $output = "<pre class=\"dump\">$output</pre>\n";
        } else {
            $output = preg_replace('#\]=>\n\ +#i', '] => ', $output) . "\n";
        }

        if (!$return) echo $output;

        return $output;
    }



    /**
     * Starts/stops stopwatch.
     * @return elapsed seconds
     */
    public static function timer()
    {
        static $time = 0;
        $now = microtime(TRUE);
        $delta = $now - $time;
        $time = $now;
        return $delta;
    }



    /**
     * Register error handler routine.
     * @param  int   error_reporting level
     * @return void
     */
    public static function enable($level = NULL)
    {
        self::$enabled = TRUE;
        if ($level !== NULL) error_reporting($level);
        set_error_handler(array(__CLASS__, 'errorHandler'));
        set_exception_handler(array(__CLASS__, 'exceptionHandler')); // buggy in PHP 5.2.1
    }



    /**
     * Unregister error handler routine.
     * @return void
     */
    public static function disable()
    {
        if (self::$enabled) {
            restore_error_handler();
            restore_exception_handler();
            self::$enabled = FALSE;
        }
    }



    /**
     * Unregister error handler routine.
     * @return void
     */
    public static function isEnabled()
    {
        return self::$enabled;
    }



    /**
     * NDebug exception handler.
     *
     * @param  Exception
     * @return void
     */
    public static function exceptionHandler(Exception $exception)
    {
        self::disable();
        while (ob_get_level() && ob_end_clean());

        if (self::$html) {
            self::blueScreen($exception);
        } else {
            echo $exception->__toString() . "\nPHP version " . PHP_VERSION . "\nNette Framework version 0.7\n";
        }

        exit;
    }



    /**
     * NDebug error handler.
     *
     * @param  int    level of the error raised
     * @param  string error message
     * @param  string filename that the error was raised in
     * @param  int    line number the error was raised at
     * @param  array  an array of variables that existed in the scope the error was triggered in
     * @return void
     */
    public static function errorHandler($code, $message, $file, $line, $context)
    {
        $fatals = array(
            E_ERROR => 'Fatal error', // unfortunately not catchable
            E_CORE_ERROR => 'Fatal core rrror', // not catchable
            E_COMPILE_ERROR => 'Fatal compile error', // unfortunately not catchable
            E_USER_ERROR => 'Fatal error',
            E_PARSE => 'Parse error', // unfortunately not catchable
            E_RECOVERABLE_ERROR => 'Catchable fatal error', // since PHP 5.2
        );

        if (isset($fatals[$code])) {
            self::disable();
            while (ob_get_level() && ob_end_clean());

            $trace = debug_backtrace();
            array_shift($trace);
            $type = $fatals[$code];

            if (self::$html) {
                self::blueScreen(NULL, $type, $code, $message, $file, $line, $trace, $context);
            } else {
                echo "$type '$message' in $file on line $line\nPHP version " . PHP_VERSION . "\nNette Framework version 0.7\n";
            }
            exit;
        }

        if (($code & error_reporting()) === $code) {
            $types = array(
                E_WARNING => 'Warning',
                E_CORE_WARNING => 'Core warning', // not catchable
                E_COMPILE_WARNING => 'Compile warning', // not catchable
                E_USER_WARNING => 'Warning',
                E_NOTICE => 'Notice',
                E_USER_NOTICE => 'Notice',
                E_STRICT => 'Strict standards',
            );
            $type = isset($types[$code]) ? $types[$code] : 'Unknown error';
            if (self::$html) {
                echo "<b>$type:</b> $message in <b>$file</b> on line <b>$line</b>\n<br />";
            } else {
                echo "$type: $message in $file on line $line\n";
            }
        }
    }



    /**
     * Paint blue screen.
     * @return void
     */
    public static function blueScreen($exception, $type = NULL, $code = NULL, $message = NULL, $file = NULL, $line = NULL, $trace = NULL, $context = NULL)
    {
        if ($exception) {
            $type = get_class($exception);
            $code = $exception->getCode();
            $message = $exception->getMessage();
            $file = $exception->getFile();
            $line = $exception->getLine();
            $trace = $exception->getTrace();
        }
        require JOSS_APP_DIR . '/config/NDebug.tpl'; // NOTE changed! not original
    }



    /**
     * Filters output from self::dump() for sensitive informations.
     * @param  string  content
     * @param  string  additional key
     * @return void
     */
    private static function filter($content, $key = NULL)
    {
        if ($key !== NULL && array_search(strtolower($key), self::$keysToHide, TRUE)) {
            return '<i>*** hidden ***</i>';
        }

        return preg_replace(
            '#^(\s*\["(' . implode('|', self::$keysToHide) . ')"\] => <span>string</span>).+#mi',
            '$1 (?) <i>*** hidden ***</i>',
            $content
        );
    }

}


NDebug::constructStatic();
