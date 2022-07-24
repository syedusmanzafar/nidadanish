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
    'name' => __('cp_ls_search_history'),
    'pattern_id' => 'cp_search_histories',
    'key' => array('search_id'),
    'order' => 0,
    'table' => 'cp_search_history',
    'export_only' => true,
    'permissions' => array(
        'import' => 'manage_cp_live_search',
        'export' => 'view_cp_live_search',
    ),
    'condition' => array(
        'use_company_condition' => true,
    ),
    'export_fields' => array(
        'Search ID' => array(
            'db_field' => 'search_id',
            'alt_key' => true,
            'required' => true,
        ),
        'Search phrase' => array(
            'db_field' => 'search',
            'required' => true,
        ),
        'Date' => array(
            'db_field' => 'timestamp',
            'process_get' => array('fn_timestamp_to_date', '#this'),
            'required' => true,
        ),
        'Search type' => array(
            'db_field' => 'search_type',
            'process_get' => array('fn_exim_get_search_type', '#this')
        ),
        'Number of found' => array(
            'db_field' => 'result',
        ),
        'Language' => array(
            'db_field' => 'lang_code',
            'required' => true
        )
    ),
);

if (fn_allowed_for('ULTIMATE')) {
    $schema['export_fields']['Store'] = array(
        'db_field' => 'company_id',
        'process_get' => array('fn_get_company_name', '#this'),
        'required' => true
    );

}
return $schema;
