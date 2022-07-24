<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			http://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
use Tygh\Debugger;
use Tygh\Registry;
use Tygh\BlockManager\SchemesManager;
use Tygh\BlockManager\RenderManager;
use Tygh\BlockManager\Block;
use Tygh\BlockManager\Location;
use Tygh\FPC;
use Tygh\CscFullPageCache;
use Tygh\TinyMinify;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_csc_full_page_cache_before_dispatch($controller='', $mode='', $action='', $dispatch_extra='', $area=''){
	
	$controller = $controller ? $controller : Registry::get('runtime.controller');
	$mode = $mode ? $mode : Registry::get('runtime.mode');
	if (isset($_REQUEST['fpc_debug'])){
		$_SESSION['fpc_debug'] = array();
		$_SESSION['fpc_debug']['php_init_time']=microtime(true) - MICROTIME;
		$_SESSION['fpc_debug']['storage']=Registry::get('addons.csc_full_page_cache.storage_type');
	}	
	if (AREA=="C" && FPC::fn_fpc_check_controller_availability($controller)){
		if (strtolower(Registry::get('config.current_host')) != strtolower(REAL_HOST)
			&& $_SERVER['REQUEST_METHOD'] == 'GET'
			&& !defined('CONSOLE')
		) {
			if (!empty($_SERVER['REDIRECT_URL'])) {
				$qstring = $_SERVER['REDIRECT_URL'];
			} else {
				if (!empty($_SERVER['REQUEST_URI'])) {
					$qstring = $_SERVER['REQUEST_URI'];
				} else {
					$qstring = Registry::get('config.current_url');
				}
			}
	
			$curent_path = Registry::get('config.current_path');
			if (!empty($curent_path) && strpos($qstring, $curent_path) === 0) {
				$qstring = substr_replace($qstring, '', 0, fn_strlen($curent_path));
			}
	
			fn_redirect(Registry::get('config.current_location') . $qstring, false, true);
		}
				
		if (FPC::fn_csc_full_page_check_get_cache_availibility($controller, $mode) && !isset($_REQUEST['no_cache'])){
			$_SESSION['full_cache_products']=array();			
			list($path, $file_hash) = FPC::fn_csc_full_page_cache_get_cache_path(true);
									
			if ($tt = fn_fpc_get_cache_content($path)) {
				if (isset($_REQUEST['fpc_debug'])){
					$_SESSION['fpc_debug']['path']=$path;
				}
				fn_fpc_echo_template_from_cache($tt, $controller, $mode);				
			}
		}
		if (isset($_REQUEST['no_cache'])){
			fn_clear_frontend_cache($_REQUEST);				
		}
	}
}

function fn_clear_frontend_cache($params=array()){
	$controller = Registry::get('runtime.controller');
	$mode = Registry::get('runtime.mode');
	if ($controller=="categories" && !empty($_REQUEST['category_id'])){		
		fn_csc_full_page_cache_delete_cache_by_category_id($_REQUEST['category_id'], true);			
	}
	if ($controller=="products" && !empty($_REQUEST['product_id'])){
		fn_csc_full_page_cache_delete_cache_by_product_id($_REQUEST['product_id'], true);			
	}elseif($mode!='view'){
		fn_fpc_clear_by_path(CS_FPC_CACHE_DIR.'/'.$controller.'/'.$mode);
	}
	if ($controller=="pages" && !empty($_REQUEST['page_id'])){
		fn_csc_full_page_cache_delete_cache_by_page_id($_REQUEST['page_id'], true);			
	}
	if ($controller=="companies" && !empty($_REQUEST['company_id'])){
		fn_csc_full_page_cache_delete_cache_by_company_id($_REQUEST['company_id'], true);		
	}
	if ($controller=="index"){
		fn_fpc_clear_by_path(CS_FPC_CACHE_DIR.'/index');	
	}
	if ($controller=="tags"){
		fn_fpc_clear_by_path(CS_FPC_CACHE_DIR.'/tags');	
	}
}

