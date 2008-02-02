<?php
/**
 * My own implementation of title.
 *
 * Created 9.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @package   Joss
 * @version   $Revision$ ($Date$, $Author$)
 * @copyright Copyright (c) 2008 Jan Javorek
 * @link      http://joss.javorek.net/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */
 
class MMyTitle extends Module {
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	public function process() {
		$a = TexyHtml::el('a');
		$a->href = ROOT . '/';
		$a->title = 'na úvodní stranu';
		
		$logo = TexyHtml::el('span');
		$logo->id = 'logo';
		$a->add($logo);
		
		$strong = TexyHtml::el('strong')->setText('Honza Javorek');
		
		$comma = TexyHtml::el('span')->setText(', ');
		$comma->class = 'hidden';
		$strong->add($comma);
		
		$em = TexyHtml::el('em')->setText('osobní stránky');
		$strong->add($em);
		
		$a->add($strong);
		
		$title = TexyHtml::el('span');
		$title->id = 'title';
		$a->add($title);
		
		return $a;
	}
	
}
?>