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

$company_id = Registry::get('runtime.company_id');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $suffix = !empty($_REQUEST['storefront_id']) ? '&storefront_id=' . $_REQUEST['storefront_id'] : '';
    
    if ($mode == 'styles_update') {
        if (!empty($_REQUEST['settings'])) {
            fn_cp_live_search_update_style_settings($_REQUEST['settings'], $_REQUEST);
        }
        return array(CONTROLLER_STATUS_OK, 'cp_live_search.styles_update' . $suffix);
    }

    if ($mode == 'motivation_update') {
        if (!empty($_REQUEST['settings'])) {
            $company_id = Registry::get('runtime.company_id');
            if (!fn_allowed_for('MULTIVENDOR') && empty($company_id)) {
                $company_id = fn_get_default_company_id();
            }
            $data = array(
                'object_type' => 'D',
                'object_id' => !empty($_REQUEST['storefront_id']) ? intval($_REQUEST['storefront_id']) : 0,
                'company_id' => $company_id,
                'lang_code' => DESCR_SL,
                'content' => !empty($_REQUEST['settings']['content']) ? $_REQUEST['settings']['content'] : ''
            );
            fn_cp_live_search_update_search_motivation($data);
        }
        return array(CONTROLLER_STATUS_OK, 'cp_live_search.motivation_update' . $suffix);
    }
    return array(CONTROLLER_STATUS_OK);
}

if ($mode == 'styles_update') {

    $default_styles = fn_cp_live_search_get_default_styles();
    $current_settings = fn_cp_live_search_get_style_settings(DESCR_SL, $_REQUEST);
    
    if (!empty($current_settings)) { // get settings descriptions from default styles
        foreach ($default_styles as $s_name => $style) {
            if (!empty($current_settings[$s_name])) {
                $style_settings[$s_name] = array_merge($default_styles[$s_name], $current_settings[$s_name]);
            } else {
                $style_settings[$s_name] = $default_styles[$s_name];
            }
        }
    } else {
        $style_settings = $default_styles;
    }
    Registry::get('view')->assign('styles', $style_settings);
    
    if (fn_allowed_for('MULTIVENDOR:ULTIMATE')) {
        $storefront_id = !empty($_REQUEST['storefront_id'])
            ? (int) $_REQUEST['storefront_id']
            : Tygh::$app['storefront']->storefront_id;
        Tygh::$app['view']->assign('selected_storefront_id', $storefront_id);
    }

} elseif ($mode == 'motivation_update') {
    $company_id = Registry::get('runtime.company_id');
    if (!fn_allowed_for('MULTIVENDOR') && empty($company_id)) {
        $company_id = fn_get_default_company_id();
    }
    
    $storefront_id = 0;
    if (fn_allowed_for('MULTIVENDOR:ULTIMATE')) {
        $storefront_id = !empty($_REQUEST['storefront_id'])
            ? (int) $_REQUEST['storefront_id']
            : Tygh::$app['storefront']->storefront_id;
        Tygh::$app['view']->assign('selected_storefront_id', $storefront_id);
    }
    
    $search_motivation = fn_cp_live_search_get_search_motivation('D', $storefront_id, $company_id, DESCR_SL);
    Registry::get('view')->assign('search_motivation', $search_motivation);
}
