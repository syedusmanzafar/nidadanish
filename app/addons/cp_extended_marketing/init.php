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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'add_discussion_post_post',
    'change_location',
    'change_order_status',
    'checkout_update_steps_pre',
    'delete_company',
    'delete_product_post',
    'delete_promotions_post',
    'get_carts_before_select',
    'get_pages',
    'get_orders',
    'get_promotions',
    'get_users',
    'mailer_create_message_before',
    'place_order',
    'post_delete_user',
    'send_form',
    'session_regenerate_id',
    'tools_change_status',
    'update_profile'
);