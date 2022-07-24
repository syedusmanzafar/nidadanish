<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			https://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
if (!defined('BOOTSTRAP')) { die('Access denied'); }
$schema = array(
	'cuo.menu'=>array(
		'cuo.settings' => array(		
			'dispatch'=>'cuo.settings'
		)		
	)	
);
return $schema;