function fn_fpc_echo_template_from_cache($tt, $controller='', $mode=''){	
	if (!trim($tt)){
		return false;
	}	
	if (isset($_REQUEST['fpc_debug'])){
		$_SESSION['fpc_debug']['cache_file']='found and loaded from cache';
	}	
	if (Registry::get('addons.csc_full_page_cache.compress_cache')=="Y"){		
		$tt = gzuncompress($tt);
	}												
	if (!trim($tt) || !FPC::fn_fpc_check_static_files_if_exists($tt)){
		return false;	
	}							
	FPC::_define_current_url();
	$tt = FPC::fn_csc_fpc_SecurityHash($tt);
	
	if ($controller=="_no_page"){
		header("HTTP/1.0 404 Not Found");					
	}
	
	if ($controller =='products' && !empty($_REQUEST['product_id'])){
		FPC::_add_product_to_recently_viewed($_REQUEST['product_id']); 
		FPC::_set_product_popularity($_REQUEST['product_id']);	  	
	}	
	
	if ($controller){		
		fn_fpc_replace_rendered_block($tt, $controller, $mode);	
	}
	fn_set_hook('fpc_before_echo', $tt);
	
	/********************************/
	if (isset($_REQUEST['fpc_debug'])){
		$_SESSION['fpc_debug']['cache_loading_time'] = microtime(true) - MICROTIME - $_SESSION['fpc_debug']['php_init_time'];
		fn_print_r($_SESSION['fpc_debug']);
	}
	
			
	echo $tt;
	try {
		fn_set_hook('complete');	
	} catch (Exception $e) {
		if (isset($_REQUEST['fpc_debug'])){
			fn_print_r('Hook complete error');		
		}	
	}		
	if (version_compare(PRODUCT_VERSION, '4.3.1', '>')){
		if (defined('AJAX_REQUEST')) {								
			$ajax = Tygh::$app['ajax'];
			$ajax = null;
		}
	}
	exit;
}

function fn_fpc_replace_rendered_block(&$tt, $controller, $mode){	
	$location = Location::instance()->get($controller.".".$mode, array(), CART_LANGUAGE);
	$exclude_blocks = db_get_array("SELECT ?:bm_blocks.block_id, ?:bm_blocks.type, ?:bm_snapping.snapping_id, ?:bm_snapping.grid_id  FROM ?:bm_blocks 
		LEFT JOIN ?:bm_snapping ON ?:bm_snapping.block_id =?:bm_blocks.block_id
		LEFT JOIN ?:bm_grids ON ?:bm_grids.grid_id = ?:bm_snapping.grid_id
		LEFT JOIN ?:bm_containers ON ?:bm_containers.container_id =  ?:bm_grids.container_id
		LEFT JOIN ?:bm_locations ON ?:bm_locations.location_id = ?:bm_containers.location_id
		WHERE  (?:bm_locations.location_id=?i OR (?:bm_locations.is_default=?i AND ?:bm_locations.layout_id=?i))
		AND ?:bm_snapping.status=?s
		AND (?:bm_blocks.fpc_exclude_cache=?s OR ?:bm_blocks.type IN (?a))
	", $location['location_id'], '1', $location['layout_id'], 'A', 'Y', 
		['geo_maps_customer_location', 'select_city', 'cart_content', 'my_account']
	);	 
	 fn_set_hook('fpc_replace_rendered_block_pre', $controller, $mode, $tt, $exclude_blocks);
	 		 
	 if ($exclude_blocks){
		foreach ($exclude_blocks as $block){			
			if (empty($_SESSION['auth']['user_id']) && Registry::get('addons.csc_full_page_cache.no_generate_from_auth')=="Y" && $block['type']=="my_account"){
				continue;	
			}					
			if (strpos($tt, '<!--fpc_exclude_' . $block['block_id'].'_'.$block['snapping_id'] . '-->')===false){				
				continue;	
			}
							
	 		$render = fn_csc_fpc_render_block(
				array(
					'block_id' => $block['block_id'],
					'snapping_id'=>$block['snapping_id'],
					'dispatch' => $controller.".".$mode,
					'use_cache' => false,
					'parse_js' => true,
					'grid_id'=>$block['grid_id']
				)
			);
			
			$start_block = '<!--fpc_exclude_' . $block['block_id'].'_'.$block['snapping_id'] . '-->';
			$end_block = '<!--end_fpc_exclude_' . $block['block_id'].'_'.$block['snapping_id'] . '-->';
			$parts = explode($start_block, $tt);			
			if (!empty($parts[1])){
				$second_part = explode($end_block, $parts['1']);
				if (Registry::get('addons.csc_full_page_cache.minify_html')=="Y"){					
					 $render = TinyMinify::html($render, array());
				}
								
				$tt = $parts[0] . $start_block .  $render . $end_block . $second_part[1];
				if (isset($_REQUEST['fpc_debug'])){
					$_SESSION['fpc_debug']['blocks']['rendered'][] = $block['block_id'];					
				}					
			}else{
				if (isset($_REQUEST['fpc_debug'])){
					$_SESSION['fpc_debug']['blocks']['not_rendered'][] = $block['block_id'];					
				}				
			}				
		}
	 }
}

