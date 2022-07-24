<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			https://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
if (!defined('BOOTSTRAP')) { die('Access denied'); }
$schema = array(   
	'general' => array(
		'general_settings'=>array(
			'type' => 'title'			
		),				
		'unite_status' => array(
			'type' => 'order_status',
			'default' => 'O',			
			'tooltip' => true,
		),
		'delete' => array(
			'type' => 'checkbox',
			'default' => 'N',			
			'tooltip' => true,
		),
		'united_status' => array(
			'type' => 'order_status',
			'default' => 'I',			
			'tooltip' => true,
			'hide_when'=>array('delete'=>array("Y"))	
		),	
		
				
	),	
);

return $schema;