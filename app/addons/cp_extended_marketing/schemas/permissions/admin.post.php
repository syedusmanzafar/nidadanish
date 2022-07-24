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

$schema['cp_em_audience'] = array(
    'modes' => array(
        'delete_files' => array(
            'permissions' => 'manage_cp_em_notices',
        ),
        'add' => array(
            'permissions' => 'manage_cp_em_notices',
        ),
    ),
    'permissions' => array('GET' => 'view_cp_em_notices', 'POST' => 'manage_cp_em_notices'),
);
$schema['cp_em_coupons'] = array(
    'permissions' => array('GET' => 'view_cp_em_notices', 'POST' => 'manage_cp_em_notices'),
);
$schema['cp_em_logs'] = array(
    'permissions' => array('GET' => 'view_cp_em_notices', 'POST' => 'manage_cp_em_notices'),
);

$schema['cp_em_placeholders'] = array(
    'modes' => array(
        'delete' => array(
            'permissions' => 'manage_cp_em_notices',
        ),
        'm_delete' => array(
            'permissions' => 'manage_cp_em_notices',
        ),
        'add' => array(
            'permissions' => 'manage_cp_em_notices',
        ),
    ),
    'permissions' => array('GET' => 'view_cp_em_notices', 'POST' => 'manage_cp_em_notices'),
);
$schema['cp_em_notices'] = array(
    'modes' => array(
        'delete' => array(
            'permissions' => 'manage_cp_em_notices',
        ),
        'add' => array(
            'permissions' => 'manage_cp_em_notices',
        ),
        'send_test' => array(
            'permissions' => 'manage_cp_em_notices',
        ),
    ),
    'permissions' => array('GET' => 'view_cp_em_notices', 'POST' => 'manage_cp_em_notices'),
);
$schema['cp_em_stats'] = array(
    'permissions' => array('GET' => 'view_cp_em_notices', 'POST' => 'manage_cp_em_notices'),
);


return $schema;
