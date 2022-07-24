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

$schema['conditions']['coupon_code'] = array(
    'operators' => array ('eq', 'in'),
    // 'cont' - 'contains' was removed as ambiguous, but you can uncomment it back
    //'operators' => array ('eq', 'cont', 'in'),
    'type' => 'input',
    'field_function' => array('fn_cp_en_check_coupons_new_func', '#this', '@cart', '#id'),
    'after_conditions_check_function' => 'fn_promotion_check_coupon_code_once_per_customer',
    'zones' => array('cart'),
    'applicability' => array( // applicable for "positive" groups only
        'group' => array(
            'set_value' => true
        ),
    ),
);
return $schema;
