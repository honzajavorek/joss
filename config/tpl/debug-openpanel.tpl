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
 * Debugger template - open panel.
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @package    Nette
 * @version    $Revision: 9 $ $Date: 2008-05-14 08:27:22 +0200 (st, 14 V 2008) $
 */

// passed parameters: $name, $collaped, $id



?>
	<div class="panel">
		<h2><a href="#" onclick="return !toggle(this, 'pnl<?php echo $id ?>')"><?php echo htmlSpecialChars($name) ?> <span><?php echo $collaped ? '&#x25b6;' : '&#x25bc;' ?></span></a></h2>

		<div id="pnl<?php echo $id ?>" class="<?php echo $collaped ? 'collapsed ' : '' ?>inner">
