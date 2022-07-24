<?php

use Tygh\Registry;
use Tygh\Shippings\Shippings;
use Tygh\Storage;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $cart */
$cart = &Tygh::$app['session']['cart'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode === 'checkout') {

    $product_groups   = Tygh::$app['view']->getTemplateVars('product_groups');

     //fn_print_die($product_groups);

    /** @var array $cart */
    $cart = &Tygh::$app['session']['cart'];

    $auth = &Tygh::$app['session']['auth'];

    $cart['subtotal']
        = $cart['display_subtotal']
        = $cart['original_subtotal']
        = $cart['amount']
        = $cart['total']
        = $cart['discount']
        = $cart['tax_subtotal']
        = $cart['display_shipping_cost']
        = $cart['shipping_cost']
        = 0;

    $product_groups = $cart['product_groups'];

    $new_products_group = array();

    $first_product_group = reset($cart['product_groups']);

    foreach ($product_groups as $product_key => $group){
        //fn_print_r($group['package_info_full']);
        //fn_print_r($group['products']);

        if($group['company_id'] != $first_product_group['company_id']) {

            $first_product_group['products'] = array_replace($first_product_group['products'], $group['products']);
        }
    }


   // fn_print_die($cart['products']);

    foreach ($first_product_group['products'] as $kkey => $product) {

        $first_product_group['products'][$kkey]['company_id'] =2;
    }

    $location = fn_get_customer_location($auth, $cart);
    $product_groups = Shippings::groupProductsList($first_product_group['products'], $location);




    $shippings = [];

    foreach ($product_groups as $key_group => $group) {
        if ($cart['shipping_required'] === false) {
            $product_groups[$key_group]['free_shipping'] = true;
            $product_groups[$key_group]['shipping_no_required'] = true;
        }

        $product_groups[$key_group]['shippings'] = [];
        $shippings_group = Shippings::getShippingsList($group, CART_LANGUAGE, "C", ['get_images' => true]);


        foreach ($shippings_group as $shipping_id => $shipping) {
            $product_group = $group;
            if (!empty($shipping['service_params']['max_weight_of_box'])) {
                $product_group = Shippings::repackProductsByWeight($group, $shipping['service_params']['max_weight_of_box']);
            }

            $shippings[] = array_merge($shipping, [
                'package_info'      => $product_group['package_info'],
                'package_info_full' => $product_group['package_info_full'],
                'keys'              => [
                    'group_key'   => $key_group,
                    'shipping_id' => $shipping_id,
                ],
            ]);

            $shipping['group_key'] = $key_group;
            $shipping['rate'] = 0;

            // shipping is free when obtained via promotions, or group has free shipping and shipping method is suitable for free shipping
            $shipping['free_shipping'] = in_array($shipping_id, $cart['free_shipping']) ||
                $group['free_shipping'] && Shippings::isFreeShipping($shipping);

            $product_groups[$key_group]['shippings'][$shipping_id] = $shipping;

            // Adding a shipping method from the created order, if the shipping is not yet in the list.
            if (!empty($cart['chosen_shipping']) && !empty($cart['shipping']) && !empty($cart['order_id'])) {
                foreach ($cart['shipping'] as $cart_shipping) {
                    if (!isset($shippings_group[$cart_shipping['shipping_id']])) {
                        $shippings_group[$cart_shipping['shipping_id']] = $cart_shipping;
                    }
                }
            }
        }
    }

    $shipping_cache_key = 'calculated_shipping_rates';


    $rates = Shippings::calculateRates($shippings);
    Registry::set($shipping_cache_key, $rates);



    foreach ($rates as $rate) {
        $group_key = $rate['keys']['group_key'];
        $shipping_id = $rate['keys']['shipping_id'];

        if ($rate['price'] !== false) {
            $rate['price'] += !empty($product_groups[$group_key]['package_info']['shipping_freight'])
                ? $product_groups[$group_key]['package_info']['shipping_freight']
                : 0;
            $product_groups[$group_key]['shippings'][$shipping_id]['rate'] = empty($product_groups[$group_key]['shippings'][$shipping_id]['free_shipping'])
                ? $rate['price']
                : 0;
        } else {
            unset($product_groups[$group_key]['shippings'][$shipping_id]);
        }

        if (!empty($rate['service_delivery_time'])) {
            $product_groups[$group_key]['shippings'][$shipping_id]['service_delivery_time'] = $rate['service_delivery_time'];
        }
    }


    // Collect product data
    foreach ($cart['products'] as $cart_id => $cart_product) {
        $cart['products'][$cart_id]['amount_total'] = isset($amount_totals[$cart_product['product_id']])
            ? $amount_totals[$cart_product['product_id']]
            : $cart_product['amount'];

        $product_data = fn_get_cart_product_data(
            $cart_id,
            $cart['products'][$cart_id],
            false,
            $cart,
            $auth,
            0,
            CART_LANGUAGE
        );

        if (!$product_data) { // FIXME - for deleted products for OM
            fn_delete_cart_product($cart, $cart_id);
            continue;
        }

        $cart_products[$cart_id] = $product_data;
    }




    // FIXME
    $cart['shipping_cost'] = 0;
    $cart['shipping'] = array();
    if (empty($cart['chosen_shipping'])) {
        $cart['chosen_shipping'] = array();
        if (
            fn_allowed_for('ULTIMATE')
            && Registry::get('settings.Checkout.display_shipping_step') != 'Y'
            && $cart['calculate_shipping']
        ) {
            foreach ($product_groups as $key_group => $group) {
                if (!empty($group['shippings'])) {
                    $first_shipping = reset($group['shippings']);
                    $cart['chosen_shipping'][$key_group] = $first_shipping['shipping_id'];
                }
            }
        }
    }

    $count_shipping_failed = 0;

    //fn_print_die($product_groups);

    foreach ($product_groups as $key_group => $group) {
        if ($cart['calculate_shipping'] && (
                !isset($cart['chosen_shipping'][$key_group]) ||
                empty($group['shippings'][$cart['chosen_shipping'][$key_group]])
            ) && (
                !$group['free_shipping'] ||
                $group['all_free_shipping']
            )
        ) {
            $cart['chosen_shipping'][$key_group] = key($group['shippings']);
        }

        if ($group['shipping_no_required']) {
            unset($cart['chosen_shipping'][$key_group]);
        }

        if (!isset($cart['chosen_shipping'][$key_group]) && !$group['shipping_no_required']) {
            $count_shipping_failed++;
            $cart['company_shipping_failed'] = true;
        }

        foreach ($group['shippings'] as $shipping_id => $shipping) {
            if (isset($cart['chosen_shipping'][$key_group]) && $cart['chosen_shipping'][$key_group] == $shipping_id) {
                $cart['shipping_cost'] += $shipping['rate'];
            }
        }

        if (!empty($group['shippings']) && isset($cart['chosen_shipping'][$key_group]) && isset($group['shippings'][$cart['chosen_shipping'][$key_group]])) {
            $shipping = $group['shippings'][$cart['chosen_shipping'][$key_group]];
            $shipping_id = $shipping['shipping_id'];
            if (empty($cart['shipping'][$shipping_id])) {
                $cart['shipping'][$shipping_id] = $shipping;
                $cart['shipping'][$shipping_id]['rates'] = array();
            }
            $cart['shipping'][$shipping_id]['rates'][$key_group] = $shipping['rate'];
        }
    }
    $cart['display_shipping_cost'] = $cart['shipping_cost'];

    if (!empty($product_groups) && count($product_groups) == $count_shipping_failed) {
        $cart['shipping_failed'] = true;
    }

    foreach ($cart['chosen_shipping'] as $key_group => $shipping_id) {
        if (!empty($product_groups[$key_group]) && !empty($product_groups[$key_group]['shippings'][$shipping_id])) {
            $shipping = $product_groups[$key_group]['shippings'][$shipping_id];
            $shipping['group_name'] = $product_groups[$key_group]['name'];
            $product_groups[$key_group]['chosen_shippings'] = array($shipping);
        } else {
            unset($cart['chosen_shipping'][$key_group]);
        }
    }

    fn_apply_stored_shipping_rates($cart);

    fn_set_hook('calculate_cart_taxes_pre', $cart, $cart_products, $product_groups, $calculate_taxes, $auth);

    $calculated_taxes_summary = [];

    foreach ($product_groups as $key_group => &$group) {
        foreach ($group['products'] as $cart_id => $product) {
            if (!empty($cart_products[$cart_id])) {
                $group['products'][$cart_id] = $cart_products[$cart_id];
            }
        }

        // Calculate taxes
        if ($calculate_taxes && $auth['tax_exempt'] !== 'Y') {
            $calculated_taxes = fn_calculate_taxes($cart, $key_group, $group['products'], $group['shippings'], $auth);

            foreach ($calculated_taxes as $tax_id => $tax) {
                if (empty($calculated_taxes_summary[$tax_id])) {
                    $calculated_taxes_summary[$tax_id] = $calculated_taxes[$tax_id];
                } else {
                    $calculated_taxes_summary[$tax_id]['tax_subtotal'] += $calculated_taxes[$tax_id]['applies']['S'];
                    $calculated_taxes_summary[$tax_id]['applies']['S'] += $calculated_taxes[$tax_id]['applies']['S'];
                    $calculated_taxes_summary[$tax_id]['tax_subtotal'] += $calculated_taxes[$tax_id]['applies']['P'];
                    $calculated_taxes_summary[$tax_id]['applies']['P'] += $calculated_taxes[$tax_id]['applies']['P'];
                }
            }
        } elseif ($cart['stored_taxes'] !== 'Y') {
            $cart['taxes'] = $cart['tax_summary'] = [];
        }
    }
    unset($group);

    fn_apply_calculated_taxes($calculated_taxes_summary, $cart);

    $shipping_rates = [];

    /**
     * Executes after taxes are calculated when calculating cart content.
     *
     * @param array $cart
     * @param array $cart_products
     * @param array $shipping_rates Deprecated: is always an empty array
     * @param bool  $calculate_taxes
     * @param array $auth
     */
    fn_set_hook('calculate_cart_taxes_post', $cart, $cart_products, $shipping_rates, $calculate_taxes, $auth);

    $cart['subtotal'] = $cart['display_subtotal'] = 0;

    fn_update_cart_data($cart, $cart_products);

    foreach ($cart['products'] as $product_code => $product) {
        foreach ($product_groups as $key_group => $group) {
            if (in_array($product_code, array_keys($group['products']))) {
                $product_groups[$key_group]['products'][$product_code] = $product;
            }
        }
    }

    // Calculate totals
    foreach ($product_groups as $key_group => $group) {
        foreach ($group['products'] as $product_code => $product) {
            $_tax = (!empty($product['tax_summary']) ? ($product['tax_summary']['added'] / $product['amount']) : 0);
            $cart_products[$product_code]['display_price'] = $cart_products[$product_code]['price'] + (Registry::get('settings.Appearance.cart_prices_w_taxes') == 'Y' ? $_tax : 0);
            $cart_products[$product_code]['subtotal'] = $cart_products[$product_code]['price'] * $product['amount'];

            $cart_products[$product_code]['display_subtotal'] = $cart_products[$product_code]['display_price'] * $product['amount'];

            if (!empty($product['tax_summary'])) {
                $cart_products[$product_code]['tax_summary'] = $product['tax_summary'];
            }

            $cart['subtotal'] += $cart_products[$product_code]['subtotal'];
            $cart['display_subtotal'] += $cart_products[$product_code]['display_subtotal'];
            $cart['products'][$product_code]['display_price'] = $cart_products[$product_code]['display_price'];
            $product_groups[$key_group]['products'][$product_code]['display_price'] = $cart_products[$product_code]['display_price'];
            $product_groups[$key_group]['products'][$product_code]['main_category'] = $cart_products[$product_code]['main_category'];

            $cart['tax_subtotal'] += (!empty($product['tax_summary']) ? ($product['tax_summary']['added']) : 0);
            $cart['total'] += ($cart_products[$product_code]['price'] - 0) * $product['amount'];

            if (!empty($product['discount'])) {
                $cart['discount'] += $product['discount'] * $product['amount'];
            }
        }
    }

    if (Registry::get('settings.Checkout.tax_calculation') == 'subtotal') {
        $cart['tax_subtotal'] += (!empty($cart['tax_summary']['added']) ? ($cart['tax_summary']['added']) : 0);
    }

    $cart['subtotal'] = fn_format_price($cart['subtotal']);
    $cart['display_subtotal'] = fn_format_price($cart['display_subtotal']);

    $cart['total'] += $cart['tax_subtotal'];

    $cart['total'] = fn_format_price($cart['total'] + $cart['shipping_cost']);

    $cart['discounted_subtotal'] = $cart['subtotal'];

    if (!empty($cart['subtotal_discount'])) {
        $cart['discounted_subtotal'] = $cart['subtotal'] - ($cart['subtotal_discount'] < $cart['subtotal'] ? $cart['subtotal_discount'] : $cart['subtotal']);
        $cart['total'] -= ($cart['subtotal_discount'] < $cart['total']) ? $cart['subtotal_discount'] : $cart['total'];
    }


    //fn_print_die($product_groups);


    //$first_product_group = reset($cart['product_groups']);
    //$first_product_group['name'] = Registry::get('settings.Company.company_name');
    //Tygh::$app['view']->assign('product_groups', array($first_product_group));

    Tygh::$app['view']->assign('cart',$cart);

    Tygh::$app['view']->assign('product_groups',$product_groups);
}
