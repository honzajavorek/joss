<?php

/**
 * Joss framework & content management system.
 *
 * Created 31.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Joss plugin.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
abstract class JPlugin extends Object {
	
	/**
	 * Caching option.
	 * 
	 * @var bool
	 */
	public $cached = TRUE;
	
	/**
	 * Delayed processing.
	 * 
	 * Module will be processed after Texy! processes all the document.
	 *
	 * @var bool
	 */
	public $delayed = FALSE;
	
	/**
	 * Type of output.
	 * 
	 * Must be one of these values:
	 * Texy::CONTENT_MARKUP – string contains only HTML comments or inline elements (B, I, EM, …), which are „invisible“ (but no text!)
     * Texy::CONTENT_REPLACED – in addition contains „visible“ inline elements (IMG, INPUT, OBJECT, BR too)
     * Texy::CONTENT_TEXTUAL – in addition contains any text (spaces is text too)
     * Texy::CONTENT_BLOCK – in addition contains some block HTML elements (DIV, P, TABLE, TD, …)
	 */
	public $type;
	
	protected $args = array();
	
	/**
	 * Instance of Texy!
	 *
	 * @var Texy
	 */
	protected $texy;
	
	/**
	 * Instance of JTemplate
	 *
	 * @var JTemplate
	 */
	protected $tpl = NULL;
	
	function __construct(array $args, Texy &$texy) {
		if (empty($this->type)) {
			throw new InvalidStateException("Plugin's type is not set");
		}
		
		$this->args = $args;
		$this->texy = $texy;
		
		// template?
		if (empty($this->tpl)) {
			$template = new JFile(JOSS_APP_DIR . '/config/tpl/' . $this->getClass() . '.tpl');
			if ($template->exists()) {
				$this->tpl = new JTemplate($template->file); 
			}
		}
	}
	
	abstract public function process();
	
}
