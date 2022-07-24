<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == "view_all" && Registry::get('addons.rf_pro_marketing.fbc_events.fbc_events_pageview') == "Y")
{
	$data = [
		'event_name' => 'PageView',
		'event_id' => 'product_features',
	];
	fn_rf_pro_marketing_make_request($data);
}
