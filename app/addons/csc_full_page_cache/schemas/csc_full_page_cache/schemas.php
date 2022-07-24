<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			https://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
if (!defined('BOOTSTRAP')) { die('Access denied'); }
$schema = array(
	'ignored_params'=>[
		'fpc_debug',
		'no_cache',
		'gclid',
		'yclid',
		'fbclid',
		'utm_source',
		'utm_medium',
		'utm_campaign',
		'utm_term',
		'utm_content'
	],
	'deprecated_controllers'=>[
		'exim_1c', 
		'payment_notification', 
		'checkout', 
		'image', 
		'debugger', 
		'auth', 
		'theme_editor', 
		'retailcrm'
	]
);

return $schema;