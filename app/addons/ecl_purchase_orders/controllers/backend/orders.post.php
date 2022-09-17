<?php

use Tygh\Pdf;

defined('BOOTSTRAP') or die('Access denied');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'purchase_orders' && !empty($_REQUEST['order_ids'])) {
        $filename = (string) __('print_purchase_order').TIME;
        foreach ($_REQUEST['order_ids'] as $key => $order_id) {
        	$order_info_print[$key] = fn_get_order_info($order_id, false, true, true, false);
        }
        $random = rand(15,54);
        Tygh::$app['view']->assign('random', $random);
        $total_price = [];
        foreach ($order_info_print as $order_id => $order) {
            foreach ($order['product_groups'] as $key => $value) {
                $ecl_orders[$value['company_id']]['company_name'] = $value['name'];
                $ecl_orders[$value['company_id']]['company_contact'] = $value['package_info']['origination']['phone'];
                $ecl_orders[$value['company_id']]['company_adress'] = $value['package_info']['origination']['address'];
                $ecl_orders[$value['company_id']]['company_details'] = $order['details'];
                $ecl_orders[$value['company_id']]['customer_notes'] = $order['notes'];
                $ecl_orders[$value['company_id']]['ecl_subtotal'] = $order['display_subtotal'];
                $ecl_orders[$value['company_id']]['ecl_tax_subtotal'] = $order['tax_subtotal'];
                $ecl_orders[$value['company_id']]['payment_terms'] = db_get_field("SELECT payment_terms FROM ?:companies WHERE company_id =?i", $value['company_id']);
                $ecl_orders[$value['company_id']]['vendor_terms'] = 
                db_get_field("SELECT terms FROM ?:company_descriptions WHERE company_id = ?i AND lang_code = ?s", $value['company_id'], DESCR_SL);
                $ecl_orders[$value['company_id']]['orders'][$order['order_id']] = $value;
            }
        }

        foreach ($ecl_orders as $key => $value) {
            $total_price[$key]['price'] = 0;
            foreach ($value['orders'] as $k => $v) {
                foreach ($v['products'] as $key_product => $product) {
                    if(!empty($product['price']) && !empty($key)){
                        $total_price[$key]['price'] += $product['price'];
                    }
                }
            }
        }
        Tygh::$app['view']->assign('order_info_print', $order_info_print);
        Tygh::$app['view']->assign('total_price', $total_price);
        Tygh::$app['view']->assign('ecl_orders', $ecl_orders);
        $html = Tygh::$app['view']->fetch('addons/ecl_purchase_orders/views/orders/components/purchase_orders.tpl', true);
        $result = Pdf::render($html, $filename, false);
        exit;
    }
}


if ($mode === 'purchase_orders') {
  if(!empty($_REQUEST['order_id'])){
    $filename = (string) __('print_purchase_order').TIME;
    $random = rand(15,54);
    Tygh::$app['view']->assign('random', $random);
    $order_id = $_REQUEST['order_id'];
    $ecl_order_info_print = fn_get_order_info($order_id, false, true, true, false);
    Tygh::$app['view']->assign('ecl_order_info_print', $ecl_order_info_print);
    foreach ($ecl_order_info_print['product_groups'] as $key => $orders) {
      $orders['vendor_terms'] = db_get_field("SELECT terms FROM ?:company_descriptions WHERE company_id = ?i AND lang_code = ?s", $orders['company_id'], DESCR_SL);
      $orders['payment_terms'] = db_get_field("SELECT payment_terms FROM ?:companies WHERE company_id = ?i", $orders['company_id']);
    }
    $ecl_total_price = 0;
    foreach ($ecl_order_info_print['products'] as $key => $value) {
        $ecl_total_price += $value['price'];
    }
    Tygh::$app['view']->assign('ecl_total_price', $ecl_total_price);
    Tygh::$app['view']->assign('orders', $orders);
    $html = Tygh::$app['view']->fetch('addons/ecl_purchase_orders/views/orders/components/purchase_order.tpl', true);
    $result = Pdf::render($html, $filename, false);
  }
}