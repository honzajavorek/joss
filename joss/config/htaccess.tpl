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
 passed parameters: $root, $www, $php, $rewrite
*/
?>
#################################
# JOSS FRAMEWORK: HTACCESS FILE #
#################################
<? if ($rewrite) { ?><IfModule mod_rewrite.c>

## mod
AddDefaultCharset utf-8
RewriteEngine on

## base
RewriteBase <?= $root ?>/
DirectoryIndex index.html index.php  [L]

## endslashes
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{ENV:REDIRECT_STATUS} ^$
RewriteCond %{REQUEST_URI} !\/$
RewriteRule (.*) $0/ [R=301,NE,L]
<?= $www ?>

## errorpages
ErrorDocument 404 <?= $root ?>/?doc=_404
ErrorDocument 403 <?= $root ?>/?doc=_403

## rules
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]

</IfModule><? } ?>

<IfModule mod_php5.c>
<?= $php ?>
</IfModule>

<Files ~ "^[\.]">
	Order allow,deny
	Deny from all
	Satisfy All
</Files>
