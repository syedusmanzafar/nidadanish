<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
	'get_orders',
	'get_order_info',
    'print_order_invoices_pre'
);
