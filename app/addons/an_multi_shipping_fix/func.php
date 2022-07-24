<?php


use Tygh\Registry;
use Tygh\Tools\SecurityHelper;
use Tygh\Languages\Languages;
use Tygh\Tools\Url;

if (!defined('BOOTSTRAP')) {die('Access denied');}


function fn_an_multi_shipping_fix_allow_place_order($total, &$cart, $parent_order_id){

    //fn_print_r('dfdfdf');
    if(!empty($cart['company_shipping_failed'])){
        $cart['company_shipping_failed'] = false;
    }
}

function fn_an_multi_shipping_fix_get_store_locations_for_shipping_before_select($destination_id, $fields, $joins, &$conditions)
{
    if (isset($conditions['company_id'])) {
        unset($conditions['company_id']);
    }
}

function fn_an_multi_shipping_fix_shippings_get_company_shipping_ids($company_id, &$shipping_ids)
{
    $shipping_ids = db_get_fields("SELECT shipping_id FROM ?:shippings WHERE status = ?s", 'A');
}

function fn_an_multi_shipping_fix_calculate_cart_taxes_pre(&$cart, $cart_products, &$product_groups, $calculate_taxes, $auth)
{
    $first_product_group = reset($cart['product_groups']);

    /*
    if (isset($first_product_group['chosen_shippings'])) {
        $chosen_shipping = reset($first_product_group['chosen_shippings']);

        $chosen_shipping_id = $chosen_shipping['shipping_id'];

        if (isset($first_product_group['shippings'][$chosen_shipping_id]['rate'])) {
            $cart['shipping_cost'] = $first_product_group['shippings'][$chosen_shipping_id]['rate'];
            $cart['display_shipping_cost'] = $cart['shipping_cost'];
        }

        $cart['chosen_shipping'] = [];
        foreach ($cart['product_groups'] as $group_key => $group) {
            $cart['chosen_shipping'][$group_key] = $chosen_shipping_id;
            $cart['product_groups'][$group_key]['chosen_shippings'] = [];
            $cart['product_groups'][$group_key]['chosen_shippings'][] = $chosen_shipping;
        }

        $cart['shipping'] = [];
        $cart['shipping'][$chosen_shipping_id] = $chosen_shipping;

        $shipping_cost = 0;
        foreach ($cart['product_groups'] as $key => $product_group) {
            foreach ($product_group['shippings'] as $shipping_id => $shipping) {
                if ($shipping['rate_info']['rate_value']['C']) {
                    foreach ($shipping['rate_info']['rate_value']['C'] as $rate_value) {
                        if ($cart['subtotal'] >= $rate_value['amount']) {
                            $shipping_cost = $rate_value['value'];
                        }
                    }

                    $cart['product_groups'][$key]['shippings'][$shipping_id]['rate'] = $shipping_cost;
                }
            }
            $cart['product_groups'][$key]['chosen_shippings'][0]['rate'] = $cart['product_groups'][$key]['shippings'][$chosen_shipping_id]['rate'];
        }

        $product_groups = $cart['product_groups'];

        if (!empty($cart['shipping'][$chosen_shipping_id]['rate_info']['rate_value']['C'])) {
            $rate_value = $cart['shipping'][$chosen_shipping_id]['rate_info']['rate_value']['C'];

            $shipping_cost = 0;
            foreach ($rate_value as $key => $value) {
                if ($cart['subtotal'] >= $value['amount']) {
                    $shipping_cost = $value['value'];
                }
            }

            $cart['shipping'][$chosen_shipping_id]['rate'] = $shipping_cost;
            $cart['shipping_cost'] = $shipping_cost;
            $cart['display_shipping_cost'] = $cart['shipping_cost'];
        }
    }
    */
}

function fn_an_multi_shipping_fix_place_suborders($cart, &$suborder_cart)
{
    /*if (isset($suborder_cart['shipping_cost']) && $suborder_cart['shipping_cost'] > 0) {
        $suborder_cart['total'] -= $suborder_cart['shipping_cost'];
        $suborder_cart['shipping_cost'] = $suborder_cart['shipping_cost'] = 0;        
    }*/
}