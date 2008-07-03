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
 * Part of application which represents special behavior of HTML page.
 *
 * Represents generated content of web page (instead of Texy! files).
 * Uses templates etc.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
abstract class JDriver extends JDoc implements JInamed {

	/**
	 * Instance of JTemplate
	 *
	 * @var JTemplate
	 */
	protected $tpl = NULL;
	
	public function __construct($id) {
		// template
		if (empty($this->tpl)) {
			$path = self::$paths['text'] . "$id.tpl";
			$template = new JFile($path);
			if ($template->exists()) {
				$this->tpl = new JTemplate($template->file);
			} else {
				throw new TemplateNotFoundException("Driver " . $this->getClass() . " cannot find it's template at $path.");
			}
		}
		
		// headers
		$this->sendCommonHeaders();
		
		// form handling
		$form = $this->define();
		if (!empty($form)) {
			$this->act($form);
			$this->tpl->set('form', $form, FALSE);
		}
		
		// template
		$this->render($this->tpl);
		print $this->tpl->fetch(); // printing output
		
		$this->fixInternetExplorer();
	}

	/**
	 * Define forms etc.
	 * 
	 * @return Form
	 */
	protected function define() {
		// Prepared to be overriden.
	}
	
	/**
	 * Actions, processing of form.
	 * 
	 * @param Form
	 */
	protected function act(Form &$form) {
		// Prepared to be overriden.
	}

	/**
	 * Rendering of template.
	 */
	protected function render(JTemplate &$tpl) {
		// Prepared to be overriden.
	}
	
	/**
	 * Decodes name, {{some-words}} are converted to JDSomeWords.
	 *
	 * @param string $id
	 * @return string
	 */
	static public function resolveName($id) {
		$id = explode('-', $id);
		foreach ($id as &$part) {
			$part = ucfirst($part);
		}
		$id = 'JD' . implode('', $id);
		return $id;
	}

}
