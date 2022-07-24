<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == "checkout" && Registry::get('addons.rf_pro_marketing.fbc_events.fbc_events_checkout') == "Y")
{
	$data = [
		'event_name' => 'InitiateCheckout',
		'event_time' => TIME,
		'event_id' => 'checkout',
		'event_source_url' => REAL_URL,
		'user_data' => [
			'client_ip_address' => $_SERVER['REMOTE_ADDR'],
			'client_user_agent' => $_SERVER['HTTP_USER_AGENT']
		]
	];
	fn_rf_pro_marketing_make_request($data);
}
