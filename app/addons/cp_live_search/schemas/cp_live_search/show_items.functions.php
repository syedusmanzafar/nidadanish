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

function fn_cp_ls_get_search_pages($q, $lang_code = DESCR_SL, $settings = array())
{
    $items_count = !empty($settings['items_amount']) ? $settings['items_amount'] : 5;
    $limit = db_quote('LIMIT 0, ?i', $items_count);
    $items = db_get_hash_array(
        'SELECT descr.page_id, descr.page as title FROM ?:pages'
        . ' LEFT JOIN ?:page_descriptions as descr ON ?:pages.page_id = descr.page_id AND lang_code = ?s'
        . ' WHERE status = ?s AND page_type = ?s AND descr.page LIKE ?l ?p',
        'page_id', $lang_code, 'A', 'T', "%$q%", $limit
    );
    return $items;
}

function fn_cp_ls_get_search_blog_posts($q, $lang_code = DESCR_SL, $settings = array())
{
    $items_count = !empty($settings['items_amount']) ? $settings['items_amount'] : 5;
    $limit = db_quote('LIMIT 0, ?i', $items_count);
    $items = db_get_hash_array(
        'SELECT descr.page_id, descr.page as title FROM ?:pages'
        . ' LEFT JOIN ?:page_descriptions as descr ON ?:pages.page_id = descr.page_id AND lang_code = ?s'
        . ' WHERE status = ?s AND page_type = ?s AND descr.page LIKE ?l ?p',
        'page_id', $lang_code, 'A', 'B', "%$q%", $limit
    );
    return $items;
}