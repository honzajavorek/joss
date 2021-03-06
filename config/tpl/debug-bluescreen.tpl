<?php

/**
 * Nette Framework - Debugger bluescreen template.
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
 * @version    $Id: bluescreen.phtml 45 2008-08-08 10:46:16Z David Grudl $
 *
 * @param  array     $exception
 * @param  array     $colophons
 */


/**
 * Prints source code.
 * @param  string
 * @param  int
 * @param  int
 * @return void
 */
function _netteDebugPrintCode($file, $line, $count = 15)
{
	if (function_exists('ini_set')) {
		ini_set('highlight.comment', '#999; font-style: italic');
		ini_set('highlight.default', '#000');
		ini_set('highlight.html', '#06b');
		ini_set('highlight.keyword', '#d24; font-weight: bold');
		ini_set('highlight.string', '#080');
	}

	$start = max(1, $line - floor($count / 2));

	$source = explode("\n", @highlight_file($file, TRUE));
	echo $source[0]; // <code><span color=highlight.html>
	$source = explode('<br />', $source[1]);
	array_unshift($source, NULL);

	$i = $start; // find last highlighted block
	while (--$i >= 1) {
		if (preg_match('#.*(</?span[^>]*>)#', $source[$i], $m)) {
			if ($m[1] !== '</span>') echo $m[1];
			break;
		}
	}

	$source = array_slice($source, $start, $count, TRUE);
	end($source);
	$numWidth = strlen((string) key($source));

	foreach ($source as $n => $s) {
		$s = str_replace(array("\r", "\n"), array('', ''), $s);
		if ($n === $line) {
			printf(
				"<span class='highlight'>Line %{$numWidth}s:    %s\n</span>%s",
				$n,
				strip_tags($s),
				preg_replace('#[^>]*(<[^>]+>)[^<]*#', '$1', $s)
			);
		} else {
			printf("<span class='line'>Line %{$numWidth}s:</span>    %s\n", $n, $s);
		}
	}
	echo '</span></span></code>';
}



/**
 * Opens panel.
 * @param  string
 * @param  bool
 * @return void
 */
function _netteOpenPanel($name, $collaped)
{
	static $id;
	$id++;
	?>
	<div class="panel">
		<h2><a href="#" onclick="return !toggle(this, 'pnl<?php echo $id ?>')"><?php echo htmlSpecialChars($name) ?> <span><?php echo $collaped ? '&#x25b6;' : '&#x25bc;' ?></span></a></h2>

		<div id="pnl<?php echo $id ?>" class="<?php echo $collaped ? 'collapsed ' : '' ?>inner">
	<?php
}



/**
 * Closes panel.
 * @return void
 */
function _netteClosePanel()
{
	?>
		</div>
	</div>
	<?php
}



if (headers_sent()) {
	echo '</pre></xmp>';
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="robots" content="noindex,noarchive">
	<meta name="generator" content="Nette Framework">

	<title><?php echo htmlspecialchars(get_class($exception)) ?></title>

	<style type="text/css">
	/* <![CDATA[ */
		body {
			font: 78%/1.5 Verdana, sans-serif;
			background: white;
			color: #333;
			margin: 0 0 2em;
			padding: 0;
		}

		h1 {
			font-weight: normal !important;
			font-size: 18pt;
			margin: .6em 0;
		}

		h2 {
			font-family: sans-serif;
			font-weight: normal;
			font-size: 14pt;
			color: #888;
			margin: .6em 0;
		}

		a {
			text-decoration: none;
			/*color: #4197E3;*/color: #5B6F00;<?php // NOTE changed! not original ?>
		}

		a span {
			color: #999;
		}

		h3 {
			font-size: 110%;
			font-weight: bold;
			margin: 1em 0;
		}

		p { margin: .8em 0 }

		pre, code, table {
			font-family: Consolas, monospace;
		}

		pre, table {
			background: #ffffcc;
			padding: .4em .7em;
			border: 1px dotted silver;
		}

		table pre {
			padding: 0;
			margin: 0;
			border: none;
			font-size: 100%;
		}

		pre.dump span {
			color: #c16549;
		}

		div.panel {
			border-bottom: 1px solid #eee;
			padding: 1px 2em;
		}

		div.inner {
			padding: 0.1em 1em 1em;
			background: #f5f5f5;
		}

		table {
			border-collapse: collapse;
			width: 100%;
		}

		td, th {
			vertical-align: top;
			padding: 2px 3px;
			border: 1px solid #eeeebb;
		}

		ul {
			font-size: 80%;
		}

		.highlight, #error {
			background: red;
			color: white;
			font-weight: bold;
			font-style: normal;
			display: block;
		}

		.line {
			color: #9e9e7e;
			font-weight: normal;
			font-style: normal;
		}

	/* ]]> */
	</style>


	<script type="text/javascript">
	/* <![CDATA[ */
		document.write('<style> .collapsed { display: none; } </style>');

		function toggle(link, panel)
		{
			var span = link.getElementsByTagName('span')[0];
			var div = document.getElementById(panel);
			var collapsed = div.currentStyle ? div.currentStyle.display == 'none' : getComputedStyle(div, null).display == 'none';

			span.innerHTML = String.fromCharCode(collapsed ? 0x25bc : 0x25b6);
			div.style.display = collapsed ? 'block' : 'none';

			return true;
		}
	/* ]]> */
	</script>
