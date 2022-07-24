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

$menu = array(
    'href' => 'cp_addons_manager.manage',
    'position' => 21,
    'subitems' => array(
        'cp_my_addons' => array(
            'href' => 'cp_addons_manager.manage',
            'position' => 100
        ),
        'cp_all_addons' => array(
            'href' => 'cp_addons_manager.all',
            'position' => 200
        )
    )
);

if (Tygh\Addons\CpLogs::allowShow()) {
    $menu['subitems']['cp_custom_logs'] = array(
        'href' => 'cp_custom_logs.manage',
        'position' => 300
    );
}

$schema['top']['addons']['items']['cart_power_addons'] = $menu;

return $schema;
