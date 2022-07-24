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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (!empty($_REQUEST['dispatch']) && !empty($_REQUEST['search_id'])) {
    
    $dispatch = $_REQUEST['dispatch'];

    if ($dispatch == 'products.view' || $dispatch == 'products.quick_view' || $dispatch == 'product_features.add_product') {
        if (!empty($_REQUEST['product_id'])) {
            fn_cp_add_search_history_click($_REQUEST['search_id'], $_REQUEST['product_id']);

            if (!defined('AJAX_REQUEST') && $dispatch == 'products.view') {
                fn_redirect(fn_url('products.view?product_id=' . $_REQUEST['product_id']), false, false);
            }
        }
    } elseif (strpos($dispatch, 'checkout.add') !== false || strpos($dispatch, 'wishlist.add') !== false) {
        if (!empty($_REQUEST['product_data'])) {
            $product_data = $_REQUEST['product_data'];
            $product = reset($product_data);
            if (!empty($product['product_id'])) {
                fn_cp_add_search_history_click($_REQUEST['search_id'], $product['product_id']);
            }
        }
    }
}