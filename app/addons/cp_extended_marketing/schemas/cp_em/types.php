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
    'A' => array(
        'title' => __('cp_em_type_a'),
        'profile_title' => __('cp_em_type_profile_a'),
        'manage_title' => __('cp_em_menu_aband'),
        'def_period' => 2,
        'type' => 'A',
        'subject' => __('cp_em_default_subject_a'),
        'message' => __('cp_em_default_message_a')
    ),
    'W' => array(
        'title' => __('cp_em_type_w'),
        'profile_title' => __('cp_em_type_profile_w'),
        'manage_title' => __('cp_em_menu_wislist'),
        'def_period' => 2,
        'type' => 'W',
        'subject' => __('cp_em_default_subject_w'),
        'message' => __('cp_em_default_message_w')
    ),
    'O' => array(
        'title' => __('cp_em_type_o'),
        'profile_title' => __('cp_em_type_profile_o'),
        'manage_title' => __('cp_em_menu_feedb'),
        'def_period' => 10,
        'type' => 'O',
        'subject' => __('cp_em_default_subject_o'),
        'message' => __('cp_em_default_message_o')
    ),
    'T' => array(
        'title' => __('cp_em_type_t'),
        'profile_title' => __('cp_em_type_profile_t'),
        'manage_title' => __('cp_em_menu_target'),
        'def_period' => 1,
        'type' => 'T',
        'subject' => __('cp_em_default_subject_t'),
        'message' => __('cp_em_default_message_t'),
        'actions' => array(
            'B' => __('cp_em_birthday'),
            'R' => __('cp_em_user_registration'),
            'S' => __('cp_em_subscription_txt'),
            'F' => __('cp_em_first_purchase'),
            'A' => __('cp_em_not_active_user'),
        )
    ),
    'P' => array(
        'title' => __('cp_em_type_p'),
        'profile_title' => __('cp_em_type_profile_p'),
        'manage_title' => __('cp_em_menu_audience'),
        'def_period' => 10,
        'type' => 'P',
        'subject' => __('cp_em_default_subject_p'),
        'message' => __('cp_em_default_message_p')
    ),
    'V' => array(
        'title' => __('cp_em_type_v'),
        'profile_title' => __('cp_em_type_profile_v'),
        'manage_title' => __('cp_em_menu_viewed'),
        'def_period' => 10,
        'type' => 'V',
        'subject' => __('cp_em_default_subject_v'),
        'message' => __('cp_em_default_message_v')
    ),
);

if (Registry::get('addons.form_builder.status') == 'A') {
    $schema['T']['actions']['K'] = __('cp_em_fill_the_form');
}

return $schema;