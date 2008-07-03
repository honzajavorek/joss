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
 * Debugger template.
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @package    Nette
 * @version    $Revision: 7 $ $Date: 2008-05-12 03:21:36 +0200 (po, 12 V 2008) $
 */

// passed parameters: $type, $code, $message, $file, $line, $colophons [, $context] [, $exception]


if (headers_sent()) {
	echo '</pre></xmp>';
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<meta name="robots" content="noindex,noarchive">
	<meta name="generator" content="Nette Framework">

	<title><?php echo htmlspecialchars($type) ?></title>

	<style type="text/css">
	/* <![CDATA[ */
		body {
			font-family: Verdana, sans-serif;
			font-size: 78%;
			background: white;
			color: #333;
			line-height: 1.5;
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
			color: #5B6F00;<?php // NOTE changed! not original ?>
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

		pre, table {
			background: #ffffcc;
			padding: .4em .7em;
			border: 1px dotted silver;
			font-family: monospace;
		}

		table, pre, x:-moz-any-link { font-size: 115%; }

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
			display: block;
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
		<h1><?php echo htmlspecialchars($type) ?></h1>

		<p><?php echo htmlspecialchars($message) ?></p>
	</div>



	<?php if ($file): ?>
	<?php self::openPanel('Source file', FALSE) ?>
		<p><strong>File:</strong> <?php echo htmlspecialchars($file) ?> &nbsp; <strong>Line:</strong> <?php echo $line ?></p>

		<pre><?php
		$source = (array) @file($file);
		array_unshift($source, NULL);
		$source = array_slice($source, max(0, $line - 5), 10, TRUE);

		foreach ($source as $n => $s) {
			$s = rtrim($s);
			if (strlen($s) > 100) $s = substr($s, 0, 100) . '...';
			if ($n === $line) {
				printf("<span class='highlight'>Line %s:    %s\n</span>", $n, htmlSpecialChars($s));
			} else {
				printf("Line %s:    %s\n", $n, htmlSpecialChars($s));
			}
		}
		?></pre>
	<?php self::closePanel() ?>
	<?php endif?>




	<?php self::openPanel('Call stack', FALSE) ?>
		<ol>
			<?php foreach ($trace as $key => $row): ?>
			<li><p>

			<?php if (isset($row['file'])): ?>
				<?php $source = @file($row['file']); ?>
				<?php echo htmlSpecialChars(basename(dirname($row['file']))), '/<b>', htmlSpecialChars(basename($row['file'])), '</b> (', $row['line'], ')' ?>
			<?php else: ?>
				<?php $source = NULL; ?>
				&lt;PHP inner-code&gt;
			<?php endif ?>

			<?php if (!empty($source)): ?><a href="#" onclick="return !toggle(this, 'src<?php echo $key ?>')">source <span>&#x25b6;</span></a> &nbsp; <?php endif ?>

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
					echo '<td>', self::safedump($v, isset($params[$k]) ? $params[$k]->name : NULL), '</td></tr>';
				}
				?>
				</table>
				</div>
			<?php endif ?>


			<?php if (!empty($source)): ?>
				<pre class="collapsed" id="src<?php echo $key ?>"><?php
				$line = $row['line'];
				array_unshift($source, NULL);
				$source = array_slice($source, max(0, $line - 5), 10, TRUE);

				foreach ($source as $n => $s) {
					$s = rtrim($s);
					if (strlen($s) > 100) $s = substr($s, 0, 100) . '...';
					if ($n === $line) {
						printf("<span class='highlight'>Line %s:    %s\n</span>", $n, htmlSpecialChars($s));
					} else {
						printf("Line %s:    %s\n", $n, htmlSpecialChars($s));
					}
				}
				?></pre>
			<?php endif ?>

			</li>
			<?php endforeach ?>
		</ol>
	<?php self::closePanel() ?>




	<?php if ($exception instanceof /*Nette::*/IDebuggable): ?>
	<?php foreach ($exception->getPanels() as $name => $panel): ?>
	<?php self::openPanel($name, empty($panel['expanded'])) ?>
		<?php echo $panel['content'] ?>
	<?php self::closePanel() ?>
	<?php endforeach ?>
	<?php endif ?>



	<?php self::openPanel('Environment', TRUE) ?>
		<?php if (!empty($context)):?>
		<h3><a href="#" onclick="return !toggle(this, 'pnl-env-context')">Variables <span>&#x25b6;</span></a></h3>
		<table id="pnl-env-context" class="collapsed">
		<?php
		foreach ($context as $k => $v) {
			echo '<tr><td>$', htmlspecialchars($k), '</td><td>', self::safedump($v, $k), '</td></tr>';
		}
		?>
		</table>
		<?php endif ?>


		<?php
		$list = get_defined_constants(TRUE);
		if (!empty($list['user'])):?>
		<h3><a href="#" onclick="return !toggle(this, 'pnl-env-const')">Constants <span>&#x25bc;</span></a></h3>
		<table id="pnl-env-const">
		<?php
		foreach ($list['user'] as $k => $v) {
			echo '<tr><td>', htmlspecialchars($k), '</td><td>', self::safedump($v, $k), '</td></tr>';
		}
		?>
		</table>
		<?php endif ?>


		<h3><a href="#" onclick="return !toggle(this, 'pnl-env-files')">Included files <span>&#x25b6;</span></a> (<?php echo count(get_included_files()) ?>)</h3>
		<table id="pnl-env-files" class="collapsed">
		<?php
		foreach (get_included_files() as $v) {
			echo '<tr><td>', htmlspecialchars($v), '</td></tr>';
		}
		?>
		</table>


		<h3>$_SERVER</h3>
		<?php if (empty($_SERVER)):?>
		<p><i>empty</i></p>
		<?php else: ?>
		<table>
		<?php
		foreach ($_SERVER as $k => $v) echo '<tr><td>', htmlspecialchars($k), '</td><td>', self::dump($v, TRUE), '</td></tr>';
		?>
		</table>
		<?php endif ?>
	<?php self::closePanel() ?>




	<?php if ($exception):?>
	<?php self::openPanel('Exception', TRUE) ?>
			<pre><?php echo htmlspecialchars($exception->__toString()) ?></pre>
	<?php self::closePanel() ?>
	<?php endif ?>




	<?php $cause = $exception; ?>
	<?php while ($cause instanceof /*Nette::*/ICausedException && $cause = $cause->getCause()): ?>
	<?php self::openPanel('Caused by', TRUE) ?>
			<pre><?php echo htmlspecialchars($cause->__toString()) ?></pre>
	<?php self::closePanel() ?>
	<?php endwhile ?>




	<?php self::openPanel('HTTP request', TRUE) ?>
		<?php if (function_exists('apache_request_headers')): ?>
		<h3>Headers</h3>
		<table>
		<?php
		foreach (apache_request_headers() as $k => $v) echo '<tr><td>', htmlspecialchars($k), '</td><td>', htmlspecialchars($v), '</td></tr>';
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
		foreach ($GLOBALS[$name] as $k => $v) echo '<tr><td>', htmlspecialchars($k), '</td><td>', self::dump($v, TRUE), '</td></tr>';
		?>
		</table>
		<?php endif ?>
		<?php endforeach ?>
	<?php self::closePanel() ?>



	<?php self::openPanel('HTTP response', TRUE) ?>
		<h3>Headers</h3>
		<?php if (headers_list()): ?>
		<pre><?php
		foreach (headers_list() as $s) echo htmlspecialchars($s), '<br>';
		?></pre>
		<?php else: ?>
		<p><i>no headers</i></p>
		<?php endif ?>
	<?php self::closePanel() ?>


	<ul>
		<li>PHP version <?php echo PHP_VERSION ?></li>
		<?php if (isset($_SERVER['SERVER_SOFTWARE'])): ?><li><?php echo $_SERVER['SERVER_SOFTWARE'] ?></li><?php endif ?>
		<li>Joss framework<?php // NOTE changed! not original ?></li>
		<?php foreach ($colophons as $callback): ?>
		<?php foreach (call_user_func($callback) as $line): ?><li><?php echo htmlSpecialChars($line, ENT_NOQUOTES, 'ISO-8859-1', FALSE) ?></li><?php endforeach ?>
		<?php endforeach ?>
		<li>Report generated at <?php echo @strftime('%c') ?></li>
	</ul>

</body>
</html>