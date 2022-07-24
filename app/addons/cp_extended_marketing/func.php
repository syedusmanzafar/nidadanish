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
use Tygh\Languages\Languages;
use Tygh\Template\Mail\Template;
use Tygh\Template\Mail\Context;
use Tygh\Template\Collection;
use Tygh\Enum\Addons\CpExtendedMarketing\NoticeTypes;
use Tygh\Enum\YesNo;
use Tygh\Settings;
use Tygh\BlockManager\Block;
use Tygh\BlockManager\RenderManager;
use Tygh\Storage;
use Tygh\Themes\Styles;

if (version_compare(PRODUCT_VERSION, '4.12.2', '>') && Registry::get('addons.product_reviews.status') == 'A') {
    include_once(Registry::get('config.dir.addons') . 'cp_extended_marketing/src/for_reviews.php');
}

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/*addon's hook's*/

function fn_cp_extended_marketing_update_profile($action, $user_data, $current_user_data)
{
    if (isset($user_data['cp_em_types']) && !empty($user_data['user_id'])) {
        fn_cp_em_update_user_subscribtion($user_data['user_id'], $user_data['cp_em_types'], '', true);
    }
    if (!empty($action) && $action == 'add' && !empty($user_data['email']) && !empty($user_data['user_id'])) {
        db_query("UPDATE ?:cp_em_viewed_sent SET user_id = ?i WHERE user_id = ?i AND email = ?s", $user_data['user_id'], 0, $user_data['email']);
        db_query("UPDATE ?:cp_em_targeted_sent SET user_id = ?i WHERE user_id = ?i AND email = ?s", $user_data['user_id'], 0, $user_data['email']);
        db_query("UPDATE ?:cp_em_viewed_products SET user_id = ?i WHERE user_id = ?i AND email = ?s", $user_data['user_id'], 0, $user_data['email']);
        db_query("UPDATE ?:cp_em_user_subscriptions SET user_id = ?i WHERE user_id = ?i AND email = ?s", $user_data['user_id'], 0, $user_data['email']);
    }
}

function fn_cp_extended_marketing_tools_change_status($params, $result)
{
    if (!empty($params['table']) && $params['table'] == 'cp_em_notices' && !empty($params['status']) && $params['status'] == 'D' && !empty($params['id']) && !empty($result)) {
        fn_cp_em_clear_notice_queue($params['id']);
    }
}

function fn_cp_extended_marketing_mailer_create_message_before($mail, $message, $area, $lang_code, &$transport, $builder)
{
    if (!empty($message) && !empty($message['data']) && !empty($message['data']['cp_em_notice_data']) && !empty($message['data']['cp_em_notice_data']['smtp'])) {
        $transport = $mail->getTransport($message['data']['cp_em_notice_data']['smtp']);
    }
}

function fn_cp_extended_marketing_get_pages($params, &$join, &$condition, $fields, $group_by, $sortings, $lang_code)
{
    if (AREA == 'A' && !empty($params['cp_em_form_email']) && Registry::get('addons.form_builder.status') == 'A') {
        $join .= db_quote(' LEFT JOIN ?:form_options ON ?:form_options.page_id = ?:pages.page_id');
        $condition .= db_quote(" AND ?:form_options.element_type = ?s", defined('FORM_EMAIL') ? FORM_EMAIL : 'Y');
    }
}

function fn_cp_extended_marketing_send_form($page_data, $form_values, $result, $from, $sender, $attachments, $is_html, $subject)
{
    if (!empty($page_data) && !empty($page_data['page_id']) && !empty($sender)) {
        $data = array(
            'page_id' => $page_data['page_id'],
            'email' => $sender,
            'timestamp' => time(),
        );
        db_replace_into('cp_em_send_form', $data);
    }
}

function fn_cp_extended_marketing_checkout_update_steps_pre(&$cart, $auth, $params, $redirect_params)
{
    if (AREA == 'C' && isset($params['ask_about_reviews'])) {
        $cart['cp_em_send_reviews'] = $params['ask_about_reviews'];
    }
}

function fn_cp_extended_marketing_get_users($params, &$fields, $sortings, &$condition, &$join, $auth)
{
    if (!empty($params['cp_em_cron_t'])) {
        $fields['lang_code'] = '?:users.lang_code';
        if (isset($params['cp_em_time_from'])) {
            if (in_array($params['cp_em_cron_t'], array('B','R','A'))) {
                $join .= " LEFT JOIN ?:cp_em_targeted_sent ON ?:cp_em_targeted_sent.user_id = ?:users.user_id AND ?:cp_em_targeted_sent.notice_id = " . $params['cp_em_notice_id'];
            }
            if ($params['cp_em_cron_t'] == 'B') {
                $ba = !empty($params['cp_em_ba']) ? $params['cp_em_ba'] : 'A';
                if (empty($params['cp_birthday_field'])) {
                    $birth_field = $fields['birthday'] = '?:users.birthday';
                } else {
                    $fields['birthday'] = '?:profile_fields_data.value as birthday';
                    $birth_field = '?:profile_fields_data.value';
                    $join .= db_quote(" LEFT JOIN ?:profile_fields_data ON ?:profile_fields_data.object_id = ?:users.user_id");
                    $condition['cp_em_for_birthday'] = db_quote(' AND ?:profile_fields_data.object_type = ?s AND ?:profile_fields_data.field_id = ?i', 'U', $params['cp_birthday_field']);
                }
                $fields['bt'] = db_quote("UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME($birth_field),'?i-%m-%d-%H')) as cp_birth", date('Y', time()));
                $fields['now'] = db_quote("UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(?i),'?i-%m-%d-%H-%i-%s')) as cp_now", time(), date('Y', time()));
                
                if ($ba == 'A') {
                    $condition['cp_em_time_from'] = db_quote(" AND $birth_field > ?i AND (UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME($birth_field),'?i-%m-%d')) < UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(?i),'?i-%m-%d-%H-%i-%s'))) 
                        AND (UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME($birth_field),'?i-%m-%d')) + ?i) >= UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(?i),'?i-%m-%d-%H-%i-%s')) 
                        AND (?:cp_em_targeted_sent.notice_id IS NULL OR ?:cp_em_targeted_sent.timestamp < ?i)", 0, date('Y', time()),time(), date('Y', time()), date('Y', time()), 60*60*$params['cp_em_time_from'], time(), date('Y', time()), time() - 60*60*24*360);
                } else {
                    $condition['cp_em_time_from'] = db_quote(" AND $birth_field > ?i AND (UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME($birth_field),'?i-%m-%d')) >= UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(?i),'?i-%m-%d-%H-%i-%s'))) 
                        AND (UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME($birth_field),'?i-%m-%d')) - ?i) < UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(?i),'?i-%m-%d-%H-%i-%s')) 
                        AND (?:cp_em_targeted_sent.notice_id IS NULL OR ?:cp_em_targeted_sent.timestamp < ?i)", 0, date('Y', time()),time(), date('Y', time()), date('Y', time()), 60*60*$params['cp_em_time_from'], time(), date('Y', time()), time() - 60*60*24*360);
                }
                
            } elseif ($params['cp_em_cron_t'] == 'R') {
                $condition['cp_em_time_from'] = db_quote(' AND ?:users.timestamp > ?i AND ?:users.timestamp + ?i < ?i AND ?:cp_em_targeted_sent.notice_id IS NULL', 0, 60*60*$params['cp_em_time_from'], time());
                
            } elseif ($params['cp_em_cron_t'] == 'A' && !empty($params['cp_em_last_login'])) {
                $condition['cp_em_time_from'] = db_quote(' AND ?:users.last_login + ?i < ?i AND (?:cp_em_targeted_sent.notice_id IS NULL OR ?:cp_em_targeted_sent.timestamp < ?i)', 24*60*60*$params['cp_em_last_login'], time(), time() - 24*60*60*$params['cp_em_last_login']);
            }
        }
    }
}

function fn_cp_extended_marketing_change_location($location, $select, &$condition, $params)
{
    if (!empty($params['cp_em_this_type'])) {
        $condition .= db_quote('AND ?:profile_fields.field_type = ?s ', $params['cp_em_this_type']);
    }
}

function fn_cp_extended_marketing_add_discussion_post_post($post_data, $send_notifications)
{
    if (AREA == 'C' && !empty($post_data) && !empty($post_data['post_id']) && !empty(Tygh::$app['session']['cp_em_return_from_mail'])) {
        $notice_id = Tygh::$app['session']['cp_em_return_from_mail'];
        $fields = array(
            'reviews_placed' => 1,
        );
        fn_cp_em_update_statistics($notice_id, $fields, '+');
    }
}

function fn_cp_extended_marketing_get_orders($params, &$fields, $sortings, &$condition, &$join, $group)
{
    if (!empty($params['cp_em_notice_send'])) {
        $join .= db_quote(" LEFT JOIN ?:cp_em_feedback_sent ON ?:cp_em_feedback_sent.order_id = ?:orders.order_id AND ?:cp_em_feedback_sent.notice_id = ?i", $params['cp_em_notice_send']);
        if (!empty($params['cp_em_skip_changed'])) {
            $condition .= db_quote(" AND ?:orders.cp_em_changed_time = ?i AND ?:orders.cp_em_send_email = ?s AND ?:cp_em_feedback_sent.notice_id IS NULL", 0, 'Y');
        } elseif (isset($params['cp_em_from_this_time'])) {
            $condition .= db_quote(" AND ?:orders.cp_em_changed_time > ?i AND ?:orders.cp_em_changed_time <= ?i AND ?:orders.cp_em_send_email = ?s AND ?:cp_em_feedback_sent.notice_id IS NULL", 0, time() - 60*60*$params['cp_em_from_this_time'], 'Y');
        }
    }
    $fields[] = "?:orders.ip_address as cp_em_ip";
}

function fn_cp_extended_marketing_change_order_status ($status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order)
{
    if ($status_from != $status_to && !empty($order_info['order_id'])) {
        db_query("UPDATE ?:orders SET cp_em_changed_time = ?i WHERE order_id = ?i", time(), $order_info['order_id']);
    }
}

function fn_cp_extended_marketing_place_order($order_id, $action, $order_status, $cart, $auth)
{
    if (!empty($order_id) && $action != 'save') {
        if (!empty(Tygh::$app['session']['cp_em_return_from_mail']) && empty(Tygh::$app['session']['cp_em_return_placed'])) {
            $notice_id = Tygh::$app['session']['cp_em_return_from_mail'];
            $order_total = db_get_field("SELECT total FROM ?:orders WHERE order_id = ?i", $order_id);
            
            $fields = array(
                'orders_placed' => 1,
                'orders_placed_total' => $order_total
            );
            fn_cp_em_update_statistics($notice_id, $fields, '+');
            Tygh::$app['session']['cp_em_return_placed'] = true;
        }
        //add coupon used stats
        if (!empty($cart['coupons']) && !empty($cart['user_data']) && !empty($cart['user_data']['email'])) {
            foreach($cart['coupons'] as $c_code => $prom_id) {
                $result = db_query("UPDATE ?:cp_em_promocode_expire SET used = used + 1, order_id = ?i WHERE email = ?s AND coupon_code = ?s", $order_id, $cart['user_data']['email'], $c_code);
                if (!empty($result)) {
                    $get_notice_id = db_get_field("SELECT notice_id FROM ?:cp_em_promocode_expire WHERE email = ?s AND coupon_code = ?s", $cart['user_data']['email'], $c_code);
                    if (!empty($get_notice_id)) {
                        $c_fields = array('coupons_used' => 1);
                        fn_cp_em_update_statistics($get_notice_id, $c_fields, '+');
                    }
                }
            }
        }
        //remove products from viewed products
        if (!empty($cart['products']) && !empty($cart['user_data']) && !empty($cart['user_data']['email'])) {
            $ids_to_remove = array();
            foreach($cart['products'] as $cart_id => $prod_data) {
                if (!empty($prod_data['product_id'])) {
                    $ids_to_remove[] = $prod_data['product_id'];
                }
            }
            if (!empty($ids_to_remove)) {
                db_query("DELETE FROM ?:cp_em_viewed_products WHERE email = ?s AND product_id IN (?n)", $cart['user_data']['email'], $ids_to_remove);
            }
        }
        if (!empty($cart['cp_em_send_reviews']) && in_array($cart['cp_em_send_reviews'], array('N','Y')) && !empty($cart['user_data']) && !empty($cart['user_data']['email'])) {            
            db_query("UPDATE ?:orders SET cp_em_send_email = ?s WHERE order_id = ?i", $cart['cp_em_send_reviews'], $order_id);
        }
    }
}

function fn_cp_extended_marketing_session_regenerate_id($old_id, $new_id)
{
    if (!empty($old_id) && !empty($new_id)) {
        db_query("UPDATE ?:cp_em_aband_cart_sent SET session_id = ?s WHERE session_id = ?s", $new_id, $old_id);
        db_query("UPDATE ?:cp_em_logs SET session_id = ?s WHERE session_id = ?s", $new_id, $old_id);
    }
}

function fn_cp_extended_marketing_get_carts_before_select($params, $items_per_page, &$fields, &$join, &$condition, $group, $sorting, $limit)
{
    if (!empty($params['session_id'])) {
        $condition .= db_quote(" AND ?:user_session_products.session_id = ?s", $params['session_id']);
    }
    if (isset($params['cp_em_from_this_time']) && !empty($params['cp_em_for_this_notice'])) {
        $fields[] = '?:user_session_products.session_id';
        $fields[] = '?:user_session_products.type';
        $fields[] = '?:user_session_products.user_type';
        $join .= db_quote(" LEFT JOIN ?:cp_em_aband_cart_sent ON ?:cp_em_aband_cart_sent.session_id = ?:user_session_products.session_id AND ?:cp_em_aband_cart_sent.notice_id = ?i", $params['cp_em_for_this_notice']);
        $condition .= db_quote(" AND ?:user_session_products.order_id = ?i AND ?:user_session_products.timestamp <= ?i AND ?:cp_em_aband_cart_sent.notice_id IS NULL", 0, time() - 60*60*$params['cp_em_from_this_time']);
        if (!empty($params['cp_em_for_vendors']) && fn_allowed_for('MULTIVENDOR')) {
            $condition .= db_quote(" AND ?:users.user_type = ?s", 'V');
        }
    }
}

function fn_cp_extended_marketing_get_promotions($params, $fields, $sortings, &$condition, $join, $group, $lang_code)
{
    if (!empty($params['cp_is_for_notices'])) {
        $condition .= db_quote(' AND ?:promotions.cp_em_for_notices = ?s', 'Y');
    }
}

function fn_cp_extended_marketing_delete_company($company_id, $result, $storefronts)
{
    if (!empty($company_id) && !empty($result)) {
        $notice_ids = db_get_fields("SELECT notice_id FROM ?:cp_em_notices WHERE company_id = ?i", $company_id);
        if (!empty($notice_ids)) {
            fn_cp_em_delete_notice($notice_ids);
        }
    }
}

function fn_cp_extended_marketing_delete_product_post($product_id, $product_deleted)
{
    if (!empty($product_id) && !empty($product_deleted)) {
        db_query("DELETE FROM ?:cp_em_viewed_products WHERE product_id = ?i", $product_id);
    }
}

function fn_cp_extended_marketing_delete_promotions_post($promotion_ids)
{
    if (!empty($promotion_ids)) {
        db_query("UPDATE ?:cp_em_notices SET promotion_id = ?i WHERE promotion_id IN (?n)", 0, $promotion_ids);
        db_query("DELETE FROM ?:cp_em_promocode_expire WHERE promotion_id IN (?n)", $promotion_ids);
    }
}

function fn_cp_extended_marketing_post_delete_user($user_id, $user_data, $result)
{
    if (!empty($user_id) && !empty($result)) {
        db_query("DELETE FROM ?:cp_em_promocode_expire WHERE user_id = ?i", $user_id);
        db_query("DELETE FROM ?:cp_em_viewed_products WHERE user_id = ?i", $user_id);
        db_query("DELETE FROM ?:cp_em_user_subscriptions WHERE user_id = ?i", $user_id);
        db_query("DELETE FROM ?:cp_em_targeted_sent WHERE user_id = ?i", $user_id);
        db_query("DELETE FROM ?:cp_em_viewed_sent WHERE user_id = ?i", $user_id);
    }
}

/* functions */
/**
 * Check expired and used coupon codes, remove them from promotion
 *
 */
function fn_cp_em_cron_expire_coupons($val = 0)
{
    $check_time = time();
    $used_coupons = db_get_array("SELECT * FROM ?:cp_em_promocode_expire WHERE removed = ?s AND (used > ?i OR expire_time <= ?i)", 'N', 0, $check_time);
    if (!empty($used_coupons)) {
        $by_promotions = array();
        foreach($used_coupons as $coup_data) {
            if (!empty($coup_data['promotion_id']) && !empty($coup_data['coupon_code'])) {
                if (!isset($by_promotions[$coup_data['promotion_id']])) {
                    $by_promotions[$coup_data['promotion_id']] = array();
                }
                $by_promotions[$coup_data['promotion_id']][] = $coup_data['coupon_code'];
            }
        }
        if (!empty($by_promotions)) {
            foreach($by_promotions as $promotion_id => $codes) {
                $promotion_data = fn_get_promotion_data($promotion_id, CART_LANGUAGE);
                if (!empty($promotion_data) && !empty($promotion_data['conditions']) && !empty($promotion_data['conditions']['conditions'])) {
                    $go_update = false;
                    foreach ($promotion_data['conditions']['conditions'] as $p_key => &$condition) {
                        if ($condition['condition'] == 'coupon_code') {
                            if (!empty($condition['value'])) {
                                $exists_codes = explode(',', $condition['value']);
                                $new_codes_val = array_diff($exists_codes, $codes);
                                $condition['value'] = implode(',', $new_codes_val);
                                $go_update = true;
                            }
                        }
                    }
                    if (!empty($go_update)) {
                        $update_data = array();
                        $update_data['conditions'] = serialize($promotion_data['conditions']);
                        $update_data['conditions_hash'] = fn_promotion_serialize($promotion_data['conditions']['conditions']);
                        
                        db_query('UPDATE ?:promotions SET ?u WHERE promotion_id=?i', $update_data, $promotion_data['promotion_id']);
                    }
                    $conditions = $promotion_data['conditions'];
                }
            }
        }
        db_query("UPDATE ?:cp_em_promocode_expire SET removed = ?s WHERE removed = ?s AND (used > ?i OR expire_time <= ?i)", 'Y', 'N', 0, $check_time);
    }
    return true;
}

function fn_cp_em_cron_check_allow_post($ip_address, $object_type, $object_id = 0, $company_id, $type, $discussion)
{
    $allow = false;
    $check_ip = 'Y';
    if (!empty($object_type) && !empty($type) && !empty($ip_address)) {
        if (fn_allowed_for("ULTIMATE")) {
            if (!empty($company_id)) {
                $check_ip_test = Settings::instance()->getAllVendorsValues($object_type . '_post_ip_check', 'discussion');
                if (!empty($check_ip_test) && !empty($check_ip_test[$company_id])) {
                    $check_ip = $check_ip_test[$company_id];
                }
                $thread_data = db_get_row("SELECT thread_id, type FROM ?:discussion WHERE object_type = ?s AND company_id = ?i AND object_id = ?i", $type, $company_id, $object_id);
            }
        } elseif (!empty($discussion[$object_type . '_post_ip_check'])) {
            $check_ip = $discussion[$object_type . '_post_ip_check'];
            $thread_data = db_get_row("SELECT thread_id, type FROM ?:discussion WHERE object_type = ?s AND object_id = ?i", $type, $object_id);
        }
        if (!empty($thread_data) && !empty($thread_data['thread_id']) && !empty($thread_data['type']) && $thread_data['type'] != 'D') {
            if (!empty($check_ip) && $check_ip === YesNo::YES) {
                $is_exists_post = db_get_field("SELECT COUNT(*) FROM ?:discussion_posts WHERE thread_id = ?i AND ip_address = ?s", $thread_data['thread_id'], $ip_address);
                if (empty($is_exists_post)) {
                    $allow = true;
                }
            } else {
                $allow = true;
            }
        }
    }
    return $allow;
}

function fn_cp_em_get_addon_em_settings($addon_settings, $exist_settings, $company_id)
{
    if (!isset($exist_settings[$company_id])) {
        $set_unsub_link = Settings::instance()->getAllVendorsValues('add_unsubscribe_link', 'cp_extended_marketing');
        if (!empty($set_unsub_link) && !empty($set_unsub_link[$company_id])) {
            $addon_settings['add_unsubscribe_link'] = $set_unsub_link[$company_id];
        }
        $set_image_width = Settings::instance()->getAllVendorsValues('email_image_width', 'cp_extended_marketing');
        if (!empty($set_image_width) && !empty($set_image_width[$company_id])) {
            $addon_settings['email_image_width'] = $set_image_width[$company_id];
        }
        $set_log_mail_send = Settings::instance()->getAllVendorsValues('log_mail_send', 'cp_extended_marketing');
        if (!empty($set_log_mail_send) && !empty($set_log_mail_send[$company_id])) {
            $addon_settings['log_mail_send'] = $set_log_mail_send[$company_id];
        }
        $set_log_test_mail_send = Settings::instance()->getAllVendorsValues('log_test_mail_send', 'cp_extended_marketing');
        if (!empty($set_log_test_mail_send) && !empty($set_log_test_mail_send[$company_id])) {
            $addon_settings['log_test_mail_send'] = $set_log_test_mail_send[$company_id];
        }
        $set_check_stock_email = Settings::instance()->getAllVendorsValues('check_stock_email', 'cp_extended_marketing');
        if (!empty($set_check_stock_email) && !empty($set_check_stock_email[$company_id])) {
            $addon_settings['check_stock_email'] = $set_check_stock_email[$company_id];
        }
        $exist_settings[$company_id] = $addon_settings;
    } else {
        $addon_settings = $exist_settings[$company_id];
    }
    return array($addon_settings, $exist_settings);
}

function fn_cp_em_get_user_usergroups($user_data)
{
    if (!empty($user_data['user_id'])) {
        $user_data['usergroups'] = fn_get_user_usergroups($user_data['user_id']);
        if (!empty($user_data['usergroups']) && empty($user_data['usergroup_ids'])) {
            $user_data['usergroup_ids'] = array(0 => USERGROUP_ALL);
            foreach($user_data['usergroups'] as $u_gr) {
                $user_data['usergroup_ids'][$u_gr['usergroup_id']] = $u_gr['usergroup_id'];
            }
            if (empty($user_data['usergroup_ids'][2])) {
                $user_data['usergroup_ids'][2] = 2;
            }
        }
    }
    if (empty($user_data['usergroup_ids'])) {
        $user_data['usergroup_ids'] = array(0 => USERGROUP_ALL);
        $user_data['usergroup_ids'][] = 1;
    }
    return $user_data;
}

