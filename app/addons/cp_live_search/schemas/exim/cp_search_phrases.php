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

include_once(__DIR__ . '/cp_search_phrases.functions.php');

$schema = array(
    'section' => 'cp_ls_search_phrases',
    'pattern_id' => 'cp_search_phrases',
    'name' => __('cp_ls_search_phrases'),
    'key' => array('phrase_id'),
    'order' => 0,
    'table' => 'cp_search_phrases',
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
        'phrases_delimiter' => array(
            'title' => 'cp_phrases_delimiter',
            'description' => 'cp_phrases_delimiter_text',
            'type' => 'input',
            'default_value' => '///'
        ),
        'suggestions_delimiter' => array(
            'title' => 'cp_suggestions_delimiter',
            'description' => 'cp_suggestions_delimiter_text',
            'type' => 'input',
            'default_value' => '///'
        )
    ),
    'export_fields' => array(
        'Phrase ID' => array(
            'db_field' => 'phrase_id',
            'alt_key' => true,
            'required' => true,
        ),
        'Phrases' => array(
            'process_get' => array('fn_exim_get_search_phrase_variants', '#key', '@phrases_delimiter'),
            'process_put' => array('fn_exim_set_search_phrase_variants', '#key', '#this', '@phrases_delimiter'),
            'required' => true,
            'linked' => false
        ),
        'Suggestions' => array(
            'db_field' => 'suggestions',
            'process_get' => array('fn_exim_get_search_phrase_suggestions', '#this', '@suggestions_delimiter'),
            'convert_put' => array('fn_exim_set_search_phrase_suggestions', '#this', '@suggestions_delimiter')
        ),
        'Priority' => array(
            'db_field' => 'priority',
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
