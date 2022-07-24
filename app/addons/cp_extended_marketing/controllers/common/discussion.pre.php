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
    if ($mode == 'cp_em_add_rew') {
        $suffix = '';
        if (AREA == 'C' && empty($_REQUEST['cp_skip_verf'])) {
            if (fn_image_verification('discussion', $_REQUEST) == false) {
                fn_save_post_data('post_data');

                return array(CONTROLLER_STATUS_REDIRECT, $_REQUEST['redirect_url'] . $suffix);
            }
        }
        if (!empty($_REQUEST['post_data'])) {
            if (!empty($_REQUEST['post_data']['thread_id'])) {
            
                $type = fn_cp_em_get_discussion_type_by_thread($_REQUEST['post_data']['thread_id']);
                
                if ($type == 'C' || $type == 'B') {
                    $check_empty_msg = !empty($_REQUEST['post_data']['message']) ? trim($_REQUEST['post_data']['message']) : '';
                    if (empty($check_empty_msg)) {
                        fn_set_notification('E', __('error'), __('cp_em_you_entered_an_empty_message'));
                        return array(CONTROLLER_STATUS_REDIRECT, $_REQUEST['redirect_url'] . $suffix);
                    }
                }
                if (Registry::get('addons.cp_power_reviews.status') != 'A') {
                    $_REQUEST['post_data']['rating_value'] = $_REQUEST['post_data']['ratings'][0];
                    $post_id = fn_add_discussion_post($_REQUEST['post_data']);
                } else {
                    $post_id = fn_cp_power_reviews_add_prod_ratings($_REQUEST['post_data']);
                }
                if (!empty($post_id)) {
                    if (Registry::get('addons.cp_power_reviews.status') == 'A') {
                        fn_cp_em_add_imgs_to_post($post_id);
                    }
                    if (!empty($_REQUEST['order_id']) && !empty($_REQUEST['cp_item_id'])) {
                        fn_cp_em_set_rated_trigger($_REQUEST['order_id'], $_REQUEST['cp_item_id']);
                    }
                }
            }
        }
    }
}