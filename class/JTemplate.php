<?php

/**
 * Joss framework & content management system.
 *
 * Inspired by Brian Lozier's <brian@massassi.net> Template Engines
 * at http://massassi.com/php/articles/template_engines/. Created 30.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Template engine.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JTemplate extends Object {
	
    /**
	 * Template file.
	 * 
	 * @var JFile
	 */
	protected $tpl;
    
    /**
     * Holds all the template variables.
     * 
     * @var array
     */
    protected $vars = array();

    /**
     * Template.
     *  
     * @param $file string The file you want to load.
     */
    function __construct($file) {
    	$f = new JFile($file);
    	if (!$f->exists()) {
    		throw new DomainException("File '$file' does not exist.");
    	} else {
        	$this->tpl = $f;
    	} 
    }

    /**
     * Set a template variable.
     *
     * @param string $name
     * @param mixed $value
     * @param bool $escape Explicit setting of escaping.
     */
    public function set($name, $value, $escape = TRUE) {
		if (is_object($value)) { // object
			if(method_exists($value, '__toString')) {
				$this->vars[$name] = ($escape)? htmlspecialchars($value->__toString()) : $value->__toString();
			} else {
				throw new Exc("Template " . basename($this->file) . " cannot accept given object, because it is not another instance of Template class and it cannot be converted by '__toString' method.");
			}
		} elseif ($escape) { // variable (should be escaped)
			if (is_array($value)) {
				array_walk_recursive($value, array($this, 'escape'));
				$this->vars[$name] = $value;
			} else {
				$this->vars[$name] = $this->escape($value);
			}
		} else { // variable (should NOT be escaped)
			$this->vars[$name] = $value;
		}
    }
    
    /**
     * Escaping of variables.
     *
     * @param mixed $str unescaped string
     * @return mixed escaped string
     */
    private function escape($str) {
        return (is_string($str))? htmlspecialchars($str) : $str;
    }

    /**
     * Open, parse, and return the template file.
     */
    public function fetch() {
        extract($this->vars);          // extract the vars to local namespace
        ob_start();                    // start output buffering
        
        if (!ini_get('short_open_tag')) { // explicit short open tags support
				eval('?>' . preg_replace(
				array('~<\\?(\\s)~', '~<\\?=~'),
				array('<?php\\1', '<?php echo'),
			$this->tpl->content));
        } else {
        	include($this->tpl->file);                // include the file
        }
        
        $contents = ob_get_contents(); // get the contents of the buffer
        ob_end_clean();                // end buffering and discard
        return $contents;              // return the contents
    }
    
    public function __toString() {
    	return $this->fetch();
    }
	
}
