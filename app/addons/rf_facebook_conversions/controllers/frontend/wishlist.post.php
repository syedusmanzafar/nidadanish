<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($mode == "add" && !empty($_REQUEST['product_data']) && Registry::get('addons.rf_facebook_conversions.addtowishlist') == "Y")
	{
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
					'id' => $product['product_id'],
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
		fn_rf_facebook_conversions_make_request($data);
	}
}