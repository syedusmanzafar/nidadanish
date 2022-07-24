<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
	//fn.cart.php fn_get_orders()
	'get_orders',
	'get_order_info',
	//fn.cart.php fn_change_order_status()
	'change_order_status',
	//fn.cart.php fn_place_suborders()
	'an_place_suborders',
	'get_store_locations_for_shipping_before_select',
	'shippings_get_company_shipping_ids',
	'calculate_cart_taxes_pre',
    'change_order_status_child_order',
    'an_change_order_status_parent_place_order'



    ,'vendor_plans_calculate_commission_for_payout_post'
);
