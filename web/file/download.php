<?php

/**
 * Joss framework & content management system.
 *
 * Created 4.2.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */

// forces download

$_GET['item'] = (empty($_GET['item']))? '' : './' . $_GET['item'];
if (file_exists($_GET['item'])) {
	header("Content-Description: File Transfer");
	header("Content-Type: application/force-download");
	header("Content-Disposition: attachment; filename=\"" . basename($_GET['item']) . "\"");
	readfile($_GET['item']);
} else {
	header("HTTP/1.0 404 Not Found");
	include '../class/config.php';
	Config::printError('<p>The requested file to download was not found.</p>');
}
?>
