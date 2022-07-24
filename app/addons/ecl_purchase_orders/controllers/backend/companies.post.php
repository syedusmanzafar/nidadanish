<?php

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if ($mode === 'update') {
		if (!empty($_REQUEST['company_data']['company_id'])) {
			db_query("UPDATE ?:companies SET payment_terms =?s  WHERE company_id = ?i", $_REQUEST['company_data']['payment_terms']);
		}	
	}
}

if ($mode === 'update'){
	$payment_terms = db_get_row("SELECT payment_terms FROM ?:companies WHERE company_id =?i", $_REQUEST['company_id']);
	Tygh::$app['view']->assign('payment_terms',$payment_terms);

	if ($mode == 'update' && fn_allowed_for('MULTIVENDOR')) {
	    Registry::set('navigation.tabs.payment_terms', array(
	        'title' => __('payment_terms'),
	        'js' => true
	    ));
	}
}