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
    return array(CONTROLLER_STATUS_OK, 'cp_em_coupons' . $suffix);
}

if ($mode == 'manage') {
    $params = $_REQUEST;
    $notice_types = fn_get_schema('cp_em', 'types');
    
    list($coupons, $search) = fn_cp_em_get_coupons_list($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    
    Tygh::$app['view']->assign('coupons', $coupons);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('notice_types', $notice_types);
    
}