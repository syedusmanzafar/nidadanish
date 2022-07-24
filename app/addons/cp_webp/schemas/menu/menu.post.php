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

$schema['central']['website']['items']['cp_webp_menu_txt'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'cp_webp.manage_logs',
    'position' => 1000,
    'subitems' => array(
        'cp_web_menu_webp_logs' => array(
            'href' => 'cp_webp.webp_logs',
            'position' => 201
        ),
        'cp_web_menu_cron_logs' => array(
            'href' => 'cp_webp.manage_logs',
            'position' => 203
        ),
        'cp_web_menu_ignore_list' => array(
            'href' => 'cp_webp.manage',
            'position' => 205
        ),
    )
);

return $schema;
