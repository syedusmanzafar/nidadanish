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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'activate_link') {
    if (!empty($_REQUEST['hash'])) {
        $user_id = fn_cp_ac_activate_account($_REQUEST['hash']);
        if (!empty($user_id)) {
            $url = fn_link_attach('profiles.update', 'user_id=' . $user_id);
            if (empty($_REQUEST['is_reg'])) {
                fn_set_notification('W', __('important'), __('change_password_notification'));
            }
        } else {
            $url = fn_link_attach('profiles.add');
        }
        return array(CONTROLLER_STATUS_REDIRECT, fn_url($url));
    } else {
        return array(CONTROLLER_STATUS_DENIED);
    }
    
}