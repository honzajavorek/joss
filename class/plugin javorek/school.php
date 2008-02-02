<?php
/**
 * FIT VUT Brno, parser of study charts.
 *
 * First argument -- login, second -- password.
 * 
 * Created 25.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @package   Joss
 * @version   $Revision$ ($Date$, $Author$)
 * @copyright Copyright (c) 2008 Jan Javorek
 * @link      http://joss.javorek.net/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */

class MSchool extends Module {
	
	public $cached = false;

	public $type = Texy::CONTENT_BLOCK;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	private function currentPeriod() {
		$month = date('n');
		$year = date('Y');
		
		if (9 <= $month && $month <= 12) { // winter (first half)
			$period = 'semestr zimní';
			$year = $year . '/' . ($year + 1);
		} else if (1 <= $month && $month <= 3) { // winter (second half)
			$period = 'semestr zimní';
			$year = ($year - 1) . '/' . $year;
		} else { // summer
			$period = 'semestr letní';
			$year = ($year - 1) . '/' . $year;
		}
		
		return "Ak. rok $year, $period";	
	}
	
	public function process() {
		if (!extension_loaded('openssl')) {
			return '<p>Unable to log in. OpenSSL extension have to be loaded in PHP.</p>';
		}
		$config = Config::getInstance();
		$x = (($config['xhtml'])? ' /' : '');
		
		// loads a page behind the SSH, from IS
		$html = file_get_contents(
			'https://' . $this->args[0] . ':' . $this->args[1] . '@wis.fit.vutbr.cz/FIT/st/study-a.php.utf-8'
		);
		
		// some preparations for parsing
		$html = preg_replace('~\\s+~i', ' ', $html);
		$html = strip_tags($html, '<tr><th><td><table><h2>');
		preg_match('~(Login.+)Uvedené počty~iu', $html, $match);
		$match = $match[1] . '.';
		
		// additional editions, corrections, cosmetics, etc.
		$html = preg_replace(array(
			'~\\s+(width|align|valign|bgcolor|class|href|cellpadding|cellspacing|bordercolor|border)=[^\\s>]+~i',
			'~(\\s+[^=]+)=([^">\\s])+~i',
			'~Splnění podmínek absolvování studia~u',
			'~&nbsp;<table>~i',
			'~ nowrap~i',
			'~&nbsp([^;])~i',
			'~(</[^>]+>)\\s*([^\\s<][^<]+)~i',
			'~<(/)?h2>~i',
			'~(Login.+FIT) (Ak.+zápis)~iu',
			'~<th([^>]+)><h3>(' . $this->currentPeriod() . ')~iu',
			'~<td>([0-4][0-9]?)&nbsp;&nbsp;</td>~i',
			'~<td>(\\d{1,3})&nbsp;&nbsp;</td>~i',
			'~<th>Zkr</th>~i', '~<th>Uk</th>~i', '~<th>Záp</th>~iu', '~<th>Zn</th>~i', '~<th>Kr</th>~i',
			'~<td>Zk</td>~i', '~<td>ZáZk</td>~i', '~<td>KlZá</td>~iu',
			'~(\\d{4})-0?(\\d{1,2})-0?(\\d{1,2})~',
			'~\\d\\. ročník BIT~iu',
			'~' . $this->args[0] . '~iu'
		), array(
			'',
			'\\1="\\2"',
			'<h3>\\0</h3>',
			'<table class="conclusion">',
			'',
			'&nbsp;\\1',
			'\\1<p>\\2</p>',
			'<\\1h3>',
			'<p>\\1. \\2.</p>',
			'<th\\1 id="current-period"><h3>\\2',
			'<td class="warning"><strong>\\1</strong></td>',
			'<td><strong>\\1</strong></td>',
			'<th title="zkratka">Zkr</th>', '<th title="ukončení">Uk</th>', '<th title="zápočet">Záp</th>', '<th title="známka">Zn</th>', '<th title="kredity">Kr</th>',
			'<td title="zkouška">Zk</td>', '<td title="zápočet a zkouška">ZáZk</td>', '<td title="klasifikovaný zápočet">KlZá</td>',
			'\\3.&nbsp;\\2.&nbsp;\\1',
			'<a href="http://www.fit.vutbr.cz/study/stplan-l.php?id=40" title="studijní plán">\\0</a>',
			'<strong>\\0</strong>'
		), $match);
		
		// table of all subjects
		$table = file_get_contents('http://www.fit.vutbr.cz/study/course-l.php.utf-8');

		// links to my subjects
		$matches = array();
		preg_match_all('~<tr><th>[A-Z]{3,4}</th><td>([^<]+)</td>~iu', $html, $matches);
		$patterns = $replacements = array();
		foreach ($matches[1] as $subject) {
		    $m = array();
			preg_match('~id=(\\d+)">' . $subject . '</a>~iu', $table, $m);
			$patterns[] = '~<td>' . $subject . '</td>~iu';
			$replacements[] = '<td><a href="http://www.fit.vutbr.cz/study/course-l.php?id=' . $m[1] . '">' . $subject . '</a></td>';
		}
		$html = preg_replace($patterns, $replacements, $html);

		return $html;
	}
	
}
?>
