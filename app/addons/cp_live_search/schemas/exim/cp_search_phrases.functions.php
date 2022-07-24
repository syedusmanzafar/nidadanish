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

function fn_exim_get_search_phrase_variants($phrase_id, $delimiter)
{
    $variants = fn_cp_get_search_phrase_searchs($phrase_id);
    return implode($delimiter, $variants);
}

function fn_exim_set_search_phrase_variants($phrase_id, $variants, $delimiter)
{
    $variants = explode($delimiter, $variants);
    fn_cp_set_search_phrase_searchs($phrase_id, $variants);
}

function fn_exim_get_search_phrase_suggestions($suggestions, $delimiter)
{
    return str_replace("\n", $delimiter, $suggestions);
}

function fn_exim_set_search_phrase_suggestions($suggestions, $delimiter)
{
    return str_replace($delimiter, "\n", $suggestions);
}