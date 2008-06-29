<?php

/**
 * Joss framework & content management system.
 *
 * Created 9.6.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Sitemap generator.
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JSitemap extends Object {

	private $defaultLanguage = NULL;
	private $expires = NULL;
	
	public function __construct($defaultLanguage, $expires) {
		if (!is_string($defaultLanguage) || strlen($defaultLanguage) != 2) {
			throw new LogicException('Language must be a two-letter code.');			
		}
		if (!is_numeric($expires)) {
			throw new LogicException('Expiration time must be numeric.');			
		}
		$this->defaultLanguage = $defaultLanguage;
		$this->expires = (int)$expires; // in seconds
	}

	/**
	 * Decides if should be generated new sitemap and generates it.
	 *
	 * @param JFile $file
	 * @return void
	 */
	public function generate(JFile &$file) {
		if ($file->exists()) { // refresh
			if (time() - $this->expires > $file->changed()) { // check expiration
				$file->content = $this->xml();
			} // else nothing...
		} else { // create
			$file->content = $this->xml();
		}
	}
	
	private function checkDir($dir) {
		$items = '';
		$d = dir($dir);
		while (FALSE !== ($f = $d->read())) {
			if (!is_dir("$dir/$f") && $f{0} != '_' && $f{0} != '.') {
				$entry = substr($f, 0, -5);
				
				// languages
				$lang = '';
				if (is_dir(JOSS_APP_DIR . JDoc::PATH . JLang::DIRECTORY_LANG)) { // multiple language versions
					$lang = basename($dir);
					$lang = (strlen($lang) == 2)? "$lang/" : "$this->defaultLanguage/";
				}
					
				$loc = 'http://' . $_SERVER['SERVER_NAME'] . JOSS_URL_ROOT . "/$lang" . (($entry == 'index')? '' : $entry);
				$changefreq = ($entry == 'index')? 'daily' : 'monthly';
				$priority = ($entry == 'index')? '1.0' : '0.2';
				
				$items .= "\t<url>\n
					\t\t<loc>" . rtrim($loc, '/') . "/</loc>\n
					\t\t<changefreq>$changefreq</changefreq>\n
					\t\t<priority>$priority</priority>\n
					\t</url>\n";
			}
		}
		$d->close();
		return $items;
	}
	
	/**
	 * Generates XML of the sitemap.
	 *
	 * @return string
	 */
	private function xml() {
		$items = '';
		$paths = array(JOSS_APP_DIR . JDoc::PATH); // default position for content
		
		// multiple language versions
		$dir = JOSS_APP_DIR . JDoc::PATH . JLang::DIRECTORY_LANG;
		if (is_dir($dir)) {
			$d = dir($dir);
			while (FALSE !== ($f = $d->read())) {
				if (is_dir("$dir/$f") && strlen($f) == 2 && $f{0} != '_' && $f{0} != '.') {
					$paths[] = "$dir/$f";
				}
			}
			$d->close();
		}
		
		// checking dirs
		foreach ($paths as $path) {
			$items .= $this->checkDir($path);
		}
		
		return
			'<?xml version="1.0" encoding="utf-8"?><!-- Joss -->' . "\n" .
			'<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n" .
			$items .
			'</urlset>' . "\n";
	}

}
