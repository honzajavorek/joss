<?php

/**
 * Joss framework & content management system.
 *
 * Created 9.4.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * RSS/ATOM feed aggregator.
 * 
 * Arguments:
 * 	[0] ... link to RSS or ATOM feed
 *  [1] ... number of items (default 5)
 *  [2] ... expiration time in hours (default 6)
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPFeed extends JPlugin {
	
	public $cached = FALSE;
	
	private $url = '';
	
	private $numberOfItems = 5;
	
	private $expirationTime = 6;

	public $type = Texy::CONTENT_BLOCK;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
		if (!empty($this->args[0])) {
			$this->url = (string)$this->args[0];
		}
		if (!empty($this->args[1])) {
			$this->numberOfItems = (int)$this->args[1];
		}
		if (!empty($this->args[2])) {
			$this->expirationTime = (int)$this->args[2];
		}
		unset($this->args);
	}
	
	public function parseFeed() {
		$output = Html::el('ul')->class('feed');
		
		$file = new JFile($this->url, TRUE, $this->expirationTime);
		$xml = $file->content;
		
		// items
		if (!empty($xml->channel->item[0])) {
			$items = &$xml->channel->item;
		} elseif (!empty($xml->item[0])) {
			$items = &$xml->item;
		} elseif (!empty($xml->entry[0])) { // atom
			$items = &$xml->entry;
		} else {
			return NULL;
		}
		
		for ($i = 0; $i < $this->numberOfItems && isset($items[$i]); $i++) {
			$item = $items[$i];

			// link
			if (isset($item->link['href'])) { // atom
				$link = $item->link['href'];
			} elseif (!empty($item->link)) {
				$link = (string)$item->link;
			} elseif (!empty($item->guid) && strpos($item->guid, 'http://') !== false) {
				$link = (string)$item->guid;
			} else {
				continue;
			}
			
			// title
			if (!empty($item->title)) {
				$title = (string)$item->title;
			} else {
				continue;
			}
			
			$a = Html::el('a')->href($link)->setText($title);
			$output->add(Html::el('li')->add($a));
		}

		return $output;
	}
	
	public function process() {
		// URL
		if (empty($this->url)) {
			return NULL;
		}
		
		// cache
		$cache = new JCache(md5($this->url), array($this, 'parseFeed'), $this->expirationTime);
		return $cache->process();
	}
	
}
