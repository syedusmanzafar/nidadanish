<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == "view_all" && Registry::get('addons.rf_facebook_conversions.pageview') == "Y")
{
	$data = [
		'event_name' => 'PageView',
		'event_id' => 'product_features',
	];
	fn_rf_facebook_conversions_make_request($data);
}