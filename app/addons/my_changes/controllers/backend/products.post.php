<?php

use Tygh\Registry;

// Remove subscriber tab from vendor administration panel
if (!defined('BOOTSTRAP')) { die('Access denied'); }
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

	if ($mode == 'update') {  
		if ($auth['user_type'] != "A"){

			unset($tabs['subscribers']);
			Registry::del('navigation.tabs.subscribers');
		}
	}
}
