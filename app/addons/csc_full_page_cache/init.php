<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			http://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
	'before_dispatch',
	'dispatch_before_display',
	'clear_cache_post',
	'update_product_post',
	'get_products_post',
	'get_product_data_post',
	'delete_product_pre',
	'update_page_post',
	'update_category_post',
	'update_product_amount_pre',
	'update_option_combination_post',
	'delete_option_combination_pre',
	'update_product_option_post' ,
	'delete_product_option_pre',
	'render_block_content_after',
	'update_company',
	'add_discussion_post_post',
	'tools_change_status',
	'render_block_post',
	'update_block_pre'
);
foreach(Registry::get('hooks.dispatch_before_display') as $k=> $hook){
	if ($hook['addon']=="csc_full_page_cache"){
		Registry::set("hooks.dispatch_before_display.$k.priority", 4294967295);
	}
}
