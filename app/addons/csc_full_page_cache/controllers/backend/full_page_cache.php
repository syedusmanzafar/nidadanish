<?php
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'clear') {
	if (!empty($_REQUEST['controller'])){
		if ($_REQUEST['controller']=="all"){
			$controllers = fn_csc_full_page_cache_get_cache_controllers();
			foreach ($controllers as $controller){
				fn_csc_full_page_cache_cleare_cache_by_controller($controller);
			}
			db_query('TRUNCATE TABLE ?:csc_full_page_cache_files');
			db_query('TRUNCATE TABLE ?:csc_full_page_cache_files_products');		
		}else{
			fn_csc_full_page_cache_cleare_cache_by_controller($_REQUEST['controller']);
		}	
		
	}
	if (!empty($_REQUEST['type']) && $_REQUEST['type']=="expired"){		
		fn_csc_full_page_cache_cleare_expired_cache();	
	}
	fn_set_notification('N', __('notice'), __('cache_cleared'));
	return array(CONTROLLER_STATUS_REDIRECT, $_REQUEST['redirect_url']);	
}

if ($mode == 'cron_clear') {
	if (!empty($_REQUEST['cron_key']) && $_REQUEST['cron_key']==Registry::get('addons.csc_full_page_cache.cron_key')){
		$controllers = fn_csc_full_page_cache_get_cache_controllers();
		foreach ($controllers as $controller){
			if (!empty($_REQUEST[$controller]) && $_REQUEST[$controller]=="Y"){
				fn_csc_full_page_cache_cleare_cache_by_controller($controller);			
			}
		}
		if (!empty($_REQUEST['expired'])){
			fn_csc_full_page_cache_cleare_expired_cache();
		}
		die('Full page cache was cleared');		
	}
	die('Access denied');	
}