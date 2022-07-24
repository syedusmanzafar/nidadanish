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

if ($mode == 'update') {
    if (!empty($_REQUEST['user_id']) && $_REQUEST['user_type'] == 'C') {
        $notice_types = fn_cp_em_get_user_subscribtion_types();
        if (!empty($notice_types)) {
            Registry::set('navigation.tabs.cp_em_notices', array (
                'title' => __('cp_em_manage_email_notices'),
                'js' => true
            ));
            $user_types = fn_cp_em_get_user_subscribtion($_REQUEST['user_id']);
            
            Tygh::$app['view']->assign('user_types', $user_types);
            Tygh::$app['view']->assign('notice_types', $notice_types);
        }
    }
}