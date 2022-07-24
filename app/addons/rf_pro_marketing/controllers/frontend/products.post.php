<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (($mode == "view" || $mode == "quick_view") && Registry::get('addons.rf_pro_marketing.fbc_events.fbc_events_viewcontent') == "Y")
{
	$product_id = (int) $_REQUEST['product_id'];
	if ($product_id > 0)
	{
		$product_id_param = Registry::get('addons.rf_pro_marketing.fbc_product_id');
		$product = Tygh::$app['view']->getTemplateVars('product');
		$data = [
			'event_name' => 'ViewContent',
			'content_ids' => [$product[$product_id_param]],
			'content_type' => 'product',
			'content_name' => $product['product'],
			'value' => $product['price'],
			'currency' => CART_SECONDARY_CURRENCY,
			'event_id' => 'product_id.' . $_REQUEST['product_id'],
		];
		fn_rf_pro_marketing_make_request($data);
	}
}
elseif ($mode == "search" && Registry::get('addons.rf_pro_marketing.fbc_events.fbc_events_search') == "Y")
{
	$data = [
		'event_name' => 'Search',
		'event_id' => 'search_result',
		'search_string' => $_REQUEST['q']
	];
	fn_rf_pro_marketing_make_request($data);
}

if ($mode == 'quick_view')
{
	if (defined('AJAX_REQUEST') && !empty($_REQUEST['product_id']))
	{
		$product_id_param = Registry::get('addons.rf_pro_marketing.fbc_product_id');
		$product = Tygh::$app['view']->getTemplateVars('product');
		$data = [
			'product' => [
				'id' => $product[$product_id_param],
				'price' => intval($product['price']),
			],
			'event_ViewContent' => fn_rf_pro_marketing_get_event_id("ViewContent")
		];
		Tygh::$app['ajax']->assign('rf_pro_marketing', $data);
	}
}
