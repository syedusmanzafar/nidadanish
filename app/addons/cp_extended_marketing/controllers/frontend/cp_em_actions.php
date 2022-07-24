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
use Tygh\Enum\Addons\CpExtendedMarketing\NoticeTypes;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'cart') {
    if (!empty($_REQUEST['hash'])) {
        $session_data = fn_cp_em_get_session_data_by_hash($_REQUEST['hash']);
        if (!empty($session_data['type']) && !empty($session_data['notice_id']) && empty(Tygh::$app['session']['cp_em_return_from_mail'])) {//add action to stats
            fn_cp_em_update_statistics($session_data['notice_id'], array('returns_form_email' => 1), '+');
        }
        if (!empty($session_data) && !empty($session_data['session_id'])) {
            if (!empty($session_data['user_data']) && !empty($session_data['user_id'])) {
                $area = 'C';
                $new_sess_data = array(
                    'auth' => fn_fill_auth($session_data['user_data'], array(), true, $area),
                    'last_status' => empty($_SESSION['last_status']) ? '' : $_SESSION['last_status'],
                );
                fn_init_user_session_data($new_sess_data, $session_data['user_id']);
                fn_set_storage_data('session_' . $session_data['session_id'] . '_data', serialize($new_sess_data));
            } else {
                fn_set_session_data('cu_id', $session_data['sess_user_id'], COOKIE_ALIVE_TIME);
                \Tygh::$app['session']->resetID($session_data['session_id']);
                if (empty(Tygh::$app['session']['cart'])) {
                    fn_clear_cart(Tygh::$app['session']['cart']);
                }
                fn_set_session_data('cu_id', $session_data['sess_user_id'], COOKIE_ALIVE_TIME);
                
                $cart = & Tygh::$app['session']['cart'];
                if (!empty($session_data['products']) && empty($cart['products'])) { //if products not added to cart
                    if (!empty($session_data['user_data'])) {
                        $cart['user_data'] = $session_data['user_data'];
                    }
                    foreach ($session_data['products'] as $product) {
                        $product_data[$product['item_id']] = $product;
                        //$product_data[$product['item_id']]['amount'] = $product['amount'];
                    }
                    fn_add_product_to_cart($product_data, $cart, Tygh::$app['session']['auth']);
                }
            }
            
            Tygh::$app['session']['cp_em_return_from_mail'] = $session_data['notice_id']; // trigger for statistic
            if ($session_data['type'] == NoticeTypes::CP_EM_WISHLIST) {
                $url = fn_link_attach('wishlist.view', 'skey=' . $session_data['session_id']);
            } elseif($session_data['type']== NoticeTypes::CP_EM_ABAND) {
                $url = fn_link_attach('checkout.cart', 'skey=' . $session_data['session_id']);
            }
            
            return array(CONTROLLER_STATUS_REDIRECT, fn_url($url));
        } else {
            return array(CONTROLLER_STATUS_DENIED);
        }
    } else {
        return array(CONTROLLER_STATUS_DENIED);
    }
} elseif ($mode == 'reaview_all_page') {
    if (!empty($_REQUEST['hash'])) {
        $data = fn_cp_em_get_session_data_by_hash($_REQUEST['hash'], NoticeTypes::CP_EM_ORDERS_FEED);
        if (!empty($data['type']) && !empty($data['notice_id']) && empty(Tygh::$app['session']['cp_em_return_from_mail'])) {//add action to stats
            fn_cp_em_update_statistics($data['notice_id'], array('returns_form_email' => 1), '+');
        }
        if (!empty($data['notice_id'])) {
            if (!empty($data['review_type']) && $data['review_type'] == 'N' && !empty($data['order_status']) && $data['order_status'] == STATUS_INCOMPLETED_ORDER && !empty($data['session_id'])) {
                if (!empty($data['user_data']) && !empty($session_data['user_id'])) {
                    $area = 'C';
                    $new_sess_data = array(
                        'auth' => fn_fill_auth($data['user_data'], array(), true, $area),
                        'last_status' => empty($_SESSION['last_status']) ? '' : $_SESSION['last_status'],
                    );
                    fn_init_user_session_data($new_sess_data, $data['user_id']);
                    fn_set_storage_data('session_' . $data['session_id'] . '_data', serialize($new_sess_data));
                } else {
                    fn_set_session_data('cu_id', $data['sess_user_id'], COOKIE_ALIVE_TIME);
                    \Tygh::$app['session']->resetID($data['session_id']);
                    if (empty(Tygh::$app['session']['cart'])) {
                        fn_clear_cart(Tygh::$app['session']['cart']);
                    }
                    fn_set_session_data('cu_id', $data['sess_user_id'], COOKIE_ALIVE_TIME);
                    
                    $cart = & Tygh::$app['session']['cart'];
                    if (!empty($data['products']) && empty($cart['products'])) { //if products not added to cart
                        if (!empty($data['user_data'])) {
                            $cart['user_data'] = $data['user_data'];
                        }
                        foreach ($data['products'] as $product) {
                            $product_data[$product['item_id']] = $product;
                            //$product_data[$product['item_id']]['amount'] = $product['amount'];
                        }
                        fn_add_product_to_cart($product_data, $cart, Tygh::$app['session']['auth']);
                    }
                }
                $url = fn_link_attach('checkout.cart', 'skey=' . $data['session_id']);
                Tygh::$app['session']['cp_em_return_from_mail'] = $data['notice_id']; // trigger for statistic
                return array(CONTROLLER_STATUS_REDIRECT, fn_url($url));
            } else {
                $order_info = array();
                if (!empty($data) && !empty($data['order_id'])) {
                    if ($data['type'] == NoticeTypes::CP_EM_ORDERS_FEED && in_array($data['review_type'], array('T','V'))) {
                        $rate_link = fn_cp_em_get_store_review_link($data['order_id'], $data['review_type']);
                        if (empty($rate_link)) {
                            $rate_link = fn_url('index.index', 'C');
                        }
                        return array(CONTROLLER_STATUS_REDIRECT, $rate_link);
                        exit;
                    } else {
                        $order_info = fn_cp_em_get_data_for_review_page($data['order_id']);
                    }
                }
                $order_info['hash'] = $_REQUEST['hash'];
                Registry::get('view')->assign('order_info', $order_info);
                
                Tygh::$app['session']['cp_em_return_from_mail'] = $data['notice_id']; // trigger for statistic
                
                if (defined('AJAX_REQUEST')) {
                    Registry::get('view')->display('addons/cp_extended_marketing/views/cp_em_actions/reaview_all_page.tpl');
                    exit;
                }
            }
        } else {
            return array(CONTROLLER_STATUS_DENIED);
        }
    } else {
        return array(CONTROLLER_STATUS_DENIED);
    }
} elseif ($mode == 'unsubscribe') {
    if (!empty($_REQUEST['hash'])) {
        $session_data = fn_cp_em_get_session_data_by_hash($_REQUEST['hash']);
        if (!empty($session_data)) {
            if (!empty($session_data['type']) && !empty($session_data['notice_id']) && empty(Tygh::$app['session']['cp_em_return_from_mail'])) {//add action to stats
                fn_cp_em_update_statistics($session_data['notice_id'], array('returns_form_email' => 1), '+');
            }
            if (!empty($session_data['user_id'])) {
                
                fn_cp_em_update_user_subscribtion($session_data['user_id'], array($session_data['type'] => 'D'), '', true);
                fn_set_notification('N', __('notice'), __('cp_em_you_have_unsubscribed'));
                if (empty($auth['user_id'])) {
                    fn_login_user($session_data['user_id']);
                }
                return array(CONTROLLER_STATUS_REDIRECT, 'profiles.update?user_id=' . $session_data['user_id'] . '&cp_show_notice_manage=1&selected_section=cp_em_notices');
                
            } elseif (!empty($session_data['user_data']) && !empty($session_data['user_data']['email'])) {
                
                fn_cp_em_update_user_subscribtion(0, array($session_data['type'] => 'D'), $session_data['user_data']['email'], true);
                fn_set_notification('N', __('notice'), __('cp_em_you_have_unsubscribed'));
                
            } elseif (!empty($session_data['email'])) {
                
                fn_cp_em_update_user_subscribtion(0, array($session_data['type'] => 'D'), $session_data['email'], true);
                fn_set_notification('N', __('notice'), __('cp_em_you_have_unsubscribed'));
            }
            if (!empty($session_data['notice_id'])) {
                Tygh::$app['session']['cp_em_return_from_mail'] = $session_data['notice_id'];
            }
        }
    }
    return array(CONTROLLER_STATUS_REDIRECT, 'index.index');
    
} elseif ($mode == 'email_open') { // track email opening
    if (!empty($_REQUEST['hash'])) {
    
        fn_cp_em_email_open_action($_REQUEST['hash']);
    }
    exit;
}