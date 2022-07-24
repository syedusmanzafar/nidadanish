<?php
if (!defined('BOOTSTRAP')) { die('Access denied'); }
use Tygh\Registry;

if (file_exists(dirname(__FILE__).'/lic.php')){
	require_once(dirname(__FILE__).'/lic.php');
}else{	
	$addon = basename(dirname(__FILE__));
	$msg = 'File '.dirname(__FILE__)."/lic.php not found. Addon $addon is disabled now. 
		Please, restore original lic.php file and then try to enable addon.";
	fn_log_event('general', 'deprecated', ['function'=>$addon, 'message'=>$msg]);
	if (AREA=="A" && Registry::get("addons.$addon.status")=="A"){
		fn_set_notification('E', $addon, $msg);	 
	}
	fn_disable_addon($addon, $addon, false);	
}


define('CS_FPC_MAX_FILES_IN_DIR', 1000);
define('CS_FPC_CACHE_DIR', Registry::get('config.dir.var').'cache/fpc');

if (class_exists('Tygh\CscFullPageCache') 
	&& Registry::get('runtime.controller')!="addons" 
	&& function_exists('fn_csc_full_page_cache_get_cache_controllers')
){	
	$fpc_settings = Tygh\CscFullPageCache::_get_option_values(true);
	$fpc_settings['status']='A';
	$fpc_settings['is_disabled']=false;		
	Registry::set('addons.csc_full_page_cache', $fpc_settings);		
}
if (AREA=="C" && function_exists('fn_csc_full_page_cache_run_turbo_mode')){	
	fn_csc_full_page_cache_run_turbo_mode();
}