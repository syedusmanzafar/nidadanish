<?php
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']=="POST"){
	if (fn_allowed_for('MULTIVENDOR') && Registry::get('runtime.company_id')){
		fn_csc_full_page_cache_delete_cache_by_company_id(Registry::get('runtime.company_id'), true);		
	}else{
		$var = 'cfpc_cleare_cache';
		if (!fn_get_cookie($var)){
			fn_set_notification('W', __('warning'), __('cfpc.cleare_cache'));
			fn_set_cookie($var, true, 5 * 60); //5 mins	
		}		
	}
}