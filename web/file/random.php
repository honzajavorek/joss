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

// random image

/*
 * e.g.
 * 
 * random.php?item=webcam.png 
 */

$name = explode('.', $_GET['item']);
$ext = $name[count($name) - 1];
unset($name[count($name) - 1]);
$name = implode('.', $name);
if (is_file("$name/$name" . 0 . ".$ext")) {
	$info = getimagesize("$name/{$name}0.$ext");
	header("Content-type: $info[mime]");
	header("Content-Disposition: PHP Generated Data");
	
	$max = 0;
	for ($i = $max; file_exists("$name/{$name}$i.$ext"); $i++) {
		$max = $i;
	}
	readfile("./$name/$name" . rand(0, $max) . ".$ext");
} else {
	header("HTTP/1.0 404 Not Found");
	die('<p>The requested image to be displayed randomly was not found.</p>');
}
?>
