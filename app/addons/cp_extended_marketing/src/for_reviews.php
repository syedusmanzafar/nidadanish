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

use Tygh\Addons\ProductReviews\ServiceProvider as ProductReviewsProvider;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_cp_em_check_allow_new_product_reviews($user_id, $product_id, $ip_address)
{
    $allow = false;
    if (!empty($product_id)) {
        $service = ProductReviewsProvider::getService();
        $_result = $service->isUserEligibleToWriteProductReview(
            (int) $user_id,
            (int) $product_id,
            (string) $ip_address
        );
        if ($_result->isFailure()) {
            $allow = false;
        } else {
            $allow = true;
        }
    }
    return $allow;
}