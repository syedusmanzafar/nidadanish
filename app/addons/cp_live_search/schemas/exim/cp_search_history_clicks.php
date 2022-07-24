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

include_once(__DIR__ . '/cp_search_history.functions.php');

$schema = array(
    'section' => 'cp_ls_search_history',
    'pattern_id' => 'cp_search_history_clicks',
    'name' => __('cp_ls_search_history_clicks'),
    'key' => array('search_id', 'product_id'),
    'order' => 1,
    'table' => 'cp_search_history_clicks',
    'export_only' => true,
    'permissions' => array(
        'import' => 'manage_cp_live_search',
        'export' => 'view_cp_live_search',
    ),
    'export_fields' => array(
        'Search ID' => array(
            'db_field' => 'search_id',
            'alt_key' => true,
            'required' => true
        ),
        'Search phrase' => array(
            'db_field' => 'search_id',
            'process_get' => array('fn_exim_get_search_item_phrase', '#this')
        ),
        'Product ID' => array(
            'db_field' => 'product_id',
            'alt_key' => true,
            'required' => true
        ),
        'Product code' => array(
            'db_field' => 'product_id',
            'process_get' => array('fn_get_product_code', '#this')
        )
    )
);

return $schema;
