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
use Tygh\Mailer;

if (version_compare(PRODUCT_VERSION, '4.4', '>=')) {
    include_once(Registry::get('config.dir.addons') . 'cp_automaticaly_create_account/src/more_func.php');
}

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_cp_automaticaly_create_account_update_profile($action, $user_data, $current_user_data)
{
    if (!empty($action) && $action === 'add' && AREA == 'C' && Registry::get('addons.cp_automaticaly_create_account.email_activation') == 'Y' && !empty($user_data['user_id'])) {
        db_query("UPDATE ?:users SET status = ?s WHERE user_id = ?i", 'D', $user_data['user_id']);
        $hash = array(
            'user_id' => $user_data['user_id'],
            'val' => rand(1, 999)
        );
        $hash = md5(implode('|', $hash));
        $hash = crc32($hash);
        
        $activation_link = fn_url('cp_ac_actions.activate_link?is_reg=1&hash=' . $hash);
        /** @var \Tygh\Mailer\Mailer $mailer */
        $mailer = Tygh::$app['mailer'];
        $is_sent = $mailer->send(array(
            'to' => $user_data['email'],
            'from' => 'default_company_users_department',
            'data' => array(
                'url' => $activation_link,
            ),
            'template_code' => 'cp_ac_confirm_email',
            'tpl' => 'addons/cp_automaticaly_create_account/notification.tpl',
        ), 'C', CART_LANGUAGE);
        
        if (!empty($is_sent)) {
            fn_set_notification('W', __('important'), __('cp_ac_email_with_confirmation_link'));
            db_query("UPDATE ?:users SET cp_ac_activation = ?s, cp_ac_hash = ?i WHERE user_id = ?i", 'S', $hash, $user_data['user_id']);
        }
    }
}

function fn_cp_automaticaly_create_account_checkout_update_steps_user_exists($cart, $auth, $params, $redirect_params, $found_user_id) {
    fn_delete_notification('error_checkout_user_exists');
}

