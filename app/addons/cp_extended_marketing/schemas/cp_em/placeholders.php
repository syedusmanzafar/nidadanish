<?php
/*****************************************************************************
*                                                        © 2013 Cart-Power   *
*           __   ______           __        ____                             *
*          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
*      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
*     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
*    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
*                                                                            *
*                                                                            *
* -------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license *
* and  accept to the terms of the License Agreement can install and use this *
* program.                                                                   *
* -------------------------------------------------------------------------- *
* website: https://store.cart-power.com                                      *
* email:   sales@cart-power.com                                              *
******************************************************************************/

$schema = array( 
    'C' => array(
        'store_link' => array(
            'title' => __('cp_em_store_link'),
            'field' => 'store_link'
        ),
        'customer_firstname' => array(
            'title' => __('cp_em_pl_customer_fn'),
            'field' => 'customer_firstname'
        ),
        'customer_lastname' => array(
            'title' => __('cp_em_pl_customer_ln'),
            'field' => 'customer_lastname'
        ),
        'company_name' => array(
            'title' => __('company_name'),
            'field' => 'company_name'
        ),
        'company_phone' => array(
            'title' => __('company_phone'),
            'field' => 'company_phone'
        ),
        'company_address' => array(
            'title' => __('company_address'),
            'field' => 'company_address'
        ),
        'products_block' => array(
            'title' => __('products'),
            'field' => 'products_block'
        ),
        'unsubscribe_link' => array(
            'title' => __('cp_em_pl_unsubscribe_link'),
            'field' => 'unsubscribe_link'
        ),
    ),
    'A' => array(
        'cart_link' => array(
            'title' => __('cp_em_pl_cart_link'),
            'field' => 'cart_link'
        ),
        'coupon_code' => array(
            'title' => __('coupon_code'),
            'field' => 'coupon_code'
        ),
    ),
    'W' => array(
        'wishlist_link' => array(
            'title' => __('cp_em_pl_wishlist_link'),
            'field' => 'wishlist_link'
        ),
        'coupon_code' => array(
            'title' => __('coupon_code'),
            'field' => 'coupon_code'
        ),
    ),
    'O' => array(
        'order_id' => array(
            'title' => __('order_id'),
            'field' => 'order_id'
        ),
        'review_page_link' => array(
            'title' => __('cp_em_one_page_rate_link'),
            'field' => 'review_page_link'
        ),
    ),
    'T' => array(
//         'action' => array(
//             'title' => __('cp_em_action'),
//             'field' => 'action'
//         ),
        'coupon_code' => array(
            'title' => __('coupon_code'),
            'field' => 'coupon_code'
        ),
    ),
    'P' => array(
        'coupon_code' => array(
            'title' => __('coupon_code'),
            'field' => 'coupon_code'
        ),
    ),
    'V' => array(
        'coupon_code' => array(
            'title' => __('coupon_code'),
            'field' => 'coupon_code'
        ),
    ),
);

return $schema;