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

use Tygh\Storage;

if(!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode === 'update') {
        if (!empty($_REQUEST['addon']) && $_REQUEST['addon'] == 'cp_webp') {
            $object_ids = array_keys($_REQUEST['addon_data']['options']);
            $old_hash = db_get_row("SELECT value, object_id FROM ?:settings_objects WHERE object_id IN (?n) AND name = ?s", $object_ids, 'use_hash');
            if (!empty($old_hash)) {
                $new_value = $_REQUEST['addon_data']['options'][$old_hash['object_id']];
                if (!empty($new_value) && $new_value != $old_hash['value']) {
                    fn_cp_webp_clear_tables();
                    if ($new_value == 'N') {
                        Storage::instance('images')->deleteByPattern('thumbnails/*/*/*/*/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9].webp');
                        Storage::instance('images')->deleteByPattern('*/*/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9].webp');
                        
                        Storage::instance('images')->deleteByPattern('thumbnails/*/*/*/*/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9].webp');
                        Storage::instance('images')->deleteByPattern('*/*/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9].webp');
                    }
                }
            }
        }
    }
}

$cpv1 = ___cp('dGh1bWJuYWlsc19yZW1vdmVk');
$cpv2 = ___cp('dGh1bWJuYWlscw');

if ($mode == 'uninstall') {
    if($_REQUEST['addon'] == 'cp_webp') {
        if (!empty($cpv2)){
            Storage::instance('images')->deleteDir($cpv2); 
        }
        call_user_func(___cp('Zm5fc2V0P25vdGlmaWXhdGlvbg'), 'N', __('notice'), __($cpv1));
    }
}