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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update_em_subscr') {
        if (!empty($auth['user_id']) && isset($_REQUEST['types'])) {
            fn_cp_em_update_user_subscribtion($auth['user_id'], $_REQUEST['types']);
        }
        return array(CONTROLLER_STATUS_OK, 'profiles.update');
    }
}
if ($mode == 'update') {
    if (!empty($auth['user_id']) && !empty($_REQUEST['cp_show_notice_manage'])) {
        $notice_types = fn_cp_em_get_user_subscribtion_types();
        if (!empty($notice_types)) {
            Registry::set('navigation.tabs.cp_em_notices', array (
                'title' => __('cp_em_manage_email_notices'),
                'js' => true
            ));
            $user_types = fn_cp_em_get_user_subscribtion($auth['user_id']);
            
            Tygh::$app['view']->assign('user_types', $user_types);
            Tygh::$app['view']->assign('notice_types', $notice_types);
        }
    }
}