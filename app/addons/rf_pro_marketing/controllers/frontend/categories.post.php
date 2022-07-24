<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == "view" && Registry::get('addons.rf_pro_marketing.fbc_events.fbc_events_viewcontent') == "Y")
{
	$category_id = (int) $_REQUEST['category_id'];
	if ($category_id > 0)
	{
		$data = [
			'event_name' => 'ViewContent',
			'event_id' => 'category_id.' . $_REQUEST['category_id'],
		];
		fn_rf_pro_marketing_make_request($data);
	}
}
