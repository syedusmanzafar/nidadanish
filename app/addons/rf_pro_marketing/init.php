<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
	'save_cart_content_post',
	'save_cart_content_pre',
	'place_order',
	'change_order_status_post',
	'update_user_profile_post'
);
