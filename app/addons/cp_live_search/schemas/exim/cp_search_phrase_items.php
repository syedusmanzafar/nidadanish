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

$schema = array(
    'section' => 'cp_ls_search_phrases',
    'pattern_id' => 'cp_search_phrase_items',
    'name' => __('cp_ls_search_phrase_items'),
    'key' => array('product_id', 'phrase_id'),
    'order' => 1,
    'table' => 'cp_search_phrase_products',
    'permissions' => array(
        'import' => 'manage_cp_live_search',
        'export' => 'view_cp_live_search',
    ),
    'references' => array(
        'cp_search_phrases' => array(
            'reference_fields' => array('phrase_id' => '&phrase_id'),
            'join_type' => 'LEFT',
            'alt_key' => array('phrase_id'),
            'import_skip_db_processing' => true
        ),
    ),
    'export_fields' => array(
        'Phrase ID' => array(
            'db_field' => 'phrase_id',
            'alt_key' => true,
            'required' => true
        ),
        'Product ID' => array(
            'db_field' => 'product_id',
            'alt_key' => true,
            'required' => true
        ),
        'Position' => array(
            'db_field' => 'position'
        )
    ),
);

return $schema;
