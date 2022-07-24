<?php
$schema['top']['administration']['items']['fpc_storage']=array(
	'href' => 'full_page_cache.index',
    'type' => 'title',
    'position' => 1105,
	'attrs' => array(
        'class'=>'is-addon'
    ),
);
$schema['top']['administration']['items']['fpc_storage']['subitems']['cs_divider'] = array(
	 'type' => 'divider',
	 'position' => 9998,
);
$schema['top']['administration']['items']['fpc_storage']['subitems']['cfpc_clear_all'] = array(
	 'href' => 'full_page_cache.clear?controller=all&redirect_url=%CURRENT_URL',
	'position' => 9999,
);
$schema['top']['administration']['items']['fpc_storage']['subitems']['cfpc_clear_expired'] = array(
	 'href' => 'full_page_cache.clear?type=expired&redirect_url=%CURRENT_URL',
	'position' => 9999,
);
if (function_exists('fn_csc_full_page_cache_get_cache_controllers')){
	$controllers = fn_csc_full_page_cache_get_cache_controllers();
	foreach ($controllers as $controller){
		$schema['top']['administration']['items']['fpc_storage']['subitems']['cfpc_clear_'.$controller] = array(
			 'href' => 'full_page_cache.clear?controller='.$controller.'&redirect_url=%CURRENT_URL',
			'position' => 10000,
		);
	}
}


$schema['top']['addons']['items']['csc_addons']['type']='title';
$schema['top']['addons']['items']['csc_addons']['href']='cfpc.settings';
$schema['top']['addons']['items']['csc_addons']['position']='1000';
$schema['top']['addons']['items']['csc_addons']['title']=__("cfpc.csc_addons");

$schema['top']['addons']['items']['csc_addons']['subitems']['csc_full_page_cache'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'cfpc.settings',	
    'position' => 300
);


return $schema;