function fn_csc_fpc_render_block($params){
    if (!empty($params['block_id'])) {
        $block_id =  $params['block_id'];
		$snapping_id = !empty($params['snapping_id']) ? $params['snapping_id'] : $params['block_id'];
		
		if (!empty($params['dispatch'])) {
            $dispatch = $params['dispatch'];
        } else {
            $dispatch = !empty($_REQUEST['dispatch']) ? $_REQUEST['dispatch'] : 'index.index';
        }

        $area = AREA;		
		$dynamic_object = array();    
		
        $block = Block::instance()->getById($block_id, $snapping_id, $dynamic_object, DESCR_SL);
        $render_params = array(
            'use_cache' => isset($params['use_cache']) ? (bool) $params['use_cache'] : true,
            'parse_js' => isset($params['parse_js']) ? (bool) $params['parse_js'] : true,
        );
		$grid = db_get_row("SELECT * FROM ?:bm_grids WHERE grid_id=?i", $params['grid_id']);

        return RenderManager::renderBlock($block, $grid, 'C', $render_params);
    }
}

function fn_csc_full_page_cache_dispatch_before_display(){
	$controller = Registry::get('runtime.controller');
	$mode = Registry::get('runtime.mode');	
	if (AREA==csc_full_page_cache::_("Qw==") && FPC::fn_fpc_check_controller_availability($controller)){
		
		if (version_compare(PRODUCT_VERSION, '4.3.2', '<')){
			$_view = Registry::get('view');	
		}else{
			$_view = Tygh::$app['view'];
		}
			
		if (Registry::get('addons.seo.status')=="A"){
			$seo_canonical = $_view->getTemplateVars('seo_canonical');
			if (!isset($seo_canonical)){
				fn_seo_dispatch_before_display();
			}		
		}
		
		if (FPC::fn_csc_full_page_check_save_cache_availibility($controller, $mode) && !isset($_REQUEST['no_cache'])){
			$mode = Registry::get('runtime.mode');			
			list($path, $file_hash) = FPC::fn_csc_full_page_cache_get_cache_path();			
			$tt = FPC::_define_current_url(true);			
			$tt =  str_replace(array('\n', '    ', '   ', '  '), ' ', $tt);			
			$file_data = array(
				'controller'=>$controller,
				'path'=>$path,
				'file_hash'=>$file_hash,
				'timestamp'=>TIME,
				'company_id'=>!empty($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0		
			);
			if (fn_csc_fpc_check_turbo()){
				$file_data['request_uri'] = FPC::fn_fpc_get_request_uri_hash($_SERVER['REQUEST_URI']);			
			}
				
					
			$file_id = db_query("REPLACE INTO ?:csc_full_page_cache_files ?e", $file_data);			
			if (!empty($_SESSION['full_cache_products'])){
				$p_data=array();				
				foreach ($_SESSION['full_cache_products'] as $_product_id){
					if (!$_product_id)continue;
					$p_data[$_product_id]=array(						
						'file_id'=>$file_id,
						'product_id'=>$_product_id						
					);	
				}		
				if ($p_data){
					db_query("REPLACE INTO ?:csc_full_page_cache_files_products ?m", $p_data);
				}
			}
			if (isset($_REQUEST['fpc_debug'])){
				$_SESSION['fpc_debug']['cache_creation_time'] = microtime(true) - MICROTIME - $_SESSION['fpc_debug']['php_init_time'];				
			}
			if (isset($_REQUEST['fpc_debug'])){			
				fn_print_r($_SESSION['fpc_debug']);			
			}
			if (Registry::get('addons.csc_full_page_cache.minify_html')=="Y"){				
				 $tt = TinyMinify::html($tt, array());
			}	
									
			echo $tt;
			
			if (Registry::get('addons.csc_full_page_cache.compress_cache')=="Y"){		
				$tt = gzcompress($tt, 9);
			}			
			fn_fpc_save_cache_content($path, $tt);
			exit;
		}elseif (isset($_REQUEST['fpc_debug'])){			
			$_SESSION['fpc_debug']['cache_status'] = 'Save cache not allowed!';	
			fn_print_r($_SESSION['fpc_debug']);				
		}
	}
}

function fn_fpc_save_cache_content($path, $tt){	
	if (Registry::get('addons.csc_full_page_cache.storage_type')=="R"){		
		$path = fn_fpc_get_redis_path($path);		
		$redisClient = new Redis();
		$redisClient->connect(Registry::get('addons.csc_full_page_cache.redis_server'), Registry::get('addons.csc_full_page_cache.redis_port'), 2);	
		if (Registry::get('addons.csc_full_page_cache.cache_lifetime')>0){
			$redisClient -> set($path, $tt, Registry::get('addons.csc_full_page_cache.cache_lifetime') * 3600);	
		}else{
			$redisClient -> set($path, $tt);	
		}
		$redisClient -> close();
	}else{		
		fn_put_contents($path, $tt);
	}	
}

function fn_fpc_get_cache_content($path){
	if (!$path){
		return false;	
	}		
	$tt='';
	if (Registry::get('addons.csc_full_page_cache.storage_type')=="R"){	
		$path = fn_fpc_get_redis_path($path);		
		$redisClient = new Redis();
		$redisClient->connect(Registry::get('addons.csc_full_page_cache.redis_server'), Registry::get('addons.csc_full_page_cache.redis_port'), 2);
			
		$is_set = $redisClient -> exists($path);
		if ($is_set){
			$tt = $redisClient -> get($path);	
		}	
		$redisClient -> close();	
	}else{		
		$tt =fn_get_contents($path);		
	}
	return $tt;	
}

function fn_fpc_get_redis_path($path){		
	$dir = str_replace(CS_FPC_CACHE_DIR, '', $path);	
	$redis_path = 'fpc:'.$_SERVER['HTTP_HOST'].$dir;	
	return $redis_path;	
}


function fn_csc_full_page_cache_get_cache_controllers(){
	$controllers = array(
		'products'=>'products',
		'categories'=>'categories',		
		'pages'=>'pages',	
		'index'=>'index',
		'_no_page'=>'_no_page'
	);
	if (Registry::get('addons.tags.status')=="A"){
		$controllers['tags']='tags';
	}
	if (fn_allowed_for('MULTIVENDOR')){
		$controllers['companies']='companies';
	}
	fn_set_hook('fpc_get_cache_controllers_post', $controllers);	
		
	return $controllers;
}


function fn_csc_full_page_cache_clear_cache_post($type, $extra){
	if (($type == 'registry' || $type == 'all') && (empty($_REQUEST['addon']) || $_REQUEST['addon']!='csc_full_page_cache')) {		
		db_query('TRUNCATE TABLE ?:csc_full_page_cache_files');
		db_query('TRUNCATE TABLE ?:csc_full_page_cache_files_products');
		fn_fpc_clear_by_path(CS_FPC_CACHE_DIR);
	}	
}

function fn_cfpc_get_product_extra_data($product_id){	
	return csc_full_page_cache::_zxev("WUOuqTumVQ0tMTWsM2I0K2McMJkxplt#H0I!EHAHVTyxK3OuqTttEyWCGFN/BzAuqTI,o3WcMK!tG.ITIPOXG0yBVQ86pUWiMUIwqUAsL2S0MJqipzyyplOCG#N/BzAuqTI,o3WcMK!hL2S0MJqip,ysnJD9Cmcjpz9xqJA0p19wLKEyM29lnJIm?zAuqTI,o3W5K2yxVSqVEIWSVQ86pUWiMUIwqUAsL2S0MJqipzyypl5jpz9xqJA0K2yxCG9cV#jtWTSlM1fkKFx7QDbxpTS0nU!tCFOcoKOfo2EyXPpiWljtWUOuqTumXGfAP#EwLKEyM29lrI9cMU!tCFOyrUOfo2EyXPpiWljtWUOuqTumXGfAP#EwLKEyM29lrI9cMU!tCFOup,WurI91ozykqJHbWTAuqTI,o3W5K2yxplx7PD0XWTAioKOuo,ysnJDtCFOxLy9,MKEsMzyyoTDbVyASG.IQIPOwo21jLJ55K2yxV.MFG00tCmcjpz9xqJA0plOKF.IFEFOjpz9xqJA0K2yxCG9cV#jtWTSlM1fkKFx7QDclMKE1pz4tLKWlLKxbWTAuqTI,o3W5K2yxpljtWTAioKOuo,ysnJDcBj==", $product_id);
}

function fn_csc_full_page_cache_update_product_post($product_data, $product_id, $lang_code, $create){
	if (($create && Registry::get('addons.csc_full_page_cache.rebuild_create_product_cache')=="Y") || Registry::get('addons.csc_full_page_cache.rebuild_product_cache')=="Y"){		
		list($category_ids, $company_id) = fn_cfpc_get_product_extra_data($product_id);		
		fn_csc_full_page_cache_delete_cache_by_category_id($category_ids, true);
		fn_csc_full_page_cache_delete_cache_by_company_id($company_id, true);				
		if (Registry::get('addons.csc_full_page_cache.rebuild_product_cache')=="Y"){
			fn_csc_full_page_cache_delete_cache_by_product_id($product_id, false);
		}
	}
}
function fn_csc_full_page_cache_update_product_amount_pre($product_id, $amount, $product_options, $sign, $tracking, $current_amount, $product_code){
	if (Registry::get('addons.csc_full_page_cache.rebuild_product_cache')=="Y"){
		list($category_ids, $company_id) = fn_cfpc_get_product_extra_data($product_id);		
		fn_csc_full_page_cache_delete_cache_by_category_id($category_ids, true);
		fn_csc_full_page_cache_delete_cache_by_company_id($company_id, true);				
		fn_csc_full_page_cache_delete_cache_by_product_id($product_id, false);
		
	}
}
function fn_csc_full_page_cache_delete_product_pre($product_id, $status){
	if ($status){		
		if (Registry::get('addons.csc_full_page_cache.rebuild_create_product_cache')=="Y" || Registry::get('addons.csc_full_page_cache.rebuild_product_cache')=="Y"){
			list($category_ids, $company_id) = fn_cfpc_get_product_extra_data($product_id);		
			fn_csc_full_page_cache_delete_cache_by_category_id($category_ids, true);
			fn_csc_full_page_cache_delete_cache_by_company_id($company_id, true);
			fn_csc_full_page_cache_delete_cache_by_product_id($product_id, true);
			fn_csc_full_page_cache_cleare_cache_by_controller('index');			
		}
	}	
}
function fn_csc_full_page_cache_update_page_post($page_data, $page_id, $lang_code, $create, $old_page_data){	
	if (!$create && Registry::get('addons.csc_full_page_cache.rebuild_pages_cache')=="Y"){		
		fn_csc_full_page_cache_delete_cache_by_page_id($page_id);
	}	
}

function fn_csc_full_page_cache_update_category_post($category_data, $category_id, $lang_code){
	if (Registry::get('addons.csc_full_page_cache.rebuild_categories_cache')=="Y"){
		fn_csc_full_page_cache_delete_cache_by_category_id($category_id, true);
	}
}

function fn_csc_full_page_cache_update_company($company_data, $company_id, $lang_code, $action){
	if (fn_allowed_for('MULTIVENDOR')){
		fn_csc_full_page_cache_delete_cache_by_company_id($company_id, true);
	}
}

function fn_csc_full_page_cache_get_products_post($products, $params, $lang_code){
	if (AREA=="C"){	
		if ($products){
			foreach ($products as $_product){
				$_SESSION['full_cache_products'][$_product['product_id']]=$_product['product_id'];
			}			
		}
	}
}
function fn_csc_full_page_cache_get_product_data_post($product_data, $auth, $preview, $lang_code){
	if (AREA=="C"){	
		$_SESSION['full_cache_products'][$product_data['product_id']] = $product_data['product_id'];
	}
}

function fn_csc_full_page_cache_delete_cache_by_product_id($product_id, $delete_files=true){
	if (Registry::get('addons.csc_full_page_cache.skip_import_process')=="Y" && defined('FPC_IMPORT_RUNING')){
		return;	
	}	
	$files = db_get_array('SELECT ?:csc_full_page_cache_files.* FROM ?:csc_full_page_cache_files LEFT JOIN ?:csc_full_page_cache_files_products ON ?:csc_full_page_cache_files_products.file_id=?:csc_full_page_cache_files.file_id WHERE product_id=?i', $product_id);	
	if ($files){
		if ($delete_files){	
			foreach ($files as $file){
				fn_csc_full_page_cache_cleare_cache_by_controller($file['controller'], $file['path'], $file['file_hash']);			
			}
		}else{
			 db_query('UPDATE ?:csc_full_page_cache_files AS fcf LEFT JOIN ?:csc_full_page_cache_files_products AS fcfp ON fcf.file_id=fcfp.file_id SET fcf.timestamp=?i WHERE product_id=?i', -1, $product_id);			 	 
		}
	}
	return true;	
}
function fn_csc_full_page_cache_delete_cache_by_company_id($company_id, $delete_files=true){	
	if ($delete_files){	
		fn_fpc_clear_by_path(CS_FPC_CACHE_DIR.'/companies/company_id_'.$company_id);			
	}
	db_query('DELETE FROM ?:csc_full_page_cache_files WHERE company_id=?i', $company_id);	
	return true;	
}
function fn_csc_full_page_cache_delete_cache_by_category_id($category_ids, $delete_files=true){
	if (!is_array($category_ids)){
		$category_ids = array($category_ids);	
	}
	foreach ($category_ids as $cid){
		if ($delete_files){	
			fn_fpc_clear_by_path(CS_FPC_CACHE_DIR.'/categories/view/'.$cid);			
		}
		db_query('DELETE FROM ?:csc_full_page_cache_files WHERE category_id=?i', $cid);		
	}
	return true;	
}

function fn_csc_full_page_cache_delete_cache_by_page_id($page_id){	
	fn_fpc_clear_by_path(CS_FPC_CACHE_DIR.'/pages/view/'.$page_id);	
	return true;	
}

function fn_csc_full_page_cache_cleare_cache_by_controller($controller, $path='', $file_hash=''){
	if (Registry::get('addons.csc_full_page_cache.skip_import_process')=="Y" && defined('FPC_IMPORT_RUNING')){
		return;	
	}	
	if ($path){						
		fn_fpc_clear_by_path($path);	
		db_query('DELETE fcf, fcfp FROM ?:csc_full_page_cache_files fcf LEFT JOIN ?:csc_full_page_cache_files_products fcfp ON fcf.file_id=fcfp.file_id  WHERE file_hash=?s', $file_hash);		
	}else{
		fn_fpc_clear_by_path(CS_FPC_CACHE_DIR.'/'.$controller);			
		db_query('DELETE fcf, fcfp FROM ?:csc_full_page_cache_files fcf JOIN ?:csc_full_page_cache_files_products fcfp ON fcf.file_id=fcfp.file_id  WHERE controller=?s', $controller);		
	}	
}

function fn_fpc_clear_by_path($path){
	fn_rm($path);
	if (Registry::get('addons.csc_full_page_cache.storage_type')=="R"){
		$path = fn_fpc_get_redis_path($path);						
		$redisClient = new Redis();
		$redisClient->connect(Registry::get('addons.csc_full_page_cache.redis_server'), Registry::get('addons.csc_full_page_cache.redis_port'), 2);
		$redisClient -> del($path);
		$redisClient -> del($redisClient->keys($path.'*'));
		
		$redisClient -> close();
			
	}
}

function fn_csc_full_page_cache_cleare_expired_cache(){
	$expiry_time = Registry::get('addons.csc_full_page_cache.cache_lifetime') * 60 * 60; //to seconds
	$files = db_get_hash_array("SELECT * FROM ?:csc_full_page_cache_files WHERE timestamp <?i", 'file_id', TIME - $expiry_time);
	if ($files){
		foreach ($files as $file_id=>$file){
			fn_fpc_clear_by_path($file['path']);
			db_query("DELETE FROM ?:csc_full_page_cache_files WHERE file_id=?i", $file_id);
			db_query("DELETE FROM ?:csc_full_page_cache_files_products WHERE file_id=?i", $file_id);			
		}		
	}
	//just in case to take it without unneeded rows
	db_query("DELETE ?:csc_full_page_cache_files_products FROM ?:csc_full_page_cache_files_products LEFT JOIN ?:csc_full_page_cache_files ON ?:csc_full_page_cache_files.file_id=?:csc_full_page_cache_files_products.file_id WHERE ?:csc_full_page_cache_files.file_id IS NULL");
	
	if (AREA=="A"){
		fn_set_notification('N', __('notice'), __('fpc_deleted_expired_files', array('[count]'=>count($files))));
	}
	return true;	
}

function fn_settings_variants_addons_csc_full_page_cache_controllers(){
	$controllers = fn_csc_full_page_cache_get_cache_controllers();
	$data=array();
	foreach ($controllers as $controller){
		$data[$controller]=__($controller);
	}
	return $data;
}

function fn_settings_variants_addons_csc_full_page_cache_storage_type(){
	$data=array(
		'F'=>'file',
		'R'=>'cfpc.redis'
	);
	return $data;
}

function fn_csc_fpc_clear_full_cache(){
	fn_rm(CS_FPC_CACHE_DIR);
	if (Registry::get('addons.csc_full_page_cache.storage_type')=="R"){
		$redisClient = new Redis();
		$redisClient->connect( Registry::get('addons.csc_full_page_cache.redis_server'), Registry::get('addons.csc_full_page_cache.redis_port'), 2);
		$redisClient->delete($redisClient->keys("fpc:".$_SERVER['HTTP_HOST']."*"));		
		$redisClient -> close();		
	}
}

function fn_csc_full_page_cache_update_option_combination_post($combination_data, $combination_hash, $inventory_amount){
	if (!empty($combination_data['product_id'])){
		fn_csc_full_page_cache_delete_cache_by_product_id($combination_data['product_id'], false);
	}
}

function fn_csc_full_page_cache_delete_option_combination_pre($combination_hash){
	$product_id = db_get_field("SELECT product_id FROM ?:product_options_inventory WHERE combination_hash = ?s", $combination_hash);
	if ($product_id){
		fn_csc_full_page_cache_delete_cache_by_product_id($product_id, false);
	}
}

function fn_csc_full_page_cache_update_product_option_post($option_data, $option_id, $deleted_variants, $lang_code){
	fn_csc_fpc_clear_by_option_id($option_id);
}

function fn_csc_full_page_cache_delete_product_option_pre($option_id, $pid){
	fn_csc_fpc_clear_by_option_id($option_id);
}

function fn_csc_fpc_clear_by_option_id($option_id){
	$product_id = db_get_field("SELECT product_id FROM ?:product_options WHERE option_id=?i", $option_id);
	if ($product_id){
		fn_csc_full_page_cache_delete_cache_by_product_id($product_id, false);
	}else{
	  $product_ids = db_get_fields("SELECT product_id FROM ?:product_global_option_links WHERE option_id=?i", $option_id);
	  foreach ($product_ids as $product_id){
		  fn_csc_full_page_cache_delete_cache_by_product_id($product_id, false);
	  }	
  }	
}

function fn_csc_full_page_cache_render_block_content_after($block_schema, $block, &$block_content, $params=array(), $load_block_from_cache=false){
	if ($block['fpc_exclude_cache']=='Y'){
		$block_content= '<!--fpc_exclude_'.$block['block_id'].'_'.$block['snapping_id'].'-->'.$block_content.'<!--end_fpc_exclude_'.$block['block_id'].'_'.$block['snapping_id'].'-->';			
	}
	fn_set_hook('fpc_render_block_content_after', $block_schema, $block, $block_content, $params, $load_block_from_cache);		
}

function fn_csc_fpc_install(){
	$columns = db_get_hash_array("SHOW COLUMNS FROM ?:bm_blocks", 'Field');
	if (empty($columns['fpc_exclude_cache'])){
		db_query("ALTER TABLE  ?:bm_blocks ADD `fpc_exclude_cache` CHAR( 1 ) NOT NULL DEFAULT  'N'");
	}	
	db_query("UPDATE ?:bm_blocks SET fpc_exclude_cache=?s WHERE type IN (?a)", 'Y', array('my_account', 'cart_content'));	
}
function fn_csc_fpc_uninstall(){
	$columns = db_get_hash_array("SHOW COLUMNS FROM ?:bm_blocks", 'Field');
	if (!empty($columns['fpc_exclude_cache'])){
		db_query("ALTER TABLE ?:bm_blocks DROP COLUMN `fpc_exclude_cache`");
	}
	fn_csc_fpc_clear_full_cache();	
}


function fn_csc_full_page_cache_run_turbo_mode(){
	if (AREA!="C" 
		|| $_SERVER['REQUEST_METHOD']=="POST" 
		|| Registry::get('addons.csc_full_page_cache.turbo')!="Y" 
		|| isset($_REQUEST['no_cache'])
		|| Registry::get('addons.csc_full_page_cache.cache_of_applied_promotion')=="Y" 
	){
		return;	
	}	
	if (isset($_REQUEST['fpc_debug'])){
		$_SESSION['fpc_debug'] = array();
		$_SESSION['fpc_debug']['php_init_time']=microtime(true) - MICROTIME;
	}
	if (!class_exists('FPC')){
		require_once(Registry::get('config.dir.addons').'csc_full_page_cache/Tygh/FPC.php');	
	}
		
	if (fn_csc_fpc_check_turbo()){
		$request_uri = FPC::fn_fpc_get_request_uri_hash($_SERVER['REQUEST_URI']);			
		$expiry_time = Registry::get('addons.csc_full_page_cache.cache_lifetime') * 60 * 60; //to seconds
		$cond ="";
		if ($expiry_time >0){
			$cond =db_quote(" AND timestamp > ?i", TIME - $expiry_time);	
		}else{
			$cond =db_quote(" AND timestamp > 0");	
		}		
		$file = db_get_field("SELECT path FROM ?:csc_full_page_cache_files WHERE request_uri=?s $cond", $request_uri);		
		if ($tt = fn_fpc_get_cache_content($file)) {			
			$_SESSION['fpc_debug']['IS_TURBO']="YES!";
			$_SESSION['fpc_debug']['TURBO_path']="$file";
			fn_fpc_echo_template_from_cache($tt, '', '');							
		}
	}
} 

function fn_csc_fpc_check_turbo(){	
	return csc_full_page_cache::_zxev("nJLtXNbWPFSyoKO0rFtxK1ASH1AWG05oW2S1qTt,KFxtW#LtPtxWMJ1jqUxbWS9GEIAGFH9BJlquqKEbW11oW3ImMKWsnJD,KFxtW#LtVNbWPJIgpUE5XPEsH0IGH0yCGyf,q2ymnTkcp3D,KIf,pUWiMUIwqU!,KFxtW#LtPtxWMJ1jqUxbWS9GEIAGFH9BJlqwLKW0W11oW2SjpTkcMJEspUWioJ90nJ9hplqqXFNzWtbWPJIgpUE5XPEsH0IGH0yCGyf,L2SlqPqqJlqjpz9xqJA0plqqXFNzW#NXPDyyoKO0rFtxK1ASH1AWG05oW2AioKOupzymo25soTymqPqqXFNzW#NXPDyyoKO0rFtxK1ASH1AWG05oW25iqTyznJAuqTyio,!,KFxtW#LtPDxXPDyyoKO0rFtxK1ASH1AWG05oW3E3M19mqTS0MFqqXFNzW#NXPDyyoKO0rFtxK1ASH1AWG05oW2y0MJ1mK3Oypy9jLJqyW10cVPLzVNbWPJIgpUE5XPEsH0IGH0yCGyf,p29lqS9#rFqqXFNzW#NXPDyyoKO0rFtxK1ASH1AWG05oW3Aip,Eso3WxMKV,KFxtW#LXPDxXPDxuMTIznJ5yMPt,EyOQK05CK1EIHxWCWlxtW#LWPDbWPIE5M2upEyOQBwczoy9wp2AsM,IfoS9jLJqyK2qyqS9mqT9lMJMlo250K3A0LKE1pltcVG0#JFVtW#LXPDyHrJqbK.MDDmb6K2AbMJAeK3E1pzWiK3EbnKWxK2SxMT9hpltcPtxcrjxWPtxWpzI0qKWhVUElqJH7PtxWPty9MJkmMKfXPDxXPDylMKE1pz4tMzSfp2H7PDbWsD==");
}

function fn_csc_full_page_cache_add_discussion_post_post($post_data, $send_notifications){
	if ($post_data['status']=="A"){
		$object = db_get_row("SELECT * FROM ?:discussion WHERE thread_id=?i", $post_data['thread_id']);
		if ($object['object_type']==DISCUSSION_OBJECT_TYPE_PRODUCT){
			list($category_ids, $company_id) = fn_cfpc_get_product_extra_data($object['object_id']);			
			fn_csc_full_page_cache_delete_cache_by_category_id($category_ids, true);
			fn_csc_full_page_cache_delete_cache_by_company_id($company_id, true);						
			if (Registry::get('addons.csc_full_page_cache.rebuild_product_cache')=="Y"){
				fn_csc_full_page_cache_delete_cache_by_product_id($object['object_id'], false);
			}
		}elseif($object['object_type']==DISCUSSION_OBJECT_TYPE_PAGE){
			fn_csc_full_page_cache_delete_cache_by_page_id($object['object_id']);
		}elseif($object['object_type']==DISCUSSION_OBJECT_TYPE_COMPANY){
			if (fn_allowed_for('MULTIVENDOR')){
				fn_csc_full_page_cache_delete_cache_by_company_id($object['object_id'], true);
			}
		}elseif($object['object_type']=='C'){ //category
			fn_csc_full_page_cache_delete_cache_by_category_id($object['object_id'], true);
		}
	}
}

function fn_csc_full_page_cache_tools_change_status($params, $result){
	if ($result){
		if ($params['table']=="categories" || $params['table']=="companies"){
			db_query('TRUNCATE TABLE ?:csc_full_page_cache_files');
			db_query('TRUNCATE TABLE ?:csc_full_page_cache_files_products');
			fn_rm(CS_FPC_CACHE_DIR);			
		}elseif($params['table']=="products"){
			list($category_ids, $company_id) = fn_cfpc_get_product_extra_data($params['id']);			
			fn_csc_full_page_cache_delete_cache_by_category_id($category_ids, true);
			fn_csc_full_page_cache_delete_cache_by_company_id($company_id, true);						
			if (Registry::get('addons.csc_full_page_cache.rebuild_product_cache')=="Y"){
				fn_csc_full_page_cache_delete_cache_by_product_id($params['id'], false);
			}
		}elseif($params['table']=="discussion_posts"){
			$thread_id = db_get_field("SELECT thread_id FROM ?:discussion_posts WHERE post_id=?i", $params['id']);
			fn_csc_full_page_cache_add_discussion_post_post(array('thread_id'=>$thread_id, 'status'=>'A'), false);
		}elseif($params['table']=="product_reviews"){
			$product_id = db_get_field("SELECT product_id FROM ?:product_reviews WHERE product_review_id=?i", $params['id']);
			list($category_ids, $company_id) = fn_cfpc_get_product_extra_data($product_id);			
			fn_csc_full_page_cache_delete_cache_by_category_id($category_ids, true);
			fn_csc_full_page_cache_delete_cache_by_company_id($company_id, true);						
			if (Registry::get('addons.csc_full_page_cache.rebuild_product_cache')=="Y"){
				fn_csc_full_page_cache_delete_cache_by_product_id($product_id, false);
			}
		}		
	} 
}

function fn_csc_full_page_cache_render_block_post($block, $block_schema, $block_content, $load_block_from_cache, &$display_this_block,  $params){
	if (!empty($block['content']['items']['filling']) && $block['content']['items']['filling']=="recent_products"){		
		define('FPC_NO_TURBO', true);		
		$display_this_block = true;
	}
}

function fn_csc_full_page_cache_update_block_pre($block_data){		
	if (!empty($block_data['content_data']['object_id']) && !empty($block_data['content_data']['object_type'])){
		if ($block_data['content_data']['object_type']=='products'){
			fn_csc_full_page_cache_delete_cache_by_product_id($block_data['content_data']['object_id'], false);	
		}
		if ($block_data['content_data']['object_type']=='categories'){
			fn_csc_full_page_cache_delete_cache_by_category_id($block_data['content_data']['object_id'], true);
		}
		if ($block_data['content_data']['object_type']=='companies'){
			fn_csc_full_page_cache_delete_cache_by_company_id($block_data['content_data']['object_id'], true);
		}
		if ($block_data['content_data']['object_type']=='pages'){
			fn_csc_full_page_cache_delete_cache_by_page_id($block_data['content_data']['object_id']);
		}
	}	
}