function fn_cp_em_cron_build_queue()
{
    $n_params = array(
        'active' => true
    );
    list($avail_notices,) = fn_cp_em_get_notices($n_params, 0, CART_LANGUAGE);
    
    $is_new_reviews = false;
    if (version_compare(PRODUCT_VERSION, '4.12.2', '>') && Registry::get('addons.product_reviews.status') == 'A') {
        $is_new_reviews = true;
    }
    list($avail_notices,) = fn_cp_em_get_notices($n_params, 0, CART_LANGUAGE);

    if (!empty($avail_notices)) {
        $exist_settings = $exist_companies = array();
        $addon_settings = Registry::get('addons.cp_extended_marketing');
        $addon_settings['mailer_sendmail_path'] = Registry::get('settings.Emails.mailer_sendmail_path');
        $discussion = Registry::get('addons.discussion');
        $newsletters = Registry::get('addons.newsletters');
        $e_market = Registry::get('addons.email_marketing');
        $f_builder = Registry::get('addons.form_builder');
        if (!empty($discussion) && $discussion['status'] == 'A') {
            $discussion_object_types = fn_get_discussion_objects();
        }
        $cron_limits = $addon_settings['emails_send_cron'];
        $send_counter = 0;
        foreach($avail_notices as $notice_data) {
            
            if (fn_allowed_for('ULTIMATE')) {
                if (!empty($notice_data['company_id'])) {
                    $company_id = $notice_data['company_id'];
                } else {
                    $company_id = 1;
                }
                $addon_settings['check_stock_email'] = fn_cp_em_get_settings_val('check_stock_email', $company_id, 'cp_extended_marketing');
                $addon_settings['time_for_type'] = fn_cp_em_get_settings_val('time_for_type', $company_id, 'cp_extended_marketing');
            }
            
            $notice_data['all_langs'] = db_get_hash_array("SELECT * FROM ?:cp_em_notice_descriptions WHERE notice_id = ?i", 'lang_code', $notice_data['notice_id']);
            $notice_data['placeholders'] = fn_cp_em_get_placeholders($notice_data['type']);
            $notice_data['usergroup_ids'] = explode(',', $notice_data['usergroup_ids']);
            if ($notice_data['type'] == NoticeTypes::CP_EM_ABAND || $notice_data['type'] == NoticeTypes::CP_EM_WISHLIST) { // abandoned carts and wishlists
                $cart_params = array(
                    'check_shipping_billing' => true,
                    'cp_em_from_this_time' => $notice_data['send_after'],
                    'cp_em_for_this_notice' => $notice_data['notice_id'],
                );
                if ($notice_data['type'] == NoticeTypes::CP_EM_ABAND) {
                    $cart_params['product_type_c'] = true;
                    $sess_type = 'C';
                    if (!empty($notice_data['for_vendors']) && $notice_data['for_vendors'] == 'Y') {
                        $cart_params['cp_em_for_vendors'] = true;
                    }
                }
                if ($notice_data['type'] == NoticeTypes::CP_EM_WISHLIST) {
                    $cart_params['product_type_w'] = true;
                    $sess_type = 'W';
                }
                if (fn_allowed_for("ULTIMATE")) {
                    $cart_params['company_id'] = $notice_data['company_id'];
                }
                list($carts_list, $search, $user_ids) = fn_get_carts($cart_params, 0);
                if (!empty($carts_list)) {
                    foreach($carts_list as $a_cart) {
                        $a_cart['cart_products'] = db_get_hash_array("SELECT item_id, extra FROM ?:user_session_products WHERE session_id = ?s AND type = ?s", 'item_id', $a_cart['session_id'], $sess_type);
                        $a_cart['products'] = array();
                        if (!empty($a_cart['cart_products'] )) {
                            foreach($a_cart['cart_products'] as $cart_id => $ab_product) {
                                $a_cart['products'][$cart_id] = unserialize($ab_product['extra']);
                                $a_cart['products'][$cart_id]['item_id'] = $ab_product['item_id'];
                            }
                            if (isset($a_cart['extra'])) {
                                $a_cart['extra'] = unserialize($a_cart['extra']);
                            }
                            if (isset($a_cart['extra']['user_data'])) {
                                $a_cart['user_data'] = array_merge($a_cart['user_data'], $a_cart['extra']['user_data']);
                            }
                            if (empty($a_cart['user_data']) && empty($a_cart['email'])) {
                                continue;
                            }
                            if (!empty($a_cart['user_data']['usergroups']) && empty($a_cart['user_data']['usergroup_ids'])) {
                                $a_cart['user_data']['usergroup_ids'] = array(0 => USERGROUP_ALL);
                                foreach($a_cart['user_data']['usergroups'] as $u_gr) {
                                    $a_cart['user_data']['usergroup_ids'][$u_gr['usergroup_id']] = $u_gr['usergroup_id'];
                                }
                                if (!empty($a_cart['user_data']['user_id']) && empty($a_cart['user_data']['usergroup_ids'][2])) {
                                    $a_cart['user_data']['usergroup_ids'][2] = 2;
                                }
                            }
                            if (empty($a_cart['user_data']['usergroup_ids'])) {
                                $a_cart['user_data']['usergroup_ids'] = array(0 => USERGROUP_ALL);
                                if ($a_cart['user_type'] == 'U') {
                                    $a_cart['user_data']['usergroup_ids'][] = 1;
                                }
                            }
                            $send_notice = 'A';
                            $user_data = $a_cart['user_data'];
                            $send_notice = fn_cp_em_check_user_subscribe_notice($user_data, $notice_data['type']);
                            if (!empty($send_notice) && $send_notice == 'D') {
                                continue;
                            }
                            $user_data['products'] = $a_cart['products'];
                            if (empty($user_data['lang_code'])) {
                                $user_data['lang_code'] = CART_LANGUAGE;
                            }
                            if (!empty($user_data['user_type']) && $user_data['user_type'] == 'A') {
                                continue;
                            }
                            if (!empty($addon_settings['time_for_type'])) {
                                $skip_row = db_get_field("SELECT ?:cp_em_aband_cart_sent.notice_id FROM ?:cp_em_aband_cart_sent 
                                    LEFT JOIN ?:cp_em_notices ON ?:cp_em_notices.notice_id = ?:cp_em_aband_cart_sent.notice_id
                                    WHERE ?:cp_em_notices.type = ?s AND ?:cp_em_aband_cart_sent.email = ?s AND ?:cp_em_aband_cart_sent.timestamp > ?i", $notice_data['type'], $user_data['email'], time() - 60*60*$addon_settings['time_for_type']);
                                    if (!empty($skip_row)) {
                                        continue;
                                    }
                            }
                            if ($notice_data['type'] == NoticeTypes::CP_EM_WISHLIST) {
                                foreach($user_data['products'] as $ud_key => &$prod) {
                                    $sel_opt = array();
                                    if (!empty($prod['extra']) && !empty($prod['extra']['product_options'])) {
                                        $sel_opt = $prod['extra']['product_options'];
                                    }
                                    $prod = fn_get_product_data($prod['product_id'], $user_data, $user_data['lang_code'], '', false, true, true, false, false, false, true, false);
                                    if (!empty($sel_opt)) {
                                        $prod['selected_options'] = $sel_opt;
                                    }
                                    if (!empty($addon_settings) && !empty($addon_settings['check_stock_email']) && $addon_settings['check_stock_email'] == 'Y' && empty($prod['amount'])) {
                                        unset($user_data['products'][$ud_key]);
                                    }
                                }
                            }
                            if (empty($user_data['products'])) {
                                continue;
                            }
                            $user_data['session_id'] = $a_cart['session_id'];
                            $user_data['storefront_id'] = !empty($a_cart['storefront_id']) ? $a_cart['storefront_id'] : '';
                            if (!empty($user_data['lang_code']) && !empty($notice_data['all_langs'][$user_data['lang_code']])) {
                                $notice_data['subject'] = $notice_data['all_langs'][$user_data['lang_code']]['subject'];
                                $notice_data['message'] = $notice_data['all_langs'][$user_data['lang_code']]['message'];
                            }
                            list($company_data, $exist_companies, $user_data) = fn_cp_em_get_company_data_for_notice($user_data, $exist_companies);
                            $user_data['orders_total_sent'] = db_get_field("SELECT sum(price) FROM ?:user_session_products WHERE session_id = ?s", $user_data['session_id']);
                            
                            if (fn_allowed_for("ULTIMATE")) {
                                list($addon_settings, $exist_settings) = fn_cp_em_get_addon_em_settings($addon_settings, $exist_settings, $notice_data['company_id']);
                            }
                            
                            list($send_counter, $result) = fn_cp_em_send_notification($notice_data, $user_data, $is_test = false, $user_data['lang_code'], $company_data, $addon_settings, $send_counter);
//                             if (!empty($cron_limits) && $cron_limits > 0 && $send_counter >=  $cron_limits) {
//                                 return true;
//                             }
                        }
                    }
                }
            } elseif ($notice_data['type'] == NoticeTypes::CP_EM_ORDERS_FEED && !empty($notice_data['order_statuses']) && !empty($discussion) && $discussion['status'] == 'A') {
                $statuses = explode(',', $notice_data['order_statuses']);
                if (!empty($statuses)) {
                    $ord_params = array(
                        'status' => $statuses,
                        'cp_em_notice_send' => $notice_data['notice_id'],
                        'cp_em_from_this_time' => $notice_data['send_after'],
                        'include_incompleted' => true
                    );
                    if (fn_allowed_for("ULTIMATE")) {
                        $ord_params['company_id'] = $notice_data['company_id'];
                    }
                    if ($notice_data['review_type'] == 'N' && in_array('N', $statuses)) {
                        $inc_ord_params = array(
                            'status' => array('N'),
                            'cp_em_notice_send' => $notice_data['notice_id'],
                            'cp_em_skip_changed' => true,
                            'include_incompleted' => true
                        );
                        list($inc_orders, ) = fn_get_orders($inc_ord_params, 0, false);
                    }
                    list($orders, $search) = fn_get_orders($ord_params, 0, false);
                    if (!empty($inc_orders)) {
                        $orders = array_merge($orders, $inc_orders);
                    }
                    if (!empty($orders)) {
                        foreach($orders as $order_data) {
                            
                            $user_data = array(
                                'order_id' => !empty($order_data['order_id']) ? $order_data['order_id'] : '',
                                'user_id' => !empty($order_data['user_id']) ? $order_data['user_id'] : '',
                                'firstname' => !empty($order_data['firstname']) ? $order_data['firstname'] : '',
                                'lastname' => !empty($order_data['lastname']) ? $order_data['lastname'] : '',
                                'email' => !empty($order_data['email']) ? $order_data['email'] : '',
                                'lang_code' => !empty($order_data['lang_code']) ? $order_data['lang_code'] : CART_LANGUAGE,
                                'company_id' => ''
                            );
                            if (!empty($addon_settings['time_for_type'])) {
                                $skip_row = db_get_field("SELECT ?:cp_em_feedback_sent.notice_id FROM ?:cp_em_feedback_sent 
                                    LEFT JOIN ?:cp_em_notices ON ?:cp_em_notices.notice_id = ?:cp_em_feedback_sent.notice_id
                                    LEFT JOIN ?:orders ON ?:orders.order_id = ?:cp_em_feedback_sent.order_id
                                    WHERE ?:cp_em_notices.type = ?s AND ?:orders.email = ?s AND ?:cp_em_feedback_sent.timestamp > ?i", $notice_data['type'], $user_data['email'], time() - 60*60*$addon_settings['time_for_type']);
                                    if (!empty($skip_row)) {
                                        continue;
                                    }
                            }
                            $send_notice = fn_cp_em_check_user_subscribe_notice($user_data, $notice_data['type']);
                            if (!empty($send_notice) && $send_notice == 'D') {
                                continue;
                            }
                            $order_info = fn_get_order_info($order_data['order_id']);
                            if (!empty($order_info)) {
                                if ($order_info['status'] == STATUS_INCOMPLETED_ORDER) {
                                    $session_id = db_get_field("SELECT session_id FROM ?:user_session_products WHERE order_id = ?i", $order_info['order_id']);
                                    if (!empty($session_id)) {
                                        $user_data['session_id'] = $session_id;
                                    }
                                }
                                list($company_data, $exist_companies, $user_data) = fn_cp_em_get_company_data_for_notice($user_data, $exist_companies);
                                
                                if ($notice_data['review_type'] == 'T') {
                                    $is_exists_post = false;
                                    $user_data['products'] = array();
                                //check already rate the store, in case 1 post from IP. Check discussion type
                                    if (!empty($order_data['cp_em_ip'])) {
                                        $object_type = $discussion_object_types['E'];
                                        $allow = fn_cp_em_cron_check_allow_post($order_data['cp_em_ip'], $object_type, 0, $order_data['company_id'], 'E', $discussion);
                                        if (empty($allow)) {
                                            continue;
                                        }
                                    }
                                } elseif ($notice_data['review_type'] == 'P' || $notice_data['review_type'] == 'N') {
                                    $user_data['products'] = $order_info['products'];
                                } elseif ($notice_data['review_type'] == 'V') {
                                    if (empty($order_data['company_id'])) {
                                        continue;
                                    } else {
                                        //check already rate the store, in case 1 post from IP. Check discussion type
                                        if (!empty($order_data['cp_em_ip'])) {
                                            $object_type = $discussion_object_types['M'];
                                            $allow = fn_cp_em_cron_check_allow_post($order_data['cp_em_ip'], $object_type, $order_data['company_id'], 0, 'M', $discussion);
                                            if (empty($allow)) {
                                                continue;
                                            }
                                        }
                                    }
                                }
                            //check products
                                if (!empty($user_data['products'])) {
                                    foreach($user_data['products'] as $item_id => &$pr_data) {
                                        //get image
                                        if (empty($pr_data['main_pair'])) {
                                            $pr_data['main_pair'] = fn_get_image_pairs($pr_data['product_id'], 'product', 'M', true, true, $user_data['lang_code']);
                                        }
                                        if ($notice_data['review_type'] != 'N') {
                                            $check_rated = db_get_field("SELECT item_id FROM ?:order_details WHERE item_id = ?i AND order_id = ?i AND cp_em_rated = ?s", $item_id, $user_data['order_id'], 'N');
                                            if (empty($check_rated)) {
                                                unset($user_data['products'][$item_id]);
                                                continue;
                                            }
                                            $object_type = $discussion_object_types['P'];
                                            if (!empty($is_new_reviews)) {
                                                $allow = fn_cp_em_check_allow_new_product_reviews($user_data['user_id'], $pr_data['product_id'], $order_data['cp_em_ip']);
                                            } else {
                                                $allow = fn_cp_em_cron_check_allow_post($order_data['cp_em_ip'], $object_type, $pr_data['product_id'], $order_data['company_id'], 'P', $discussion);
                                            }
                                            if (empty($allow)) {
                                                unset($user_data['products'][$item_id]);
                                                continue;
                                            }
                                        }
                                    }
                                }
                                if ($notice_data['review_type'] == 'P' && empty($user_data['products'])) {
                                    continue;
                                }
                            //seat settings from store if ultimate
                                if (fn_allowed_for("ULTIMATE")) {
                                    list($addon_settings, $exist_settings) = fn_cp_em_get_addon_em_settings($addon_settings, $exist_settings, $order_data['company_id']);
                                }
                                if (!empty($user_data['lang_code']) && !empty($notice_data['all_langs'][$user_data['lang_code']])) {
                                    $notice_data['subject'] = $notice_data['all_langs'][$user_data['lang_code']]['subject'];
                                    $notice_data['message'] = $notice_data['all_langs'][$user_data['lang_code']]['message'];
                                }
                                $user_data = fn_cp_em_get_user_usergroups($user_data);//get usergroups
                                
                                list($send_counter, $result) = fn_cp_em_send_notification($notice_data, $user_data, $is_test = false, $user_data['lang_code'], $company_data, $addon_settings, $send_counter);
//                                 if (!empty($cron_limits) && $cron_limits > 0 && $send_counter >= $cron_limits) {
//                                     return true;
//                                 }
                            }
                        }
                    }
                }
            } elseif ($notice_data['type'] == NoticeTypes::CP_EM_TARGET) {
                $users = array();
                $skip_user_query = false;
                $cr_auth = array(
                    'user_type' => 'A'
                );
                if (in_array($notice_data['action_type'], array('B','R','A'))) {
                    $user_params = array(
                        'status' => 'A',
                        'user_type' => 'C',
                        'cp_em_cron_t' => $notice_data['action_type'],
                        'cp_em_time_from' => $notice_data['send_after'],
                        'cp_em_notice_id' => $notice_data['notice_id']
                    );
                    if ($notice_data['action_type'] == 'B') {
                        $user_params['cp_em_ba'] = $notice_data['before_after'];
                        if ($notice_data['date_field_id'] > 0) {
                            $user_params['cp_birthday_field'] = $notice_data['date_field_id'];
                        }
                    }
                    if ($notice_data['action_type'] == 'A') {
                        $user_params['cp_em_last_login'] = $notice_data['purchase_period'];
                    }
                    if (fn_allowed_for("ULTIMATE")) {
                        $user_params['company_id'] = $notice_data['company_id'];
                    }
                    list($users,) = fn_get_users($user_params, $cr_auth, 0);
                } elseif ($notice_data['action_type'] == 'S' && ((!empty($newsletters) && $newsletters['status'] == 'A') || (!empty($e_market) && $e_market['status'] == 'A'))) {
                    if (!empty($newsletters) && $newsletters['status'] == 'A') {
                        if (!empty($notice_data['list_id'])) {
                            $users = db_get_array(
                                "SELECT ?:subscribers.* FROM ?:subscribers 
                                    LEFT JOIN ?:cp_em_targeted_sent ON ?:cp_em_targeted_sent.email = ?:subscribers.email AND ?:cp_em_targeted_sent.notice_id = ?i
                                    LEFT JOIN ?:user_mailing_lists ON ?:user_mailing_lists.subscriber_id = ?:subscribers.subscriber_id
                                    WHERE ?:subscribers.email != ?s AND ?:user_mailing_lists.timestamp > ?i 
                                        AND ?:user_mailing_lists.timestamp <= ?i AND ?:cp_em_targeted_sent.notice_id IS NULL 
                                        AND ?:user_mailing_lists.list_id = ?i", $notice_data['notice_id'], '', $notice_data['timestamp'], time() - 60*60*$notice_data['send_after'], $notice_data['list_id']
                            );
                        } else {
                            $users = array();
                        }
                    } else {
                        $users = db_get_array(
                            "SELECT ?:em_subscribers.* FROM ?:em_subscribers 
                                LEFT JOIN ?:cp_em_targeted_sent ON ?:cp_em_targeted_sent.email = ?:em_subscribers.email AND ?:cp_em_targeted_sent.notice_id = ?i
                                WHERE ?:em_subscribers.email != ?s AND ?:em_subscribers.timestamp > ?i AND ?:em_subscribers.timestamp <= ?i AND ?:cp_em_targeted_sent.notice_id IS NULL", $notice_data['notice_id'], '', $notice_data['timestamp'], time() - 60*60*$notice_data['send_after']
                        );
                    }
                } elseif ($notice_data['action_type'] == 'F') {
                    if (!empty($notice_data['purchase_period'])) {
                        $all_order_users = db_get_fields("
                            SELECT DISTINCT(?:orders.email) FROM ?:orders
                            LEFT JOIN ?:cp_em_targeted_sent ON ?:cp_em_targeted_sent.email = ?:orders.email AND ?:cp_em_targeted_sent.notice_id = ?i
                            WHERE ?:cp_em_targeted_sent.notice_id IS NULL OR ?:cp_em_targeted_sent.timestamp < ?i", $notice_data['notice_id'], time() - 60*60*24*$notice_data['purchase_period']
                        );
                        if (!empty($all_order_users)) {
                            $users_with_purchases = db_get_fields("
                                SELECT DISTINCT(?:orders.email) FROM ?:orders WHERE ?:orders.timestamp > ?i", time() - 60*60*24*$notice_data['purchase_period']
                            );
                            if (!empty($users_with_purchases)) {
                                $users = array_diff($all_order_users, $users_with_purchases);
                            } else {
                                $users = $all_order_users;
                            }
                        }
                        $from_users_table = db_get_fields("
                            SELECT DISTINCT(?:users.email) FROM ?:users
                            LEFT JOIN ?:cp_em_targeted_sent ON ?:cp_em_targeted_sent.email = ?:users.email AND ?:cp_em_targeted_sent.notice_id = ?i
                            LEFT JOIN ?:orders ON ?:orders.email = ?:users.email
                            WHERE ?:cp_em_targeted_sent.notice_id IS NULL AND ?:orders.order_id IS NULL AND ?:users.user_type = ?s", $notice_data['notice_id'], 'C'
                        );
                        if (!empty($from_users_table)) {
                            $users = array_merge($users, $from_users_table);
                            $users = array_unique($users);
                        }
                    } else {
                        $users = db_get_array("
                            SELECT ?:users.* FROM ?:users
                            LEFT JOIN ?:cp_em_targeted_sent ON ?:cp_em_targeted_sent.email = ?:users.email AND ?:cp_em_targeted_sent.notice_id = ?i
                            LEFT JOIN ?:orders ON ?:orders.email = ?:users.email
                            WHERE ?:cp_em_targeted_sent.notice_id IS NULL AND ?:orders.order_id IS NULL AND ?:users.user_type = ?s", $notice_data['notice_id'], 'C'
                        );
                        $skip_user_query = true;
                    }
                } elseif (!empty($f_builder) && $f_builder['status'] == 'A' && $notice_data['action_type'] == 'K' && !empty($notice_data['page_id'])) {
                    $users = db_get_array("
                            SELECT DISTINCT(?:cp_em_send_form.email) FROM ?:cp_em_send_form
                            LEFT JOIN ?:cp_em_targeted_sent ON ?:cp_em_targeted_sent.email = ?:cp_em_send_form.email AND ?:cp_em_targeted_sent.notice_id = ?i
                            WHERE ?:cp_em_targeted_sent.notice_id IS NULL AND ?:cp_em_send_form.timestamp <= ?i", $notice_data['notice_id'], time() - 60*60*$notice_data['send_after']
                        );
                }
                
                fn_set_hook('cp_em_send_target_notification', $users, $notice_data, $addon_settings, $newsletters, $skip_user_query);
                
                if (!empty($users)) {
                    foreach($users as $user_data) {
                        if (!empty($addon_settings['time_for_type'])) {
                            $skip_row = db_get_field("SELECT ?:cp_em_targeted_sent.notice_id FROM ?:cp_em_targeted_sent 
                                LEFT JOIN ?:cp_em_notices ON ?:cp_em_notices.notice_id = ?:cp_em_targeted_sent.notice_id
                                WHERE ?:cp_em_notices.type = ?s AND ?:cp_em_targeted_sent.email = ?s AND ?:cp_em_targeted_sent.timestamp > ?i", $notice_data['type'], $user_data['email'], time() - 60*60*$addon_settings['time_for_type']);
                                if (!empty($skip_row)) {
                                    continue;
                                }
                        }
                        if (in_array($notice_data['action_type'], array('S','F','K'))) {
                            
                            if (empty($skip_user_query)) {
                                if ($notice_data['action_type'] == 'F') {
                                    $user_data = array(
                                        'email' => $user_data
                                    );
                                }
                                $db_data = db_get_row("SELECT * FROM ?:users WHERE email = ?s", $user_data['email']);
                                if (!empty($db_data)) {
                                    $user_data = array_merge($user_data, $db_data);
                                }
                            }
                            
                            $user_data['firstname'] = !empty($user_data['firstname']) ? $user_data['firstname'] : '';
                            $user_data['lastname'] = !empty($user_data['lastname']) ? $user_data['lastname'] : '';
                            $user_data['user_id'] = !empty($user_data['user_id']) ? $user_data['user_id'] : 0;
                            $user_data['lang_code'] = !empty($user_data['lang_code']) ? $user_data['lang_code'] : CART_LANGUAGE;

                        }
                        $send_notice = fn_cp_em_check_user_subscribe_notice($user_data, $notice_data['type']);
                        if (!empty($send_notice) && $send_notice == 'D') {
                            continue;
                        }
                        $user_data = fn_cp_em_get_user_usergroups($user_data);//get usergroupss
                        list($company_data, $exist_companies, $user_data) = fn_cp_em_get_company_data_for_notice($user_data, $exist_companies);
                        
                        if (fn_allowed_for("ULTIMATE")) {
                            list($addon_settings, $exist_settings) = fn_cp_em_get_addon_em_settings($addon_settings, $exist_settings, $notice_data['company_id']);
                        }
                        if (!empty($user_data['lang_code']) && !empty($notice_data['all_langs'][$user_data['lang_code']])) {
                            $notice_data['subject'] = $notice_data['all_langs'][$user_data['lang_code']]['subject'];
                            $notice_data['message'] = $notice_data['all_langs'][$user_data['lang_code']]['message'];
                        }
                        list($send_counter, $result) = fn_cp_em_send_notification($notice_data, $user_data, $is_test = false, $user_data['lang_code'], $company_data, $addon_settings, $send_counter);
//                         if (!empty($cron_limits) && $cron_limits > 0 && $send_counter >= $cron_limits) {
//                             return true;
//                         }
                    }
                }
            } elseif ($notice_data['type'] == NoticeTypes::CP_EM_AUDIENCE && !empty($notice_data['audience_id'])) {
                $aud_params = array(
                    'check_aud_status' => true,
                    'cp_em_notice_id' => $notice_data['notice_id']
                );
                list($users, ) = fn_cp_em_get_audience_users($notice_data['audience_id'], $aud_params, 0, CART_LANGUAGE);
                if (!empty($users)) {
                    foreach($users as $user_data) {
                        if (!empty($addon_settings['time_for_type'])) {
                            $skip_row = db_get_field("SELECT ?:cp_em_targeted_sent.notice_id FROM ?:cp_em_targeted_sent 
                                LEFT JOIN ?:cp_em_notices ON ?:cp_em_notices.notice_id = ?:cp_em_targeted_sent.notice_id
                                WHERE ?:cp_em_notices.type = ?s AND ?:cp_em_targeted_sent.email = ?s AND ?:cp_em_targeted_sent.timestamp > ?i", $notice_data['type'], $user_data['email'], time() - 60*60*$addon_settings['time_for_type']);
                                if (!empty($skip_row)) {
                                    continue;
                                }
                        }
                        $send_notice = fn_cp_em_check_user_subscribe_notice($user_data, $notice_data['type']);
                        if (!empty($send_notice) && $send_notice == 'D') {
                            continue;
                        }
                        $user_data = fn_cp_em_get_user_usergroups($user_data);//get usergroupss
                        list($company_data, $exist_companies, $user_data) = fn_cp_em_get_company_data_for_notice($user_data, $exist_companies);
                        
                        if (fn_allowed_for("ULTIMATE")) {
                            list($addon_settings, $exist_settings) = fn_cp_em_get_addon_em_settings($addon_settings, $exist_settings, $notice_data['company_id']);
                        }
                        if (!empty($user_data['lang_code']) && !empty($notice_data['all_langs'][$user_data['lang_code']])) {
                            $notice_data['subject'] = $notice_data['all_langs'][$user_data['lang_code']]['subject'];
                            $notice_data['message'] = $notice_data['all_langs'][$user_data['lang_code']]['message'];
                        }
                        list($send_counter, $result) = fn_cp_em_send_notification($notice_data, $user_data, $is_test = false, $user_data['lang_code'], $company_data, $addon_settings, $send_counter);
//                         if (!empty($cron_limits) && $cron_limits > 0 && $send_counter >= $cron_limits) {
//                             return true;
//                         }
                    }
                }
            } elseif ($notice_data['type'] == NoticeTypes::CP_EM_VIEWED) {
                $comp_conditon = '';
                if (fn_allowed_for("ULTIMATE")) {
                    $comp_conditon = db_quote(' AND ?:cp_em_viewed_products.company_id = ?i', $notice_data['company_id']);
                }
                $viewed_data = db_get_hash_multi_array("
                    SELECT ?:cp_em_viewed_products.* FROM ?:cp_em_viewed_products 
                    LEFT JOIN ?:cp_em_viewed_sent ON ?:cp_em_viewed_sent.email = ?:cp_em_viewed_products.email AND ?:cp_em_viewed_sent.notice_id = ?i
                    WHERE ?:cp_em_viewed_products.timestamp < ?i AND (?:cp_em_viewed_sent.notice_id IS NULL OR ?:cp_em_viewed_sent.timestamp < ?i) $comp_conditon ORDER BY ?:cp_em_viewed_products.timestamp DESC", array('email','product_id'), $notice_data['notice_id'], time() - 60*60*$notice_data['send_after'], time() - 24*60*60*$notice_data['purchase_period']);
                if (!empty($viewed_data)) {
                    foreach($viewed_data as $u_email => $v_data) {
                        $user_data = array(
                            'product_ids' => array()
                        );
                        $send_products = db_get_field("SELECT product_ids FROM ?:cp_em_viewed_sent WHERE notice_id = ?i AND email = ?s",  $notice_data['notice_id'], $u_email);
                        if (!empty($send_products)) {
                            $send_products = explode(',',$send_products);
                        } else {
                            $send_products = array();
                        }
                        foreach($v_data as $product) {
                            if (!in_array($product['product_id'], $send_products)) {
                                $user_data['email'] = empty($user_data['email']) ? $product['email'] : $user_data['email'];
                                $user_data['user_id'] = empty($user_data['user_id']) ? $product['user_id'] : $user_data['user_id'];
                                $user_data['company_id'] = empty($user_data['company_id']) ? $product['company_id'] : $user_data['company_id'];
                                $user_data['product_ids'][] = $product['product_id'];
                            }
                        }
                        if (empty($user_data['product_ids'])) {
                            continue;
                        }
                        if (!empty($addon_settings['time_for_type'])) {
                            $skip_row = db_get_field("SELECT ?:cp_em_viewed_sent.notice_id FROM ?:cp_em_viewed_sent 
                                LEFT JOIN ?:cp_em_notices ON ?:cp_em_notices.notice_id = ?:cp_em_viewed_sent.notice_id
                                WHERE ?:cp_em_notices.type = ?s AND ?:cp_em_viewed_sent.email = ?s AND ?:cp_em_viewed_sent.timestamp > ?i", $notice_data['type'], $user_data['email'], time() - 60*60*$addon_settings['time_for_type']);
                                if (!empty($skip_row)) {
                                    continue;
                                }
                        }
                        $send_notice = fn_cp_em_check_user_subscribe_notice($user_data, $notice_data['type']);
                        if (!empty($send_notice) && $send_notice == 'D') {
                            continue;
                        }
                        if (!empty($user_data['user_id'])) {
                            $more_user_data = db_get_row("SELECT firstname, lastname, lang_code FROM ?:users WHERE user_id = ?i", $user_data['user_id']);
                            if (!empty($more_user_data)) {
                                $user_data = array_merge($user_data, $more_user_data);
                            }
                        }
                        $user_data['firstname'] = !empty($user_data['firstname']) ? $user_data['firstname'] : '';
                        $user_data['lastname'] = !empty($user_data['lastname']) ? $user_data['lastname'] : '';
                        $user_data['lang_code'] = !empty($user_data['lang_code']) ? $user_data['lang_code'] : CART_LANGUAGE;
                        
                        $user_data = fn_cp_em_get_user_usergroups($user_data);//get usergroupss
                        list($company_data, $exist_companies, $user_data) = fn_cp_em_get_company_data_for_notice($user_data, $exist_companies);
                        
                        if (fn_allowed_for("ULTIMATE")) {
                            list($addon_settings, $exist_settings) = fn_cp_em_get_addon_em_settings($addon_settings, $exist_settings, $notice_data['company_id']);
                        }
                        if (!empty($user_data['lang_code']) && !empty($notice_data['all_langs'][$user_data['lang_code']])) {
                            $notice_data['subject'] = $notice_data['all_langs'][$user_data['lang_code']]['subject'];
                            $notice_data['message'] = $notice_data['all_langs'][$user_data['lang_code']]['message'];
                        }
                    
                        list($send_counter, $result) = fn_cp_em_send_notification($notice_data, $user_data, $is_test = false, $user_data['lang_code'], $company_data, $addon_settings, $send_counter);
                    }
                }
            }
        }
    }
    return true;
}

function fn_cp_em_send_notification($notice_data, $user_data = array(), $is_test = false, $lang_code = CART_LANGUAGE, $company_data = array(), $addon_settings = array(), $send_counter = 0)
{
    if (!empty($notice_data)) {
        if (empty($is_test) && empty($user_data)) {
            return array($send_counter, false);
        }
        if (!empty($is_test)) {
            if (!empty($notice_data['test_email'])) {
                $user_data = array(
                    'firstname' => 'Test',
                    'lastname' => 'Test',
                    'email' => $notice_data['test_email'],
                    'lang_code' => $lang_code,
                    'company_id' => $notice_data['company_id'],
                    'session_id' => ''
                );
            } else {
                fn_set_notification('E', __('error'), __('cp_em_no_test_email'));
                return array($send_counter, false);
            }
        } else {
            //check usergroups
            if (!empty($user_data['usergroup_ids'])) {
                $allow_usergroups = array_intersect($notice_data['usergroup_ids'], $user_data['usergroup_ids']);
                if (empty($allow_usergroups)) {
                    return array($send_counter, false);
                }
            } else {
                return array($send_counter, false);
            }
        }
        if (!empty($notice_data['is_test']) && $notice_data['is_test'] == 'Y' && !empty($notice_data['test_email'])) {
            $user_data['email'] = $notice_data['test_email'];
        }
        if (empty($user_data['email'])) {
            return array($send_counter, false);
        }
        if (empty($company_data)) {
            if (fn_allowed_for("ULTIMATE") && !empty($user_data['company_id'])) {
                $company_data = fn_get_company_placement_info($user_data['company_id'], $user_data['lang_code']);
            } else {
                $company_data = Registry::get('settings.Company');
                if (empty($company_data)) {
                    $company_data = fn_get_company_placement_info(1, $user_data['lang_code']);
                }
            }
        }
        $address = !empty($company_data['company_address']) ? $company_data['company_address'] : '';
        if (!empty($company_data['company_city'])) {
            $address .= ' ' . $company_data['company_city']; 
        }
        if (!empty($company_data['company_state_descr'])) {
            $address .= ', ' . $company_data['company_state_descr']; 
        }
        if (!empty($company_data['company_zipcode'])) {
            $address .= ', ' . $company_data['company_zipcode']; 
        }
        if (!empty($company_data['company_country_descr'])) {
            $address .= ', ' . $company_data['company_country_descr']; 
        }
        if (!empty($is_test)) {
            $hash = array(
                'is_test' => true
            );
            $hash = md5(implode('|', $hash));
        } else {
            $hash = array(
                'is_test' => false,
                'notice_id' => $notice_data['notice_id'],
                'user_id' => !empty($user_data['user_id']) ? $user_data['user_id'] : 0,
                'email' => !empty($user_data['email']) ? $user_data['email'] : '',
                'val' => rand(1, 999)
            );
            $hash = md5(implode('|', $hash));
        }
        $review_page_link = $wishlist_link = $cart_link = '';
        $store = '';
        $btn_color = '';
        $theme_name = Registry::get('runtime.layout.theme_name');
        $style_id = Registry::get('runtime.layout.style_id');
        if (!empty($style_id) && !empty($theme_name)) {
            $current_style = Styles::factory($theme_name)->get($style_id, array('parse' => true));
            if (!empty($current_style) && !empty($current_style['data']) && !empty($current_style['data']['primary_button'])) {
                $btn_color = $current_style['data']['primary_button'];
            }
        }
        if (empty($btn_color)) {
            $btn_color = '#ff5319';
        }
        if (fn_allowed_for('ULTIMATE') && !empty($notice_data['company_id'])) {
            $store = '&company_id=' . $notice_data['company_id'];
        } elseif (fn_allowed_for('MULTIVENDOR') && !empty($user_data['storefront_id'])) {
            $store = '&storefront_id=' . $user_data['storefront_id'];
        }
        $unsubscribe_link = '<a href="' . fn_url('cp_em_actions.unsubscribe?hash=' . $hash . $store, 'C') . '">' . __('cp_em_unsubscribe_text', array(), $user_data['lang_code']) . '</a>';
        if ($notice_data['type'] == NoticeTypes::CP_EM_ABAND) {
            $cart_link = '<a href="' . fn_url('cp_em_actions.cart?hash=' . $hash . $store, 'C') . '">' . __('cp_em_cart_link_text', array(), $user_data['lang_code']) . '</a>';
        }
        if ($notice_data['type'] == NoticeTypes::CP_EM_WISHLIST) {
            $wishlist_link = '<a href="' . fn_url('cp_em_actions.cart?hash=' . $hash . $store, 'C') . '">' . __('cp_em_wishlist_text', array(), $user_data['lang_code']) . '</a>';
        }
        if ($notice_data['type'] == NoticeTypes::CP_EM_ORDERS_FEED) {

            $review_link = fn_url('cp_em_actions.reaview_all_page?hash=' . $hash . $store, 'C');
            if ($notice_data['review_type'] == 'N') {
                $review_page_name = __('cp_em_continue_txt', array(), !empty($order_info['lang_code']) ? $order_info['lang_code'] : $user_data['lang_code']);
            } else {
                $review_page_name = __('cp_em_page_leave_reviews', array(), !empty($order_info['lang_code']) ? $order_info['lang_code'] : $user_data['lang_code']);
            }
            $review_page_link = '<!--[if mso]>
  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $review_link . '" style="height:34px;v-text-anchor:middle;width:160px;" arcsize="12%" stroke="f" fillcolor="' . $btn_color . '">
    <w:anchorlock/>
    <center style="color:#ffffff;font-family:sans-serif;font-size:13px;font-weight:bold;">' . $review_page_name . '</center>
  </v:roundrect>
<![endif]--><a target="_blank" href="' . $review_link . '"
style="background-color:' . $btn_color . ';border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:34px;text-align:center;text-decoration:none;width:160px;-webkit-text-size-adjust:none;mso-hide:all;">' . $review_page_name . '</a>';
        }
        $store_name = !empty($company_data['company_name']) ? $company_data['company_name'] :  __('cp_em_store_link', array(), $user_data['lang_code']);
        $store_link = '<a href="' . fn_url('?hash=' . $hash . $store, 'C') . '">' . $store_name . '</a>';
        
        $mail_data = array(
            'action' => '',
            'store_link' => $store_link,
            'coupon_code' => '',
            'products_block' => '',
            'customer_firstname' => !empty($user_data['firstname']) ? $user_data['firstname'] : '',
            'customer_lastname' => !empty($user_data['lastname']) ? $user_data['lastname'] : '',
            'company_name' => !empty($company_data['company_name']) ? $company_data['company_name'] : '',
            'company_phone' => !empty($company_data['company_phone']) ? $company_data['company_phone'] : '',
            'company_address' => $address,
            'unsubscribe_link' => !empty($unsubscribe_link) ? $unsubscribe_link : '',
            'cart_link' => !empty($cart_link) ? $cart_link : '',
            'wishlist_link' => !empty($wishlist_link) ? $wishlist_link : '',
            'review_page_link' => !empty($review_page_link) ? $review_page_link : '',
            'order_id' => !empty($user_data['order_id']) ? $user_data['order_id'] : ''
        );
        
        if (version_compare(PRODUCT_VERSION, '4.12.2', '>') && Registry::get('addons.product_reviews.status') == 'A' && $notice_data['review_type'] == 'P') {
            $mail_data['review_page_link'] ='';
        }
        
        list($cust_placeholders, ) = fn_cp_em_get_product_placeholders(array(), 0, $user_data['lang_code']);
        if (!empty($cust_placeholders)) {
            foreach($cust_placeholders as $holder) {
                $mail_data[$holder['placeholder']] = '';
            }
        }
        if (!empty($addon_settings['add_unsubscribe_link']) && $addon_settings['add_unsubscribe_link'] == 'Y') {
            if (strpos($notice_data['message'], '{{ unsubscribe_link }}') === false) {
                $notice_data['message'] .= '<br />' . __('cp_em_unsubscribe_txt', array('[link]' => '{{ unsubscribe_link }}'), $user_data['lang_code']);
            }
        }
        $hid_send_data = $notice_data;
        $pixel_txt = '';
        if (!empty($notice_data['add_pixel']) && $notice_data['add_pixel'] == 'Y' && empty($is_test)) {
            $pixel_txt = '<img src="' . fn_url('cp_em_actions.email_open?hash=' . $hash . $store, 'C') . '&vvvvvv=.gif" width="1px" height="1px" style="position: absolute;” alt="" />';
            $notice_data['message'] .= $pixel_txt;
        }
        //list($cust_placeholders, ) = fn_cp_em_get_product_placeholders(array(), 0, $user_data['lang_code']);
        if (!empty($cust_placeholders)) {
            $mail_data = fn_cp_em_render_custom_placeholder($mail_data, $notice_data['message'], $cust_placeholders, $user_data['lang_code'], $addon_settings, $hash, $store);
        }
        $skip_products = false;
        if ($notice_data['type'] == NoticeTypes::CP_EM_TARGET && $notice_data['action_type'] != 'F') {
            $skip_products = true;
        }
        if ($notice_data['type'] == NoticeTypes::CP_EM_ORDERS_FEED && !in_array($notice_data['review_type'], array('P','N'))) {
            $skip_products = true;
        }
        
        fn_set_hook('cp_em_send_before_products', $skip_products, $notice_data, $mail_data, $user_data, $addon_settings, $hash, $store);
        
        if (!empty($is_test) && empty($skip_products) || ($notice_data['type'] == NoticeTypes::CP_EM_VIEWED && !empty($user_data['product_ids']))) {
            $prod_params = array();
            if (fn_allowed_for('ULTIMATE')) {
                $prev_run_company_id = Registry::get('runtime.company_id');
                Registry::set('runtime.company_id', $notice_data['company_id']);
            }
            if (!empty($is_test)) {
                $prod_params['limit'] = 2;
            } else {
                $prod_params['pid'] = $user_data['product_ids'];
                if (!empty($notice_data['products_limit'])) {
                    $prod_params['limit'] = $notice_data['products_limit'];
                }
            }
            $prod_params['extend'][] = 'product_name';
            $prod_params['extend'][] = 'description';
            if (!empty($addon_settings) && !empty($addon_settings['check_stock_email']) && $addon_settings['check_stock_email'] == 'Y') {
                $prod_params['amount_from'] = 1;
            }
            list($products, ) = fn_get_products($prod_params, 0, $user_data['lang_code']);
            if (empty($products)) {
                return array($send_counter, false);
            }
            $prod_params['get_options'] = false;
            $prod_params['get_features'] = false;
            $prod_params['get_detailed'] = true;
            $prod_params['get_icon'] = true;
            $prod_params['get_discounts'] = true;
            $prod_params['get_additional'] = true;
            $prod_params['detailed_params'] = false;
            
            fn_gather_additional_products_data($products, $prod_params);
            
            if (fn_allowed_for('ULTIMATE')) {
                Registry::set('runtime.company_id', $prev_run_company_id);
            }
            $user_data['products'] = $products;
        }
        if (!empty($user_data['products'])) {
            $img_height = $img_width = !empty($addon_settings['email_image_width']) ? $addon_settings['email_image_width'] : 160;
            foreach($user_data['products'] as &$p_product) {
                $p_product['full_description'] = db_get_field("SELECT full_description FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s", $p_product['product_id'], $user_data['lang_code']);
                $p_product['list_price'] = db_get_field("SELECT list_price FROM ?:products WHERE product_id = ?i", $p_product['product_id']);
            }
            Registry::get('view')->assign('image_width', $img_width);
            Registry::get('view')->assign('image_height', $img_height);
            
            if ($notice_data['type'] == NoticeTypes::CP_EM_ORDERS_FEED && $notice_data['review_type'] != 'N') {
                Registry::get('view')->assign('cp_is_reviews', true);
                
                
                $review_btn = '<!--[if mso]>
  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" cp_em_btn_here style="height:34px;v-text-anchor:middle;width:160px;" arcsize="12%" stroke="f" fillcolor="' . $btn_color . '">
    <w:anchorlock/>
    <center style="color:#ffffff;font-family:sans-serif;font-size:13px;font-weight:bold;">' . __('cp_em_page_leave_review', array(), $user_data['lang_code']) . '</center>
  </v:roundrect>
<![endif]--><a target="_blank" cp_em_btn_here
style="background-color:' . $btn_color . ';border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:34px;text-align:center;text-decoration:none;width:160px;-webkit-text-size-adjust:none;mso-hide:all;">' . __('cp_em_page_leave_review', array(), $user_data['lang_code']) . '</a>';
                Registry::get('view')->assign('cp_review_btn', $review_btn);
            }
            
            Registry::get('view')->assign('products', $user_data['products']);
            Registry::get('view')->assign('link_hash', $hash);
            Registry::get('view')->assign('link_store', $store);
            if (version_compare(PRODUCT_VERSION, '4.12.2', '>') && Registry::get('addons.product_reviews.status') == 'A') {
                Registry::get('view')->assign('is_new_reviews', true);
            }
            $mail_data['products_block'] = Registry::get('view')->fetch('addons/cp_extended_marketing/components/products_block.tpl');
        }
    //check promo
        if (!empty($notice_data['generate_promo']) && $notice_data['generate_promo'] == 'Y') {
            if (!empty($is_test)) {
                $rand_string = fn_generate_code('promo', 12);
                $mail_data['coupon_code'] = 'TEST_'.  $rand_string;
            } elseif ($notice_data['promotion_id']) {
                
                $promotion_data = fn_get_promotion_data($notice_data['promotion_id'], $user_data['lang_code']);
                if (!empty($promotion_data) && $promotion_data['status'] != 'D') {
                    list($_new_condition, $coupon_code) = fn_cp_em_check_and_generate_condition($promotion_data);
                    if (!empty($_new_condition) && !empty($coupon_code)) {
                        $mail_data['coupon_code'] = $coupon_code;
                        $promotion_data['conditions'] = $_new_condition;
                        $update_data = array();
                        $update_data['conditions'] = serialize($promotion_data['conditions']);
                        $update_data['conditions_hash'] = fn_promotion_serialize($promotion_data['conditions']['conditions']);
                        
                        db_query('UPDATE ?:promotions SET ?u WHERE promotion_id=?i', $update_data, $promotion_data['promotion_id']);
                        
                        $code_data = array(
                            'notice_id' => $notice_data['notice_id'],
                            'promotion_id' => $promotion_data['promotion_id'],
                            'coupon_code' => $coupon_code,
                            'generate_time' => time(),
                            'expire_time' => time() + $notice_data['promocode_duration']*24*60*60,
                            'user_id' => !empty($user_data['user_id']) ? $user_data['user_id'] : 0,
                            'email' => $user_data['email']
                        );
                        
                        db_replace_into('cp_em_promocode_expire', $code_data);
                    }
                }
            }
        }
        fn_set_hook('cp_em_send_before_render', $notice_data, $mail_data, $user_data, $addon_settings, $hash, $store);
    //render data for email
        $renderer = Tygh::$app['template.renderer'];
        $rend_template = new Template();
        $rendered_data = array(
            'subject' => '',
            'message' => ''
        );

        if (!empty($is_test)) {
            $placeholders_validation = $renderer->validate($notice_data['message']);

            if (!$placeholders_validation->isSuccess()) {
                $placeholders_validation->showNotifications();  
                $return_url = !empty($_REQUEST['return_url']) ? fn_url($_REQUEST['return_url']) : '';
                
                return array(CONTROLLER_STATUS_REDIRECT, fn_url($return_url));
            }
        }

        foreach($rendered_data as $field => $val) {
            $used_placeholders = $renderer->retrieveVariables($notice_data[$field]);
            if (empty($used_placeholders)){
                $used_placeholders = array();

                foreach ($mail_data as $key => $value) {
                    if (!empty($value)) {
                        $used_placeholders[] = $key; 
                    }
                }
            }
            
            $placeholder_values = fn_cp_em_get_placeholder_values($mail_data, $notice_data['placeholders'], $used_placeholders, $lang_code);
            
            $rend_template->setTemplate($notice_data[$field]);
            $context = new Context($placeholder_values, 'C', $lang_code);
            $collection = new Collection($context->data);
            
            try {
                $content = $renderer->renderTemplate($rend_template, $context, $collection);
                $content = str_replace(array("\n", "\r"), ' ', $content);
            } catch (Exception $e) {
                $content = '';
            }

            if (!empty($content)) {
                $rendered_data[$field] = $content;
            }
        }
        if (!empty($notice_data['hidden_email'])) {
            $h_rend_template = new Template();
            $rendered_data_hid = array(
                'subject' => '',
                'message' => ''
            );
            foreach($rendered_data_hid as $field => $val) {
                $h_used_placeholders = $renderer->retrieveVariables($hid_send_data[$field]);
                if (empty($h_used_placeholders)){
                    $h_used_placeholders = array();
                    foreach ($mail_data as $key => $value) {
                        if (!empty($value)) {
                            $h_used_placeholders[] = $key; 
                        }
                    }
                }
                
                $h_placeholder_values = fn_cp_em_get_placeholder_values($mail_data, $hid_send_data['placeholders'], $h_used_placeholders, $lang_code);
                
                $h_rend_template->setTemplate($hid_send_data[$field]);
                $h_context = new Context($h_placeholder_values, 'C', $lang_code);
                $h_collection = new Collection($context->data);
                try {
                    $h_content = $renderer->renderTemplate($h_rend_template, $h_context, $h_collection);
                    $h_content = str_replace(array("\n", "\r"), ' ', $h_content);
                } catch (Exception $e) {
                    $h_content = '';
                }
                if (!empty($h_content)) {
                    $rendered_data_hid[$field] = $h_content;
                }
            }
        }
        $skip_logs = false;
        $log_time = time();
        
        fn_set_hook('cp_em_send_after_queue', $notice_data, $mail_data, $user_data, $addon_settings, $hash, $log_time, $skip_logs, $is_test);
        
        if (empty($is_test)) {
            $user_array = $user_data;
            $user_array['rendered_data'] = $rendered_data;
            $user_array['hash'] = crc32($hash);
            $put_mail_data = array(
                'notice_id' => $notice_data['notice_id'],
                'user_data' => serialize($user_array),
                'mail_data' => serialize($mail_data),
                'rendered_data_hid' => !empty($rendered_data_hid) ? serialize($rendered_data_hid) : '',
                'timestamp' => time()
            );
            db_replace_into('cp_em_send_queue', $put_mail_data);
            
            if (empty($skip_logs)) {
                if ($notice_data['type'] == NoticeTypes::CP_EM_ABAND || $notice_data['type'] == NoticeTypes::CP_EM_WISHLIST) {
                    $sent_data = array(
                        'notice_id' => $notice_data['notice_id'],
                        'user_id' => !empty($user_data['user_id']) ? $user_data['user_id'] : 0,
                        'email' => !empty($user_data['email']) ? $user_data['email'] : '',
                        'timestamp' => $log_time,
                        'session_id' => !empty($user_data['session_id']) ? $user_data['session_id'] : '',
                        'hash' => crc32($hash),
                        'in_queue' => 'Y',
                    );
                    db_replace_into('cp_em_aband_cart_sent', $sent_data);
                    
                } elseif ($notice_data['type'] == NoticeTypes::CP_EM_ORDERS_FEED) {
                    $sent_data = array(
                        'notice_id' => $notice_data['notice_id'],
                        'order_id' => !empty($user_data['order_id']) ? $user_data['order_id'] : 0,
                        'timestamp' => $log_time,
                        'session_id' => !empty($user_data['session_id']) ? $user_data['session_id'] : '',
                        'hash' => crc32($hash),
                        'in_queue' => 'Y',
                    );
                    db_replace_into('cp_em_feedback_sent', $sent_data);
                    
                } elseif ($notice_data['type'] == NoticeTypes::CP_EM_TARGET || $notice_data['type'] == NoticeTypes::CP_EM_AUDIENCE) {
                    $sent_data = array(
                        'notice_id' => $notice_data['notice_id'],
                        'user_id' => !empty($user_data['user_id']) ? $user_data['user_id'] : 0,
                        'email' => !empty($user_data['email']) ? $user_data['email'] : '',
                        'timestamp' => $log_time,
                        'hash' => crc32($hash),
                        'in_queue' => 'Y',
                    );
                    db_replace_into('cp_em_targeted_sent', $sent_data);
                } elseif ($notice_data['type'] == NoticeTypes::CP_EM_VIEWED) {
                    $send_products = db_get_field("SELECT product_ids FROM ?:cp_em_viewed_sent WHERE notice_id = ?i AND email = ?s",  $notice_data['notice_id'], $user_data['email']);
                    if (!empty($send_products)) {
                        $send_products = explode(',', $send_products);
                    }
                    if (!empty($user_data['products'])) {
                        $ud_products = array_keys($user_data['products']);
                        $send_products = !empty($send_products) ? array_merge($send_products, $ud_products) : $ud_products;
                        $send_products = array_unique($send_products);

                    }
                    if (!empty($send_products)) {
                        $send_products = implode(',',$send_products);
                    }
                    $sent_data = array(
                        'notice_id' => $notice_data['notice_id'],
                        'user_id' => !empty($user_data['user_id']) ? $user_data['user_id'] : 0,
                        'email' => !empty($user_data['email']) ? $user_data['email'] : '',
                        'timestamp' => $log_time,
                        'hash' => crc32($hash),
                        'product_ids' => $send_products,
                        'in_queue' => 'Y',
                    );
                    db_replace_into('cp_em_viewed_sent', $sent_data);
                }
            }
        } else {
            if (!empty($notice_data['company_id'])) {
                $company_id = $notice_data['company_id'];
            } else {
                $company_id = 1;
            }
            if (fn_allowed_for('ULTIMATE')) {
                $addon_settings['smtp_server'] = fn_cp_em_get_settings_val('smtp_server', $company_id, 'cp_extended_marketing');
                $addon_settings['smtp_user'] = fn_cp_em_get_settings_val('smtp_user', $company_id, 'cp_extended_marketing');
                $addon_settings['smtp_pass'] = fn_cp_em_get_settings_val('smtp_pass', $company_id, 'cp_extended_marketing');
                $addon_settings['smtp_crypt'] = fn_cp_em_get_settings_val('smtp_crypt', $company_id, 'cp_extended_marketing');
                $addon_settings['smtp_uath'] = fn_cp_em_get_settings_val('smtp_uath', $company_id, 'cp_extended_marketing');
                $addon_settings['log_mail_send'] = fn_cp_em_get_settings_val('log_mail_send', $company_id, 'cp_extended_marketing');
                $addon_settings['log_test_mail_send'] = fn_cp_em_get_settings_val('log_test_mail_send', $company_id, 'cp_extended_marketing');
            }
            $smtp = array();
            if (!empty($addon_settings['smtp_server']) && !empty($addon_settings['smtp_user']) && !empty($addon_settings['smtp_pass'])) {
                $smtp = array(
                    'mailer_send_from_admin' => 'N',
                    'mailer_send_method' => 'smtp',
                    'mailer_smtp_host' => $addon_settings['smtp_server'],
                    'mailer_smtp_username' => $addon_settings['smtp_user'],
                    'mailer_smtp_password' => $addon_settings['smtp_pass'],
                    'mailer_smtp_ecrypted_connection' => $addon_settings['smtp_crypt'],
                    'mailer_smtp_auth' => $addon_settings['smtp_uath'],
                    'mailer_sendmail_path' => $addon_settings['mailer_sendmail_path']
                );
            }
            $_from = array(
                'email' => !empty($notice_data['send_from']) ? $notice_data['send_from'] : 'company_orders_department',
            );
            if (version_compare(PRODUCT_VERSION, '4.11.5', '>')) {
                $cp_em_notice_data = array(
                    'to_email' => $user_data['email'],
                    'from_email' => $_from,
                    'lang_code' => $user_data['lang_code'],
                    'company_id' => $user_data['company_id'],
                    'subject' => $rendered_data['subject'],
                    'body' => $rendered_data['message'],
                    'reply_to' => !empty($notice_data['reply_to']) ? $notice_data['reply_to'] : '',
                    'smtp' => $smtp
                );
                
                /** @var \Tygh\Notifications\EventDispatcher $event_dispatcher */
                $event_dispatcher = Tygh::$app['event.dispatcher'];
                
                $is_sent = true;
                $mail_exists = false;
                $converted_disp = (array) $event_dispatcher;
                foreach($converted_disp as $ed_key => $ev_vals) {
                    $ev_vals = (array)$ev_vals;
                    if (isset($ev_vals['cp_extended_marketing.cp_em_notice'])) {
                        $mail_exists = true;
                        break;
                    }
                }
                if (!empty($mail_exists)) {
                    try {
                        $event_dispatcher->dispatch('cp_extended_marketing.cp_em_notice', [
                            'cp_em_notice_data' => $cp_em_notice_data
                        ]);
                    } catch (\Exception $e) {
                        $is_sent = false;
                    }
                } else {
                    $is_sent = false;
                }
            } else {
                /** @var \Tygh\Mailer\Mailer $mailer */
                $mailer = Tygh::$app['mailer'];
                $is_sent = $mailer->send(array(
                    'to' => $user_data['email'],
                    'from' => $_from,
                    'reply_to' => !empty($notice_data['reply_to']) ? $notice_data['reply_to'] : '',
                    'data' => array(
                        'subject' => $rendered_data['subject'],
                        'body' => $rendered_data['message'],
                    ),
                    'template_code' => 'cp_em_notice',
                    'tpl' => 'addons/cp_extended_marketing/notification.tpl',
                    'company_id' => $user_data['company_id']
                ), 'C', $user_data['lang_code'], $smtp);
            }
            $send_to = $user_data['email'];
            if (!empty($is_sent)) {
                fn_set_notification('N', __('notice'), __('text_email_sent'));
            }
            $need_log = false;
            if (!empty($addon_settings['log_test_mail_send']) && $addon_settings['log_test_mail_send'] == 'Y') {
                $need_log = true;
            }
            if (!empty($need_log)) {
                //add log row
                $log_data = array(
                    'notice_id' => $notice_data['notice_id'],
                    'session_id' => '',
                    'status' => !empty($is_sent) ? 'S' : 'E',
                    'is_test' => 'Y',
                    'timestamp' => time(),
                    'email' => $send_to,
                    'message' => $rendered_data['message']
                );
                db_replace_into('cp_em_logs', $log_data);
            }
        }
    }
    return array($send_counter, true);
}

function fn_cp_em_cron_send_notifications()
{
    
    $notices_to_send = db_get_fields("SELECT DISTINCT(notice_id) FROM ?:cp_em_send_queue");
    if (!empty($notices_to_send)) {
        
        $addon_settings = Registry::get('addons.cp_extended_marketing');
        
        $addon_settings['mailer_sendmail_path'] = Registry::get('settings.Emails.mailer_sendmail_path');
        $cron_limits = $addon_settings['emails_send_cron'];
        $send_counter = 0;
        if ($cron_limits > 0) {
            $send_per_notice = $cron_limits/count($notices_to_send);
            $send_per_notice= ceil($send_per_notice);
        }
        $companies_em_settings = array();
        foreach($notices_to_send as $notice_id) {
            
            
            $limit = '';
            if (!empty($send_per_notice)) {
                $limit = 'LIMIT ' . $send_per_notice;
            }
            
            $all_mails = db_get_array("SELECT * FROM ?:cp_em_send_queue WHERE notice_id = ?i ORDER BY queue_id ASC $limit",$notice_id);
            if (!empty($all_mails)) {
                $notice_data = fn_cp_em_get_notice_data($notice_id, CART_LANGUAGE);
                if (!empty($notice_data)) {
                    if (!empty($notice_data['company_id'])) {
                        $company_id = $notice_data['company_id'];
                    } else {
                        $company_id = 1;
                    }
                    if (empty($companies_em_settings[$company_id])) {
                        if (fn_allowed_for('ULTIMATE')) {
                            $addon_settings['smtp_server'] = fn_cp_em_get_settings_val('smtp_server', $company_id, 'cp_extended_marketing');
                            $addon_settings['smtp_user'] = fn_cp_em_get_settings_val('smtp_user', $company_id, 'cp_extended_marketing');
                            $addon_settings['smtp_pass'] = fn_cp_em_get_settings_val('smtp_pass', $company_id, 'cp_extended_marketing');
                            $addon_settings['smtp_crypt'] = fn_cp_em_get_settings_val('smtp_crypt', $company_id, 'cp_extended_marketing');
                            $addon_settings['smtp_uath'] = fn_cp_em_get_settings_val('smtp_uath', $company_id, 'cp_extended_marketing');
                            $addon_settings['log_mail_send'] = fn_cp_em_get_settings_val('log_mail_send', $company_id, 'cp_extended_marketing');
                            $addon_settings['log_test_mail_send'] = fn_cp_em_get_settings_val('log_test_mail_send', $company_id, 'cp_extended_marketing');
                            $addon_settings['time_for_type'] = fn_cp_em_get_settings_val('time_for_type', $company_id, 'cp_extended_marketing');
                        }
                        $smtp = array();
                        if (!empty($addon_settings['smtp_server']) && !empty($addon_settings['smtp_user']) && !empty($addon_settings['smtp_pass'])) {
                            $smtp = array(
                                'mailer_send_from_admin' => 'N',
                                'mailer_send_method' => 'smtp',
                                'mailer_smtp_host' => $addon_settings['smtp_server'],
                                'mailer_smtp_username' => $addon_settings['smtp_user'],
                                'mailer_smtp_password' => $addon_settings['smtp_pass'],
                                'mailer_smtp_ecrypted_connection' => $addon_settings['smtp_crypt'],
                                'mailer_smtp_auth' => $addon_settings['smtp_uath'],
                                'mailer_sendmail_path' => $addon_settings['mailer_sendmail_path']
                            );
                        }
                        $addon_settings['smtp'] = $smtp;
                        $companies_em_settings[$company_id] = $addon_settings;
                    } else {
                        $addon_settings = $companies_em_settings[$company_id];
                        $smtp = $addon_settings['smtp'];
                    }
                    
                    foreach($all_mails as $u_data) {
                        if (!empty($u_data['user_data'])) {
                            
                            $user_data = unserialize($u_data['user_data']);

                            if (!filter_var($user_data['email'], FILTER_VALIDATE_EMAIL)) {
                                db_query("DELETE FROM ?:cp_em_send_queue WHERE queue_id = ?i", $u_data['queue_id']);
                                continue;
                            }
                            if ($notice_data['type'] == NoticeTypes::CP_EM_ABAND) {
                                $session_time = db_get_field("SELECT ?:user_session_products.timestamp FROM ?:user_session_products
                                    LEFT JOIN ?:cp_em_aband_cart_sent ON ?:cp_em_aband_cart_sent.session_id = ?:user_session_products.session_id
                                    WHERE ?:cp_em_aband_cart_sent.hash = ?i", $user_data['hash']);
                                $skip_this_row = false;
                                if (!empty($session_time)) {
                                    $is_have_order = db_get_field("SELECT ?:orders.order_id FROM ?:orders WHERE email = ?s AND timestamp >= ?i", $user_data['email'], $session_time);
                                    if (!empty($is_have_order)) {
                                        $skip_this_row = true;
                                    }
                                } else {
                                    $skip_this_row = true;
                                }
                                if (!empty($skip_this_row)) {
                                    db_query("DELETE FROM ?:cp_em_send_queue WHERE queue_id = ?i", $u_data['queue_id']);
                                    db_query("UPDATE ?:cp_em_aband_cart_sent SET in_queue = ?s WHERE hash = ?i", 'N', $user_data['hash']);
                                    continue;
                                }
                            }
                            if (!empty($addon_settings['time_for_type'])) {
                                $skip = db_get_field("SELECT ?:cp_em_logs.log_id FROM ?:cp_em_logs
                                    LEFT JOIN ?:cp_em_notices ON ?:cp_em_notices.notice_id = ?:cp_em_logs.notice_id
                                    WHERE ?:cp_em_notices.type = ?s AND ?:cp_em_logs.timestamp > ?i 
                                        AND (?:cp_em_logs.email = ?s OR ?:cp_em_logs.email LIKE ?l)", $notice_data['type'],  time() - 60*60*$addon_settings['time_for_type'], $user_data['email'], '%' . $user_data['email'] . ',%');
                                if (!empty($skip)) {
                                    continue;
                                }
                            }
                            
                            $rendered_data = $user_data['rendered_data'];
                            $rendered_data_hid = $mail_data = array();
                            $hash = $user_data['hash'];
                            if (!empty($u_data['rendered_data_hid'])) {
                                $rendered_data_hid = unserialize($u_data['rendered_data_hid']);
                            }
                            if (!empty($u_data['mail_data'])) {
                                $mail_data = unserialize($u_data['mail_data']);
                            }
                            
                            $_from = array(
                                'email' => !empty($notice_data['send_from']) ? $notice_data['send_from'] : 'company_orders_department',
                            );
                            if (version_compare(PRODUCT_VERSION, '4.11.5', '>')) {
                                $cp_em_notice_data = array(
                                    'to_email' => $user_data['email'],
                                    'from_email' => $_from,
                                    'lang_code' => $user_data['lang_code'],
                                    'company_id' => $user_data['company_id'],
                                    'subject' => $rendered_data['subject'],
                                    'body' => $rendered_data['message'],
                                    'reply_to' => !empty($notice_data['reply_to']) ? $notice_data['reply_to'] : '',
                                    'smtp' => $smtp
                                );
                                
                                /** @var \Tygh\Notifications\EventDispatcher $event_dispatcher */
                                $event_dispatcher = Tygh::$app['event.dispatcher'];
                                
                                $is_sent = true;
                                $mail_exists = false;
                                $converted_disp = (array) $event_dispatcher;
                                foreach($converted_disp as $ed_key => $ev_vals) {
                                    $ev_vals = (array)$ev_vals;
                                    if (isset($ev_vals['cp_extended_marketing.cp_em_notice'])) {
                                        $mail_exists = true;
                                        break;
                                    }
                                }
                                if (!empty($mail_exists)) {
                                    try {
                                        $event_dispatcher->dispatch('cp_extended_marketing.cp_em_notice', [
                                            'cp_em_notice_data' => $cp_em_notice_data
                                        ]);
                                    } catch (\Exception $e) {
                                        $is_sent = false;
                                    }
                                } else {
                                    $is_sent = false;
                                }
                                
                                if (!empty($is_sent)) {
                                    db_query("DELETE FROM ?:cp_em_send_queue WHERE queue_id = ?i", $u_data['queue_id']);
                                }
                                
                                if (!empty($notice_data['hidden_email']) && !empty($rendered_data_hid)) { // send copy without pixel script
                                
                                    $cp_em_notice_data['to_email'] = $notice_data['hidden_email'];
                                    $cp_em_notice_data['subject'] = $rendered_data_hid['subject'];
                                    $cp_em_notice_data['body'] = $rendered_data_hid['message'];
                                    
                                    if (!empty($mail_exists)) {
                                        $event_dispatcher->dispatch('cp_extended_marketing.cp_em_notice', [
                                            'cp_em_notice_data' => $cp_em_notice_data
                                        ]);
                                    }
                                }
                                
                            } else {
                                /** @var \Tygh\Mailer\Mailer $mailer */
                                $mailer = Tygh::$app['mailer'];
                                $is_sent = $mailer->send(array(
                                    'to' => $user_data['email'],
                                    'from' => $_from,
                                    'reply_to' => !empty($notice_data['reply_to']) ? $notice_data['reply_to'] : '',
                                    'data' => array(
                                        'subject' => $rendered_data['subject'],
                                        'body' => $rendered_data['message'],
                                    ),
                                    'template_code' => 'cp_em_notice',
                                    'tpl' => 'addons/cp_extended_marketing/notification.tpl',
                                    'company_id' => $user_data['company_id']
                                ), 'C', $user_data['lang_code'], $smtp);
                                
                                if (!empty($is_sent)) {
                                    db_query("DELETE FROM ?:cp_em_send_queue WHERE queue_id = ?i", $u_data['queue_id']);
                                }
                                
                                if (!empty($notice_data['hidden_email']) && !empty($rendered_data_hid)) { // send copy without pixel script
                                    /** @var \Tygh\Mailer\Mailer $mailer */
                                    $mailer = Tygh::$app['mailer'];
                                    $mailer->send(array(
                                        'to' => $notice_data['hidden_email'],
                                        'from' => $_from,
                                        'reply_to' => !empty($notice_data['reply_to']) ? $notice_data['reply_to'] : '',
                                        'data' => array(
                                            'subject' => $rendered_data_hid['subject'],
                                            'body' => $rendered_data_hid['message'],
                                        ),
                                        'template_code' => 'cp_em_notice',
                                        'tpl' => 'addons/cp_extended_marketing/notification.tpl',
                                        'company_id' => $user_data['company_id']
                                    ), 'C', $user_data['lang_code'], $smtp);
                                }
                            }
                            $send_to = $user_data['email'];
                            if (!empty($notice_data['hidden_email'])) {
                                $send_to .=  ',' . $notice_data['hidden_email'];
                            }
                            $need_log = false;
                            if (!empty($addon_settings['log_mail_send']) && $addon_settings['log_mail_send'] == 'Y') {
                                $need_log = true;
                            }
                            $log_time = time();
                            if (!empty($need_log)) {
                                //add log row
                                $log_data = array(
                                    'notice_id' => $notice_data['notice_id'],
                                    'session_id' => !empty($user_data['session_id']) ? $user_data['session_id'] : '',
                                    'status' => !empty($is_sent) ? 'S' : 'E',
                                    'is_test' => 'N',
                                    'timestamp' => $log_time,
                                    'email' => $send_to,
                                    'message' => $rendered_data['message']
                                );
                                db_replace_into('cp_em_logs', $log_data);
                            }
                            $skip_logs = false;
                            fn_set_hook('cp_em_send_after_mail', $notice_data, $mail_data, $user_data, $addon_settings, $hash, $log_time, $skip_logs, $is_sent);
                            
                            
                            if (empty($skip_logs) && !empty($is_sent)) {
                                if ($notice_data['type'] == NoticeTypes::CP_EM_ABAND || $notice_data['type'] == NoticeTypes::CP_EM_WISHLIST) {
                                    db_query("UPDATE ?:cp_em_aband_cart_sent SET in_queue = ?s WHERE hash = ?i", 'N', $hash);
                                    
                                } elseif ($notice_data['type'] == NoticeTypes::CP_EM_ORDERS_FEED) {
                                    db_query("UPDATE ?:cp_em_feedback_sent SET in_queue = ?s WHERE hash = ?i", 'N', $hash);
                                    
                                } elseif ($notice_data['type'] == NoticeTypes::CP_EM_TARGET || $notice_data['type'] == NoticeTypes::CP_EM_AUDIENCE) {
                                    db_query("UPDATE ?:cp_em_targeted_sent SET in_queue = ?s WHERE hash = ?i", 'N', $hash);
                                    
                                } elseif ($notice_data['type'] == NoticeTypes::CP_EM_VIEWED) {
                                    db_query("UPDATE ?:cp_em_viewed_sent SET in_queue = ?s WHERE hash = ?i", 'N', $hash);
                                    
                                }
                            }
                            //add statistics
                            $stat_fields = array();
                            if (!empty($is_sent)) {
                                $stat_fields['notices_sent'] = 1;
                                if (isset($user_data['orders_total_sent'])) {
                                    $stat_fields['orders_total_sent'] = $user_data['orders_total_sent'];
                                }
                            }
                            if (!empty($mail_data['coupon_code'])) {
                                $stat_fields['coupons_generated'] = 1;
                            }
                            if (!empty($stat_fields)) {
                                fn_cp_em_update_statistics($notice_data['notice_id'], $stat_fields, '+');
                            }
                        }
                    }
                }
            }
        }
    } else {
        db_query("TRUNCATE TABLE ?:cp_em_send_queue");
    }
    
    return true;
}

function fn_cp_em_render_custom_placeholder($mail_data, $body, $placeholders = array(), $lang_code = CART_LANGUAGE, $addon_settings = array(), $hash = '', $store = '') 
{
    
    $values = array();
    if (!empty($placeholders)) {
        foreach ($placeholders as $placeholder) {
            if (!empty($placeholder['product_ids']) && strpos($body, '{{ ' . $placeholder['placeholder'] . ' }}') !== false) {
                $params['item_ids'] = $placeholder['product_ids'];
                if (!empty($addon_settings) && !empty($addon_settings['check_stock_email']) && $addon_settings['check_stock_email'] == 'Y') {
                    $params['amount_from'] = 1;
                }
                $params['extend'] = array('product_name','description');
                list($products, ) = fn_get_products($params, 0, $lang_code);
                $params['get_options'] = false;
                $params['get_features'] = false;
                $params['get_detailed'] = true;
                $params['get_discounts'] = true;
                $params['get_icon'] = true;
                $params['get_additional'] = true;
                $params['detailed_params'] = false;
                
                fn_gather_additional_products_data($products, $params);

                $img_width = !empty($placeholder['image_width']) ? $placeholder['image_width'] : Registry::get('settings.Thumbnails.product_lists_thumbnail_width');
                $img_height = !empty($placeholder['image_height']) ? $placeholder['image_height'] : Registry::get('settings.Thumbnails.product_lists_thumbnail_height');

                Registry::get('view')->assign('image_width', $img_width);
                Registry::get('view')->assign('image_height', $img_height);
                Registry::get('view')->assign('products', $products);
                Registry::get('view')->assign('link_hash', $hash);
                Registry::get('view')->assign('link_store', $store);
                
                if (version_compare(PRODUCT_VERSION, '4.12.2', '>') && Registry::get('addons.product_reviews.status') == 'A') {
                     Registry::get('view')->assign('is_new_reviews', true);
                }
                $products_block = Registry::get('view')->fetch('addons/cp_extended_marketing/components/products_block.tpl');
                if (!empty($products_block)) {
                    $mail_data[$placeholder['placeholder']] = $products_block;
                }
            }
        }
    }
    
    return $mail_data;
}

function fn_cp_em_check_user_subscribe_notice($user_data, $type)
{
    $status = 'A';
    if (!empty($type)) {
        if (!empty($user_data) && !empty($user_data['user_id'])) { // check if user unsubscribed from notices
            $status = db_get_field("SELECT status FROM ?:cp_em_user_subscriptions WHERE user_id = ?i AND type = ?s", $user_data['user_id'], $type);
        } elseif (!empty($user_data) && !empty($user_data['email'])) {
            $status = db_get_field("SELECT status FROM ?:cp_em_user_subscriptions WHERE user_id = ?i AND type = ?s AND email = ?s", 0, $type, $user_data['email']);
        }
    }
    return $status;
}

function fn_cp_em_get_company_data_for_notice($user_data, $exist_companies)
{
    if (fn_allowed_for("ULTIMATE")) {
        if (empty($user_data['company_id'])) {
            $user_data['company_id'] = fn_get_default_company_id();
        }
        if (!isset($exist_companies[$user_data['company_id']])) {
            $exist_companies[$user_data['company_id']] = fn_get_company_placement_info($user_data['company_id'], $user_data['lang_code']);
        }
        $company_data = $exist_companies[$user_data['company_id']];
    } else {
        $user_data['company_id'] = 0;
        if (isset($exist_companies[$user_data['company_id']])) {
            $company_data = $exist_companies[$user_data['company_id']];
        } else {
            $company_data = $exist_companies[$user_data['company_id']] = Registry::get('settings.Company');
            if (empty($exist_companies[$user_data['company_id']])) {
                $company_data = $exist_companies[$user_data['company_id']] = fn_get_company_placement_info($user_data['company_id'], $user_data['lang_code']);
            }
        }
    }
    return array($company_data, $exist_companies, $user_data);
}

function fn_cp_em_get_placeholder_values($data, $placeholders, $used_placeholders = array(), $lang_code = CART_LANGUAGE)
{
    if (empty($placeholders) || empty($used_placeholders)) {
        return array();
    }
    $placeholder_values = array();
    foreach ($placeholders as $pl_key => $pl_data) {
        if (!empty($used_placeholders) && !in_array($pl_key, $used_placeholders)) {
            continue;
        }

        $field = !empty($pl_data['field']) ? $pl_data['field'] : $pl_key;
        $placeholder_value = !empty($data[$field]) ? $data[$field] : '';
        
        $placeholder_values[$pl_key] = $placeholder_value;
    }
    return $placeholder_values;
}
function fn_cp_em_check_and_generate_condition($promotion_data = array()) {
    
    $coupon_code = '';
    $condition_hash = '';
    if (!empty($promotion_data['conditions']['conditions'])) {
        $condition_hash = explode(';', $promotion_data['conditions_hash']);
    }
    $coupon_code = fn_generate_code('promo', 12);
    if (empty($promotion_data['conditions']['conditions'])) {
        $conditions = array(
            'set' => 'all',
            'set_value' => true
        );
        $conditions['conditions'] = array(
            1 =>  array (
                'operator' => 'in',
                //'condition' => 'coupon_code',
                'condition' => 'coupon_code',
                'value' => $coupon_code
            ),
//             2 =>  array (
//                 'operator' => 'lte',
//                 'condition' => 'number_of_usages',
//                 'value' => '1'
//             ),
        );
    } elseif (!empty($promotion_data['conditions']['conditions'])) {
        foreach ($promotion_data['conditions']['conditions'] as $p_key => &$condition) {
            //if ($condition['condition'] == 'coupon_code') {
            if ($condition['condition'] == 'coupon_code') {
                if (!empty($condition['value'])) {
                    $condition['value'] .= ',' . $coupon_code;
                } else {
                    $condition['value'] = $coupon_code;
                }
            }
        }
        $conditions = $promotion_data['conditions'];
    }
    
    return array($conditions, $coupon_code);
}
function fn_cp_em_get_notices($params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
    );
    $params = array_merge($default_params, $params);
    
    $fields = array (
        "?:cp_em_notices.*",
        "?:cp_em_notice_descriptions.*",
    );
   
    $sortings = array (
        'name' => "?:cp_em_notice_descriptions.name",
        'status' => "?:cp_em_notices.status",
        'type' => "?:cp_em_notices.type"
    );
    
    $condition = $join = $group = '';
    if (fn_allowed_for('ULTIMATE')) {
        $condition .= fn_get_company_condition('?:cp_em_notices.company_id');
    }
    if (!empty($params['notice_id'])) {
        if (!is_array()) {
            $params['notice_id'] = (array) $params['notice_id'];
        }
        $condition .= db_quote(' AND ?:cp_em_notices.notice_id IN (?n)', $params['notice_id']);
    }
    if (!empty($params['active'])) {
        $condition .= db_quote(
            ' AND IF(?:cp_em_notices.from_date, ?:cp_em_notices.from_date <= ?i, 1) AND IF(?:cp_em_notices.to_date, ?:cp_em_notices.to_date >= ?i, 1) AND ?:cp_em_notices.status = ?s',
            TIME,
            TIME,
            'A'
        );
    }
    $join .= db_quote(" LEFT JOIN ?:cp_em_notice_descriptions ON ?:cp_em_notice_descriptions.notice_id = ?:cp_em_notices.notice_id AND ?:cp_em_notice_descriptions.lang_code = ?s", $lang_code);
    
    if (!empty($params['type'])) {
        if (!is_array($params['type'])) {
            $params['type'] = (array) $params['type'];
        }
        $condition .= db_quote(' AND ?:cp_em_notices.type IN (?a)', $params['type']);
    }
    if (!empty($params['action_type'])) {
        if (!is_array($params['action_type'])) {
            $params['action_type'] = (array) $params['action_type'];
        }
        $condition .= db_quote(' AND ?:cp_em_notices.action_type IN (?a)', $params['action_type']);
    }
    $sorting = db_sort($params, $sortings, 'name', 'desc');
    
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_em_notices $join WHERE 1 $condition $group");
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }
    
    $notices = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:cp_em_notices $join WHERE 1 $condition $group $sorting $limit", 'notice_id');
    if (!empty($notices) && !empty($params['get_queue'])) {
        foreach($notices as &$not_data) {
            $not_data['emails_in_queue'] = db_get_row("SELECT COUNT(notice_id) as total, MIN(timestamp) as min_time, MAX(timestamp) as max_time FROM ?:cp_em_send_queue WHERE notice_id = ?i", $not_data['notice_id']);
        }
    }
    return array($notices, $params);
}
function fn_cp_em_update_notice_data($notice_data, $notice_id = 0, $lang_code = CART_LANGUAGE)
{
    if (isset($notice_data['usergroup_ids'])) {
        $notice_data['usergroup_ids'] = empty($notice_data['usergroup_ids']) ? '0' : implode(',', $notice_data['usergroup_ids']);
    }
    $from_date = $notice_data['from_date'];
    $to_date = $notice_data['to_date'];
    $notice_data['from_date'] = !empty($from_date) ? fn_parse_date($from_date) : 0;
    $notice_data['to_date'] = !empty($to_date) ? fn_parse_date($to_date, true) : 0;

    // protection from incorrect date range (special for isergi :))
    if (!empty($notice_data['to_date']) && $notice_data['to_date'] < $notice_data['from_date']) {
        $notice_data['from_date'] = fn_parse_date($to_date);
        $notice_data['to_date'] = fn_parse_date($from_date, true);
    }
    if (isset($notice_data['order_statuses'])) {
        $notice_data['order_statuses'] = implode(',', $notice_data['order_statuses']);
    }
    if (fn_allowed_for('ULTIMATE') && $notice_data['type'] == NoticeTypes::CP_EM_AUDIENCE && !empty($notice_data['audience_id'])) {
        $get_audience_company = db_get_field("SELECT company_id FROM ?:cp_em_audiences WHERE audience_id = ?i", $notice_data['audience_id']);
        if ($get_audience_company != $notice_data['company_id']) {
            fn_set_notification('E', __('error'), __('cp_em_audience_company_shoul_be_the_same'));
            return false;
        }
    }
    if (!empty($notice_id)) {
        $old_status = db_get_field("SELECT status FROM ?:cp_em_notices WHERE notice_id = ?i", $notice_id);
        if ($old_status == 'A' && !empty($notice_data['status']) && $notice_data['status'] == 'D') {
            fn_cp_em_clear_notice_queue($notice_id);
        }
        db_query("UPDATE ?:cp_em_notices SET ?u WHERE notice_id = ?i", $notice_data, $notice_id);

        if (!empty($notice_data['name'])) {
            $notice_data['name'] = trim($notice_data['name']);
        }

        db_query(
            'UPDATE ?:cp_em_notice_descriptions SET ?u WHERE notice_id = ?i AND lang_code = ?s',
            $notice_data, $notice_id, $lang_code
        );
    } else {
        $notice_data['timestamp'] = time();
        $notice_id = db_query("INSERT INTO ?:cp_em_notices ?e", $notice_data);

        if (empty($notice_id)) {
            $notice_id = false;
        }

        $notice_data['notice_id'] =  $notice_id;
        $notice_data['name'] = trim($notice_data['name']);

        foreach (Languages::getAll() as $notice_data['lang_code'] => $_v) {
            db_query("INSERT INTO ?:cp_em_notice_descriptions ?e", $notice_data);
        }
    }
    return $notice_id;
}

function fn_cp_em_get_notice_data($notice_id = 0, $lang_code = CART_LANGIAGE)
{
    $notice_data = array();
    if (!empty($notice_id)) {
        $condition = '';
        if (fn_allowed_for('ULTIMATE')) {
            $condition .= fn_get_company_condition('?:cp_em_notices.company_id');
        }
        $notice_data = db_get_row(
            "SELECT ?:cp_em_notices.*, ?:cp_em_notice_descriptions.* FROM ?:cp_em_notices 
                LEFT JOIN ?:cp_em_notice_descriptions ON ?:cp_em_notice_descriptions.notice_id = ?:cp_em_notices.notice_id AND ?:cp_em_notice_descriptions.lang_code = ?s
                WHERE ?:cp_em_notices.notice_id = ?i $condition", $lang_code, $notice_id
        );
        if (!empty($notice_data) && !empty($notice_data['type']) && $notice_data['type'] == NoticeTypes::CP_EM_ORDERS_FEED) {
            $notice_data['order_statuses'] = explode(',',$notice_data['order_statuses']);
            if (!empty($notice_data['order_statuses'])) {
                $statuses = array();
                foreach($notice_data['order_statuses'] as $key => $value) {
                    $statuses[$value] = $value;
                }
                $notice_data['order_statuses'] = $statuses;
            }
        }
    }
    return $notice_data;
}

function fn_cp_em_get_placeholders($type = 'A')
{
    $placeholders = array();
    $all_placeholders = fn_get_schema('cp_em', 'placeholders');
    $general = $all_placeholders['C'];
    $type_vars = !empty($all_placeholders[$type]) ? $all_placeholders[$type] : array();
    
    $placeholders = array_merge($general, $type_vars);
    list($custom_holders,) = fn_cp_em_get_product_placeholders(array(), 0 ,DESCR_SL);
    if (!empty($custom_holders)) {
        foreach($custom_holders as $c_holder) {
            $placeholders[$c_holder['placeholder']] = array(
                'title' => $c_holder['name'],
                'field' => $c_holder['placeholder']
            );
        }
    }
    
    return $placeholders;
}

function fn_cp_em_delete_notice($notice_ids)
{
    if (!empty($notice_ids)) {
        if (!is_array($notice_ids)) {
            $notice_ids = (array) $notice_ids;
        }
        db_query("DELETE FROM ?:cp_em_notices WHERE notice_id IN (?n)", $notice_ids);
        db_query("DELETE FROM ?:cp_em_notice_descriptions WHERE notice_id IN (?n)", $notice_ids);
        db_query("DELETE FROM ?:cp_em_promocode_expire WHERE notice_id IN (?n)", $notice_ids);
    }
    return true;
}

function fn_cp_extended_marketing_install() 
{
    if (version_compare(PRODUCT_VERSION, '4.9.3', '>')) {
        db_query("UPDATE ?:privileges SET is_view = ?s, group_id = ?s WHERE privilege = ?s", 'Y', 'cp_em_priv_group', 'view_cp_em_notices');
        db_query("UPDATE ?:privileges SET group_id = ?s WHERE privilege = ?s", 'cp_em_priv_group', 'manage_cp_em_notices');
    }
    return true;
}

//LOGS
function fn_cp_em_get_logs($params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
    );
    $params = array_merge($default_params, $params);
    
    $fields = array (
        "?:cp_em_logs.*",
        "?:cp_em_notices.type",
        "?:cp_em_notices.company_id",
        "?:cp_em_notice_descriptions.name"
    );
   
    $sortings = array (
        'status' => "?:cp_em_logs.status",
        'date' => "?:cp_em_logs.timestamp",
        'is_test' => "?:cp_em_logs.is_test",
        'email' => "?:cp_em_logs.email",
        'notice_type' => "?:cp_em_notices.type",
        'notice_name' => "?:cp_em_notice_descriptions.name"
    );
    
    $condition = $join = $group = '';
    if (fn_allowed_for('ULTIMATE')) {
        $condition .= fn_get_company_condition('?:cp_em_notices.company_id');
    }
    if (!empty($params['notice_id'])) {
        if (!is_array($params['notice_id'])) {
            $params['notice_id'] = (array) $params['notice_id'];
        }
        $condition .= db_quote(' AND ?:cp_em_logs.notice_id IN (?n)', $params['notice_id']);
    }

    if (!empty($params['status'])) {
        if (!is_array($params['status'])) {
            $params['status'] = (array) $params['status'];
        }
        $condition .= db_quote(" AND ?:cp_em_logs.status IN (?a)", $params['status']);
    }
    if (!empty($params['p_ids']) && !empty($params['type'])) {
        $arr = (strpos($params['p_ids'], ',') !== false || !is_array($params['p_ids'])) ? explode(',', $params['p_ids']) : $params['p_ids'];
        if ($params['type'] == NoticeTypes::CP_EM_ABAND || $params['type'] == NoticeTypes::CP_EM_WISHLIST) {
            $condition .= db_quote(" AND ?:user_session_products.product_id IN (?n)", $arr);
            $join .= " LEFT JOIN ?:user_session_products ON ?:user_session_products.session_id = ?:cp_em_logs.session_id";
        }
    }
    if (!empty($params['email'])) {
        $trimed_email = trim($params['email']);
        if (!empty($trimed_email)) {
            $condition .= db_quote(" AND ?:cp_em_logs.email LIKE ?l", '%' . $trimed_email . '%');
        }
    }
    if (!empty($params['period']) && $params['period'] != 'A') {
        list($time_from, $time_to) = fn_create_periods($params);
        $condition .= db_quote(" AND (?:cp_em_logs.timestamp >= ?i AND ?:cp_em_logs.timestamp <= ?i)", $time_from, $time_to);
    }
    $join .= db_quote(" LEFT JOIN ?:cp_em_notices ON ?:cp_em_notices.notice_id = ?:cp_em_logs.notice_id");
    $join .= db_quote(" LEFT JOIN ?:cp_em_notice_descriptions ON ?:cp_em_notice_descriptions.notice_id = ?:cp_em_notices.notice_id AND ?:cp_em_notice_descriptions.lang_code = ?s", $lang_code);
    if (!empty($params['type'])) {
        if (!is_array($params['type'])) {
            $params['type'] = (array) $params['type'];
        }
        $condition .= db_quote(' AND ?:cp_em_notices.type IN (?a)', $params['type']);
    }
    $sorting = db_sort($params, $sortings, 'date', 'desc');
    
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_em_logs $join WHERE 1 $condition $group");
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }
    
    $logs = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:cp_em_logs $join WHERE 1 $condition $group $sorting $limit", 'log_id');
    
    if (!empty($logs)) {
        foreach($logs as $l_id => &$l_val) {
            if (!empty($l_val['session_id']) && in_array($l_val['type'], array(NoticeTypes::CP_EM_ABAND,NoticeTypes::CP_EM_WISHLIST))) { // check if abandonned cart is exists
                $l_val['session_id'] = db_get_field("SELECT session_id FROM ?:user_session_products WHERE session_id = ?s", $l_val['session_id']);
            }
        }
    }
    return array($logs, $params);
}

function fn_cp_em_get_log_message($log_id = 0)
{
    $message = '';
    if (!empty($log_id)) {
        $message = db_get_field("SELECT message FROM ?:cp_em_logs WHERE log_id = ?i", $log_id);
        if (!empty($message)) { // remove links from display message
            preg_match_all('#<a(.+?)</a>#is', $message, $arr);
            if (!empty($arr) && !empty($arr[0])) {
                foreach($arr[0] as $link) {
                    if (strpos($link, 'dispatch=cp_em_actions.') !== false) {
                        $for_replace = strip_tags($link);
                        $message = str_replace($link, $for_replace, $message);
                //remove links with hash to avoid fake statistics
                    } elseif (strpos($link, 'hash=') !== false) {
                        preg_match_all('#href="(.+?)"#is', $link, $hash_arr);
                        if (!empty($hash_arr) && !empty($hash_arr[0])) {
                            foreach($hash_arr[0] as $hash_href) {
                                $message = str_replace($hash_href, '', $message);
                            }
                        }
                    }
                }
            }
        }
        if (!empty($message)) { 
            preg_match_all('#<img(.+?)/>#is', $message, $arr);
            if (!empty($arr) && !empty($arr[0])) {
                foreach($arr[0] as $key => $img_track) {
                    if (strpos($img_track, 'dispatch=cp_em_actions.') !== false) { // remove tracking pixel from display message
                        //$for_replace_img = strip_tags($img_track);
                        $for_replace_img = '';
                        $message = str_replace($img_track, $for_replace_img, $message);
                    } else { // remove not exists images
                        preg_match_all('#src="(.+?)"#is', $arr[1][$key], $arr_img);
                        if (!empty($arr_img) && !empty($arr_img[1]) && !empty($arr_img[1][0])) {
                            $explode_path = explode('/images/', $arr_img[1][0]);
                            if (!empty($explode_path) && !empty($explode_path[1])) {
                                if (!Storage::instance('images')->isExist($explode_path[1])) {
                                    $message = str_replace($img_track, '', $message);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $message;
}

function fn_cp_em_clear_logs()
{
    db_query("TRUNCATE TABLE ?:cp_em_logs");
    return true;
}

function fn_cp_em_clear_logs_cron()
{
    $clear_time = Registry::get('addons.cp_extended_marketing.clear_logs_day');
    $clear_time = intval($clear_time);
    if ($clear_time > 0) {
        db_query("DELETE FROM ?:cp_em_logs WHERE timestamp <= ?i", time() - 24*60*60*$clear_time);
    }
    return true;
}

//profile
function fn_cp_em_update_user_subscribtion($user_id, $types, $email = '', $skip_notice = false)
{
    if ((!empty($user_id) || !empty($email)) && !empty($types)) {
        foreach($types as $type => $value) {
            if (in_array($value, array('A','D'))) {
                $data = array(
                    'user_id' => $user_id,
                    'type' => $type,
                    'status' => $value,
                    'email' => $email
                );
                db_replace_into('cp_em_user_subscriptions', $data);
            }
        }
        if (empty($skip_notice)) {
            fn_set_notification('N', __('notice'), __('text_changes_saved'));
        }
    }
    return true;
}

function fn_cp_em_get_user_subscribtion_types()
{
    $notice_types = fn_get_schema('cp_em', 'types');
    
    return $notice_types;
}

function fn_cp_em_get_user_subscribtion($user_id, $email = '')
{
    if (!empty($user_id)) {
        $types = db_get_hash_array("SELECT * FROM ?:cp_em_user_subscriptions WHERE user_id = ?i", 'type', $user_id);
    } elseif (!empty($email)) {
        $types = db_get_hash_array("SELECT * FROM ?:cp_em_user_subscriptions WHERE user_id = ?i AND email = ?s", 'type', 0, $email);
    }
    $notice_types = fn_get_schema('cp_em', 'types');
    
    if (!empty($notice_types)) {
        foreach($notice_types as $n_type) {
            if (empty($types[$n_type['type']])) {
                $types[$n_type['type']] = array(
                    'user_id' => $user_id,
                    'type' => $n_type['type'],
                    'status' => 'A'
                );
            }
        }
    }
    return $types;
}

//cron
function fn_cp_em_cron_run_info()
{
    $admin_ind = Registry::get('config.admin_index');
    $__params = Registry::get('addons.cp_extended_marketing');
    if (!empty($__params) && !empty($__params['cron_pass'])) {
        $cron_pass = $__params['cron_pass'];
    } else {
        $cron_pass = '';
    }
    $hint = '<b>' . __("cp_em_use_this_for_make_queue") . ':</b><br>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_em_notices.build_queue --cron_pass=' . $cron_pass;
    $hint .= '<br /><b>' . __("cp_em_use_this_for_send_notices") . ':</b><br>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_em_notices.cron_send --cron_pass=' . $cron_pass;
    $hint .= '<br /><b>' . __("cp_em_remvoe_expire_coupons_from_promotion") . ' ( <span class="cp-em__red-color">' . __('cp_em_run_after_notices_send') . '</span>):</b><br>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_em_notices.cron_expire_coupons --cron_pass=' . $cron_pass;
    $hint .= '<br /><b>' . __("cp_em_use_this_for_clear_logs") . ':</b><br>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_em_logs.cron_clear --cron_pass=' . $cron_pass;
    
    return $hint;
}

function fn_cp_em_get_session_data_by_hash($hash, $type = '')
{
    $data = array();
    if (!empty($hash)) {
        
        if ($type ==  NoticeTypes::CP_EM_ORDERS_FEED) {
            $table = '?:cp_em_feedback_sent';
        } elseif ($type ==  NoticeTypes::CP_EM_TARGET) {
            $table = '?:cp_em_targeted_sent';
        } elseif (in_array($type, array(NoticeTypes::CP_EM_ABAND, NoticeTypes::CP_EM_WISHLIST))) {
            $table = '?:cp_em_aband_cart_sent';
        } elseif ($type ==  NoticeTypes::CP_EM_VIEWED) {
            $table = '?:cp_em_viewed_sent';
        }
        $check_tables = array('?:cp_em_feedback_sent','?:cp_em_targeted_sent','?:cp_em_aband_cart_sent','?:cp_em_viewed_sent');
        if (empty($table)) {
            foreach($check_tables as $tbl) {
                $data = db_get_row("
                    SELECT $tbl.*, ?:cp_em_notices.type, ?:cp_em_notices.review_type FROM $tbl 
                    LEFT JOIN ?:cp_em_notices ON ?:cp_em_notices.notice_id = $tbl.notice_id
                    WHERE $tbl.hash = ?s OR $tbl.hash = ?i", $hash, crc32($hash)
                );
                if (!empty($data)) {
                    break;
                }
            }
        } else {
            $data = db_get_row("
                SELECT $table.*, ?:cp_em_notices.type, ?:cp_em_notices.review_type FROM $table 
                LEFT JOIN ?:cp_em_notices ON ?:cp_em_notices.notice_id = $table.notice_id
                WHERE $table.hash = ?s OR $table.hash = ?i", $hash, crc32($hash)
            );
        }
        
        fn_set_hook('cp_em_get_session_data', $hash, $data, $type);
        
        if (!empty($data)) {
            if (!empty($data['order_id'])) {
                $data['order_status'] = db_get_field("SELECT status FROM ?:orders WHERE order_id = ?i ", $data['order_id']);
                $data['user_id'] = db_get_field("SELECT user_id FROM ?:orders WHERE order_id = ?i ", $data['order_id']);
            }
            if (!empty($data['user_id'])) {
                $data['user_data'] = db_get_row("SELECT * FROM ?:users WHERE user_id = ?i ", $data['user_id']);
            }
        }
        if (!empty($data['session_id'])) {
            $data['sess_user_id'] = db_get_field("SELECT user_id FROM ?:user_session_products WHERE session_id = ?s", $data['session_id']);
            $data['products'] = db_get_array("SELECT product_id, item_id, amount, extra FROM ?:user_session_products WHERE session_id = ?s", $data['session_id']);
            foreach($data['products'] as &$product) {
                $product = unserialize($product['extra']);
                if (empty($data['user_data']) && !empty($product['user_data'])) {
                    $data['user_data'] = $product['user_data'];
                }
            }
        }
    }
    return $data;
}

function fn_cp_em_email_open_action($hash)
{
    if (!empty($hash)) {
        $tables = array('?:cp_em_aband_cart_sent', '?:cp_em_feedback_sent', '?:cp_em_targeted_sent', '?:cp_em_viewed_sent');
        foreach($tables as $table) {
            $data = db_get_row("SELECT notice_id,is_open FROM $table WHERE hash = ?s OR hash = ?i", $hash, crc32($hash));
            if (!empty($data)) {
                if ($data['is_open'] == 'N') {
                    db_query("UPDATE $table SET is_open = ?s WHERE hash = ?s OR hash = ?i", 'M', $hash, crc32($hash));
                } elseif ($data['is_open'] == 'M') {
                    db_query("UPDATE $table SET is_open = ?s WHERE hash = ?s OR hash = ?i", 'Y', $hash, crc32($hash));
                    $notice_id = $data['notice_id'];
                }
                break;
            }
        }
        
        fn_set_hook('cp_em_open_action', $notice_id, $hash);
        
        if (!empty($notice_id)) {
            fn_cp_em_update_statistics($notice_id, array('email_openings' => 1), '+');
        }
    }
    return true;
}

function fn_cp_em_update_statistics($notice_id, $fields, $sign)
{
    if (!empty($notice_id) && !empty($fields) && !empty($sign)) {
        $notice_data = db_get_row("SELECT * FROM ?:cp_em_notices WHERE notice_id = ?i", $notice_id);
        if (!empty($notice_data)) {
            $company_id = 0;
            if (fn_allowed_for("ULTIMATE")) {
                $company_id = $notice_data['company_id'];
            }
            $fields_name = array_keys($fields);
            if (!empty($fields_name)) {
                $put_data = array(
                    'company_id' => $company_id,
                    'notice_id' => $notice_id,
                    'type' => $notice_data['type'],
                );
                
                $cur_values = db_get_row("SELECT " . implode(',', $fields_name) . " FROM ?:cp_em_statistics WHERE company_id = ?i AND notice_id = ?i", $company_id, $notice_id);
                
                foreach($fields as $name => $val) {
                    if (empty($cur_values) || empty($cur_values[$name])) {
                        $put_data[$name] = $val;
                    } else {
                        if ($sign == '+') {
                            $put_data[$name] = $cur_values[$name] + $val;
                        } else {
                            $put_data[$name] = $cur_values[$name] - $val;
                        }
                    }
                    if ($put_data[$name] < 0) {
                        $put_data[$name] = 0;
                    }
                }
                db_replace_into('cp_em_statistics', $put_data);
            }
        }
    }
    return true;
}

function fn_cp_em_get_statistics($params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
    );
    $params = array_merge($default_params, $params);
    
    $fields = array (
        "?:cp_em_statistics.*",
        "?:cp_em_notice_descriptions.name"
    );
   
    $sortings = array (
        'notice_id' => "?:cp_em_statistics.notice_id",
        'type' => "?:cp_em_statistics.type",
        'orders_placed_total' => "?:cp_em_statistics.orders_placed_total",
        'notices_sent' => "?:cp_em_statistics.notices_sent",
        'orders_placed' => "?:cp_em_statistics.orders_placed",
        'returns_form_email' => "?:cp_em_statistics.returns_form_email",
        'notice_name' => "?:cp_em_notice_descriptions.name",
        'reviews_placed' => "?:cp_em_statistics.reviews_placed"
    );
    
    $condition = $join = $group = '';
    if (fn_allowed_for('ULTIMATE')) {
        $condition .= fn_get_company_condition('?:cp_em_statistics.company_id');
    }
    if (!empty($params['notice_id'])) {
        if (!is_array($params['notice_id'])) {
            $params['notice_id'] = (array) $params['notice_id'];
        }
        $condition .= db_quote(' AND ?:cp_em_statistics.notice_id IN (?n)', $params['notice_id']);
    }
    if (!empty($params['notice'])) {
        $trimed_notice = trim($params['notice']);
        if (!empty($trimed_notice)) {
            $condition .= db_quote(' AND ?:cp_em_notice_descriptions.name LIKE ?l', '%' . $trimed_notice . '%');
        }
    }
    $join .= db_quote(" LEFT JOIN ?:cp_em_notice_descriptions ON ?:cp_em_notice_descriptions.notice_id = ?:cp_em_statistics.notice_id AND ?:cp_em_notice_descriptions.lang_code = ?s", $lang_code);
    if (!empty($params['type'])) {
        if (!is_array($params['type'])) {
            $params['type'] = (array) $params['type'];
        }
        $condition .= db_quote(' AND ?:cp_em_statistics.type IN (?a)', $params['type']);
    }
    $sorting = db_sort($params, $sortings, 'notice_id', 'desc');
    
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_em_statistics $join WHERE 1 $condition $group");
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }
    
    $stats = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:cp_em_statistics $join WHERE 1 $condition $group $sorting $limit", 'notice_id');
    
    return array($stats, $params);
}

function fn_cp_em_get_notice_statistics($notice_id)
{
    $statistics = array();
    if (!empty($notice_id)) {
        $statistics = db_get_row("SELECT * FROM ?:cp_em_statistics WHERE notice_id = ?i", $notice_id);
    }
    return $statistics;
}

function fn_cp_en_check_coupons_new_func(&$promotion, &$cart, $promotion_id = 0)
{
    $values = fn_explode(',', $promotion['value']);
    
    // Check already applied coupons
    if (!empty($cart['coupons'])) {
        $coupons = array_keys($cart['coupons']);
        $coupons = array_map('fn_strtolower', $coupons);

        if ($promotion['operator'] == 'cont') {
            $codes = array();
            foreach ($coupons as $coupon_val) {
                foreach ($values as $cond_val) {
                    $cond_val = fn_strtolower($cond_val);
                    if (stripos($coupon_val, $cond_val) !== false) {
                        $codes[] = $cond_val;
                        if (!empty($cart['pending_coupon']) && $cart['pending_coupon'] == $coupon_val) {
                            $cart['pending_original_coupon'] = $cond_val;
                        }
                    }
                }
            }
        } else {
            $codes = array();

            foreach ($values as $expected_coupon_code) {
                if (in_array(fn_strtolower($expected_coupon_code), $coupons, true)) {
                    $codes[] = $expected_coupon_code;
                }
            }
        }
        if (!empty($codes) && !empty($promotion_id)) {
            foreach ($codes as $_code) {
                $_code = fn_strtolower($_code);
                if (is_array($cart['coupons'][$_code]) && !in_array($promotion_id, $cart['coupons'][$_code])) {
                    $cart['coupons'][$_code][] = $promotion_id;
                }
            }
        }
        if (!empty($promotion_id)) {
            $is_aband = db_get_field("SELECT cp_em_for_notices FROM ?:promotions WHERE promotion_id = ?i", $promotion_id);
            if (!empty($is_aband) && $is_aband == 'Y') {
                //additional check for codes
                if (empty($cart['user_data']) || (!empty($cart['user_data']) && empty($cart['user_data']['email']))) {
                    return array();
                }
                if (!empty($cart['user_data']['email']) && !empty($codes)) {
                    $check_avail_codes = db_get_fields("SELECT coupon_code FROM ?:cp_em_promocode_expire WHERE email = ?s AND expire_time >= ?i AND used = ?i", $cart['user_data']['email'], time(), 0);
                    if (!empty($check_avail_codes)) {
                        foreach($codes as $code_key => $code) {
                            if (!in_array($code, $check_avail_codes)) {
                                unset($codes[$code_key]);
                            }
                        }
                    } else {
                        return array();
                    }
                }
            }
        }
        return $codes;
    }

    return false;
}

function fn_cp_em_get_coupons_list($params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
    );
    $params = array_merge($default_params, $params);
    
    $fields = array (
        "?:cp_em_promocode_expire.*",
        "?:cp_em_notice_descriptions.name",
        "?:promotion_descriptions.name as promo_name",
        "?:cp_em_notices.company_id"
    );
   
    $sortings = array (
        'notice_id' => "?:cp_em_promocode_expire.notice_id",
        'promotion_id' => "?:cp_em_promocode_expire.promotion_id",
        'coupon_code' => "?:cp_em_promocode_expire.coupon_code",
        'generate_time' => "?:cp_em_promocode_expire.generate_time",
        'expire_time' => "?:cp_em_promocode_expire.expire_time",
        'email' => "?:cp_em_promocode_expire.email",
        'used' => "?:cp_em_promocode_expire.used",
        'removed' => "?:cp_em_promocode_expire.removed",
        'order_id' => "?:cp_em_promocode_expire.order_id",
        'notice_name' => "?:cp_em_notice_descriptions.name"
    );
    
    $condition = $join = $group = '';
    if (fn_allowed_for('ULTIMATE')) {
        $condition .= fn_get_company_condition('?:cp_em_notices.company_id');
    }
    if (!empty($params['notice_id'])) {
        if (!is_array($params['notice_id'])) {
            $params['notice_id'] = (array) $params['notice_id'];
        }
        $condition .= db_quote(' AND ?:cp_em_promocode_expire.notice_id IN (?n)', $params['notice_id']);
    }
    if (!empty($params['promotion_id'])) {
        if (!is_array($params['promotion_id'])) {
            $params['promotion_id'] = (array) $params['promotion_id'];
        }
        $condition .= db_quote(' AND ?:cp_em_promocode_expire.promotion_id IN (?n)', $params['promotion_id']);
    }
    if (!empty($params['notice'])) {
        $trimed_notice = trim($params['notice']);
        if (!empty($trimed_notice)) {
            $condition .= db_quote(' AND ?:cp_em_notice_descriptions.name LIKE ?l', '%' . $trimed_notice . '%');
        }
    }
    if (!empty($params['expired']) && $params['expired'] == 'Y') {
        $condition .= db_quote(' AND ?:cp_em_promocode_expire.expire_time < ?i', time());
    }
    if (!empty($params['coupon_code'])) {
        $trimed_code = trim($params['coupon_code']);
        if (!empty($trimed_code)) {
            $condition .= db_quote(' AND ?:cp_em_promocode_expire.coupon_code LIKE ?l', '%' . $trimed_code . '%');
        }
    }
    if (!empty($params['email'])) {
        $trimed_email = trim($params['email']);
        if (!empty($trimed_email)) {
            $condition .= db_quote(' AND ?:cp_em_promocode_expire.email LIKE ?l', '%' . $trimed_email . '%');
        }
    }
    if (!empty($params['order_id'])) {
        if (!is_array($params['order_id'])) {
            $params['order_id'] = (array) $params['order_id'];
        }
        $condition .= db_quote(' AND ?:cp_em_promocode_expire.order_id IN (?n)', $params['order_id']);
    }
    $join .= db_quote(" LEFT JOIN ?:cp_em_notices ON ?:cp_em_notices.notice_id = ?:cp_em_promocode_expire.notice_id");
    $join .= db_quote(" LEFT JOIN ?:cp_em_notice_descriptions ON ?:cp_em_notice_descriptions.notice_id = ?:cp_em_promocode_expire.notice_id AND ?:cp_em_notice_descriptions.lang_code = ?s", $lang_code);
    $join .= db_quote(" LEFT JOIN ?:promotion_descriptions ON ?:promotion_descriptions.promotion_id = ?:cp_em_promocode_expire.promotion_id AND ?:promotion_descriptions.lang_code = ?s", $lang_code);
//     if (!empty($params['type'])) {
//         if (!is_array($params['type'])) {
//             $params['type'] = (array) $params['type'];
//         }
//         $condition .= db_quote(' AND ?:cp_em_promocode_expire.type IN (?a)', $params['type']);
//     }
    $sorting = db_sort($params, $sortings, 'generate_time', 'desc');
    
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_em_promocode_expire $join WHERE 1 $condition $group");
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }
    
    $coupons = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:cp_em_promocode_expire $join WHERE 1 $condition $group $sorting $limit", 'coupon_code');
    
    return array($coupons, $params);
}

function fn_cp_em_get_tpl_id($code = '')
{
    $tpl_id = 0;
    if (!empty($code)) {
        $tpl_id = db_get_field("SELECT template_id FROM ?:template_emails WHERE code = ?s", $code);
    }
    return $tpl_id;
}

//OF
function fn_cp_em_get_order_statuses()
{
    if (version_compare(PRODUCT_VERSION, '4.3.5', '<')) {
        $statuses = db_get_array("
            SELECT ?:statuses.status, ?:status_descriptions.description FROM ?:statuses 
            LEFT JOIN ?:status_descriptions ON ?:statuses.status = ?:status_descriptions.status 
            WHERE ?:status_descriptions.lang_code = ?s AND ?:status_descriptions.type = ?s  AND ?:statuses.type = ?s", DESCR_SL, 'O', 'O'
        );
    }
    else {
        $statuses = db_get_array("
            SELECT ?:statuses.status, ?:status_descriptions.description FROM ?:statuses 
            LEFT JOIN ?:status_descriptions ON ?:statuses.status_id = ?:status_descriptions.status_id 
            WHERE ?:status_descriptions.lang_code = ?s AND ?:statuses.type = ?s", DESCR_SL, 'O'
        );
    }
    $statuses[] = array(
        'status' => 'N',
        'description' => __('incompleted')
    );
    return $statuses;
}

function fn_cp_em_get_data_for_review_page($order_id)
{
    $order_info = array();
    if (!empty($order_id)) {
        $order_info = fn_get_order_info($order_id);
        
        if (!empty($order_info['products'])) {
            $discussion_settings = Registry::get('addons.discussion');
            $pow_reviews = Registry::get('addons.cp_power_reviews');
            $object_name = 'product';
            
            $prod_array = $order_info['products'];
            
            if (fn_allowed_for('ULTIMATE') && !empty($pow_reviews) && $pow_reviews['status'] == 'A') {
                $condition = db_quote(" AND ?:cp_power_ext_reviews.company_id = ?i", $order_info['company_id']);
            } else {
                $condition = '';
            }
            foreach ($prod_array as $k => $v) {
                $check_rated = db_get_field("SELECT item_id FROM ?:order_details WHERE order_id = ?i AND item_id = ?i AND cp_em_rated = ?s", $order_info['order_id'], $k, 'Y');
                if (!empty($check_rated)) {
                    unset($order_info['products'][$k]);
                    unset($prod_array[$k]);
                    continue;
                }
                
                $ip = fn_get_ip();
                $ip_address = fn_ip_to_db($ip['host']);
                
                $n_k = $k;
                
                $order_info['products'][$n_k]['cp_item_id'] = $v['cp_item_id'] = $k;
                if (fn_allowed_for('ULTIMATE')) {
                    $order_info['products'][$n_k]['thread_id'] = $thread_id = db_get_field("SELECT thread_id FROM ?:discussion WHERE object_id = ?i AND object_type = ?s AND company_id = ?i", $v['product_id'], 'P', $order_info['company_id']);
                } else {
                    $order_info['products'][$n_k]['thread_id'] = $thread_id = db_get_field("SELECT thread_id FROM ?:discussion WHERE object_id = ?i AND object_type = ?s", $v['product_id'], 'P');
                }
                if (AREA != 'A' && !empty($discussion_settings[$object_name . '_post_ip_check']) && $discussion_settings[$object_name . '_post_ip_check'] == 'Y') {
                    $is_exists = db_get_field(
                        "SELECT COUNT(*) FROM ?:discussion_posts WHERE thread_id = ?i AND ip_address = ?s",
                        $thread_id, $ip_address);
                    if (!empty($is_exists)) {
                        unset($order_info['products'][$n_k]);
                        continue;
                    }
                }
                if (!empty($order_info['products'][$n_k])) {
                    $order_info['products'][$n_k]['main_pair'] = fn_get_cart_product_icon(
                        $v['product_id'], $order_info['products'][$n_k]
                    );
                    if (fn_allowed_for('ULTIMATE')) {
                        $order_info['products'][$n_k]['discussion_type'] = db_get_field("SELECT type FROM ?:discussion WHERE object_id = ?i AND object_type = ?s AND company_id = ?i", $v['product_id'], 'P', $order_info['company_id']);
                    } else {
                        $order_info['products'][$n_k]['discussion_type'] = db_get_field("SELECT type FROM ?:discussion WHERE object_id = ?i AND object_type = ?s", $v['product_id'], 'P');
                    }
                    if (empty($order_info['products'][$n_k]['discussion_type']) || (!empty($order_info['products'][$n_k]['discussion_type']) && $order_info['products'][$n_k]['discussion_type'] == 'D')) {
                        unset($order_info['products'][$n_k]);
                        continue;
                    }
                    if (!empty($pow_reviews) && $pow_reviews['status'] == 'A') {
                        $order_info['products'][$n_k]['cp_all_prod_attrs'] = db_get_hash_array("SELECT ?:cp_power_rev_products.*, ?:cp_pow_attr_descr.cp_attr_name, ?:cp_power_ext_reviews.* FROM ?:cp_power_rev_products 
                            LEFT JOIN ?:cp_power_ext_reviews ON ?:cp_power_ext_reviews.cp_attr_id = ?:cp_power_rev_products.cp_attr_id 
                            LEFT JOIN ?:cp_pow_attr_descr ON ?:cp_pow_attr_descr.cp_attr_id = ?:cp_power_rev_products.cp_attr_id 
                            WHERE ?:cp_power_rev_products.product_id =?i AND ?:cp_pow_attr_descr.lang_code = ?s AND ?:cp_power_ext_reviews.status = ?s ?p ORDER BY ?:cp_power_rev_products.attr_pos", 'cp_attr_id', $v['product_id'], CART_LANGUAGE, 'A', $condition);
                        $prod_main_cat = db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = ?s", $v['product_id'], 'M');
                        if (!empty($prod_main_cat)) {
                            if (!empty($order_info['products'][$n_k]['cp_all_prod_attrs'])) {
                                $already_get_ids = array_keys($order_info['products'][$n_k]['cp_all_prod_attrs']);
                            } else {
                                $already_get_ids = array();
                            }
                            if (fn_allowed_for('MULTIVENDOR')) {
                                $comp_id = db_get_field("SELECT ?:products.company_id FROM ?:products
                                    LEFT JOIN ?:discussion ON ?:discussion.object_id = ?:products.product_id WHERE ?:discussion.thread_id = ?i", $thread_id);
                                if (!empty($comp_id)) {
                                    $comps = array($comp_id, 0);
                                    $cat_condition = db_quote(" AND ?:cp_power_ext_reviews.company_id IN (?n)", $comps);
                                } else {
                                    $cat_condition = '';
                                }
                            }
                            $cat_prod_attr = db_get_hash_array("SELECT ?:cp_power_rev_cats.*, ?:cp_pow_attr_descr.cp_attr_name, ?:cp_power_ext_reviews.* FROM ?:cp_power_rev_cats 
                                LEFT JOIN ?:cp_power_ext_reviews ON ?:cp_power_ext_reviews.cp_attr_id = ?:cp_power_rev_cats.cp_attr_id 
                                LEFT JOIN ?:cp_pow_attr_descr ON ?:cp_pow_attr_descr.cp_attr_id = ?:cp_power_rev_cats.cp_attr_id 
                                WHERE ?:cp_power_rev_cats.category_id = ?i AND ?:cp_pow_attr_descr.lang_code = ?s AND ?:cp_power_ext_reviews.status = ?s AND ?:cp_power_ext_reviews.cp_attr_id NOT IN (?n) ?p 
                                ORDER BY ?:cp_power_rev_cats.attr_pos", 'cp_attr_id', $prod_main_cat, CART_LANGUAGE, 'A', $already_get_ids, $cat_condition);
                            if (!empty($cat_prod_attr)) {
                                if (!empty($order_info['products'][$n_k]['cp_all_prod_attrs'])) {
                                    $order_info['products'][$n_k]['cp_all_prod_attrs'] = $cat_prod_attr + $order_info['products'][$n_k]['cp_all_prod_attrs'];
                                    uasort($order_info['products'][$n_k]['cp_all_prod_attrs'], "fn_cp_power_reviews_sort_reviews_by_pos");
                                } else {
                                    $order_info['products'][$n_k]['cp_all_prod_attrs'] = $cat_prod_attr;
                                }
                            }
                        }
                    }
                }
            }
            $order_info['cp_tst_array'] = array();
        }
    }
    return $order_info;
}

function fn_cp_em_get_store_review_link($order_id, $review_type)
{
    $link = '';
    $discussion = Registry::get('addons.discussion');
    
    if (!empty($order_id) && !empty($discussion) && $discussion['status'] == 'A' && !empty($review_type)) {
        $discussion_object_types = fn_get_discussion_objects();
        $order_data = db_get_row("SELECT * FROM ?:orders WHERE order_id = ?i", $order_id);
        if (!empty($order_data)) {
            $order_data['ip_address'] = fn_ip_from_db($order_data['ip_address']);
            $allow = false;
            $check_ip = 'Y';
            if ($review_type == 'T') {
                $object_type = $discussion_object_types['E'];
                $type = 'E';
                $object_id = 0;
            } elseif ($review_type == 'V' && fn_allowed_for('MULTIVENDOR')) {
                $object_type = $discussion_object_types['M'];
                $type = 'M';
                $object_id = $order_data['company_id'];
            }
            if (!empty($object_type)) {
                if (fn_allowed_for("ULTIMATE")) {
                    if (!empty($order_data['company_id'])) {
                        $check_ip_test = Settings::instance()->getAllVendorsValues($object_type . '_post_ip_check', 'discussion');
                        if (!empty($check_ip_test) && !empty($check_ip_test[$order_data['company_id']])) {
                            $check_ip = $check_ip_test[$order_data['company_id']];
                        }
                        $thread_data = db_get_row("SELECT thread_id, type FROM ?:discussion WHERE object_type = ?s AND company_id = ?i AND object_id = ?i", $type, $order_data['company_id'], $object_id);
                    }
                } else {
                    if (!empty($discussion[$object_type . '_post_ip_check'])) {
                        $check_ip = $discussion[$object_type . '_post_ip_check'];
                    }
                    $thread_data = db_get_row("SELECT thread_id, type FROM ?:discussion WHERE object_type = ?s AND object_id = ?i", $type, $object_id);
                }
                if (!empty($thread_data) && !empty($thread_data['thread_id']) && !empty($thread_data['type']) && $thread_data['type'] != 'D') {
                    if (!empty($check_ip) && $check_ip === YesNo::YES) {
                        if (!empty($order_data['ip_address'])) {
                            $is_exists_post = db_get_field("SELECT COUNT(*) FROM ?:discussion_posts WHERE thread_id = ?i AND ip_address = ?s", $thread_data['thread_id'], $order_data['ip_address']);
                            if (empty($is_exists_post)) {
                                $allow = true;
                            }
                        }
                    } else {
                        $allow = true;
                    }
                }
            }
            if (!empty($allow) && !empty($thread_data['thread_id'])) {
                if (!empty($type) && $type == 'M' && !empty($order_data['company_id'])) {
                    $link = fn_url('companies.view?company_id=' . $order_data['company_id'] . '&selected_section=discussion#discussion', 'C');
                } else {
                    $link = fn_url('discussion.view?thread_id=' . $thread_data['thread_id'], 'C');
                }
            }
        }
    }
    return $link;
}

function fn_cp_em_set_rated_trigger($order_id, $item_id)
{
    if (!empty($order_id) && !empty($item_id)) {
        db_query("UPDATE ?:order_details SET cp_em_rated = ?s WHERE order_id = ?i AND item_id = ?i", 'Y', $order_id,$item_id);
    }
    return true;
}

function fn_cp_em_get_discussion_type_by_thread($thread_id = 0)
{
    $type = '';
    if (!empty($thread_id)) {
        $type = db_get_field("SELECT type FROM ?:discussion WHERE thread_id = ?i", $thread_id);
    }
    return $type;
}

function fn_cp_em_add_imgs_to_post($post_id)
{
    if (!empty($post_id) && Registry::get('addons.cp_power_reviews.status') == 'A') {
        $pairs_data = fn_attach_image_pairs('cp_review_post', 'cp_rev_post', $post_id, DESCR_SL);
        if (!empty($pairs_data)) {
            if (!is_array($pairs_data)) {
                $pairs_data = array($pairs_data);
            }
            foreach($pairs_data as $kry => $img_pair_id) {
                $data_rev_image = array(
                    'post_image_id' => $img_pair_id,
                    'post_id' => $post_id,
                    'status' => 'A'
                );
                db_query("INSERT INTO ?:cp_review_images ?e", $data_rev_image);
            }
        }
    }
    return true;
}
function fn_cp_em_delete_placeholder($placeholder_id) 
{
    
    db_query('DELETE FROM ?:cp_em_placeholders WHERE placeholder_id = ?i', $placeholder_id);
    db_query('DELETE FROM ?:cp_em_placeholders_descriptions WHERE placeholder_id = ?i', $placeholder_id);
    
    return true;
}

function fn_cp_em_get_placeholder_data($placeholder_id, $lang_code = CART_LANGUAGE) 
{
    $condition = '';
    if (fn_allowed_for('ULTIMATE')) {
        $condition .= fn_get_company_condition('?:cp_em_placeholders.company_id');
    }
    if (!empty($placeholder_id)) {
        $placeholder_data = db_get_row("SELECT ?:cp_em_placeholders.*, ?:cp_em_placeholders_descriptions.* FROM ?:cp_em_placeholders 
            LEFT JOIN ?:cp_em_placeholders_descriptions ON ?:cp_em_placeholders.placeholder_id = ?:cp_em_placeholders_descriptions.placeholder_id 
            WHERE ?:cp_em_placeholders.placeholder_id=?i AND ?:cp_em_placeholders_descriptions.lang_code=?s $condition", $placeholder_id, $lang_code);
    } else {
        $placeholder_data = array();
    }
    
    return $placeholder_data;
}

function fn_cp_em_update_placeholder_data($placeholder_data, $placeholder_id = 0, $lang_code = CART_LANGUAGE) 
{
    //check exists placeholders
    if (!empty($placeholder_data['placeholder'])) {
        $check_exist = db_get_fields("SELECT placeholder_id FROM ?:cp_em_placeholders WHERE placeholder = ?s AND placeholder_id != ?i", $placeholder_data['placeholder'], $placeholder_id);
        if (!empty($check_exist)) {
            fn_set_notification('E', __('error'), __('cp_em_you_already_have_this'));
            return $placeholder_id;
        }
        $all_placeholders = fn_get_schema('cp_em', 'placeholders');
        foreach($all_placeholders as $type => $holders) {
            foreach($holders as $holder_data) {
                if ($placeholder_data['placeholder'] == $holder_data['field']) {
                    fn_set_notification('E', __('error'), __('cp_em_you_already_have_this'));
                    return $placeholder_id;
                }
            }
        }
    }
    
    if (empty($placeholder_id)) {
        $placeholder_id = db_query('INSERT INTO ?:cp_em_placeholders ?e', $placeholder_data);
        
        if (!empty($placeholder_id)) {
            $placeholder_data['placeholder_id'] = $placeholder_id;
            foreach (fn_get_translation_languages() as $placeholder_data['lang_code'] => $v) {
                $_data = array();
                db_query("INSERT INTO ?:cp_em_placeholders_descriptions ?e", $placeholder_data);
                $data['name'] = $placeholder_data['placeholder'];
                $data['value'] = $placeholder_data['name'];
                $_data[] = $data;
                fn_update_lang_var($_data, $placeholder_data['lang_code']);
            }
      
        }
    } else {
        db_query('UPDATE ?:cp_em_placeholders SET ?u WHERE placeholder_id=?i', $placeholder_data, $placeholder_id);
        db_query('UPDATE ?:cp_em_placeholders_descriptions SET ?u WHERE placeholder_id=?i AND lang_code=?s', $placeholder_data, $placeholder_id, $lang_code);
        $_data = array();
        $data['name'] = $placeholder_data['placeholder'];
        $data['value'] = $placeholder_data['name'];
        $_data[] = $data;
        fn_update_lang_var($_data, $lang_code);
    }
    
    return $placeholder_id;
    
}

function fn_cp_em_get_product_placeholders($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE) 
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    $sortings = array(
        'placeholder' => '?:cp_em_placeholders.placeholder',
        'name' => '?:cp_em_placeholders_descriptions.name',
    );

    $condition = $limit = '';

    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }

    $sorting = db_sort($params, $sortings, 'name', 'asc');
    
    if (fn_allowed_for('ULTIMATE')) {
        $condition .= fn_get_company_condition('?:cp_em_placeholders.company_id');
    }
    if (!empty($params['item_ids'])) {
        $condition .= db_quote(' AND ?:cp_em_placeholders.placeholder_id IN (?n)', explode(',', $params['item_ids']));
    }

    $fields = array (
        '?:cp_em_placeholders.*',
        '?:cp_em_placeholders_descriptions.*'  
    );

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_em_placeholders WHERE 1 ?p", $condition);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $placeholders = db_get_array(
        "SELECT ?p FROM ?:cp_em_placeholders 
        LEFT JOIN ?:cp_em_placeholders_descriptions ON ?:cp_em_placeholders_descriptions.placeholder_id = ?:cp_em_placeholders.placeholder_id AND ?:cp_em_placeholders_descriptions.lang_code = ?s 
        WHERE 1 ?p ?p ?p", implode(", ", $fields), $lang_code, $condition, $sorting, $limit
    );
    
    return array($placeholders, $params);
}

function fn_cp_em_get_audiences($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE) 
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    $sortings = array(
        'audience_id' => '?:cp_em_audiences.audience_id',
        'status' => '?:cp_em_audiences.status',
        'name' => '?:cp_em_audiences_descriptions.name',
    );

    $condition = $limit = '';

    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }
    if (fn_allowed_for('ULTIMATE')) {
        $condition .= fn_get_company_condition('?:cp_em_audiences.company_id');
    }
    $sorting = db_sort($params, $sortings, 'name', 'asc');
 
    if (!empty($params['item_ids'])) {
        $condition .= db_quote(' AND ?:cp_em_audiences.audience_id IN (?n)', explode(',', $params['item_ids']));
    }

    $fields = array (
        '?:cp_em_audiences.*',
        '?:cp_em_audiences_descriptions.*'  
    );

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_em_audiences WHERE 1 ?p", $condition);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $audiences = db_get_array(
        "SELECT ?p FROM ?:cp_em_audiences 
        LEFT JOIN ?:cp_em_audiences_descriptions ON ?:cp_em_audiences_descriptions.audience_id = ?:cp_em_audiences.audience_id AND ?:cp_em_audiences_descriptions.lang_code = ?s 
        WHERE 1 ?p ?p ?p", implode(", ", $fields), $lang_code, $condition, $sorting, $limit
    );
    
    return array($audiences, $params);
}

function fn_cp_em_get_audience_data($audience_id, $lang_code = CART_LANGUAGE)
{
    $data = array();
    if (!empty($audience_id)) {
        $condition = '';
        if (fn_allowed_for('ULTIMATE')) {
            $condition .= fn_get_company_condition('?:cp_em_audiences.company_id');
        }
        $data = db_get_row("SELECT ?:cp_em_audiences.*, ?:cp_em_audiences_descriptions.* FROM ?:cp_em_audiences 
            LEFT JOIN ?:cp_em_audiences_descriptions ON ?:cp_em_audiences.audience_id = ?:cp_em_audiences_descriptions.audience_id 
            WHERE ?:cp_em_audiences.audience_id = ?i AND ?:cp_em_audiences_descriptions.lang_code = ?s  $condition", $audience_id, $lang_code);
        if (!empty($data)) {
            $folder_path = defined('CP_EM_CSV_FOLDER') ? CP_EM_CSV_FOLDER : 'var/cp_em_audiences';
            $folder_path .= '/' . $audience_id;
            $files = array();
            if (file_exists($folder_path)) {
                $handle = opendir($folder_path);
                while (false !== ($entry = readdir($handle))) {
                    if (filetype($folder_path . '/' . $entry) == 'file') {
                        $files[] = $folder_path . '/' . $entry;
                    }
                
                }
                if (!empty($files)) {
                    $data['files'] = $files;
                }
            }
        }
    }

    return $data;
}

function fn_cp_em_get_audience_viewed_users($audience_id, $params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $audience_data = array();
    if (!empty($audience_id)) {
        $audience_data = fn_cp_em_get_audience_data($audience_id, $lang_code);
        if (!empty($params['check_aud_status']) && (empty($audience_data) || (!empty($audience_data['status']) && $audience_data['status'] != 'A'))) {
            return array(array(), $params, array());
        }
        if (!empty($audience_data)) {
            $params = array_merge(unserialize($audience_data['params']), $params);
        }
    } else {
        $folder_path = defined('CP_EM_CSV_FOLDER') ? CP_EM_CSV_FOLDER : 'var/cp_em_audiences';
        $files = array();
        if (file_exists($folder_path)) {
            $handle = opendir($folder_path);
            while (false !== ($entry = readdir($handle))) {
                if (filetype($folder_path . '/' . $entry) == 'file') {
                    $files[] = $folder_path . '/' . $entry;
                }
            
            }
            if (!empty($files)) {
                $audience_data['files'] = $files;
            }
        }
    }
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);
    
    $fields = array(
        "?:cp_em_viewed_products.user_id",
        "?:cp_em_viewed_products.email",
    );
    $sortings = array(
        'email' => "?:cp_em_viewed_products.email",
    );
    if (fn_allowed_for("ULTIMATE")) {
        if (!empty($audience_data['company_id'])) {
            $condition['company_id'] = db_quote(" AND ?:cp_em_viewed_products.company_id  = ?i", $audience_data['company_id']);
        } elseif (!empty($params['company_id'])) {
            $condition['company_id'] = db_quote(" AND ?:cp_em_viewed_products.company_id  = ?i", $params['company_id']);
        }
    }
    $condition = $compact_fields = array();
    $join = $group = $having = $group_by = '';
    $group .= " GROUP BY ?:cp_em_viewed_products.email";
    
    if (!empty($params['cid'])) {
        $cids = is_array($params['cid']) ? $params['cid'] : explode(',', $params['cid']);
        
        $_ids = db_get_fields(
            "SELECT a.category_id"."
                FROM ?:categories as a"."
                LEFT JOIN ?:categories as b"."
                ON b.category_id IN (?n)"."
                WHERE a.id_path LIKE CONCAT(b.id_path, '/%')",
            $cids
        );

        $cids = fn_array_merge($cids, $_ids, false);
        $condition['cid'] = db_quote(" AND ?:categories.category_id IN (?n)", $cids);
        $join .= db_quote(" LEFT JOIN ?:products_categories ON ?:products_categories.product_id = ?:cp_em_viewed_products.product_id");
        $join .= db_quote(" LEFT JOIN ?:categories ON ?:categories.category_id = ?:products_categories.category_id");
    }   
    

    $sorting = db_sort($params, $sortings, 'email', 'asc');
    if (!empty($params['p_ids'])) {
        $p_ids = array();
        foreach($params['p_ids'] as $itm_id => $pr_data) {
            $p_ids[] = $pr_data['product_id'];
        }
        if (!empty($params['prod_oper'])) {
            if ($params['prod_oper'] == 'AND' || $params['prod_oper'] == 'OR') {
            
                $condition['order_product_id'] = db_quote(" AND ?:cp_em_viewed_products.product_id IN (?n)", $p_ids);
                if ($params['prod_oper'] == 'AND') {
                    $total_ids = count($p_ids);
                    $having = db_quote(" HAVING COUNT(DISTINCT ?:cp_em_viewed_products.product_id) = ?i", $total_ids);
                    $group_by = ' GROUP BY ?:cp_em_viewed_products.email ';
                }
            } else {
                $more_cond = $condition;
                $more_cond['order_product_id'] = db_quote(" AND ?:cp_em_viewed_products.product_id IN (?n)", $p_ids);
                $not_this_users = db_get_fields("SELECT DISTINCT(?:cp_em_viewed_products.email) FROM ?:cp_em_viewed_products $join WHERE 1" . implode('', $more_cond) . " $sorting ");
                if (!empty($not_this_users)) {
                    $condition['not_this_orders'] = db_quote(" AND ?:cp_em_viewed_products.email NOT IN (?a)", $not_this_users);
                }
                $condition['order_product_id'] = db_quote(" AND ?:cp_em_viewed_products.product_id NOT IN (?n)", $p_ids);
            }
        } else {
            $condition['order_product_id'] = db_quote(" AND ?:cp_em_viewed_products.product_id IN (?n)", $p_ids);
        }

    }
    $limit = '';
    if (!empty($params['items_per_page'])) {
    
        $params['total_items'] = db_get_field("SELECT COUNT(DISTINCT(?:cp_em_viewed_products.email)) FROM ?:cp_em_viewed_products $join WHERE 1 ". implode(' ', $condition) . $group_by . $having);        
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        
    }    
    
    $notice_cond = '';
    if (!empty($params['cp_em_notice_id'])) {
        $join .= " LEFT JOIN ?:cp_em_targeted_sent ON ?:cp_em_targeted_sent.email = ?:cp_em_viewed_products.email AND ?:cp_em_targeted_sent.notice_id = " . $params['cp_em_notice_id'];
        $notice_cond = ' AND ?:cp_em_targeted_sent.notice_id IS NULL';
    }
    
    $_users = db_get_hash_array("SELECT " . implode(', ', $fields) . " FROM ?:cp_em_viewed_products $join WHERE 1 $notice_cond " . implode('', $condition) . $group_by . $having . " $sorting ", 'email');
    if (!empty($params['get_all_data']) && !empty($_users)) {
        foreach($_users as &$user_data) {
            $more = db_get_row("SELECT ?:user_profiles.*, ?:users.email, ?:users.lang_code FROM ?:users 
                LEFT JOIN ?:user_profiles ON ?:user_profiles.user_id = ?:users.user_id
                WHERE ?:users.email = ?s", $user_data['email']);
            if (!empty($more)) {
                $user_data = $more;
            } else {
               // $export_fields = fn_cp_em_get_custome_export_fields(!empty($params['type']) ? $params['type'] : 'O');
            }
        }
    }
    return array($_users, $params, $audience_data);
}

function fn_cp_em_get_audience_users($audience_id, $params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{  
    $audience_data = array();
    if (!empty($audience_id)) {
        $audience_data = fn_cp_em_get_audience_data($audience_id, $lang_code);
        if (!empty($params['check_aud_status']) && (empty($audience_data) || (!empty($audience_data['status']) && $audience_data['status'] != 'A'))) {
            return array(array(), $params, array());
        }
        if (!empty($audience_data)) {
            $params = array_merge(unserialize($audience_data['params']), $params);
        }
    } else {
        $folder_path = defined('CP_EM_CSV_FOLDER') ? CP_EM_CSV_FOLDER : 'var/cp_em_audiences';
        $files = array();
        if (file_exists($folder_path)) {
            $handle = opendir($folder_path);
            while (false !== ($entry = readdir($handle))) {
                if (filetype($folder_path . '/' . $entry) == 'file') {
                    $files[] = $folder_path . '/' . $entry;
                }
            
            }
            if (!empty($files)) {
                $audience_data['files'] = $files;
            }
        }
    }

    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);
    
    $fields = array(
        "?:orders.user_id",
        "?:orders.order_id",
        "?:orders.b_firstname",
        "?:orders.b_lastname",
        "?:orders.email",
        "?:orders.lang_code",
    );
    if (!empty($params['get_export_fields'])) {
        $export_fields = fn_cp_em_get_custome_export_fields();
        $export_fields = array_keys($export_fields);
        
        foreach($export_fields as $export_field) {
                $fields[] = "?:orders." . $export_field;
        }
    }
    $sortings = array(
        'email' => "?:orders.email",
        'name' => array("?:orders.b_lastname", "?:orders.b_firstname"),
    );

    $condition = $compact_fields = array();
    $join = $group = $having = $group_by = '';

    $group .= " GROUP BY ?:orders.email";
    
    if (isset($params['orders_total_from']) && fn_is_numeric($params['orders_total_from'])) {
        $condition['orders_total_from'] = db_quote(" AND ?:orders.total >= ?d", fn_convert_price($params['orders_total_from']));
    }

    if (!empty($params['orders_total_to']) && fn_is_numeric($params['orders_total_to'])) {
        $condition['orders_total_to'] = db_quote(" AND ?:orders.total <= ?d", fn_convert_price($params['orders_total_to']));
    }

    if (!empty($params['period']) && $params['period'] != 'A') {
        list($params['time_from'], $params['time_to']) = fn_create_periods($params);

        $condition['period'] = db_quote(" AND (?:orders.timestamp >= ?i AND ?:orders.timestamp <= ?i)", $params['time_from'], $params['time_to']);
    }
   
    if (!empty($params['country'])) {
        $condition['country'] = db_quote(" AND (?:user_profiles.b_country LIKE ?l OR ?:user_profiles.s_country LIKE ?l)", "%$params[country]%", "%$params[country]%");
    }
    if (isset($params['state']) && fn_string_not_empty($params['state'])) {
        $condition['state'] = db_quote(" AND (?:user_profiles.b_state LIKE ?l OR ?:user_profiles.s_state LIKE ?l)", "%".trim($params['state'])."%", "%".trim($params['state'])."%");
    }
     if (isset($params['city']) && fn_string_not_empty($params['city'])) {
        $condition['city'] = db_quote(" AND (?:user_profiles.b_city LIKE ?l OR ?:user_profiles.s_city LIKE ?l)", "%".trim($params['city'])."%", "%".trim($params['city'])."%");
    }

    
    if (!empty($params['status'])) {
        $condition['status'] = db_quote(' AND ?:orders.status IN (?a)', $params['status']);
    } else {
        $statuses = fn_get_simple_statuses(STATUSES_ORDER);
        foreach ($statuses as $status => $status_name) {
            $status_id = db_get_field("SELECT status_id FROM ?:statuses WHERE status = ?s AND type = ?s", $status, 'O');
            $inventory = db_get_field("SELECT value FROM ?:status_data WHERE status_id = ?i AND param = ?s", $status_id, 'inventory');
            if ($inventory == 'D') {
                $params['status'][] = $status;
            }
        }
    }
    
    if (isset($params['usergroup_id']) && $params['usergroup_id'] != ALL_USERGROUPS) {
        if (!empty($params['usergroup_id']) && $params['usergroup_id'] == 1) {
            $condition['usergroup_links'] = db_quote(' AND ?:orders.user_id = ?i', 0);
        } elseif(!empty($params['usergroup_id']) && $params['usergroup_id'] == 2) {
            $condition['usergroup_links'] = db_quote(' AND ?:orders.user_id > ?i', 0);
        } elseif(!empty($params['usergroup_id']) && $params['usergroup_id'] > 2) {
            $join .= db_quote(" LEFT JOIN ?:usergroup_links ON ?:usergroup_links.user_id = ?:orders.user_id AND ?:usergroup_links.usergroup_id = ?i", $params['usergroup_id']);
            $condition['usergroup_links'] = " AND ?:usergroup_links.status = 'A'";
        }
    }
    
    if (!empty($params['cid'])) {
        $cids = is_array($params['cid']) ? $params['cid'] : explode(',', $params['cid']);
        
        $_ids = db_get_fields(
            "SELECT a.category_id"."
                FROM ?:categories as a"."
                LEFT JOIN ?:categories as b"."
                ON b.category_id IN (?n)"."
                WHERE a.id_path LIKE CONCAT(b.id_path, '/%')",
            $cids
        );

        $cids = fn_array_merge($cids, $_ids, false);
        $condition['cid'] = db_quote(" AND ?:categories.category_id IN (?n)", $cids);
    }
    
    $join .= db_quote(" LEFT JOIN ?:users ON ?:users.user_id = ?:orders.user_id");
    $join .= db_quote(" LEFT JOIN ?:order_details ON ?:order_details.order_id = ?:orders.order_id");
    $join .= db_quote(" LEFT JOIN ?:user_profiles ON ?:user_profiles.user_id = ?:users.user_id");
    $join .= db_quote(" LEFT JOIN ?:companies ON ?:companies.company_id = ?:users.company_id");
    $join .= db_quote(" LEFT JOIN ?:products_categories ON ?:products_categories.product_id = ?:order_details.product_id");
    $join .= db_quote(" LEFT JOIN ?:categories ON ?:categories.category_id = ?:products_categories.category_id");

    $sorting = db_sort($params, $sortings, 'name', 'asc');
    if (!empty($params['p_ids'])) {
        $p_ids = array();
        foreach($params['p_ids'] as $itm_id => $pr_data) {
            $p_ids[] = $pr_data['product_id'];
        }
        if (!empty($params['prod_oper'])) {
            if ($params['prod_oper'] == 'AND' || $params['prod_oper'] == 'OR') {
            
                $condition['order_product_id'] = db_quote(" AND ?:order_details.product_id IN (?n)", $p_ids);
                if ($params['prod_oper'] == 'AND') {
                    $total_ids = count($p_ids);
                    $having = db_quote(" HAVING COUNT(DISTINCT ?:order_details.product_id) = ?i", $total_ids);
                    $group_by = ' GROUP BY ?:orders.order_id ';
                }
            } else {
                $more_cond = $condition;
                $more_cond['order_product_id'] = db_quote(" AND ?:order_details.product_id IN (?n)", $p_ids);
                $not_this_orders = db_get_fields("SELECT DISTINCT(?:orders.email) FROM ?:orders $join WHERE 1" . implode('', $more_cond) . " $sorting ");
                if (!empty($not_this_orders)) {
                    $condition['not_this_orders'] = db_quote(" AND ?:orders.email NOT IN (?a)", $not_this_orders);
                }
                $condition['order_product_id'] = db_quote(" AND ?:order_details.product_id NOT IN (?n)", $p_ids);
            }
        } else {
            $condition['order_product_id'] = db_quote(" AND ?:order_details.product_id IN (?n)", $p_ids);
        }

    }
    if (fn_allowed_for("ULTIMATE")) {
        if (!empty($audience_data['company_id'])) {
            $condition['company_id'] = db_quote(" AND ?:orders.company_id  = ?i", $audience_data['company_id']);
        } elseif (!empty($params['company_id'])) {
            $condition['company_id'] = db_quote(" AND ?:orders.company_id  = ?i", $params['company_id']);
        }
    }
    $limit = '';
    if (!empty($params['items_per_page'])) {
    
        $params['total_items'] = db_get_field("SELECT COUNT(DISTINCT(?:orders.email)) FROM ?:orders $join WHERE 1 ". implode(' ', $condition) . $group_by . $having);        
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        
    }
    
    $users = db_get_hash_array("SELECT " . implode(', ', $fields) . " FROM ?:orders $join WHERE 1" . implode('', $condition) . $group_by . $having . " $sorting ", 'order_id');
    
    if (!empty($params['p_ids'])) {
        foreach ($params['p_ids'] as $product_data) {
            if (!empty($product_data['product_options'])) {
                foreach ($users as $order_id => $user_data) {
                    if (!empty($product_data['amount'])) {
                        $product_amount = $product_data['amount'];
                    }  
                    
                    if (!empty($product_data['price'])) {
                        $product_price = $product_data['price'];
                    } 
                    if (!empty($product_amount) && !empty($product_price)) {
                        $order_data = db_get_row("SELECT extra, order_id FROM ?:order_details WHERE order_id = ?i AND product_id = ?i AND amount = ?i AND price = ?i", $order_id, $product_data['product_id'], $product_amount, $product_price);
                    } elseif (!empty($product_amount) && empty($product_price)) {
                        $order_data = db_get_row("SELECT extra, order_id FROM ?:order_details WHERE order_id = ?i AND product_id = ?i AND amount = ?i", $order_id, $product_data['product_id'], $product_amount);
                    } elseif (empty($product_amount) && !empty($product_price)) {
                        $order_data = db_get_row("SELECT extra, order_id FROM ?:order_details WHERE order_id = ?i AND product_id = ?i AND price = ?i", $order_id, $product_data['product_id'], $product_price);
                    } else{
                        $order_data = db_get_row("SELECT extra, order_id FROM ?:order_details WHERE order_id = ?i AND product_id = ?i", $order_id, $product_data['product_id']);
                    }
                    
                    if (!empty($order_data['extra'])) {
                        $order_extra = unserialize($order_data['extra']);
                        if (!empty($order_extra['product_options'])) {
                            $product_options = $order_extra['product_options'];
                            ksort($product_options);
                            ksort($product_data['product_options']);
                            $result = array_diff($product_options, $product_data['product_options']);
                            if ($result != array()) {
                                unset($users[$order_id]);
                            }
                        }
                    }
                }
            }
        }
    }
    
    if (!empty($users) && !empty($params['last_period']) && $params['last_period'] != 'A') {
        list($params['last_time_from'], $params['last_time_to']) = fn_cp_ca_create_periods($params);
        foreach ($users as $order_id => $user) {
            $order_ids = db_get_fields("SELECT order_id FROM ?:orders WHERE user_id = ?i ORDER BY order_id DESC", $user['user_id']);
       
            if (!empty($order_ids) && !db_get_field("SELECT order_id FROM ?:orders WHERE order_id = ?i AND (?:orders.timestamp >= ?i AND ?:orders.timestamp <= ?i)", $order_ids[0], $params['last_time_from'], $params['last_time_to'])) {
                unset($users[$order_id]);
            }
        }
    }
    $notice_cond = '';
    if (!empty($params['cp_em_notice_id'])) {
        $join .= " LEFT JOIN ?:cp_em_targeted_sent ON ?:cp_em_targeted_sent.email = ?:orders.email AND ?:cp_em_targeted_sent.notice_id = " . $params['cp_em_notice_id'];
        $notice_cond = ' AND ?:cp_em_targeted_sent.notice_id IS NULL';
        $fields[] = "?:orders.lang_code";
        $fields[] = "?:orders.company_id";
        $fields[] = "?:orders.firstname";
        $fields[] = "?:orders.lastname";
    }
    
    $_users = db_get_hash_array("SELECT " . implode(', ', $fields) . " FROM ?:orders $join WHERE ?:orders.order_id IN (?n) $notice_cond $group $sorting $limit", 'email', array_keys($users));
   
    return array($_users, $params, $audience_data);
}

function fn_cp_em_get_custome_export_fields($type = 'O')
{   
    $export_fields = array(
        "user_id" => __("cp_em_user_id_txt"),
        "email" => __("email"),
        "b_firstname" => __("cp_em_b_firstname"),
        "b_lastname" => __("cp_em_b_last_name"),
        "b_address" => __("cp_em_b_address"),
        "b_address_2" => __("cp_em_b_address_2"),
        "b_city" => __("cp_em_b_city"),
        "b_state" => __("cp_em_b_state"),
        "b_country" => __("cp_em_b_country"),
        "b_zipcode" => __("cp_em_b_zipcode"),
        "s_firstname" => __("cp_em_s_first_name"),
        "s_lastname" => __("cp_em_s_last_name"),
        "s_address" => __("cp_em_s_address"),
        "s_address_2" => __("cp_em_s_address_2"),
        "s_city" => __("cp_em_s_city"),
        "s_state" => __("cp_em_s_state"),
        "s_country" => __("cp_em_s_country"),
        "s_zipcode" => __("cp_em_s_zipcode"),
    );
    if ($type == 'O') {
        $export_fields['order_id'] = __("cp_em_order_id_txt");
        $export_fields['timestamp'] = __("order_date");
    }
    
    return $export_fields;
}

function fn_cp_em_update_audience_data($name = '', $params = array(), $audience_id = 0, $lang_code = CART_LANGUAGE) 
{
    $trimed_name = trim($name);
    if (!empty($trimed_name)) {
        $unset_params = array('security_hash','dispatch','name','company_id');
        $company_id = 0;
        if (isset($params['company_id'])) {
            $company_id = $params['company_id'];
        }
        foreach($unset_params as $param_name) {
            if (isset($params[$param_name])) {
                unset($params[$param_name]);
            }
        }
        $save_data = array(
            //'audience_id' => !empty($audience_id) ? $audience_id : 0,
            'params' => serialize($params),
            'name' => $trimed_name,
            'lang_code' => $lang_code,
            'company_id' => $company_id
        );
        if (empty($audience_id)) {
            $audience_id = db_query('INSERT INTO ?:cp_em_audiences ?e', $save_data);
            
            if (!empty($audience_id)) {
                $save_data['audience_id'] = $audience_id;
                foreach (Languages::getAll() as $save_data['lang_code'] => $_v) {
                    db_query("INSERT INTO ?:cp_em_audiences_descriptions ?e", $save_data);
                }
            }
        } else {
            db_query('UPDATE ?:cp_em_audiences SET ?u WHERE audience_id = ?i', $save_data, $audience_id);
            db_query('UPDATE ?:cp_em_audiences_descriptions SET ?u WHERE audience_id = ?i AND lang_code = ?s', $save_data, $audience_id, $lang_code);
        }
    }
    return $audience_id;
    
}

function fn_cp_em_delete_audience($audience_id)
{
    if (!empty($audience_id)) {
        db_query("DELETE FROM ?:cp_em_audiences WHERE audience_id = ?i", $audience_id);
        db_query("DELETE FROM ?:cp_em_audiences_descriptions WHERE audience_id = ?i", $audience_id);
    }
    return true;
}

function fn_cp_em_export_to_csv($users, $export_fields, $export_delimiter, $audience_id = 0)
{
    if ($export_delimiter == 'S') {
        $_export_delimiter = ";";
    } elseif ($export_delimiter == 'C') {
        $_export_delimiter = ",";
    } elseif ($export_delimiter == "T") {
        $_export_delimiter = "\t";
    } else {
        $_export_delimiter = ",";
    }  
    
    $folder_path = defined('CP_EM_CSV_FOLDER') ? CP_EM_CSV_FOLDER : 'var/cp_em_audiences';
    if (!empty($audience_id)) {
        $folder_path .= '/' . $audience_id;
    }
    if (!file_exists($folder_path)) {
        fn_mkdir($folder_path);
    }
    $titles = $export_fields;
    
    if (count($users) == 0) {
        return null;
    }
    
    ob_start();
    $date = date("d-m-Y--H_i");
    $df = fopen($folder_path . "/custom_audience_" . $date . ".csv", "w");
    
    fputcsv($df, $titles, $_export_delimiter);
    
    $default_export_fields = fn_cp_em_get_custome_export_fields();
    $default_export_fields = array_keys($default_export_fields);
    $non_exportable_fields = array_diff($default_export_fields, $export_fields);
    
    foreach ($users as $user) {
        unset($user['firstname']);
        unset($user['lastname']);
        $put_data = array();
        foreach($export_fields as $exp_field) {
            $put_data[$exp_field] = isset($user[$exp_field]) ? $user[$exp_field] : '';
        }
        fputcsv($df, $put_data, $_export_delimiter);
//         foreach($non_exportable_fields as $non_exportable_field) {
//             unset($user[$non_exportable_field]);
//         }
//         fputcsv($df, $user, $_export_delimiter);
    }
    
    fclose($df);
    ob_get_clean();
    
    return $folder_path . "/custom_audience_" . $date . ".csv"; 
}

function fn_cp_em_delete_audience_files($audience_id, $filename = '')
{
    $folder_path = defined('CP_EM_CSV_FOLDER') ? CP_EM_CSV_FOLDER : 'var/cp_em_audiences';
    if (!empty($audience_id)) {
        $folder_path .= '/' . $audience_id;
        if (file_exists($folder_path)) {
            fn_rm($folder_path);
        }
    } else {
        if (file_exists($folder_path)) {
            $handle = opendir($folder_path);
            while (false !== ($entry = readdir($handle))) {
                if (filetype($folder_path . '/' . $entry) == 'file') {
                    fn_rm($folder_path . '/' . $entry);
                }
            
            }
        }
    }
    return true;
}

function fn_cp_em_export_to_newsletters($subscriber_data, $subscriber_id = 0)
{
    $invalid_emails = array();

    if (empty($subscriber_data['list_ids'])) {
        $subscriber_data['list_ids'] = array();
    }
    if (empty($subscriber_data['mailing_lists'])) {
        $subscriber_data['mailing_lists'] = array();
    }

    $subscriber_data['list_ids'] = array_filter($subscriber_data['list_ids']);
    $subscriber_data['mailing_lists'] = array_filter($subscriber_data['mailing_lists']);
    $result = '';
    if (empty($subscriber_id)) {
        if (!empty($subscriber_data['email'])) {
            if ($existing_subscriber_id = fn_get_subscriber_id_by_email($subscriber_data['email'])) {
                $existing_subscriptions = db_get_fields("SELECT list_id FROM ?:user_mailing_lists WHERE subscriber_id = ?i", $existing_subscriber_id);
                $subscriber_id = $existing_subscriber_id;

                $can_continue = true;
                $reason = '';
                if (empty($subscriber_data['list_ids'])) {
                    // adding new subscriber
                    $can_continue = false;
                    $result = 'error';
                } elseif (array_intersect($subscriber_data['list_ids'], $existing_subscriptions)) {
                    // adding subscriber into list
                    $can_continue = false;
                    $result = 'exists';
                }
                if (!$can_continue) {
                    return $result;
                }
                $subscriber_data['list_ids'] = array_unique(array_merge($existing_subscriptions, $subscriber_data['list_ids']));

            } else {
                if (fn_validate_email($subscriber_data['email']) == false) {
                    $invalid_emails[] = $subscriber_data['email'];
                } else {
                    $subscriber_data['timestamp'] = TIME;
                    $subscriber_id = db_query("INSERT INTO ?:subscribers ?e", $subscriber_data);
                }
            }
        }
    } else {
        db_query("UPDATE ?:subscribers SET ?u WHERE subscriber_id = ?i", $subscriber_data, $subscriber_id);
    }

    fn_cp_em_update_subscriptions_own($subscriber_id, $subscriber_data['list_ids'], isset($subscriber_data['confirmed']) ? $subscriber_data['confirmed'] : $subscriber_data['mailing_lists'], array('C' => false,'A' => false, 'V' => false), $subscriber_data['lang_code']);

    if (!empty($invalid_emails)) {
        fn_set_notification('E', __('error'), __('error_invalid_emails', array(
            '[emails]' => implode(', ', $invalid_emails)
        )));
    }
    $result = 'added';
    return $result;
}

function fn_cp_em_update_subscriptions_own($subscriber_id, $user_list_ids = array(), $confirmed = NULL, $force_notification = array(), $lang_code = CART_LANGUAGE)
{
    $subscription_succeed = false;
    $subscriber = array();

    if (!empty($user_list_ids)) {
        list($lists) = fn_get_mailing_lists();
        $subscriber = db_get_row("SELECT * FROM ?:subscribers WHERE subscriber_id = ?i", $subscriber_id);

        $all_lists = fn_array_column($lists, 'list_id');

        foreach ($user_list_ids as $list_id) {
            $subscribed = db_get_array("SELECT confirmed FROM ?:user_mailing_lists WHERE subscriber_id = ?i AND list_id = ?i", $subscriber_id, $list_id);

            $already_confirmed = !empty($subscribed['confirmed']) ? true : false;
            $already_subscribed = !empty($subscribed) ? true : false;

            if ($already_confirmed) {
                $_confirmed = 1;
            } else {
                if (is_array($confirmed)) {
                    $_confirmed = !empty($confirmed[$list_id]['confirmed']) ? $confirmed[$list_id]['confirmed'] : 0;
                } else {
                    $_confirmed = !empty($lists[$list_id]['register_autoresponder']) ? 0 : 1;
                }
            }

            if ($already_subscribed && $already_confirmed == $_confirmed) {
                continue;
            }

            $_data = array(
                'subscriber_id' => $subscriber_id,
                'list_id' => $list_id,
                'activation_key' => md5(uniqid(rand())),
                'unsubscribe_key' => md5(uniqid(rand())),
                'email' => $subscriber['email'],
                'timestamp' => TIME,
                'confirmed' => $_confirmed,
            );

            $subscription_succeed = true;

            db_replace_into('user_mailing_lists', $_data);

            // send confirmation email for each mailing list
            if (empty($_confirmed)) {
                fn_send_confirmation_email($subscriber_id, $list_id, $subscriber['email'], $lang_code);
            }
        }
    }

    $params = array(
        'subscribed' => $subscription_succeed,
    );

    fn_set_hook('newsletters_update_subscriptions_post', $subscriber_id, $user_list_ids, $subscriber, $params);
}

function fn_cp_em_export_to_mailchimp($users)
{   
    $company_id = Registry::get('runtime.company_id');
    if ($company_id == 0) {
        $company_id = 1;
    }
    if (!empty($users)) {
        foreach ($users as $user) {
            if (!empty($user['b_firstname']) && empty($user['b_lastname'])) {
                $user_name = $user['b_firstname'];
            } elseif(empty($user['b_firstname']) && !empty($user['b_lastname'])) {
                $user_name = $user['b_lastname'];
            } elseif (!empty($user['b_firstname']) && !empty($user['b_lastname'])) {
                $user_name = $user['b_firstname'] . ' ' . $user['b_lastname'];
            } else {
                $user_name = '';
            }
          
            $subscriber_data = array(
                'email' => $user['email'],
                'name' => $user_name,
                'lang_code' => DESCR_SL
            );
            
            $subscriber_id = db_get_field("SELECT subscriber_id FROM ?:em_subscribers WHERE email = ?s AND company_id = ?i", $user['email'], $company_id);
            
            $result = fn_em_update_subscriber($subscriber_data, $subscriber_id);
            
            return $result;
        }
    } else {
        return false;
    }
}

function fn_cp_em_export_to_unisender($users)
{   
    $notify = true;
    $api_key = Registry::get('addons.rus_unisender.api_key');
    $list_name = Registry::get('addons.rus_unisender.list_name');

    if (empty($list_name) && $notify) {
        fn_set_notification('E', __('notice'), __('addons.rus_unisender.users_not_added_list'));

        return false;
    }

    if (empty($api_key) && $notify) {
        fn_set_notification('E', __('notice'), __('addons.rus_unisender.users_not_added_key'));

        return false;
    }

    $post = array(
        'api_key' => $api_key,
        'double_optin' => '1'
    );

    $post['field_names'] = fn_unisender_get_export_fields();
    $post['data'] = fn_cp_em_uniseder_get_export_users_fields($users);

    if (fn_unisender_api('importContacts', $post, $response, $notify)) {
        if ($notify) {
            fn_set_notification('N', __('notice'), __('addons.rus_unisender.users_added'));
            if (!empty($response['invalid'])) {
                foreach ($response['log'] as $log) {
                    fn_set_notification('W', __('notice'), $post['data'][$log['index']][0] . ": " . $log['message']);
                }
            }
        }

        return true;
    }

    return false;
}

function fn_cp_em_uniseder_get_export_users_fields($users)
{
    $list_name = Registry::get('addons.rus_unisender.list_name');
    $user_field = fn_unisender_get_user_fields();

    $data = array();

    foreach ($users as $user_id => $user_data) {
        $data[$user_id] = fn_uniseder_get_fields($user_data, $user_field, $list_name);
        $data[$user_id] = array_values($data[$user_id]);
    }

    return array_values($data);
}

function fn_cp_em_add_view_product($product_id)
{
    if (!empty($product_id)) {
        if (empty(Tygh::$app['session']['viewed_products']) || !in_array($_REQUEST['product_id'], Tygh::$app['session']['viewed_products'])) {
            $session = &Tygh::$app['session'];
            $email = '';
            $user_id = 0;
            if (!empty($session['cart']) && !empty($session['cart']['user_data']) && !empty($session['cart']['user_data']['email'])) {
                $email = $session['cart']['user_data']['email'];
            }
            if (!empty($session['auth']) && !empty($session['auth']['user_id'])) {
                $user_id = $session['auth']['user_id'];
            }
            if (!empty($email) || !empty($user_id)) {
                if (empty(Tygh::$app['session']['viewed_products'])) {
                    Tygh::$app['session']['viewed_products'] = array();
                }
                array_unshift(Tygh::$app['session']['viewed_products'], $_REQUEST['product_id']);
                $user_id = Tygh::$app['session']['auth']['user_id'];
                if (!empty($user_id) && empty($email)) {
                    $email = db_get_field("SELECT email FROM ?:users WHERE user_id = ?i", $user_id);
                }
                $data = array(
                    'user_id' => $user_id,
                    'email' => $email,
                    'product_id' => $product_id,
                    'timestamp' => time(),
                    'company_id' => Registry::get('runtime.company_id')
                );
                db_replace_into('cp_em_viewed_products', $data);
            }
        }
    }
    return true;
}

function fn_cp_em_get_settings_val($val, $company_id, $addon) {
    if (!empty($val) && !empty($company_id) && !empty($addon)) {
        $comp_vals = Settings::instance()->getAllVendorsValues($val, $addon);
        if (!empty($comp_vals) && !empty($comp_vals[$company_id])) {
            return $comp_vals[$company_id];
        }
    }
    return false;
}

function fn_cp_em_clear_notice_queue($notice_id)
{
    if (!empty($notice_id)) {
        $type = db_get_field("SELECT type FROM ?:cp_em_notices WHERE notice_id = ?i", $notice_id);
        db_query("DELETE FROM ?:cp_em_send_queue WHERE notice_id = ?i", $notice_id);
        if ($type ==  NoticeTypes::CP_EM_ABAND || $type ==  NoticeTypes::CP_EM_WISHLIST) {
            db_query("DELETE FROM ?:cp_em_aband_cart_sent WHERE notice_id = ?i AND in_queue = ?s", $notice_id, 'Y');
            
        } elseif ($type ==  NoticeTypes::CP_EM_TARGET || $type ==  NoticeTypes::CP_EM_AUDIENCE) {
            db_query("DELETE FROM ?:cp_em_targeted_sent WHERE notice_id = ?i AND in_queue = ?s", $notice_id, 'Y');
            
        } elseif ($type ==  NoticeTypes::CP_EM_VIEWED) {
            db_query("DELETE FROM ?:cp_em_viewed_sent WHERE notice_id = ?i AND in_queue = ?s", $notice_id, 'Y');
            
        } elseif ($type ==  NoticeTypes::CP_EM_ORDERS_FEED) {
            db_query("DELETE FROM ?:cp_em_feedback_sent WHERE notice_id = ?i AND in_queue = ?s", $notice_id, 'Y');
        }
        
        fn_set_hook('cp_em_remove_from_queue', $notice_id, $type);
        
        fn_set_notification('W', __('warning'), __('cp_em_notice_removed_from_queue'));
    }
    return true;
}