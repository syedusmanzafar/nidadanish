<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			https://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
if ($_SERVER['REQUEST_METHOD']=="POST"){
	return;	
}
if ($mode=='update'){
	if (!empty($_REQUEST['addon']) && $_REQUEST['addon']=="csc_full_page_cache"){
		$redirect_to = 'cfpc.settings';
		csc_full_page_cache::_ar($redirect_to);
		return array(CONTROLLER_STATUS_REDIRECT, $redirect_to);
		
	}
} 
