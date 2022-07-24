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
		'general_info'=>array(
			'type' => 'title'			
		),
		'controllers'=>array(
			'type' => 'multiple',
			'variants'=>fn_csc_full_page_cache_get_cache_controllers(),
			'default'=>array_keys(fn_csc_full_page_cache_get_cache_controllers())		
		),
		'turbo'=>array(
			'type' => 'checkbox',			
			'default'=>'Y',
			'tooltip'=>true		
		),
		'compress_cache'=>array(
			'type' => 'checkbox',			
			'default'=>'Y',
			'tooltip'=>true				
		),
		'minify_html'=>array(
			'type' => 'checkbox',			
			'default'=>'Y',
			'tooltip'=>true				
		),
		'minify_js'=>array(
			'type' => 'checkbox',			
			'default'=>'Y'		
		),
		'mobile_devices'=>array(
			'type' => 'checkbox',			
			'default'=>'N',
			'tooltip'=>true			
		),		
	), 	
	'expiry' => array(
		'expiry_info'=>array(
			'type' => 'title'			
		),
		'cache_lifetime'=>array(
			'type' => 'input',			
			'default'=>'8',
			'tooltip'=>true				
		),
		'rebuild_product_cache'=>array(
			'type' => 'checkbox',			
			'default'=>'Y'		
		),
		'rebuild_create_product_cache'=>array(
			'type' => 'checkbox',			
			'default'=>'Y'		
		),
		'rebuild_pages_cache'=>array(
			'type' => 'checkbox',			
			'default'=>'Y'		
		),
		'rebuild_categories_cache'=>array(
			'type' => 'checkbox',			
			'default'=>'Y'		
		),		
	),
	'skip' => array(
		'skip_info'=>array(
			'type' => 'title'			
		),
		'skip_ajax'=>array(
			'type' => 'checkbox',			
			'default'=>'N'		
		),	
		'no_cache_filters'=>array(
			'type' => 'checkbox',			
			'default'=>'Y',
			'tooltip'=>true
		),	
		'skip_import_process'=>array(
			'type' => 'checkbox',			
			'default'=>'Y',
			'tooltip'=>true				
		),	
		
	), 
	'users' => array(
		'users_info'=>array(
			'type' => 'title'			
		),
		'no_generate_from_auth'=>array(
			'type' => 'checkbox',			
			'default'=>'N'		
		),	
		'disable_for_auth'=>array(
			'type' => 'checkbox',			
			'default'=>'N'		
		),	
		'cache_of_usergroup'=>array(
			'type' => 'checkbox',			
			'default'=>'N'		
		),
		'cache_of_applied_promotion'=>array(
			'type' => 'checkbox',			
			'default'=>'N'		
		),		
	),
	'storage' => array(
		'storage_info'=>array(
			'type' => 'title'			
		),
		'storage_type'=>array(
			'type' => 'selectbox',
			'variants'=>fn_settings_variants_addons_csc_full_page_cache_storage_type(),		
			'default'=>'F'		
		),
		'redis_server'=>array(
			'type' => 'input',				
			'default'=>'localhost',
			'show_when'=>array('storage_type'=>array('R'))		
		),	
		'redis_port'=>array(
			'type' => 'input',				
			'default'=>'6379',
			'show_when'=>array('storage_type'=>array('R'))		
		),
		
	), 
	'info' => array(
		'info_info'=>array(
			'type' => 'title'			
		),
		'cron_key'=>array(
			'type' => 'input',			
			'default'=>'JD630DS'		
		),
		
		'template'=>array(
			'type' => 'template',
			'template'=>'addons/csc_full_page_cache/settings/setup_desc.tpl'				
			
			
		),
		
	), 
	
		 	 	
);

return $schema;