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

include_once(__DIR__ . '/cp_search_synonyms.functions.php');

$schema = array(
    'section' => 'cp_ls_search_synonyms',
    'name' => __('cp_ls_search_synonyms'),
    'pattern_id' => 'cp_search_synonyms',
    'key' => array('synonym_id'),
    'order' => 0,
    'table' => 'cp_search_synonyms',
    'permissions' => array(
        'import' => 'manage_cp_live_search',
        'export' => 'view_cp_live_search',
    ),
    'condition' => array(
        'use_company_condition' => true,
    ),
    'options' => array(
        'lang_code' => array(
            'title' => 'language',
            'type' => 'languages',
            'default_value' => array(DEFAULT_LANGUAGE),
        ),
        'synonyms_delimiter' => array(
            'title' => 'cp_synonyms_delimiter',
            'description' => 'cp_synonyms_delimiter_text',
            'type' => 'input',
            'default_value' => '///'
        ),
    ),
    'export_fields' => array(
        'Synonym ID' => array(
            'db_field' => 'synonym_id',
            'alt_key' => true,
            'required' => true,
        ),
        'Search phrase' => array(
            'db_field' => 'value',
            'required' => true,
        ),
        'Synonyms' => array(
            'process_get' => array('fn_exim_get_search_synonyms_variants', '#key', '@synonyms_delimiter'),
            'process_put' => array('fn_exim_set_search_synonyms_variants', '#key', '#this', '@synonyms_delimiter'),
            'required' => true,
            'linked'      => false
        ),
        'Status' => array(
            'db_field' => 'status',
        ),
        'Language' => array(
            'db_field' => 'lang_code',
            'required' => true
        ),
    ),
);

if (fn_allowed_for('ULTIMATE')) {
    $schema['export_fields']['Store'] = array(
        'db_field' => 'company_id',
        'process_get' => array('fn_get_company_name', '#this'),
        'convert_put' => array('fn_get_company_id_by_name', '#this'),
        'required' => true
    );

}
return $schema;