function fn_cp_automaticaly_create_account_place_order($order_id, $action, $order_status, &$cart, &$auth) {
    if (!function_exists('___cp')) {
        return;
    }
    $acc_settings = Registry::get('addons.cp_automaticaly_create_account');
    if (($cart['user_id'] == 0 && AREA == 'C' && empty(Tygh::$app['session']['cp_acc_skip_creation'])) || (AREA == 'A' && $acc_settings['create_user_in_admin'] == 'Y' && empty($cart['order_id']))) {
        if (defined('PRODUCT_VERSION') && version_compare(PRODUCT_VERSION, '4.3.5', '>')) {
            if (isset(Tygh::$app['session']['cp_new_account_psw'])) {
                unset(Tygh::$app['session']['cp_new_account_psw']);
            }
            if (isset(Tygh::$app['session']['cp_new_account_ekey'])) {
                unset(Tygh::$app['session']['cp_new_account_ekey']);
            }
            if (isset(Tygh::$app['session']['cp_new_account_email'])) {
                unset(Tygh::$app['session']['cp_new_account_email']);
            }
        } else {
            if (isset($_SESSION['cp_new_account_psw'])) {
                unset($_SESSION['cp_new_account_psw']);
            }
            if (isset($_SESSION['cp_new_account_ekey'])) {
                unset($_SESSION['cp_new_account_ekey']);
            }
            if (isset($_SESSION['cp_new_account_email'])) {
                unset($_SESSION['cp_new_account_email']);
            }
        }
        if (!empty($cart['user_data']['email'])) {
            $user_id = db_get_field('SELECT user_id FROM ?:users WHERE email = ?s', $cart['user_data']['email']);
            if (!empty($user_id)) {
                return;
            }
        }
        $password = $cart['user_data']['password1'] = $cart['user_data']['password2'] = fn_generate_password();
        if (empty($cart['user_data']['phone'])) {
            unset($cart['user_data']['phone']);
        }
        if (fn_cp_check_user_data_fields($cart['user_data'])) {
            list($user_id, $profile_id) = fn_update_user(0, $cart['user_data'], $auth, false, true);
            
            if ($acc_settings['email_activation'] == 'Y' && !empty($user_id)) {
                db_query("UPDATE ?:users SET status = ?s WHERE user_id = ?i", 'D', $user_id);
            }
            if (!empty($user_id)) {
                if ($acc_settings['email_activation'] == 'N' && Registry::get('settings.General.approve_user_profiles') != 'Y') {
                    $result = fn_recover_password_generate_key($cart['user_data']['email'], false);
                    Mailer::sendMail(array(
                        'to' => $cart['user_data']['email'],
                        'from' => 'default_company_users_department',
                        'data' => array(
                            'ekey' => $result['key'],
                            'url' => fn_url("auth.recover_password?ekey=" . $result['key'], 'C')
                        ),
                        'template_code' => 'recover_password',
                        'tpl' => 'profiles/recover_password.tpl', // this parameter is obsolete and is used for back compatibility
                    ), 'C', CART_LANGUAGE);

                    if (defined('PRODUCT_VERSION') && version_compare(PRODUCT_VERSION, '4.3.5', '>')) {
                        Tygh::$app['session']['cp_new_account_psw'] = $password;
                        Tygh::$app['session']['cp_new_account_ekey'] = $result['key'];
                        Tygh::$app['session']['cp_new_account_email'] = $cart['user_data']['email'];
                    } else {
                        $_SESSION['cp_new_account_psw'] = $password;
                        $_SESSION['cp_new_account_ekey'] = $result['key'];
                        $_SESSION['cp_new_account_email'] = $cart['user_data']['email'];
                    }
                }
                if (AREA == 'C' && Registry::get('settings.General.approve_user_profiles') == 'Y' && $acc_settings['email_activation'] != 'Y') {
                    if (defined('PRODUCT_VERSION') && version_compare(PRODUCT_VERSION, '4.3.5', '>')) {
                        Tygh::$app['session']['cp_new_account_psw'] = $password;
                        Tygh::$app['session']['cp_new_account_email'] = $cart['user_data']['email'];
                    } else {
                        $_SESSION['cp_new_account_psw'] = $password;
                        $_SESSION['cp_new_account_email'] = $cart['user_data']['email'];
                    }
                }
                if (AREA == 'A') {
                    fn_set_notification('W', __('attention'), __("cp_user_was_create").": " . $user_id . __("password_has_been_emailed") . $cart['user_data']['email']);
                }
                if (AREA == 'C' && Registry::get('settings.General.approve_user_profiles') != 'Y' && $acc_settings['email_activation'] != 'Y') {
                    fn_login_user($user_id, false);
                }
                if ($acc_settings['email_activation'] == 'Y') {
                    $check_send = db_get_field("SELECT cp_ac_hash FROM ?:users WHERE user_id = ?i", $user_id);
                    if (empty($check_send)) {
                        $hash = array(
                            'user_id' => $user_id,
                            'val' => rand(1, 999)
                        );
                        $hash = md5(implode('|', $hash));
                        $hash = crc32($hash);
                        
                        $activation_link = fn_url('cp_ac_actions.activate_link?hash=' . $hash);
                        /** @var \Tygh\Mailer\Mailer $mailer */
                        $mailer = Tygh::$app['mailer'];
                        $is_sent = $mailer->send(array(
                            'to' => $cart['user_data']['email'],
                            'from' => 'default_company_users_department',
                            'data' => array(
                                'url' => $activation_link,
                            ),
                            'template_code' => 'cp_ac_confirm_email',
                            'tpl' => 'addons/cp_automaticaly_create_account/notification.tpl',
                        ), 'C', CART_LANGUAGE);
                        
                        if (!empty($is_sent)) {
                            fn_set_notification('W', __('important'), __('cp_ac_email_with_confirmation_link'));
                            db_query("UPDATE ?:users SET cp_ac_activation = ?s, cp_ac_hash = ?i WHERE user_id = ?i", 'S', $hash, $user_id);
                        }
                    }
                }
            }
        }
    }
}
function fn_cp_check_user_data_fields(&$user_data)  {
    $fields_check = array('lastname','firstname','s_firstname','s_lastname','s_address','s_address_2','s_city','s_county','s_state','s_zipcode',
        's_phone','s_address_type','b_firstname',' b_lastname','b_address','b_address_2','b_city','b_county','b_state','b_zipcode','b_phone');
    
    foreach($fields_check as $prof_field) {
        if (empty($user_data[$prof_field])) {
            $user_data[$prof_field] = '';
        }
    }
    return true;
}
function fn_cp_get_new_account_password() {
    $password = '';
    if (defined('PRODUCT_VERSION') && version_compare(PRODUCT_VERSION, '4.3.5', '>')) {
        if (isset(Tygh::$app['session']['cp_new_account_psw'])) {
            $password = Tygh::$app['session']['cp_new_account_psw'];
        }
    } elseif (isset($_SESSION['cp_new_account_psw'])) {
        $password = $_SESSION['cp_new_account_psw'];
    }
    return $password;    
}
function fn_cp_get_reset_password_href() {
    $restore_href = '';
    if (defined('PRODUCT_VERSION') && version_compare(PRODUCT_VERSION, '4.3.5', '>')) {
        if (isset(Tygh::$app['session']['cp_new_account_ekey'])) {
            $restore_href = fn_url("auth.recover_password?ekey=" . Tygh::$app['session']['cp_new_account_ekey'], 'C');
        }
    } elseif (isset($_SESSION['cp_new_account_ekey'])) {
        $restore_href = fn_url("auth.recover_password?ekey=" . $_SESSION['cp_new_account_ekey'], 'C');
    }
    return $restore_href;
}
function fn_cp_get_new_account_email() {
    $email = '';
    if (defined('PRODUCT_VERSION') && version_compare(PRODUCT_VERSION, '4.3.5', '>')) {
        if (isset(Tygh::$app['session']['cp_new_account_email'])) {
            $email = Tygh::$app['session']['cp_new_account_email'];
            unset(Tygh::$app['session']['cp_new_account_email']);
        }
    } elseif (isset($_SESSION['cp_new_account_email'])) {
        $email = $_SESSION['cp_new_account_email'];
        unset($_SESSION['cp_new_account_email']);
    }
    return $email;
}

