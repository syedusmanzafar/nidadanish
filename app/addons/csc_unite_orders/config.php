<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			http://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
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