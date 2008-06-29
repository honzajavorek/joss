<?php

/**
 * Nette Framework
 *
 * Copyright (c) 2004, 2008 David Grudl (http://davidgrudl.com)
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


/**
 * Profiler template.
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @package    Nette
 * @version    $Revision: 7 $ $Date: 2008-05-12 03:21:36 +0200 (po, 12 V 2008) $
 */

// passed parameters: (none)


?>
<table style="position: absolute; color: white; background: black; font-size: 10px; font-family: Arial; left: 0; bottom: 0; width: 200px;">
<tr>
	<td>Time:</td>
	<td><?php echo time() - $_SERVER['REQUEST_TIME'] ?> s</td>
</tr>

<?php
$list = get_included_files();
$al = class_exists(/*Nette::Loaders::*/'AutoLoader', FALSE) ?  /*Nette::Loaders::*/AutoLoader::$count : 0;
?><tr title="<?php echo implode("\n", $list) ?>">
	<td>Included files:</td>
	<td><?php echo count($list), ' (', $al ?> by autoloader)</td>
</tr>

<?php
$list = array_diff(get_declared_classes(), spl_classes());
?><tr title="<?php echo implode(", ", $list) ?>">
	<td>Defined classes:</td>
	<td><?php echo count($list) ?></td>
</tr>

<?php
$list = get_declared_interfaces();
?><tr title="<?php echo implode(", ", $list) ?>">
	<td>Defined interfaces:</td>
	<td><?php echo count($list) ?></td>
</tr>

<?php
$list = get_defined_functions();
$list = (array) @$list['user'];
?><tr title="<?php echo implode(", ", $list) ?>">
	<td>Defined functions:</td>
	<td><?php echo count($list) ?></td>
</tr>

<?php
$list = get_defined_constants(TRUE);
$list = array_keys((array) @$list['user']);
?><tr title="<?php echo implode(", ", $list) ?>">
	<td>Defined constants:</td>
	<td><?php echo count($list) ?></td>
</tr>
</table>
