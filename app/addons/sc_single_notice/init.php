<?php



if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'cp_change_order_status_pre_notice',
    'finish_payment',
	'checkout_place_orders_pre_route'
);
