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
    // Fix search by supplier for CS-Cart > 4.13
    if (version_compare(PRODUCT_VERSION, '4.13', '>')) {
        $check_names = array('Cart-Power', 'Cart Power', 'CartPower');
        if (!empty($_REQUEST['supplier'])) {
            if (is_array($_REQUEST['supplier'])) {
                $exists = array_intersect($check_names, $_REQUEST['supplier']);
                if (!empty($exists)) {
                    $_REQUEST['supplier'] = array_unique(array_merge($_REQUEST['supplier'], $check_names));
                }
            } elseif (in_array($_REQUEST['supplier'], $check_names)) {
                $_REQUEST['supplier'] = $check_names;
            }
        }
    }
}
