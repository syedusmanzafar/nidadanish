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

if ($_SERVER['REQUEST_METHOD']  == 'POST') {

    if ($mode == 'delete') {
        if (!empty($_REQUEST['audience_id'])) {
            fn_cp_em_delete_audience($_REQUEST['audience_id']);
            fn_set_notification('N', __('notice'), __('cp_em_audeince_deleted'));
        }
        return array (CONTROLLER_STATUS_OK, "cp_em_audience.manage");
    }
    if ($mode == 'export_to_csv') {
        $params = unserialize($_REQUEST['params']);
        $params['get_export_fields'] = true;
        $audience_id = !empty($_REQUEST['audience_id']) ? $_REQUEST['audience_id'] : 0;
        if (!empty($params['type']) && $params['type'] == 'V') {
            $params['get_all_data'] = true;
            list($users, $search, $audience) = fn_cp_em_get_audience_viewed_users($audience_id, $params, 0, DESCR_SL);
        } else {
            list($users, $search, $audience) = fn_cp_em_get_audience_users($audience_id, $params, 0, DESCR_SL);
        }
        
        $export_fields = $_REQUEST['export_fields'];
        $export_delimiter = $_REQUEST['export_delimiter'];
        
        if (!empty($users)) {
            if (!empty($_REQUEST['user_emails'])) {
                $users_for_export = array();
                
                foreach ($_REQUEST['user_emails'] as $key => $user_email) {
                    $users_for_export[$user_email] = $users[$user_email];
                    unset($users[$user_email]);
                }
               
                $filename = fn_cp_em_export_to_csv($users_for_export, $export_fields, $export_delimiter, $audience_id);
            } else {
                $users_for_export = $users;
                $filename = fn_cp_em_export_to_csv($users_for_export, $export_fields, $export_delimiter, $audience_id);
            }
            
            
            fn_set_notification('N', __('notice'), __('cp_em_file_created'));
            
            if (!empty($audience_id)) {
                $suffix = '.update?audience_id=' . $audience_id;
            } else {
                $suffix = '.manage';
            }
            
            return array(CONTROLLER_STATUS_OK, 'cp_em_audience' . $suffix);
        }
    }

    if ($mode == 'export_to_newsletters') {
        $params = unserialize($_REQUEST['params']);
        $audience_id = !empty($_REQUEST['audience_id']) ? $_REQUEST['audience_id'] : 0;
        if (empty($_REQUEST['list_id'])) {
            $redirect_url = !empty($_REQUEST['redirect_url']) ? $_REQUEST['redirect_url'] : '';
            if (empty($redirect_url)) {
                if (!empty($audience_id)) {
                    $redirect_url = 'cp_em_audience.find_export?audience_id=' . $audience_id;
                } else {
                    $redirect_url = 'cp_em_audience.add?type=' . !empty($params['type']) ? $params['type'] : 'O';
                }
            }
            fn_set_notification('E', __('error'), __('cp_em_select_mailing_list'));
            return array(CONTROLLER_STATUS_REDIRECT, fn_url($redirect_url));
        }
        $params['get_export_fields'] = true;
        if (!empty($params['type']) && $params['type'] == 'V') {
            $params['get_all_data'] = true;
            list($users, $search, $audience) = fn_cp_em_get_audience_viewed_users($audience_id, $params, 0, DESCR_SL);
        } else {
            list($users, $search, $audience) = fn_cp_em_get_audience_users($audience_id, $params, 0, DESCR_SL);
        }
        if (!empty($users)) {
            $stats = array(
                'added' => 0,
                'error' => 0,
                'exists' => 0
            );
            foreach ($users as $_key => $user) {
                if (!empty($_REQUEST['user_emails']) && !in_array($user['email'], $_REQUEST['user_emails'])) {
                    continue;
                } else {
                    $user_for_export = array(
                        'user_id' => $user['user_id'],
                        'email' => $user['email'],
                        'list_ids' => array($_REQUEST['list_id']),
                        'lang_code' => !empty($user['lang_code']) ? $user['lang_code'] : DESCR_SL,
                        'confirmed' => true
                    );
                    $res = fn_cp_em_export_to_newsletters($user_for_export);
                    if (!empty($res) && isset($stats[$res])) {
                        $stats[$res] += 1;
                    }
                }
            }
            if (!empty($stats)) {
                $msg = __('cp_em_added_txt') . ' - <b>' . $stats['added'] . '</b><br />';
                $msg .= __('cp_em_exists_txt') . ' - <b>' . $stats['exists'] . '</b><br />';
                $msg .= __('cp_em_errors_txt') . ' - <b>' . $stats['error'] . '</b><br />';
                fn_set_notification('N', __('notice'), $msg);
            }
        }
    }   
    
    if ($mode == 'export_to_mailchimp') {
        $params = unserialize($_REQUEST['params']);
        $audience_id = !empty($_REQUEST['audience_id']) ? $_REQUEST['audience_id'] : 0;
        $params['get_export_fields'] = true;
        
        if (!empty($params['type']) && $params['type'] == 'V') {
            $params['get_all_data'] = true;
            list($users, $search, $audience) = fn_cp_em_get_audience_viewed_users($audience_id, $params, 0, DESCR_SL);
        } else {
            list($users, $search, $audience) = fn_cp_em_get_audience_users($audience_id, $params, 0, DESCR_SL);
        }
        
        if (!empty($users)) {
            if (!empty($_REQUEST['user_emails'])) {
                $users_for_export = array();
                foreach ($_REQUEST['user_emails'] as $key => $user_email) {
                    $users_for_export[] = $users[$user_email];
                }
                fn_cp_em_export_to_mailchimp($users_for_export);
            } else {
                fn_cp_em_export_to_mailchimp($users);
            }
        }
    } 
    
    if ($mode == 'export_to_unisender') {
        $params = unserialize($_REQUEST['params']);
        $audience_id = !empty($_REQUEST['audience_id']) ? $_REQUEST['audience_id'] : 0;
        $params['get_export_fields'] = true;
        
        if (!empty($params['type']) && $params['type'] == 'V') {
            $params['get_all_data'] = true;
            list($users, $search, $audience) = fn_cp_em_get_audience_viewed_users($audience_id, $params, 0, DESCR_SL);
        } else {
            list($users, $search, $audience) = fn_cp_em_get_audience_users($audience_id, $params, 0, DESCR_SL);
        }
        
        if (!empty($users)) {
            if (!empty($_REQUEST['user_emails'])) {
                $users_for_export = array();
                foreach ($_REQUEST['user_emails'] as $key => $user_email) {
                    $users_for_export[$users[$user_email]['user_id']] = $users[$user_email];
                }
                fn_cp_em_export_to_unisender($users_for_export);
            } else {
                fn_cp_em_export_to_unisender($users);
            }
        }
        
    } 
    
    return array(CONTROLLER_STATUS_OK, 'cp_em_audience.manage');
}
if ($mode == 'manage') {
    
    list($audiences, $search) = fn_cp_em_get_audiences($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    
    Registry::get('view')->assign('audiences', $audiences);
    Registry::get('view')->assign('search', $search);
    
} elseif ($mode == 'update_aud') {
     if (!empty($_REQUEST['name']) && !empty($_REQUEST)) {
        $audience_id = fn_cp_em_update_audience_data($_REQUEST['name'], $_REQUEST, $_REQUEST['audience_id'], DESCR_SL);
        
        return array(CONTROLLER_STATUS_REDIRECT, 'cp_em_audience.update?audience_id=' . $audience_id);
    } else {
        return array(CONTROLLER_STATUS_REDIRECT, 'cp_em_audience.add');
        
    }
} elseif ($mode == 'update' || $mode == 'add') {

    $audience_id = !empty($_REQUEST['audience_id']) ? $_REQUEST['audience_id'] : 0;
    $params = $_REQUEST;
    list($users, $search, $audience) = fn_cp_em_get_audience_users($audience_id, $params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
  
    $countries = fn_get_simple_countries(true, CART_LANGUAGE);
    $states = fn_get_all_states();
    $export_fields = fn_cp_em_get_custome_export_fields(!empty($params['type']) ? $params['type'] : 'O');
    
    if (empty($audience_id)) {
        $aud_type = !empty($params['type']) ? $params['type'] : 'O';
        if (!in_array($aud_type, array('O','V'))) {
            $aud_type = 'O';
        }
    } else {
        $aud_type = !empty($audience['type']) ? $audience['type'] : 'O';
    }
    Tygh::$app['view']->assign('usergroups', fn_get_usergroups(array('type'=> 'C','status' => array('A', 'H')), DESCR_SL));
    Tygh::$app['view']->assign('aud_type', $aud_type);
    Tygh::$app['view']->assign('users', $users);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('countries', $countries);
    Tygh::$app['view']->assign('states', $states);
    Tygh::$app['view']->assign('export_fields', $export_fields);
    Tygh::$app['view']->assign('audience', $audience);
}
if ($mode == 'find_export') {
    
    $audience_id = !empty($_REQUEST['audience_id']) ? $_REQUEST['audience_id'] : 0;
    $params = $_REQUEST;
    if (!empty($params['type']) && $params['type'] == 'V') {
        $params['get_all_data'] = true;
        list($users, $search, $audience) = fn_cp_em_get_audience_viewed_users($audience_id, $params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    } else {
        list($users, $search, $audience) = fn_cp_em_get_audience_users($audience_id, $params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    }
    $export_fields = fn_cp_em_get_custome_export_fields(!empty($params['type']) ? $params['type'] : 'O');
    
    if (Registry::get('addons.newsletters.status') == 'A') {
        list($mailing_lists) = fn_get_mailing_lists(array(), 0, DESCR_SL);
    }
    Tygh::$app['view']->assign('mailing_lists', $mailing_lists);
    
    Tygh::$app['view']->assign('users', $users);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('audience', $audience);
    Tygh::$app['view']->assign('export_fields', $export_fields);
}
if ($mode == 'get_states') {
    $states = fn_get_all_states();
    $_country = $_REQUEST['country'];
    
    Tygh::$app['view']->assign('_country', $_country);
    Tygh::$app['view']->assign('states', $states);
    Tygh::$app['view']->display('addons/cp_extended_marketing/views/cp_em_audience/get_states.tpl');
}

if ($mode == 'picker') {
    $params = $_REQUEST;
    $params['extend'] = array('description');
    $params['skip_view'] = 'Y';

    list($products, $search) = fn_get_products($params, AREA == 'C' ? Registry::get('settings.Appearance.products_per_page') : Registry::get('settings.Appearance.admin_products_per_page'));

    if (!empty($_REQUEST['display']) || (AREA == 'C' && !defined('EVENT_OWNER'))) {
        fn_gather_additional_products_data($products, array('get_icon' => true, 'get_detailed' => true, 'get_options' => true, 'get_discounts' => true));
    }

    if (!empty($products)) {
        foreach ($products as $product_id => $product_data) {
            $products[$product_id]['options'] = fn_get_product_options($product_data['product_id'], DESCR_SL, true, false, true);
            if (!fn_allowed_for('ULTIMATE:FREE')) {
                $products[$product_id]['exceptions'] = fn_get_product_exceptions($product_data['product_id']);
                if (!empty($products[$product_id]['exceptions'])) {
                    foreach ($products[$product_id]['exceptions'] as $exceptions_data) {
                        $products[$product_id]['exception_combinations'][fn_get_options_combination($exceptions_data['combination'])] = '';
                    }
                }
            }
        }
    }

    Tygh::$app['view']->assign('products', $products);
    Tygh::$app['view']->assign('search', $search);

    if (isset($_REQUEST['company_id'])) {
        Tygh::$app['view']->assign('picker_selected_company', $_REQUEST['company_id']);
    }
    if (!empty($_REQUEST['company_ids'])) {
        Tygh::$app['view']->assign('picker_selected_companies', $_REQUEST['company_ids']);
    }
    Tygh::$app['view']->display('addons/cp_extended_marketing/pickers/products/picker_contents.tpl');
    exit;
}
if ($mode == 'get_file') {
    if (!empty($_REQUEST['file'])) {
        fn_get_file($_REQUEST['file']);
    }
} elseif ($mode == 'delete_files') {
    if (!empty($_REQUEST['audience_id'])) {
        fn_cp_em_delete_audience_files($_REQUEST['audience_id']);
        return array(CONTROLLER_STATUS_OK, 'cp_em_audience.find_export?audience_id=' . $_REQUEST['audience_id']);    
    } else {
        fn_cp_em_delete_audience_files(0);
        return array(CONTROLLER_STATUS_REDIRECT, 'cp_em_audience.add');
    }
}