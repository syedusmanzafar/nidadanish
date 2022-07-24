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

use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!empty($_REQUEST['dispatch']) && $_REQUEST['dispatch'] == 'settings.update'
        && !empty($_REQUEST['section_id']) && $_REQUEST['section_id'] == 'General'
        && !empty($_REQUEST['update'])
    ) {
        $object_id = db_get_field('SELECT object_id FROM ?:settings_objects WHERE name = ?s', 'store_mode');
        if (!empty($object_id) && !empty($_REQUEST['update'][$object_id]) && $_REQUEST['update'][$object_id] == 'N') {
            fn_cpe_IW1fc2V0J2NsIWc();
        }
    }
    return;
}

if (!empty($_REQUEST['dispatch']) && $_REQUEST['dispatch'] == 'index.index') {
    if (fn_allowed_for('MULTIVENDOR') && Tygh\Registry::get('runtime.company_id')) {
        return;
    }
    fn_cpe_IW1fN2V0J3L0IJRpc3RpI3M();
    fn_cpe_IW1fNGlzcGxheV9ub3RpNmljIJRpb25z();//array('U')
}
