<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'add' && AREA == "C" && Registry::get('addons.rf_facebook_conversions.addtocart') == "Y")
{
	if (!empty($_REQUEST['product_data']))
	{
		$product = current($_REQUEST['product_data']);
		$data = [
				'event_name' => 'AddToCart',
				'event_id' => 'cart_id.' . $product['product_id'],
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
				'content_ids' => [
					$product['product_id']
				],
				'content_type' => 'product',
				'content_name' => fn_get_product_name($product['product_id']),
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
elseif ($mode == "checkout" && Registry::get('addons.rf_facebook_conversions.checkout') == "Y")
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
	fn_rf_facebook_conversions_make_request($data);
}