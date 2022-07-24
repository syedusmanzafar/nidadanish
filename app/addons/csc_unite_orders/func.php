<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			https://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
use Tygh\Registry;
use Tygh\Http;
use Tygh\Storage;
if (!defined('BOOTSTRAP')) { die('Access denied'); }




function fn_cuo_check_before_unite($order_ids){
	$allow=true;
	$company_ids=array();
	foreach ($order_ids as $oid){
		$company_ids[$oid] = db_get_field("SELECT company_id FROM ?:orders WHERE order_id=?i", $oid);				
	}
	$company_ids = array_unique($company_ids);	
	if (count($company_ids)>1){
		$allow=false;	
	}
	fn_set_hook('cuo_check_before_unite', $order_ids, $allow);	
	return $allow;
}
