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

$schema['controllers']['cp_em_audience'] = array(
    'permissions' => false,
);
$schema['controllers']['cp_em_coupons'] = array(
    'permissions' => false,
);
$schema['controllers']['cp_em_logs'] = array(
    'permissions' => false,
);
$schema['cp_em_placeholders']['cp_em_logs'] = array(
    'permissions' => false,
);
$schema['controllers']['cp_em_notices'] = array(
    'permissions' => false,
);
$schema['controllers']['cp_em_stats'] = array(
    'permissions' => false,
);
return $schema;