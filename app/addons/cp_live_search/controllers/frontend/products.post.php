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

if ($mode == 'search') {
    // Prevented execution of default controller
    if (!empty($_REQUEST['ls_features_hash'])) {
        $_REQUEST['features_hash'] = $_REQUEST['ls_features_hash'];
    }
    if (!empty($_REQUEST['ls_search_performed'])) {
        $_REQUEST['search_performed'] = $_REQUEST['ls_search_performed'];
    }
    
    // Search history block
    $search = Registry::get('view')->getTemplateVars('search');
    if (isset($search['q']) && fn_string_not_empty($search['q'])) {
        $q = trim($search['q']);
        $bc = Registry::get('view')->getTemplateVars('breadcrumbs');
        if (!empty($bc)) {
            $last_bc = array_pop($bc);
            $last_bc['title'] = __('cp_ls_search_results_for', array('[q]' => $q));
            $bc[] = $last_bc;
            Registry::get('view')->assign('breadcrumbs', $bc);
            Registry::get('view')->assign('cp_search_result_title', $last_bc['title']);
        }

        $total_items = !empty($search['total_items']) ? $search['total_items'] : 0;
        
        $search_id = '';
        if (!empty($_REQUEST['search_id'])) {
            $search_id = $_REQUEST['search_id'];
        } else {
            $search_id = fn_cp_add_search_history($q, 'S', $total_items);
        }
        
        Registry::get('view')->assign('search_id', $search_id);
        
        if (!empty($search_id)) {
            $redirect_url = fn_link_attach(Registry::get('config.current_url'), 'search_id=' . $search_id);
            Registry::get('view')->assign('redirect_url', $redirect_url);
        }
    }
}
