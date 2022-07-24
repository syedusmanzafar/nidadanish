<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (!empty(Tygh::$app['session']['rf_pro_marketing']) && !defined('AJAX_REQUEST')) {
	Tygh::$app['view']->assign('rf_pro_marketing', Tygh::$app['session']['rf_pro_marketing']);

	unset(Tygh::$app['session']['rf_pro_marketing']);
}

if (!empty($auth['user_id'])) {
	$user = fn_get_user_info($auth['user_id']);
	Tygh::$app['view']->assign('rf_hashed_email', md5(strtolower($user['email'])));
}

Tygh::$app['view']->assign('rf_device_type', fn_rf_pro_marketing_detect_mobile_device() ? 'm' : 'd');