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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'manage') {
    $upgrade_packages = Tygh::$app['view']->getTemplateVars('upgrade_packages');
    $cst = fn_cp_am_get_schema_setting('custom_addons');
    if (!empty($cst) && !empty($upgrade_packages['addon'])) {
        foreach ($cst as $a_key) {
            if (array_key_exists($a_key, $upgrade_packages['addon'])) {
                unset($upgrade_packages['addon'][$a_key]);
            }
        }
        Tygh::$app['view']->assign('upgrade_packages', $upgrade_packages);
    }
}
