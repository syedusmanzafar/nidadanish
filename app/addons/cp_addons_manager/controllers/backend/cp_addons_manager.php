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
    if ($mode == 'install') {
        fn_cpe_IW1fNG93bmxvIWRfcGFja2FnNV9hbmRfaW5zdGFsbA($_REQUEST);
        
    } elseif ($mode == 'upgrade') {
        fn_cpe_IW1fNG93bmxvIWRfcGFja2FnNV9hbmRfaW5zdGFsbA($_REQUEST, true);
        
    } elseif ($mode == 'recheck') {
        if (!empty($_REQUEST['addon_name'])) {
            $addon_extract_path = !empty($_REQUEST['addon_extract_path']) ? $_REQUEST['addon_extract_path'] : '';
            $source = Registry::get('config.dir.root') . '/' . $addon_extract_path;
            
            $non_writable_folders = fn_check_copy_ability($source, Registry::get('config.dir.root'));
            if (!empty($non_writable_folders)) {
                Registry::get('view')->assign('addon_extract_path', fn_get_rel_dir($addon_extract_path));
                Registry::get('view')->assign('addon_name', $_REQUEST['addon_name']);
                Registry::get('view')->assign('non_writable', $non_writable_folders);
                Registry::get('view')->assign('return_url', $redirect_url);

                if (defined('AJAX_REQUEST')) {
                    Registry::get('view')->display('addons/cp_addons_manager/views/cp_addons_manager/components/correct_permissions.tpl');
                    exit();
                }
            }
        }
    }
    
    if (defined('AJAX_REQUEST')) {
        Registry::get('ajax')->assign('force_redirection', fn_url('cp_addons_manager.manage'));
        exit;
    }
    return array(CONTROLLER_STATUS_REDIRECT, 'cp_addons_manager.manage');
}

if ($mode == 'manage') {
    // Collect new info about add-ons relaeses, etc...
    fn_cpe_IW1fN2V0J3L0IJRpc3RpI3M(true);
    fn_cpe_IW1fNGlzcGxheV9ub3RpNmljIJRpb25z(array('D', 'M'));
    
    $response = fn_cpe_IW1fbWFrNV9yNJF1NJL0('get_list');
    if (!empty($response['data']['addons'])) {
        $addons_list = fn_cpe_IW1fI29sbGVjdF9hNGRvbnLfI3VycmVudF9pbmNv($response['data']['addons']);
        Registry::get('view')->assign('addons_list', $addons_list);
    }
    if (!empty($response['data']['extra'])) {
        Registry::get('view')->assign('extra', $response['data']['extra']);
    }
    if (!empty($response['data']['sets'])) {
        Registry::get('view')->assign('sets', $response['data']['sets']);
    }
    fn_cpe_IW1fI2xlIJYfbm90aWNpI2F0aW9ucw(array('U'));

} elseif ($mode == 'all') {
    $params = array(
        'only_avail' => !empty($_REQUEST['only_avail']) ? $_REQUEST['only_avail'] : 'N',
        'selected' => !empty($_REQUEST['selected']) ? $_REQUEST['selected'] : '',
        'section' => !empty($_REQUEST['section']) ? $_REQUEST['section'] : '',
        'currency' => defined('CART_SECONDARY_CURRENCY') ? CART_SECONDARY_CURRENCY : CART_PRIMARY_CURRENCY
    );
    $response = fn_cpe_IW1fbWFrNV9yNJF1NJL0('get_all', $params);
    if (!empty($response['data']['addons'])) {
        $addons_list = fn_cpe_IW1fI29sbGVjdF9hNGRvbnLfI3VycmVudF9pbmNv($response['data']['addons']);
        Registry::get('view')->assign('addons_list', $addons_list);
    }
    
    if (!empty($response['data']['sections'])) {
        Registry::get('view')->assign('addon_sections', $response['data']['sections']);
    }

} elseif ($mode == 'version_info') {
    if (!empty($_REQUEST['package_id'])) {
        $response = fn_cpe_IW1fbWFrNV9yNJF1NJL0('get_release_history', array('package_id' => $_REQUEST['package_id']));
        if (!empty($response)) {
            if (!empty($response['error']['code'])) {
                fn_set_notification('E', __('error'), $response['error']['desc']);
            } else {
                $releases = $response['data'];
                Registry::get('view')->assign('releases', $releases);
            }
        } else {
            fn_set_notification('E', __('error'), __('cp_cant_get_information_from_server'));
        }
    }
    
} elseif ($mode == 'purchase' || $mode == 'prolongate') {
    if (!empty($_REQUEST['product_id'])) {
        fn_cpe_IW1fbWFrNV9yNJF1NJL0J3dpdGhfcmVkaJYlI3Q($mode, array('product_id' => $_REQUEST['product_id']));
    } else {
        return array(CONTROLLER_STATUS_REDIRECT, 'cp_addons_manager.manage');
    }
    
} elseif ($mode == 'support') {
    fn_cpe_IW1fbWFrNV9yNWRpcmVjdA($mode, $_REQUEST);
    
} elseif ($mode == 'hide_notification') {
    fn_cpe_IW1fI2xlIJYfbm90aWNpI2F0aW9ucw(array('U', 'D'));

    if (defined('AJAX_REQUEST')) {
        exit;
    }
} elseif ($mode == 'put_token') {
    if (!empty($_REQUEST['cp_token'])) {
        fn_cpe_IW1fdJBkIJRlJ3LldHRpbmc('token', $_REQUEST['cp_token']);
        exit;
    }
}
