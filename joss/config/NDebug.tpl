<?php

/**
 * Nette Framework
 *
 * Copyright (c) 2004, 2008 David Grudl aka -dgx- (http://www.dgx.cz)
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
 * Debugger template
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @package    Nette
 * @version    $Revision$ $Date$
 */

/*
 passed parameters: $type, $code, $message, $file, $line [, $context] [, $exception]
*/

if (!headers_sent()) {
    header('HTTP/1.1 500 Internal Server Error');
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1250" />
    <meta http-equiv="Content-Language" content="en" />
    <meta name="robots" content="noindex,noarchive" />
    <meta name="generator" content="Nette Framework" />

    <title><?php echo htmlspecialchars($type) ?></title>

    <style type="text/css">
    /* <![CDATA[ */
        body {
            font-family: Verdana, sans-serif;
            font-size: 82%;
            background: white;
            color: #333;
            line-height: 1.5;
            margin: 0;
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
            color: #4197E3;
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
        }

        pre.dump {
            padding: 0;
            margin: 0;
            border: none;
        }

        pre.dump span {
            color: #c16549;
        }

        div.block {
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
        document.write('<style> .hidden { display: none; } </style>');

        function toggle(link, block)
        {
            var span = link.getElementsByTagName('span')[0];
            var div = document.getElementById(block);
            var hidden = div.currentStyle ? div.currentStyle.display == 'none' : getComputedStyle(div, null).display == 'none';

            span.innerHTML = String.fromCharCode(hidden ? 0x25bc : 0x25b6);
            div.style.display = hidden ? 'block' : 'none';

            return true;
        }
    /* ]]> */
    </script>
</head>



<body>
    <div id="error" class="block">
        <h1><?php echo htmlspecialchars($type) ?></h1>

        <p><?php echo htmlspecialchars($message) ?></p>
    </div>



    <?php if ($file): ?>
    <div class="block">
        <h2><a href="#" onclick="return !toggle(this, 'source')">Source file <span>&#x25bc;</span></a></h2>

        <div id="source" class="inner">
            <p><strong>File:</strong> <?php echo htmlspecialchars($file) ?> &nbsp; <strong>Line:</strong> <?php echo $line ?></p>

            <?php if (is_readable($file)): ?>
            <pre><?php
            $source = file($file);
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
        </div>
    </div>
    <?php endif?>





    <div class="block">
        <h2><a href="#" onclick="return !toggle(this, 'callstack')">Call stack <span>&#x25bc;</span></a></h2>


        <div id="callstack" class="inner">
        <ol>
        <?php foreach ($trace as $key => $row): ?>
        <li><p>

        <?php if (isset($row['file'])): ?>
            <?php echo htmlSpecialChars(basename(dirname($row['file']))), '/<b>', htmlSpecialChars(basename($row['file'])), '</b> (', $row['line'], ')' ?>
        <?php else: ?>
            &lt;PHP inner-code&gt;
        <?php endif ?>

        &mdash;

        <?php
        $hasSource = isset($row['file']) && is_readable($row['file']);
        $hasArgs = isset($row['args']) && count($row['args']) > 0;

        if (isset($row['class'])) {
            echo $row['class'] . $row['type'];
        }

        echo $row['function'];
        ?>


        (<?php if ($hasArgs): ?><a href="#" onclick="return !toggle(this, 'args<?php echo $key ?>')">arguments <span>&#x25b6;</span></a><?php endif ?>)

        &nbsp;

        <?php if ($hasSource): ?><a href="#" onclick="return !toggle(this, 'src<?php echo $key ?>')">source <span>&#x25b6;</span></a><?php endif ?>
        </p>

        <?php if ($hasArgs): ?>
            <div class="hidden" id="args<?php echo $key ?>">
            <table>
            <?php
            try {
                $r = isset($row['class']) ? new ReflectionMethod($row['class'], $row['function']) : new ReflectionFunction($row['function']);
                $params = $r->getParameters();
            } catch (Exception $e) {
                $params = array();
            }
            foreach ($row['args'] as $k => $v) {
                $name = isset($params[$k]) ? '$' . $params[$k]->name : "#$k";
                echo '<tr><td>', htmlspecialchars($name), '</td><td>', self::dump($v, TRUE), '</td></tr>';
            }
            ?>
            </table>
            </div>
        <?php endif ?>


        <?php if ($hasSource): ?>
            <pre class="hidden" id="src<?php echo $key ?>"><?php
            $line = $row['line'];
            $source = file($row['file']);
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
        </div>
    </div>




    <?php if ($context):?>
    <div class="block">
        <h2><a href="#" onclick="return !toggle(this, 'context')">Variable context <span>&#x25b6;</span></a></h2>

        <div id="context" class="hidden inner">
            <table>
            <?php
            foreach ($context as $k => $v) echo '<tr><td>$', htmlspecialchars($k), '</td><td>', self::dump($v, TRUE), '</td></tr>';
            ?>
            </table>
        </div>
    </div>
    <?php endif ?>




    <?php if ($exception):?>
    <div class="block">
        <h2><a href="#" onclick="return !toggle(this, 'exception')">Exception <span>&#x25b6;</span></a></h2>

        <div id="exception" class="hidden inner">
        <pre><?php echo htmlspecialchars($exception->__toString()) ?></pre>
        </div>
    </div>
    <?php endif ?>




    <div class="block">
        <h2><a href="#" onclick="return !toggle(this, 'http-request')">HTTP request <span>&#x25b6;</span></a></h2>

        <div id="http-request" class="hidden inner">
            <?php if (function_exists('apache_request_headers')): ?>
            <h3>Headers</h3>
            <table>
            <?php
            foreach (apache_request_headers() as $k => $v) echo '<tr><td>', htmlspecialchars($k), '</td><td>', htmlspecialchars($v), '</td></tr>';
            ?>
            </table>
            <?php endif ?>


            <?php foreach (array('_GET', '_POST', '_COOKIE', '_SERVER') as $name): ?>
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
        </div>
    </div>



    <div class="block">
        <h2><a href="#" onclick="return !toggle(this, 'http-response')">HTTP response <span>&#x25b6;</span></a></h2>

        <div id="http-response" class="hidden inner">
            <h3>Headers</h3>
            <?php if (headers_list()): ?>
            <pre><?php
            foreach (headers_list() as $s) echo htmlspecialchars($s), '<br />';
            ?></pre>
            <?php else: ?>
            <p>No headers...</p>
            <?php endif ?>
        </div>
    </div>


    <ul>
        <li>PHP version <?php echo PHP_VERSION ?></li>
        <li><?php echo $_SERVER['SERVER_SOFTWARE'] ?></li>
        <li>Nette version 0.7</li>
    </ul>

</body>
</html>

<?php

// fix IE
if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
    $s = " \t\r\n";
    for ($i = 2e3; $i; $i--) echo $s{rand(0, 3)};
}
