<?php

/**
 * Joss framework & content management system.
 *
 * Created 10.4.2008.
 *
 * @author    Jan (Honza) Javorek aka Littlemaple http://www.javorek.net
 * @copyright Copyright (c) 2008 Jan Javorek
 * @package   Joss
 * @link      http://code.google.com/p/joss-cms/
 * @license   GNU GENERAL PUBLIC LICENSE version 2
 */



/**
 * Google Analytics.
 * 
 * Arguments:
 * 	[0] ... ID
 * 	[1] ... generate the old measuring code (bool, default false)
 *
 * @author     Jan (Honza) Javorek aka Littlemaple <honza@javorek.net>
 * @copyright  Copyright (c) 2008 Jan Javorek
 * @package    Joss
 * @version    $Revision$ ($Date$, $Author$)
 */
class JPGoogleAnalytics extends JPlugin {
	
	public $cached = FALSE;

	public $type = Texy::CONTENT_MARKUP;
	
	public function __construct($args, $texy) {
		parent::__construct($args, $texy);
	}
	
	public function process() {
		if (empty($this->args[0])) {
			return NULL;
		}
		if (!empty($this->args[1]) && $this->args[1]) {
			return '
				<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
				</script>
				<script type="text/javascript">
				_uacct = "' . (string)$this->args[0] . '";
				urchinTracker();
				</script>
			';
		}
		return '
			<script type="text/javascript">
			var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
			document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
			</script>
			<script type="text/javascript">
			var pageTracker = _gat._getTracker("' . (string)$this->args[0] . '");
			pageTracker._initData();
			pageTracker._trackPageview();
			</script>
		';
	}
	
}
