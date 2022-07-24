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

if($mode == 'generate') {
    if(!empty($_REQUEST['src'])) {
        fn_cp_webp_generate_webp_source($_REQUEST['src']);
    } elseif (!empty(Tygh::$app['session']['cp_wb_img_to_webp'])) {
        foreach(Tygh::$app['session']['cp_wb_img_to_webp'] as $key => $src) {
            fn_cp_webp_generate_webp_source($src);
            unset(Tygh::$app['session']['cp_wb_img_to_webp'][$key]);
        }
    }
    die();
}