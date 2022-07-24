<?php

use Tygh\Enum\ProductTracking;
use Tygh\Registry;
use Tygh\Storage;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

Tygh::$app['session']['wishlist'] = isset(Tygh::$app['session']['wishlist']) ? Tygh::$app['session']['wishlist'] : array();
$wishlist = & Tygh::$app['session']['wishlist'];
Tygh::$app['session']['continue_url'] = isset(Tygh::$app['session']['continue_url']) ? Tygh::$app['session']['continue_url'] : '';
$auth = & Tygh::$app['session']['auth'];

if ($mode == 'delete')
{
	if (!empty($_REQUEST['cart_id']))
	{
		$product_id_param = Registry::get('addons.rf_pro_marketing.gtag_product_id');
		foreach (Tygh::$app['session']['wishlist']['products'] as $cart_id => $_product)
		{
			if ($cart_id == $_REQUEST['cart_id'])
			{
				Tygh::$app['session']['rf_pro_marketing'] = array ('wishlist_deleted' => array(array ('id' => $_product[$product_id_param], 'price' => intval(fn_get_product_price($_product['product_id'], 1, Tygh::$app['session']['auth'])))));
			}
		}
	}
}
