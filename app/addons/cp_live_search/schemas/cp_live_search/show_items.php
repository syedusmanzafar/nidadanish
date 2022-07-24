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

include_once(__DIR__ . '/show_items.functions.php');

$schema = array(
    'pages' => array(
        'title' => __('pages'),
        'link' => 'pages.view?page_id=[id]',
        'link_params' => array('id' => 'page_id'),
        'get_function' => 'fn_cp_ls_get_search_pages'
    )
);

if (Registry::get('addons.blog.status') == 'A') {
    $schema['blog_posts'] = array(
        'title' => __('blog'),
        'link' => 'pages.view?page_id=[id]',
        'link_params' => array('id' => 'page_id'),
        'get_function' => 'fn_cp_ls_get_search_blog_posts'
    );
}

return $schema;