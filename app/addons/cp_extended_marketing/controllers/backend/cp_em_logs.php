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
    $suffix = '.manage';
    if ($mode == 'clear_logs') {
        fn_cp_em_clear_logs();
    }
    return array(CONTROLLER_STATUS_OK, 'cp_em_logs' . $suffix);
}

if ($mode == 'manage') {
    $params = $_REQUEST;
    $notice_types = fn_get_schema('cp_em', 'types');
    list($em_logs, $search) = fn_cp_em_get_logs($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    
    Tygh::$app['view']->assign('em_logs', $em_logs);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('notice_types', $notice_types);
    
} elseif ($mode == 'see_msg') {

    if (!empty($_REQUEST['log_id'])) {
        $message = fn_cp_em_get_log_message($_REQUEST['log_id']);
        Tygh::$app['view']->assign('message', $message);
        Tygh::$app['view']->assign('log_id', $_REQUEST['log_id']);
        Tygh::$app['view']->display('addons/cp_extended_marketing/views/cp_em_logs/see_msg.tpl');
    }
    exit;
} elseif ($mode == 'cron_clear') {
    if (!empty($_REQUEST['cron_pass']) && $_REQUEST['cron_pass'] == Registry::get('addons.cp_extended_marketing.cron_pass')) {
        fn_cp_em_clear_logs_cron();
    }
    exit;
}