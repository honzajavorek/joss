<?php

/**
 * Joss framework & content management system.
 *
 * Created 26.1.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://work.javorek.net/joss
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Caching of data.
 * 
 * @author     Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JCache extends NObject {
	
	/**
	 * Content to be stored.
	 *
	 * @var callback
	 */
	private $content;
	
	/**
	 * Hash.
	 *
	 * @var string
	 */
	private $hash;
	
	/**
	 * Sets up a cache file.
	 *
	 * @param string $identifier Unique cache identifier.
	 * @param callback $contentGetter Function which provides the content to be stored.
	 * @param int $expires Expiration time in hours.
	 */
	public function __construct($identifier, $content, $expires = 24) {
		$this->hash = md5($identifier);
		if (is_callable($content)) {
			$this->content = $content;
		} else {
			throw new LogicException('Invalid callback for cache.');
		}
 
		$fCache = new JFile('safe://' . JOSS_APP_DIR . '/cache/' . $this->hash . '.cache');
		
		if ($fCache->exists()) { // exists cache?

			if (microtime(TRUE) - $fCache->content['timestamp'] > ($expires * 60 * 60)) {
				$fCache->unlink();
				$this->create();
			}
		} else { // doesn't exist
			$this->create();
		}
	}
	
	/**
	 * Creates a cache file.
	 */
	private function create() {
		$fCache = new JFile('safe://' . JOSS_APP_DIR . '/cache/' . $this->hash . '.cache');
		$fCache->content = self::save(call_user_func($this->content));
	}
	
	/**
	 * Returns a content from cache.
	 * 
	 * @return mixed
	 */
	public function process() {
		$f = new JFile(JOSS_APP_DIR . '/cache/' . $this->hash . '.cache');
		return $f->content['content'];
	}
	
	/**
	 * Serializes a cache data.
	 *
	 * Inspired by http://blog.makemepulse.com/2007/09/27/serialize-and-unserialize-simplexml-in-php/.
	 * 
	 * @param mixed $data
	 * @return string
	 */
	public static function save($data) {
		if ($data instanceof SimpleXMLElement) {
			$data = array(
				'type' => 'SimpleXMLElement',
				'data' => $data->asXml()
			);
		}
		return serialize(array(
			'timestamp' => microtime(TRUE),
			'content' => $data
		));
	}
	
	/**
	 * Unserializes a cache data.
	 *
	 * Inspired by http://blog.makemepulse.com/2007/09/27/serialize-and-unserialize-simplexml-in-php/.
	 * 
	 * @param string $data
	 * @return array
	 */
	public static function load($data) {
		$data = unserialize($data);
		if (isset($data['content']['type']) && $data['content']['type'] == 'SimpleXMLElement'
		) {
			$data['content'] = simplexml_load_string($data['content']['data']);
		}
		return $data;
	}
	
}
