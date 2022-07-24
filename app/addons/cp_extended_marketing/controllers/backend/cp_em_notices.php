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
use Tygh\Mailer\Message;
use Tygh\Mailer\MessageStyleFormatter;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (version_compare(PRODUCT_VERSION, '4.11.5', '>')) {
    Tygh::$app['view']->assign('need_tooltip_tpl', true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    fn_trusted_vars (
        'notice_data'
    );
    $suffix = '.manage';
    if ($mode == 'update') {
        $notice_id = null;
        if (!empty($_REQUEST['notice_data'])) {
            $notice_id = fn_cp_em_update_notice_data($_REQUEST['notice_data'], $_REQUEST['notice_id'], DESCR_SL);
            if ($notice_id === false) {
                // Some error occurred
                fn_save_post_data('notice_data');

                return array(CONTROLLER_STATUS_REDIRECT, !empty($_REQUEST['notice_id']) ? 'cp_em_notices.update?notice_id=' . $_REQUEST['notice_id'] : 'cp_em_notices.add?type=' . $_REQUEST['notice_data']['type']);
            }
            $suffix = ".update?notice_id=" . $notice_id;
        }
    }
    if ($mode == 'delete') {
        if (!empty($_REQUEST['notice_id'])) {
            fn_cp_em_delete_notice($_REQUEST['notice_id']);
        }
    }
    if ($mode == 'send_test') {
        if (!empty($_REQUEST['notice_id'])) {
            $notice_data = fn_cp_em_get_notice_data($_REQUEST['notice_id'], DESCR_SL);
            if (!empty($notice_data)) {
                $notice_data['placeholders'] = fn_cp_em_get_placeholders($notice_data['type']);
                $addon_settings = Registry::get('addons.cp_extended_marketing');
                $addon_settings['mailer_sendmail_path'] = Registry::get('settings.Emails.mailer_sendmail_path');
                fn_cp_em_send_notification($notice_data, array(), true, DESCR_SL, array(), $addon_settings);
                
                $suffix = ".update?notice_id=" . $_REQUEST['notice_id'];
            }
        }
    }
    if ($mode == 'preview') {
        $message = $subject = '';
        if (isset($_REQUEST['notice_data'])) {
            if (isset($_REQUEST['notice_data']['message'])) {
                $message = $_REQUEST['notice_data']['message'];
            }
            if (isset($_REQUEST['notice_data']['subject'])) {
                $subject = $_REQUEST['notice_data']['subject'];
            }
        }

        Tygh::$app['view']->assign('message', $message);
        Tygh::$app['view']->assign('subject', $subject);
        
        Tygh::$app['view']->display('addons/cp_extended_marketing/views/cp_em_notices/preview.tpl');
        exit;
    }
    return array(CONTROLLER_STATUS_OK, 'cp_em_notices' . $suffix);
}

if ($mode == 'manage') {
    $cpv1 = ___cp('c2V0dGluZ3MuQPBwZWFyYW5jZS5hZG1pbl9lbGVtZW50c19wZPJfcGFnZQ');
    $params = $_REQUEST;
    $notice_types = fn_get_schema('cp_em', 'types');
    $avail_types = array_keys($notice_types);
    
    if (empty($params['type']) || (!empty($params['type']) && $params['type'] != 'all' && !in_array($params['type'], $avail_types))) {
        $params['type'] = 'all';
    }
    if ($params['type'] == 'all') {
        unset($params['type']);
    }
    $params['get_queue'] = true;
    
    list($em_notices, $search) = call_user_func(___cp('Zm5fY3BfZW1fZ2V0P25vdGljZPM'), $params, Registry::get($cpv1), DESCR_SL);
    if (!empty($params['type'])) {
        $cur_type = $notice_types[$params['type']];
        Tygh::$app['view']->assign('cur_type', $cur_type);
    }
    
    Tygh::$app['view']->assign('notice_types', $notice_types);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('em_notices', $em_notices);
    
} elseif ($mode == 'update' || $mode == 'add') {
    
    $notice_id = !empty($_REQUEST['notice_id']) ? $_REQUEST['notice_id'] : 0;
    $notice_data = fn_cp_em_get_notice_data($notice_id, DESCR_SL);
    $notice_types = fn_get_schema('cp_em', 'types');
    
    if (empty($notice_id)) {
        if (!empty($_REQUEST['type'])) {
            $type = $_REQUEST['type'];
        } else {
            $avail_types = array_keys($notice_types);
            $type = reset($avail_types);
        }
        $notice_data['type'] = $type;
        $notice_data['subject'] = $notice_types[$type]['subject'];
        $notice_data['message'] = $notice_types[$type]['message'];
        $notice_data['send_after'] = $notice_types[$type]['def_period'];
    } else {
        $type = !empty($notice_data['type']) ? $notice_data['type'] : NoticeTypes::CP_EM_ABAND;
    }
    $saved_data = (array) fn_restore_post_data('notice_data');
    if (!empty($saved_data)) {
        $notice_data = $saved_data;
    }
    $statuses = $audiences = array();
    if ($type == NoticeTypes::CP_EM_ORDERS_FEED) {
    
        $statuses = fn_cp_em_get_order_statuses();
        
    } elseif ($type == NoticeTypes::CP_EM_TARGET) {
    
        $date_fields = fn_get_profile_fields('C', array(), CART_LANGUAGE, array('cp_em_this_type' => 'D'));
        if (!empty($date_fields) && !empty($date_fields['C'])) {
            Tygh::$app['view']->assign('date_fields', $date_fields['C']);
        }
        $mailing_lists = array();
        if (Registry::get('addons.newsletters.status') == 'A') {
            list($mailing_lists) = fn_get_mailing_lists(array(), 0, DESCR_SL);
        }
        Tygh::$app['view']->assign('mailing_lists', $mailing_lists);
        
        if (Registry::get('addons.form_builder.status') == 'A') {
            $page_params = array(
                'simple' => true,
                'cp_em_form_email' => true,
                'page_type' => defined('PAGE_TYPE_FORM') ? PAGE_TYPE_FORM : 'F'
            );
            list($form_pages, $page_params) = fn_get_pages($page_params, 0);
            Tygh::$app['view']->assign('form_pages', $form_pages);
        }
    } elseif ($type == NoticeTypes::CP_EM_AUDIENCE) {
        $audience_params = array();
        list($audiences, ) = fn_cp_em_get_audiences($audience_params, 0, DESCR_SL);
    }
    $placeholders = fn_cp_em_get_placeholders($type);
    $promo_params = array(
        'zone' => 'cart',
        'coupons' => true,
        'cp_is_for_notices' => true
    );
    $tabs = array (
        'general' => array (
            'title' => __('general'),
            'js' => true
        ),
        'message' => array(
            'title' => __('cp_em_message_txt'),
            'js' => true
        ),
    );
    
    if (!empty($notice_data) && !empty($notice_data['notice_id'])) {
        $notice_data['statistics'] = fn_cp_em_get_notice_statistics($notice_data['notice_id']);
        $tabs['stats'] = array(
           'title' => __('cp_em_statistics'),
            'js' => true
        );
    }
    Registry::set('navigation.tabs', $tabs);
    list($avail_promotions,) = fn_get_promotions($promo_params, 0, DESCR_SL);
    
    Tygh::$app['view']->assign('avail_promotions', $avail_promotions);
    Tygh::$app['view']->assign('notice_types', $notice_types);
    Tygh::$app['view']->assign('placeholders', $placeholders);
    Tygh::$app['view']->assign('notice_data', $notice_data);
    Tygh::$app['view']->assign('audiences', $audiences);
    Tygh::$app['view']->assign('statuses', $statuses);
    
} elseif ($mode == 'cron_send') {
    if (!empty($_REQUEST['cron_pass']) && $_REQUEST['cron_pass'] == Registry::get('addons.cp_extended_marketing.cron_pass')) {
        fn_cp_em_cron_send_notifications();
    }
    exit;
    
} elseif ($mode == 'build_queue') {
    if (!empty($_REQUEST['cron_pass']) && $_REQUEST['cron_pass'] == Registry::get('addons.cp_extended_marketing.cron_pass')) {
        fn_cp_em_cron_build_queue();
    }
    exit;
    
} elseif ($mode == 'cron_expire_coupons') {
    if (!empty($_REQUEST['cron_pass']) && $_REQUEST['cron_pass'] == Registry::get('addons.cp_extended_marketing.cron_pass')) {
        fn_cp_em_cron_expire_coupons(0);
    }
    exit;
}