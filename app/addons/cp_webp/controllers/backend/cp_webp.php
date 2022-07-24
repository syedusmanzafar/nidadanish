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

if(!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $suffix = '.manage';
    if ($mode == 'clear_logs') {
        fn_cp_webp_clear_cron_logs();
        $suffix = '.manage_logs';
    }
    if ($mode == 'clear_webp_logs') {
        fn_cp_webp_clear_webp_logs();
        $suffix = '.webp_logs';
    }
    if ($mode == 'add_ignore') {
        if (!empty($_REQUEST['image_path'])) {
            $trim_path = trim($_REQUEST['image_path']);
            if (!empty($trim_path)) {
                fn_cp_webp_update_ignore_img($trim_path);
            }
        }
    }
    if ($mode == 'delete_image') {
        if (!empty($_REQUEST['image_id'])) {
            fn_cp_webp_delete_ignore_img($_REQUEST['image_id']);
        }
    }
    if ($mode == 'm_delete') {
        if (!empty($_REQUEST['image_ids'])) {
            fn_cp_webp_delete_ignore_img($_REQUEST['image_ids']);
        }
    }
    if ($mode == 'delete_webp_img') {
        fn_cp_web_delete_webp_images();
        fn_set_notification('N', __('notice'), __('cp_web_webp_images_deleted'));
        exit;
    }
    if ($mode == 'add_to_ignor') {
        if (!empty($_REQUEST['image_crc'])) {
            fn_cp_web_convert_to_ignore($_REQUEST['image_crc']);
        }
        $suffix = '.webp_logs';
    }
    return array(CONTROLLER_STATUS_REDIRECT, 'cp_webp' . $suffix);
}

if ($mode == 'crop_cron') {
    if (Registry::get('addons.cp_webp.use_cron') == 'Y' && !empty($_REQUEST['cron_pass']) && $_REQUEST['cron_pass'] == Registry::get('addons.cp_webp.cron_pass')) {
        fn_cp_webp_cron_convert();
    }
    exit;
} elseif ($mode == 'check_key') {
    $api_key = Registry::get('addons.cp_webp.pixel_key');
    if (!empty($api_key)) {
        fn_cp_webp_check_key_status($api_key);
    } else {
        fn_set_notification('E', __('error'), __('cp_webp_no_key_vaue'));
    }
    exit;
} elseif ($mode == 'manage_logs') {

    $params = $_REQUEST;
    list($logs, $search) = fn_cp_webp_get_logs($params, Registry::get('settings.Appearance.admin_elements_per_page'));
    
    Tygh::$app['view']->assign('logs', $logs);
    Tygh::$app['view']->assign('search', $search);

} elseif ($mode == 'webp_logs') {

    $params = $_REQUEST;
    list($logs, $search) = fn_cp_webp_get_webp_logs($params, Registry::get('settings.Appearance.admin_elements_per_page'));
    
    Tygh::$app['view']->assign('webp_logs', $logs);
    Tygh::$app['view']->assign('search', $search);

} elseif ($mode == 'manage') {

    $params = $_REQUEST;
    list($images, $search) = fn_cp_webp_get_ignore_list($params, Registry::get('settings.Appearance.admin_elements_per_page'));
    
    Tygh::$app['view']->assign('images', $images);
    Tygh::$app['view']->assign('search', $search);
}
