<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
	'place_order',
	'change_order_status_post',
	'update_user_profile_post'
);