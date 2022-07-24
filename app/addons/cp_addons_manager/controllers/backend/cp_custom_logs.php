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
use Tygh\Addons\CpLogs;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'clear') {
        $suffix = '';
        if (!empty($_REQUEST['section'])) {
            $cpl = new CpLogs($_REQUEST['section']);
            $cpl->clear();
            fn_set_notification('N', __('notice'), __('successful'));
            $suffix = '?section=' . $_REQUEST['section'];
        }
    }
    return array(CONTROLLER_STATUS_REDIRECT, 'cp_custom_logs.manage' . $suffix);
}

if ($mode == 'manage') {
    $params = $_REQUEST;
    $log_sections = CpLogs::getSections();
    if (empty($log_sections)) {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
    uasort($log_sections, function ($a, $b) {
        $a_pos = !empty($a['position']) ? $a['position'] : 10000;
        $b_pos = !empty($b['position']) ? $b['position'] : 10000;
        return $a_pos > $b_pos;
    });

    $active_section = !empty($params['section']) ? $params['section'] : '';
    $section_settings = array();
    if (!empty($log_sections[$active_section])) {
        $section_settings = $log_sections[$active_section];
    } else {
        $section_settings = reset($log_sections);
        $active_section = key($log_sections);
    }
    $params['section'] = $active_section;
    Registry::get('view')->assign('section_settings', $section_settings);
    Registry::get('view')->assign('active_section', $active_section);

    $dyn_sections = array();
    foreach ($log_sections as $section_key => $section) {
        $dyn_sections[$section_key] = array (
            'title' => $section['title'],
            'href' => 'cp_custom_logs.manage?section=' . $section_key
        );
    }
    Registry::set('navigation.dynamic.sections', $dyn_sections);
    Registry::set('navigation.dynamic.active_section', $active_section);
    
    $cpl = new CpLogs($active_section);
    list($logs, $search) = $cpl->get($params, Registry::get('settings.Appearance.admin_elements_per_page'));

    Registry::get('view')->assign('logs', $logs);
    Registry::get('view')->assign('search', $search);
}