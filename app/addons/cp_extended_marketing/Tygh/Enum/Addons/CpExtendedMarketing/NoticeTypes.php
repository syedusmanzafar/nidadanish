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

namespace Tygh\Enum\Addons\CpExtendedMarketing;

/**
 * NoticeTypes contains available notices types for abandoned cart , wishlist, etc.
 *
 * @package Tygh\Enum
 */
class NoticeTypes
{
    const CP_EM_ABAND = 'A'; // abandoned carts
    const CP_EM_TARGET = 'T'; // targeted customers
    const CP_EM_VIEWED = 'V'; // viewed products
    const CP_EM_WISHLIST = 'W'; // wishlist
    const CP_EM_AUDIENCE = 'P'; // audience
    const CP_EM_ORDERS_FEED = 'O'; // orders feedback
}