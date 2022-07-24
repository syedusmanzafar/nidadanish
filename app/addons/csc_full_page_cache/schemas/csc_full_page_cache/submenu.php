<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			https://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
if (!defined('BOOTSTRAP')) { die('Access denied'); }
$schema = array(
	'cfpc.menu'=>array(
		'cfpc.settings' => array(		
			'dispatch'=>'cfpc.settings'
		),
		/*'cfpc.landing' => array(		
			'dispatch'=>'cfpc.landing'
		)	*/		
	)	
);
return $schema;