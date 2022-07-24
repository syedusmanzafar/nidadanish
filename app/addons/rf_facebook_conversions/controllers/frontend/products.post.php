<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (($mode == "view" || $mode == "quick_view") && Registry::get('addons.rf_facebook_conversions.viewcontent') == "Y")
{
	$product_id = (int) $_REQUEST['product_id'];
	if ($product_id > 0)
	{
		$product = Tygh::$app['view']->getTemplateVars('product');
		$data = [
			'event_name' => 'ViewContent',
			'content_ids' => [$_REQUEST['product_id']],
			'content_type' => 'product',
			'content_name' => $product['product'],
			'value' => $product['price'],
			'currency' => CART_SECONDARY_CURRENCY,
			'event_id' => 'product_id.' . $_REQUEST['product_id'],
		];
		fn_rf_facebook_conversions_make_request($data);
	}
}
elseif ($mode == "search" && Registry::get('addons.rf_facebook_conversions.search') == "Y")
{
	$data = [
		'event_name' => 'Search',
		'event_id' => 'search_result',
		'search_string' => $_REQUEST['q']
	];
	fn_rf_facebook_conversions_make_request($data);
}