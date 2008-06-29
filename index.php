<?php

/**
 * Joss framework & content management system.
 *
 * "In the beginning was the index.php." Created 29.12.2007.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */

// version
if (version_compare(PHP_VERSION , '5.1', '<')) exit('Application needs \'PHP 5.1\' or newer.');

/*
 * JOSS_APT_DIR - root directory for all includes except of autoload
 * JOSS_CLASS_DIR - root directory for autoload (can be changed to load only one core for more websites)
 * JOSS_URL_ROOT - url root used for links
 */
define('JOSS_APP_DIR', rtrim(str_replace('\\', '/', dirname(__FILE__)), '/'));
define('JOSS_CLASS_DIR', rtrim(str_replace('\\', '/', JOSS_APP_DIR . '/class')), '/');
define('JOSS_URL_ROOT', rtrim(str_replace('\\', '/', (dirname($_SERVER['PHP_SELF']) == '/')? '' : dirname($_SERVER['PHP_SELF']))), '/');

// exceptions and autoload
require JOSS_CLASS_DIR . '/nette/exceptions.php';
require JOSS_CLASS_DIR . '/JAutoLoad.php';

// application
new JDoc();
