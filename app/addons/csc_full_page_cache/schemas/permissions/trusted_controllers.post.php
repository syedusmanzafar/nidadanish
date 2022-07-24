<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			https://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$schema['full_page_cache'] = array (
    'default_allow' => false,
	'allow'=>array(
		'cron_clear'=>true
	)
);

return $schema;
