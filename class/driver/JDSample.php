<?php

/**
 * Joss framework & content management system.
 *
 * Created 29.6.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Driver sample.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JDSample extends JDriver {

	public function __construct($id) {
		parent::__construct($id);
	}

	/**
	 * Define forms etc.
	 * 
	 * @return Form
	 */
	protected function define() {
		$form = new Form();
		$form->addText('name', 'Jméno:', 35);
		$form->addSubmit('submit', 'ODESLAT');
		
		// define form rules
		$form->addRule('name', Form::FILLED, 'Zadejte jméno!');
		
		return $form;
	}
	
	/**
	 * Actions, processing of form.
	 * 
	 * @param Form
	 */
	protected function act(Form &$form) {
		if ($form->isSubmitted()) {
		    echo '<h2>Submitted</h2>';
		
		    // check validation
		    if ($form->isValid()) {
		        echo '<h2>And successfully validated!</h2>';
		
		        $values = $form->getValues();
		        echo '<pre>';
		        print_r($values);
		        echo '</pre>';
		
		        // this is the end :-)
		        exit;
		    }
		
		} else { // not submitted?
		
		    // so define default values
		    $defaults = array(
		        'name' => 'žanek',
		    );
		
		    $form->setDefaults($defaults);
		}
	}

	/**
	 * Rendering of template.
	 */
	protected function render(JTemplate &$tpl) {
		// maybe later...
	}

}
