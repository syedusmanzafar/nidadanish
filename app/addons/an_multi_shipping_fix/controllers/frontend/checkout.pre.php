<?php

use Tygh\Registry;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $cart */
$cart = &Tygh::$app['session']['cart'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode === 'checkout') {

    if (!empty($_REQUEST['shipping_ids']) && isset($cart['product_groups'])) {
        $shipping_id = reset($_REQUEST['shipping_ids']);
        foreach ($cart['product_groups'] as $group_key => $group) {
            $_REQUEST['shipping_ids'][$group_key] = $shipping_id;
        }       
    }


}
