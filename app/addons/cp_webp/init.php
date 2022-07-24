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

fn_register_hooks(
    'delete_image',
    'get_image_pairs_post',
    'image_to_display_post',
    'mailer_send_pre',
    'update_company_pre'
);
if (AREA == 'C') {
    $addon_settings = Registry::get('addons.cp_webp');
    if (!defined('CP_WEBP_GENERATE_MODE')) {
        define('CP_WEBP_GENERATE_MODE', $addon_settings['force_generate']);
    }
    if (!defined('CP_WEBP_USE_HASH')) {
        define('CP_WEBP_USE_HASH', $addon_settings['use_hash']);
    }
}