//cron
function fn_cp_ac_cron_check_info()
{
    $admin_ind = Registry::get('config.admin_index');
    $__params = Registry::get('addons.cp_automaticaly_create_account');
    if (!empty($__params) && !empty($__params['cron_pass'])) {
        $cron_pass = $__params['cron_pass'];
    } else {
        $cron_pass = '';
    }
    $hint = '<b>' . __("cp_ac_use_this_for_delete_inactivated") . ':</b><br>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_ac_actions.delete_unconfirmed --cron_pass=' . $cron_pass;
    
    return $hint;
}

function fn_cp_ac_activate_account($hash)
{
    if (!empty($hash)) {
        $user_id = db_get_field("SELECT user_id FROM ?:users WHERE cp_ac_hash = ?i AND cp_ac_activation = ?s", $hash, 'S');
        if (!empty($user_id)) {
            db_query("UPDATE ?:users SET status = ?s, cp_ac_activation = ?s WHERE user_id = ?i", 'A','A', $user_id);
            fn_login_user($user_id, false);
            return $user_id;
        }
    }
    return false;
}

function fn_cp_ac_delete_unconfirmed_accounts()
{
    $days = Registry::get('addons.cp_automaticaly_create_account.delete_after_cron');
    if (!empty($days)) {
        $del_type = Registry::get('addons.cp_automaticaly_create_account.del_users');
        if ($del_type == 'all') {
            $users = db_get_fields("SELECT user_id FROM ?:users WHERE timestamp <= ?i AND cp_ac_activation =  ?s AND status = ?s", TIME - 60*60*24*$days, 'S', 'D');
        } elseif ($del_type == 'no_orders') {
            $users = db_get_fields("SELECT ?:users.user_id FROM ?:users 
                LEFT JOIN ?:orders ON ?:orders.user_id = ?:users.user_id
                WHERE ?:users.timestamp <= ?i AND ?:users.cp_ac_activation =  ?s AND ?:orders.order_id IS NULL AND ?:users.status = ?s", TIME - 60*60*24*$days, 'S', 'D');
        }
        if (!empty($users)) {
            foreach($users as $user_id) {
                fn_delete_user($user_id);
            }
        }
    }
    return true;
}