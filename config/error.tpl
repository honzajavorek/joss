<?php

/**
 * Joss framework & content management system.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://work.javorek.net/joss
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/*
 passed parameters: $errors
*/
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>An error has occured</title>
</head><body>
<!-- Apache-like error page :) -->
<h1>An error has occured</h1>
<p>Something really bad happened. Please contact the maintainer.</p>
<ul>
<? foreach($errors as $error): ?>
	<li><?= $error ?></li>
<? endforeach ?>
</ul>
</body></html>
<?php

// fix IE
if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
    $s = " \t\r\n";
    for ($i = 2e3; $i; $i--) echo $s{rand(0, 3)};
}
