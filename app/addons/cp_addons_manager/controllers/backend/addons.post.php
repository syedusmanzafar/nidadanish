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
    return;
}

if ($mode == 'manage') {
    if (!version_compare(PRODUCT_VERSION, '4.12', '>')) {
        return;
    }

    $change_name = 'Cart-Power';
    $check_names = array('Cart Power', 'CartPower');

    // Fix search by supplier for CS-Cart < 4.13
    if (!version_compare(PRODUCT_VERSION, '4.13', '>')) {
        $addons = Registry::get('view')->getTemplateVars('addons_list');
        $params = $_REQUEST;
        
        if (!empty($params['supplier']) && $params['supplier'] == $change_name) {
            unset($params['supplier']);

            $params['for_company'] = (bool) Registry::get('runtime.company_id');
            list($addons) = fn_get_addons($params);
            if (!empty($addons)) {
                $ext_check_names = array_merge($check_names, array($change_name));
                $addons = array_filter($addons, function ($addon) use ($ext_check_names) {
                    return !empty($addon['supplier']) && in_array($addon['supplier'], $ext_check_names);
                });
            }
        }
    }
    
    if (!empty($addons)) {
        foreach ($addons as &$addon) {
            if (in_array($addon['supplier'], $check_names)) {
                $addon['supplier'] = $change_name;
            }
        }
        Registry::get('view')->assign('addons_list', $addons);
    }

    $developers = Registry::get('view')->getTemplateVars('developers');
    if (!empty($developers)) {
        $f_developers = array_filter($developers, function ($name) use ($check_names) {
            $title = !empty($name['title']) ? $name['title'] : $name;
            return !in_array($title, $check_names);
        });
        if (version_compare(PRODUCT_VERSION, '4.13', '>')) {
            $filtered_count = count($developers) - count($f_developers);
            if ($filtered_count > 0) {
                foreach ($f_developers as &$developer) {
                    if (is_array($developer) && isset($developer['position'])
                        && !empty($developer['title']) && $developer['title'] == 'Cart-Power'
                    ) {
                        $developer['position'] += $filtered_count;
                    }
                }
            }
        }
        Registry::get('view')->assign('developers', $f_developers);
    }

    $suppliers = Registry::get('view')->getTemplateVars('suppliers');
    if (!empty($suppliers)) {
        $suppliers = array_filter($suppliers, function ($name) use ($check_names) {
            return !in_array($name, $check_names);
        }, ARRAY_FILTER_USE_KEY);
        Registry::get('view')->assign('suppliers', $suppliers);
    }

    $all_suppliers = Registry::get('view')->getTemplateVars('all_suppliers');
    if (!empty($all_suppliers)) {
        $all_suppliers = array_filter($all_suppliers, function ($name) use ($check_names) {
            return !in_array($name, $check_names);
        }, ARRAY_FILTER_USE_KEY);
        Registry::get('view')->assign('all_suppliers', $all_suppliers);
    }
}
