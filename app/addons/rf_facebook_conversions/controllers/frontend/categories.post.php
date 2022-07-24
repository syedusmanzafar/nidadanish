<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == "view" && Registry::get('addons.rf_facebook_conversions.viewcontent') == "Y")
{
	$category_id = (int) $_REQUEST['category_id'];
	if ($category_id > 0)
	{
		$data = [
			'event_name' => 'ViewContent',
			'event_id' => 'category_id.' . $_REQUEST['category_id'],
		];
		fn_rf_facebook_conversions_make_request($data);
	}
}