</head>



<body>
	<div id="error" class="panel">
		<h1><?php echo htmlspecialchars(get_class($exception)), ($exception->getCode() ? ' #' . $exception->getCode() : '') ?></h1>

		<p><?php echo htmlspecialchars($exception->getMessage()) ?></p>
	</div>



	<?php if (is_file($exception->getFile())): ?>
	<?php _netteOpenPanel('Source file', FALSE) ?>
		<p><strong>File:</strong> <?php echo htmlspecialchars($exception->getFile()) ?> &nbsp; <strong>Line:</strong> <?php echo $exception->getLine() ?></p>
		<pre><?php _netteDebugPrintCode($exception->getFile(), $exception->getLine()) ?></pre>
	<?php _netteClosePanel() ?>
	<?php endif?>




	<?php _netteOpenPanel('Call stack', FALSE) ?>
		<ol>
			<?php foreach ($exception->getTrace() as $key => $row): ?>
			<li><p>

			<?php if (isset($row['file'])): ?>
				<span title="<?php echo htmlSpecialChars($row['file'])?>"><?php echo htmlSpecialChars(basename(dirname($row['file']))), '/<b>', htmlSpecialChars(basename($row['file'])), '</b></span> (', $row['line'], ')' ?>
			<?php else: ?>
				&lt;PHP inner-code&gt;
			<?php endif ?>

			<?php if (isset($row['file']) && is_file($row['file'])): ?><a href="#" onclick="return !toggle(this, 'src<?php echo $key ?>')">source <span>&#x25b6;</span></a> &nbsp; <?php endif ?>

			<?php if (isset($row['class'])) echo $row['class'] . $row['type'] ?>
			<?php echo $row['function'] ?>

			(<?php if (!empty($row['args'])): ?><a href="#" onclick="return !toggle(this, 'args<?php echo $key ?>')">arguments <span>&#x25b6;</span></a><?php endif ?>)
			</p>

			<?php if (!empty($row['args'])): ?>
				<div class="collapsed" id="args<?php echo $key ?>">
				<table>
				<?php
				try {
					$r = isset($row['class']) ? new ReflectionMethod($row['class'], $row['function']) : new ReflectionFunction($row['function']);
					$params = $r->getParameters();
				} catch (Exception $e) {
					$params = array();
				}
				foreach ($row['args'] as $k => $v) {
					echo '<tr><td>', (isset($params[$k]) ? '$' . $params[$k]->name : "#$k"), '</td>';
					echo '<td>', self::safeDump($v, isset($params[$k]) ? $params[$k]->name : NULL), "</td></tr>\n";
				}
				?>
				</table>
				</div>
			<?php endif ?>


			<?php if (isset($row['file']) && is_file($row['file'])): ?>
				<pre class="collapsed" id="src<?php echo $key ?>"><?php _netteDebugPrintCode($row['file'], $row['line']) ?></pre>
			<?php endif ?>

			</li>
			<?php endforeach ?>
		</ol>
	<?php _netteClosePanel() ?>



	<?php if ($exception instanceof /*Nette::*/IDebuggable): ?>
	<?php foreach ($exception->getPanels() as $name => $panel): ?>
	<?php _netteOpenPanel($name, empty($panel['expanded'])) ?>
		<?php echo $panel['content'] ?>
	<?php _netteClosePanel() ?>
	<?php endforeach ?>
	<?php endif ?>



	<?php if (isset($exception->context) && is_array($exception->context)):?>
	<?php _netteOpenPanel('Variables', TRUE) ?>
	<table>
	<?php
	foreach ($exception->context as $k => $v) {
		echo '<tr><td>$', htmlspecialchars($k), '</td><td>', self::safeDump($v, $k), "</td></tr>\n";
	}
	?>
	</table>
	<?php _netteClosePanel() ?>
	<?php endif ?>



	<?php _netteOpenPanel('Environment', TRUE) ?>
		<?php
		$list = get_defined_constants(TRUE);
		if (!empty($list['user'])):?>
		<h3><a href="#" onclick="return !toggle(this, 'pnl-env-const')">Constants <span>&#x25bc;</span></a></h3>
		<table id="pnl-env-const">
		<?php
		foreach ($list['user'] as $k => $v) {
			echo '<tr><td>', htmlspecialchars($k), '</td><td>', self::safeDump($v, $k), "</td></tr>\n";
		}
		?>
		</table>
		<?php endif ?>


		<h3><a href="#" onclick="return !toggle(this, 'pnl-env-files')">Included files <span>&#x25b6;</span></a> (<?php echo count(get_included_files()) ?>)</h3>
		<table id="pnl-env-files" class="collapsed">
		<?php
		foreach (get_included_files() as $v) {
			echo '<tr><td>', htmlspecialchars($v), "</td></tr>\n";
		}
		?>
		</table>


		<h3>$_SERVER</h3>
		<?php if (empty($_SERVER)):?>
		<p><i>empty</i></p>
		<?php else: ?>
		<table>
		<?php
		foreach ($_SERVER as $k => $v) echo '<tr><td>', htmlspecialchars($k), '</td><td>', self::dump($v, TRUE), "</td></tr>\n";
		?>
		</table>
		<?php endif ?>
	<?php _netteClosePanel() ?>




	<?php if ($exception):?>
	<?php _netteOpenPanel('Exception', TRUE) ?>
		<pre><?php echo htmlspecialchars($exception->__toString()) ?></pre>
	<?php _netteClosePanel() ?>
	<?php endif ?>




	<?php $previous = $exception; ?>
	<?php while (method_exists($previous, 'getPrevious') && $previous = $previous->getPrevious()): ?>
	<?php _netteOpenPanel('Caused by', TRUE) ?>
			<pre><?php echo htmlspecialchars($previous->__toString()) ?></pre>
	<?php _netteClosePanel() ?>
	<?php endwhile ?>




	<?php _netteOpenPanel('HTTP request', TRUE) ?>
		<?php if (function_exists('apache_request_headers')): ?>
		<h3>Headers</h3>
		<table>
		<?php
		foreach (apache_request_headers() as $k => $v) echo '<tr><td>', htmlspecialchars($k), '</td><td>', htmlspecialchars($v), "</td></tr>\n";
		?>
		</table>
		<?php endif ?>


		<?php foreach (array('_GET', '_POST', '_COOKIE') as $name): ?>
		<h3>$<?php echo $name ?></h3>
		<?php if (empty($GLOBALS[$name])):?>
		<p><i>empty</i></p>
		<?php else: ?>
		<table>
		<?php
		foreach ($GLOBALS[$name] as $k => $v) echo '<tr><td>', htmlspecialchars($k), '</td><td>', self::dump($v, TRUE), "</td></tr>\n";
		?>
		</table>
		<?php endif ?>
		<?php endforeach ?>
	<?php _netteClosePanel() ?>



	<?php _netteOpenPanel('HTTP response', TRUE) ?>
		<h3>Headers</h3>
		<?php if (headers_list()): ?>
		<pre><?php
		foreach (headers_list() as $s) echo htmlspecialchars($s), '<br>';
		?></pre>
		<?php else: ?>
		<p><i>no headers</i></p>
		<?php endif ?>
	<?php _netteClosePanel() ?>


	<ul>
		<?php foreach ($colophons as $callback): ?>
		<?php foreach ((array) call_user_func($callback, 'bluescreen') as $line): ?><li><?php echo $line, "\n" ?></li><?php endforeach ?>
		<?php endforeach ?>
	</ul>

</body>
</html>
