<?php

use Tygh\Enum\ProductTracking;
use Tygh\Registry;
use Tygh\Storage;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

Tygh::$app['session']['wishlist'] = isset(Tygh::$app['session']['wishlist']) ? Tygh::$app['session']['wishlist'] : array();
$wishlist = & Tygh::$app['session']['wishlist'];
Tygh::$app['session']['continue_url'] = isset(Tygh::$app['session']['continue_url']) ? Tygh::$app['session']['continue_url'] : '';
$auth = & Tygh::$app['session']['auth'];

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Facebook Conversions
	if ($mode == "add" && !empty($_REQUEST['product_data']) && Registry::get('addons.rf_pro_marketing.fbc_events.fbc_events_addtowishlist') == "Y")
	{
		$product_id_param = Registry::get('addons.rf_pro_marketing.fbc_product_id');
		$product = current($_REQUEST['product_data']);
		$data = [
			'event_name' => 'AddToWishlist',
			'event_id' => 'wishlist_id.' . $product['product_id'],
			'user_data' => [
				'em' => (!empty($_REQUEST['user_data']['email'])) ? hash('sha256', $_REQUEST['user_data']['email']) : null,
				'ph' => (!empty($_REQUEST['user_data']['phone'])) ? hash('sha256', $_REQUEST['user_data']['phone']) : null,
			],
			'contents' => [
				[
					'id' => $product[$product_id_param],
					'quantity' => $product['amount']
				]
			],
			'custom_data' => [
				'currency' => CART_SECONDARY_CURRENCY,
				'value' => fn_get_product_price($product['product_id'], $product['amount'], Tygh::$app['session']['auth']),
				'product_name' => fn_get_product_name($product['product_id']),
				'product_url' => fn_url("products.view?product_id=".$product['product_id'])
			]
		];
		fn_rf_pro_marketing_make_request($data);
	}

	// Add product to the wishlist
	if ($mode == 'add')
	{
		$product_id_param = Registry::get('addons.rf_pro_marketing.gtag_product_id');
		$products = array ();
		if (!empty($_REQUEST['product_data']) && is_array($_REQUEST['product_data']))
		{
			foreach ($_REQUEST['product_data'] as $product_id => $_product)
			{
				$products[] = array ('id' => $_product[$product_id_param], 'price' => intval(fn_get_product_price($product_id, 1, Tygh::$app['session']['auth'])));
			}
		}

		if (defined('AJAX_REQUEST'))
		{
			Tygh::$app['ajax']->assign('rf_pro_marketing', array('wishlist_added' => $products, 'event_AddToWishlist' => fn_rf_pro_marketing_get_event_id("AddToWishlist")));
		}
		else
		{
			Tygh::$app['session']['rf_pro_marketing'] = array('wishlist_added' => $products);
		}
	}
}
