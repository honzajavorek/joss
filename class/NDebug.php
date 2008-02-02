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
 * Debug static class
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @package    Nette
 * @version    $Revision$ $Date$
 */
final class NDebug
{
    /** @var bool  Is output HTML page or textual terminal? */
    public static $html;

    /** @var bool  */
    private static $enabled;


    /**
     * Static class - cannot be instantiated
     */
    final public function __construct()
    {
        throw new LogicException("Cannot instantiate static class " . get_class($this));
    }



    /**
     * Static class constructor
     */
    public static function constructStatic()
    {
        self::$html = PHP_SAPI !== 'cli';

        if (!defined('E_RECOVERABLE_ERROR')) {
            define('E_RECOVERABLE_ERROR', 4096);
        }
    }



    /**
     * Dumps information about a variable in readable format
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
     * Starts/stops stopwatch
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
     * Register error handler routine
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
     * Unregister error handler routine
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
     * Unregister error handler routine
     * @return void
     */
    public static function isEnabled()
    {
        return self::$enabled;
    }



    /**
     * NDebug exception handler
     *
     * @param  Exception
     * @return void
     */
    public static function exceptionHandler(Exception $exception)
    {
        self::disable();
        while (ob_get_level()) ob_end_clean();

        $type = get_class($exception);
        $code = $exception->getCode();
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTrace();
        $context = NULL;

        if (self::$html) {
            self::loadTemplate(
                get_class($exception),
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTrace(),
                NULL,
                $exception
            );
        } else {
            echo get_class($exception) . " '{$exception->getMessage()}' in {$exception->getFile()} on line {$exception->getLine()}\nPHP version "
            . PHP_VERSION . "\n";
        }
        exit;
    }



    /**
     * NDebug error handler
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
        if ($code === E_USER_ERROR) {
            self::disable();
            while (ob_get_level()) ob_end_clean();

            $trace = debug_backtrace();
            array_shift($trace);

            if (self::$html) {
                self::loadTemplate(
                    'User error',
                    $code,
                    $message,
                    $file,
                    $line,
                    $trace,
                    $context,
                    NULL
                );
            } else {
                echo "User error '$message' in $file on line $line\nPHP version " . PHP_VERSION . "\n";
            }
            exit;
        }

        if (($code & error_reporting()) === $code) {
            $types = array(
                E_RECOVERABLE_ERROR => 'Recoverable error',  // PHP 5.2
                E_WARNING => 'Warning',
                E_NOTICE => 'Notice',
                E_USER_WARNING => 'User warning',
                E_USER_NOTICE => 'User notice',
                E_STRICT => 'Strict',
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
     * Load template
     * @return void
     */
    private static function loadTemplate($type, $code, $message, $file, $line, $trace, $context, $exception)
    {
        require JOSS_APP_DIR . '/config/NDebug.tpl'; // NOTE changed! not original
    }

}


NDebug::constructStatic();
