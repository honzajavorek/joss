<?php
/**
 * Returns a searchfield.
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

class MSearch extends Module {
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	public function process() {
		$form = TexyHtml::el('form');
		$form->action = 'http://www.google.com/cse';
		$form->id = 'searchbox_011917998094738623377:thrjzztpnm0';
		
		$hidden = TexyHtml::el('input');
		$hidden->type = 'hidden';
		$hidden->name = 'cx';
		$hidden->value = '011917998094738623377:thrjzztpnm0';
		$form->add($hidden);
		
		$text = TexyHtml::el('input');
		$text->type = 'text';
		$text->name = 'q';
		$text->size = 20;
		$text->class = 'textfield';
		$form->add($text);
		
		$image = TexyHtml::el('input');
		$image->type = 'image';
		$image->name = 'sa';
		$image->class = 'button';
		$image->alt = (empty($this->args[0]))? 'Hledat' : $this->args[0];
		$image->src = ROOT . '/tpl/img/search.png';
		$form->add($image);
		
		return $form;
	}
	
}
?>