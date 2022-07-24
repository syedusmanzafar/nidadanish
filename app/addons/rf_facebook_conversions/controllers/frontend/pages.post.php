<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == "view" && Registry::get('addons.rf_facebook_conversions.pageview') == "Y")
{
	$data = [
		'event_name' => 'PageView',
		'event_id' => 'page_id.' . $_REQUEST['page_id'],
	];
	fn_rf_facebook_conversions_make_request($data);
}