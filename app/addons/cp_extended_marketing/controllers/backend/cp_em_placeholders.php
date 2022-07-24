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
    
    fn_trusted_vars('placeholder_data');

    if ($mode == 'update') {
       
        $placeholder_id = fn_cp_em_update_placeholder_data($_REQUEST['placeholder_data'], $_REQUEST['placeholder_id'], DESCR_SL);
        
        return array (CONTROLLER_STATUS_OK, "cp_em_placeholders.update&placeholder_id=" . $placeholder_id);
    }
    
    if ($mode == 'm_delete') {
        if (!empty($_REQUEST['placeholder_ids'])) {
            foreach ($_REQUEST['placeholder_ids'] as $placeholder_id) {
                fn_cp_em_delete_placeholder($placeholder_id);
            }
        }
        return array (CONTROLLER_STATUS_OK, "cp_em_placeholders.manage");
    }
}    
  
if ($mode == 'update') {
    if (!empty($_REQUEST['placeholder_id'])) {
        $placeholder_data = fn_cp_em_get_placeholder_data($_REQUEST['placeholder_id'], DESCR_SL);
        if (!empty($placeholder_data)) {
            Registry::get('view')->assign('placeholder_data', $placeholder_data);
        } else {
            return array(CONTROLLER_STATUS_NO_PAGE);
        }
    }
} elseif ($mode == 'manage') {
    
    list($placeholders, $search) = fn_cp_em_get_product_placeholders($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    
    Registry::get('view')->assign('placeholders', $placeholders);
    Registry::get('view')->assign('search', $search);

} elseif ($mode == 'delete') {
    if (!empty($_REQUEST['placeholder_id'])) {
        fn_cp_em_delete_placeholder($_REQUEST['placeholder_id']);
        fn_set_notification('N', __('notice'), __('cp_em_placeholder_deleted'));
    }
    return array(CONTROLLER_STATUS_REDIRECT, fn_url('cp_em_placeholders.manage'));
    
} 
if (version_compare(PRODUCT_VERSION, '4.11.5', '>')) {
    Tygh::$app['view']->assign('need_tooltip_tpl', true);
}
