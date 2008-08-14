<?php

/**
 * Nette Framework - Profiler screen template.
 *
 * Copyright (c) 2004, 2008 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license" that is bundled
 * with this package in the file license.txt.
 *
 * For more information please see http://nettephp.com
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @license    http://nettephp.com/license  Nette license
 * @link       http://nettephp.com
 * @category   Nette
 * @package    Nette
 * @version    $Id: profiler.phtml 52 2008-08-14 03:52:18Z David Grudl $
 *
 * @param      array     $colophons
 * @return     void
 */

/*namespace Nette;*/

?>
</pre></xmp>

<style type="text/css">
/* <![CDATA[ */
	#netteProfilerContainer {
		position: absolute;
		right: 5px;
		bottom: 5px;
	}

	#netteProfiler {
		position: relative;
		margin: 0;
		padding: 1px;
		width: 350px;
		color: black;
		background: #EEE;
		border: 1px dotted gray;
		cursor: move;
		opacity: .70;
		=filter: alpha(opacity=70);
	}

	#netteProfiler:hover {
		opacity: 1;
		=filter: none;
	}

	#netteProfiler li {
		margin: 0;
		padding: 1px;
		font: normal normal 11px/1.4 Consolas, Arial;
		text-align: left;
		list-style: none;
	}

	#netteProfiler span[title] {
		border-bottom: 1px dotted gray;
		cursor: help;
	}
/* ]]> */
</style>


<div id="netteProfilerContainer">
<ul id="netteProfiler">
	<?php foreach ($colophons as $callback): ?>
	<?php foreach ((array) call_user_func($callback, 'profiler') as $line): ?><li><?php echo $line, "\n" ?></li><?php endforeach ?>
	<?php endforeach ?>
</ul>
</div>


<script type="text/javascript">
/* <![CDATA[ */
document.getElementById('netteProfiler').onmousedown = function(e) {
	e = e || event;
	this.posX = parseInt(this.style.left + '0');
	this.posY = parseInt(this.style.top + '0');
	this.mouseX = e.clientX;
	this.mouseY = e.clientY;

	var thisObj = this;

	document.documentElement.onmousemove = function(e) {
		e = e || event;
		thisObj.style.left = (e.clientX - thisObj.mouseX + thisObj.posX) + "px";
		thisObj.style.top = (e.clientY - thisObj.mouseY + thisObj.posY) + "px";
		return false;
	};

	document.documentElement.onmouseup = function(e) {
		document.documentElement.onmousemove = null;
		document.documentElement.onmouseup = null;
		return false;
	};
};
/* ]]> */
</script>
