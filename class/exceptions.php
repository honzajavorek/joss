<?php

/**
 * Joss framework & content management system.
 *
 * Created 3.7.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



require_once dirname(__FILE__) . '/nette/exceptions.php';



/**
 * The exception that is thrown when accessing a template file that does not exist on disk.
 * @package    Joss
 */
class TemplateNotFoundException extends FileNotFoundException
{
}
