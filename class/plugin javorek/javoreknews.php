<?php
/**
 * Small homepage news.
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

class MJavoreknews extends Module {
	
	public $cached = false;

	public $type = Texy::CONTENT_BLOCK;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}

	public function process() {
		$news = array();
		if (date('n') == 1 || date('n') == 5) {
			$news['Je zkouškové!'] = 'Jo jo... zkouškové. Moje úspěchy můžeš sledovat na <a href="' . ROOT .'/skola">stránce o škole</a>.';
		}
		if (in_array(date('n'), array(6, 7, 8, 9))) {
			$news['Prázniny!'] = 'V těchto měsících jsem dost možná špatně k zastižení, protože <a href="' . ROOT .'/cestovani">cestuji</a>.';
		}
		if (date('n') == 12 && in_array(date('j'), array(24, 25, 26, 27))) {
			$news['Vánoce!'] = 'Čas přestat se stresovat a začít likvidovat zásoby cukroví.';
		}
		if (date('j') == 1 && date('n') == 1) {
			$news['Hezký nový rok!'] = 'A rychlé vystřízlivění ;) .';
		}
		if (date('j') == 4 && date('n') == 4) {
			$news[date('Y') - 2002 . ' let v Brně!'] = 'Tak přesně takhle dlouho se už zdržuji na jednom místě.';
		}
		if (date('j') == 22 && date('n') == 5) {
			$news[date('Y') - 2006 . ' let už mám maturitu'] = 'Angličtina a matika za jedna, informatika a čeština za 2. Přesně naopak, než jsem si myslel, že to dopadne.';
		}
		if (date('j') == 8 && date('n') == 6) {
			$news['Před ' . date('Y') - 2006 . ' lety mě vzali na VUT'] = 'Vážně!';
		}
		if (date('j') == 24 && date('n') == 6) {
			$news['Dnes jsem svatý'] = 'Díky, že jsi mi popřál/popřála k svátku! :D';
		}
		if (date('j') == 13 && date('n') == 8) {
			$news['Začal jsem blogovat'] = 'V roce 2007 touto dobou jsem začal blogovat.';
		}
		if (date('j') == 30 && date('n') == 8) {
			$news['Narozky'] = 'Dnes jsem zase o rok starší :) .';
		}

		$config = Config::getInstance();
		$n = (($config['compressed'])? '' : "\n");
		$t = (($config['compressed'])? '' : "\t");
		
		$output = '';
		foreach ($news as $term => $data) {
			$output .= "$t<dt>$term</dt>$n$t<dd>$data</dd>$n";
		}
		return $output;
	}
}
?>