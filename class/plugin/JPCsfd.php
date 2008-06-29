<?php

/**
 * Joss framework & content management system.
 *
 * Created 30.4.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Fetches latest movies ranked at CSFD (www.csfd.cz).
 * 
 * Arguments:
 * 	[0] ... link to the profile, e.g. http://www.csfd.cz/uzivatel/34059-lmaple/hodnoceni/?podle=data
 * 	[1] ... number of items (optional)
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPCsfd extends JPlugin {
	
	/**
	 * Number of items.
	 *
	 * @var int
	 */
	private $number = 5;
	
	public $cached = FALSE;

	public $type = Texy::CONTENT_BLOCK;

	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	public function process() {
		$output = Html::el('ul');

		if (!empty($this->args[1])) {
			$this->number = (int)$this->args[1];
		}
		
		if (!empty($this->args[0])) {
			$news = array();
			$file = new JFile($this->args[0], TRUE, 12);
	
			if ($file->exists()) {
				$html = preg_replace('~href="/~i', 'href="http://www.csfd.cz/', $file->content);
				
				$matches = array();
				preg_match('~charset=([^\'"]+)[\'"]>~i', $html, $matches);
				$html = iconv(strtoupper($matches[1]), 'UTF-8', $html);
				
				preg_match_all(
					'~<td\\s+colspan=[\'"]2[\'"]>\\s+([^&]+)&nbsp;&nbsp;([^\\(]+)\\([^\\)]+\\)\\s+</td>\\s+<td align=[\'"]right[\'"]>([^&]+)&nbsp;&nbsp;&nbsp;</td>\\s+<td\\s+align=[\'"]center[\'"]>([^<]+)</td>~i',
					$html,
					$matches // 1 - film quality, 2 - name & link, 3 - rating, 4 - date
				);

				for($i = 0; $i <= $this->number; $i++) {
					$output->add(Html::el('li')->class('csfd')->setText(
						$matches[2][$i] . '<br />' . $matches[3][$i],
					TRUE));
				}
			}
		}

		return $output;
	}
	
}