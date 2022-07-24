<?php
namespace Tygh;
use Tygh\Registry;
use Tygh\FPCMobileDetect;

class FPC {
  public static function fn_fpc_check_static_files_if_exists($tt){
		$files_to_check=array();
		$regexes = array(
			'link'=>'/<link [^>]+href=([\'"])(?<link>.+?)\1[^>]*>/i',
			'script'=>'/<script [^>]+src=([\'"])(?<script>.+?)\1[^>]*>/i'					
		);
		foreach($regexes as $k=> $regex){
			preg_match_all($regex, $tt, $matches);
			if (!empty($matches) && !empty($matches[$k])) {
				$files_to_check = array_merge($files_to_check, $matches[$k]);								
			}					
		}				
		$all_files_in_place=true;
		if ($files_to_check){
			foreach ($files_to_check as $file){
				if (strpos($file, Registry::get('config.current_location'))!==false && strpos($file, 'var/cache/')!==false){
					$file = str_replace(Registry::get('config.current_location'), '', $file);
					if ($pos = strpos($file, "?")){
						$file = substr($file, 0, $pos);
					}											
					if (!file_exists(DIR_ROOT.$file)){
						if (isset($_REQUEST['fpc_debug'])){
							$_SESSION['fpc_debug']['js/css']='SOME FILE NOT LOADED! FULL CACHE WILL NOT WORK!';
						}																			
						return false;
					}
				}						
			}	
		}
		if (isset($_REQUEST['fpc_debug'])){
			$_SESSION['fpc_debug']['js/css']='loaded, all found';
		}
		return true;
	}
	
	public static function fn_csc_fpc_SecurityHash($content){
		$hash = fn_generate_security_hash();
		$content = preg_replace('/<input type="hidden" name="security_hash".*?>/i', '', $content);	
		$content = str_replace('</form>', '<input type="hidden" name="security_hash" class="cm-no-hide-input" value="'. $hash .'" /></form>', $content);
		
		$content = preg_replace("/_.security_hash='.*?';/i", 'csfpc_security_hash', $content);
		$content = str_replace('csfpc_security_hash', "_.security_hash='".$hash."';", $content);
		return $content;
	}
	public static function _define_current_url($return_tt=false){
		
		if (version_compare(PRODUCT_VERSION, '4.3.1', '>')){	
			 if (defined('AJAX_REQUEST') && Registry::get('runtime.root_template') == 'index.tpl') {
				Tygh::$app['ajax']->assign('current_url', fn_url(Registry::get('config.current_url'), AREA, 'current'));
			}
			if ($return_tt){										
				$tt = Tygh::$app['view']->fetch(Registry::get('runtime.root_template'));
			}
		}else{
			if (defined('AJAX_REQUEST') && Registry::get('runtime.root_template') == 'index.tpl') {
				Registry::get('ajax')->assign('current_url', fn_url(Registry::get('config.current_url'), AREA, 'current'));
			}
			if ($return_tt){
				$tt = Registry::get('view')->fetch(Registry::get('runtime.root_template'));	
			}
		}
		if ($return_tt){
			return $tt;	
		}
	}
	public static function fn_fpc_check_controller_availability($controller, $mode=''){
		$available = true;
		$schemas = fn_get_schema('csc_full_page_cache', 'schemas');		
		if (in_array($controller, $schemas['deprecated_controllers'])){
			$available= false;	
		}
		self::_check_controller_availability_third_addons($available);
		
		fn_set_hook('fpc_check_controller_availability', $available);	
		return $available;	
	}
	public static function fn_csc_full_page_get_storefront_status(){
		$status='N';		
		if (version_compare(PRODUCT_VERSION, '4.10.1', '<')){			
			if (Registry::get('settings.General.store_mode') == 'Y') {
				$status='Y';
			}
		}else{			
			$status = db_get_field("SELECT status FROM ?:storefronts WHERE storefront_id=?i", Registry::get('runtime.company_id'));		
		}
		return $status;
	}
	
	public static function fn_csc_full_page_check_get_cache_availibility($controller, $mode=''){
		$allow=true;
		if (!in_array($controller, fn_csc_full_page_cache_get_cache_controllers())){
			$allow=false;
		}	
		
		if ($_SERVER['REQUEST_METHOD']=="POST"){
			$allow=false;
		}	
		if (!empty($_REQUEST['action']) && $_REQUEST['action']=="preview"){
			$allow=false;
		}
		if (!empty($_REQUEST['skey'])){
			$allow=false;
		}
		if (!empty($_REQUEST['sent'])){
			$allow=false;
		}
		if (isset($_REQUEST['rid'])){
			$allow=false;
		}	
			
		$allowed_controlers = Registry::get('addons.csc_full_page_cache.controllers');
		if (!in_array($controller, $allowed_controlers)){
			$allow=false;
		}		
		if (!empty($_SESSION['notifications'])){
			$allow=false;
		}
		if ($controller=="products" && $mode=="options"){
			$allow=false;
		}
		if (!empty($_REQUEST['features_hash']) && Registry::get('addons.csc_full_page_cache.no_cache_filters')=="Y"){
			$allow=false;	
		}
		if (Registry::get('addons.csc_full_page_cache.disable_for_auth') == 'Y' && !empty($_SESSION['auth']['user_id'])) {
			$allow=false;
		}
		if (Registry::get('addons.csc_full_page_cache.skip_ajax') == 'Y' && defined('AJAX_REQUEST')){
			$allow=false;
		}
		if (Registry::get('runtime.customization_mode.live_editor') || !empty($_SESSION['customize_theme'])){
			$allow=false;	
		}
		if (self::fn_csc_full_page_get_storefront_status()=="Y"){
			$allow=false;
			$_SESSION['fpc_debug']['store_is_closed'] = 'Yes';	
		}
					
		if (version_compare(PRODUCT_VERSION, '4.10.1', '>=') && $_SESSION['auth']['user_type']=="A"){
			$allow=false;
			$_SESSION['fpc_debug']['is_admin_logged'] = 'Yes';
		}
		
		if (version_compare(PRODUCT_VERSION, '4.12.1', '>=') && $_SESSION['auth']['user_type']=="V"){
			$allow=false;
			$_SESSION['fpc_debug']['is_vendor_logged'] = 'Yes';
		}
		
		self::_get_cache_availibility_third_addons($allow);
		
		fn_set_hook('fpc_check_get_cache_availibility', $allow);	
		
		return $allow;
	}
	public static function fn_csc_full_page_check_save_cache_availibility($controller, $mode=''){
		$allow=self::fn_csc_full_page_check_get_cache_availibility($controller, $mode);
		
		if (Registry::get('addons.csc_full_page_cache.no_generate_from_auth') == 'Y' && !empty($_SESSION['auth']['user_id'])) {
			$allow=false;
		}
		/*if (!empty($_REQUEST['gclid']) || !empty($_REQUEST['yclid'])){
			$allow=false;
		}*/		
		self::_check_allow_save_from_third_addons($allow);
		
		fn_set_hook('fpc_check_save_cache_availibility', $allow);
		return $allow;
	}
	
	private static function fn_csc_fpc_get_request_data(){		
		static $params=array();		
		if (empty($params)){
			$params=$_REQUEST;			
			$params['request_uri']=$_SERVER['REQUEST_URI'];			
			$schemas = fn_get_schema('csc_full_page_cache', 'schemas');			
			self::fn_remove_service_params($params['request_uri']);			
			if (isset($params['features_hash']) && empty($params['features_hash'])){
				unset($params['features_hash']);				
			}
			foreach($schemas['ignored_params'] as $p){
				if (isset($params[$p])){
					unset($params[$p]);
				}
			}	
		}
		return $params;
	}
	
	private static function fn_remove_service_params(&$request_uri){
		$url_components = parse_url($request_uri);
		if (!empty($url_components['query'])){
			parse_str($url_components['query'], $queries);		
			$params=$_REQUEST;
			$schemas = fn_get_schema('csc_full_page_cache', 'schemas');		
			if (isset($params['features_hash']) && empty($params['features_hash'])){
				$schemas['ignored_params'][]='features_hash';				
			}
			foreach($schemas['ignored_params'] as $p){
				if (isset($queries[$p])){
					unset($queries[$p]);
				}
			}
			ksort($queries);
			$request_uri = $url_components['path'].'?'.http_build_query($queries);	
		}
	}
	
	public static function fn_csc_full_page_cache_get_cache_path($check_expiry=false){
		$controller = Registry::get('runtime.controller');
		$mode = Registry::get('runtime.mode');	
		$params=self::fn_csc_fpc_get_request_data();
				
		self::_store_session_params($params, $controller, $mode);
				
		if (!empty($params['category_id']) || in_array($mode, array('bestsellers', 'newest', 'on_sale', 'search'))){
			if (empty($params['items_per_page']) && !empty($_SESSION['items_per_page'])) {
				$params['items_per_page'] = $_SESSION['items_per_page'];
			}
			if (empty($params['sort_by']) && !empty($_SESSION['sort_by'])) {
				$params['sort_by'] = $_SESSION['sort_by'];
			}
			if (empty($params['sort_order']) && !empty($_SESSION['sort_order'])) {
				$params['sort_order'] = $_SESSION['sort_order'];
			}
			$params['layout'] = self::_get_products_layout($params);			
		}
		
		if (empty($params['page'])){
			$params['page']=1;
		}	
					
		fn_set_hook('fpc_cache_handlers', $controller, $mode, $params);
		
		$data=array();
		if ($params){
			foreach ($params as $k=>$v){
				$data[$k] = $v;						
			}		
		}
		$data['http_host']=$_SERVER['HTTP_HOST'];
		$data['company_id']=Registry::get('runtime.company_id');	
		$data['mode']=$mode;
		$data['controller']= $controller;
		if (!empty($_REQUEST['sl'])){
			$data['language']=$_REQUEST['sl'];
		}else{
			$data['language']=CART_LANGUAGE;
		}
		if (!empty($_REQUEST['currency'])){
			$data['currency']=$_REQUEST['currency'];
		}else{
			$data['currency']=CART_SECONDARY_CURRENCY;
		}		
		$data['https']=defined('HTTPS')?true:false;
		if (!empty($_SESSION['use_mobile_skin'])){
			$data['mobile_skin']=$_SESSION['use_mobile_skin'];
		}
		if (Registry::get('addons.csc_full_page_cache.mobile_devices')=="Y"){		
			$data['mobile_device']=self::fn_csc_fpc_mobile_detect_device();		
		}
		if (Registry::get('addons.csc_full_page_cache.cache_of_usergroup') == 'Y') {
			$usergroups = $_SESSION['auth']['usergroup_ids'];
			sort($usergroups);
			$data['usergroups'] = $usergroups;
		}
		if (Registry::get('addons.csc_full_page_cache.cache_of_applied_promotion') == 'Y') {
			if (!empty($_SESSION['cart']['applied_promotions'])){
				$applied_promotions = $_SESSION['cart']['applied_promotions'];			
				$data['applied_promotions'] = sort($applied_promotions);
			}			 
			$data['active_promotions'] = db_get_fields("SELECT promotion_id FROM ?:promotions WHERE status=?s AND IF(from_date, from_date <= ?i, 1) AND IF(to_date, to_date >= ?i, 1)", 'A', TIME, TIME);			
		}
		
		if ($controller=="products" && $mode=="search"){
			$data['search_string']=md5(serialize($params));
		}
		self::_get_cache_path_third_addons($data, $params, $controller, $mode);
		
		if (Registry::get('addons.csc_full_page_cache.compress_cache')=="Y"){
			$data['gz'] ='Y';
		}
		
		fn_set_hook('fpc_get_cache_path', $params, $controller, $mode, $data);			
		$file_hash = substr(md5(serialize($data)), 0, 12);	
				
		$path = self::fn_csc_get_cache_directory($params).$file_hash;	
		if ($check_expiry){
			$file_id=$path;
			$expiry_time = Registry::get('addons.csc_full_page_cache.cache_lifetime') * 60 * 60; //to seconds
			$file_timestamp=db_get_field("SELECT timestamp FROM ?:csc_full_page_cache_files WHERE file_hash=?s", $file_hash);
			if ($expiry_time > 0 || $file_timestamp < 0 || $file_timestamp==''){			
				if (($file_timestamp && $file_timestamp + $expiry_time < TIME) || $file_timestamp < 0 || $file_timestamp==''){
					fn_csc_full_page_cache_cleare_cache_by_controller($controller, $path, $file_hash);
				}
			}
		}	
		
		return array($path, $file_hash); 
	}
	
	private static function _get_products_layout($params){
		if (Registry::get('addons.abt__unitheme2.status')=="A" &&  function_exists('fn_abt__unitheme2_dispatch_assign_template')){
			fn_abt__unitheme2_dispatch_assign_template();
		}
		$layout = fn_get_products_layout($params);			
		fn_set_hook('fpc_get_layout_post', $layout, $params);
		return $layout;				
		
	}
	
	public static function fn_csc_fpc_mobile_detect_device(){		
		//AB Unitheme2 Compatibility
		if (function_exists('fn_abt__ut2_get_device_type')){
			return fn_abt__ut2_get_device_type();			 
		}	
		//Energothemes Vivashop
		if (function_exists('et_get_device')){
			return et_get_device();
		}		
		if (!class_exists('FPCMobileDetect')){
			require_once(Registry::get('config.dir.addons').'csc_full_page_cache/Tygh/FPCMobileDetect.php');					
		}	
		$detect = new FPCMobileDetect;	
		if ($detect->isTablet() || $detect->isiPad()) {
			$device = 'T';
		} elseif ($detect->isMobile()) {
			$device = 'M';			
		} else {
			$device = 'D';
		}
		
		return $device;
	}
	
	public static function fn_csc_get_cache_directory($params=array()){
		$controller = Registry::get('runtime.controller');
		$mode = Registry::get('runtime.mode');	
		$suffix='';
		if (!empty($params['product_id'])){		
			$subdir = floor($params['product_id'] / (CS_FPC_MAX_FILES_IN_DIR*100)).'/';
			$suffix = $subdir . floor($params['product_id'] / CS_FPC_MAX_FILES_IN_DIR).'/';
		}			
		if (!empty($params['category_id'])){		
			$suffix .= $params['category_id'].'/';
		}
		if (!empty($params['page_id'])){		
			$suffix .= $params['page_id'].'/';
		}	
		
		if (!empty($params['company_id'])){	
			return CS_FPC_CACHE_DIR.'/'.$controller.'/company_id_'.$params['company_id'].'/'.$mode.'/'.$suffix;
		}else{
			return CS_FPC_CACHE_DIR.'/'.$controller.'/'.$mode.'/'.$suffix;
		}
		
	}
	
	public static function fn_fpc_get_request_uri_hash($request_uri){
		self::fn_remove_service_params($request_uri);		
		$extra = array(
			'host'=>$_SERVER['HTTP_HOST'],
			'lang_code'=>@$_SESSION['settings']['cart_languageC']['value'],
			'currency'=>@$_SESSION['settings']['secondary_currencyC']['value'],
			'company_id'=>Registry::get('runtime.company_id'),
			'HTTPS'=>defined('HTTPS') ? true : false
		);
		if (Registry::get('addons.csc_full_page_cache.mobile_devices')=="Y"){		
			$extra['mobile_device']=self::fn_csc_fpc_mobile_detect_device();		
		}
		if (!empty($_SESSION['use_mobile_skin'])){
			$extra['mobile_skin']=$_SESSION['use_mobile_skin'];
		}
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
			$extra['HTTP_X_REQUESTED_WITH']=$_SERVER['HTTP_X_REQUESTED_WITH'];	 		
		}		
		if (function_exists('fn_ab__ab_is_bot')){
			$extra['fn_ab__ab_is_bot']=fn_ab__ab_is_bot();			
		}
		/*WEBP SUPPORT*/		
		$extra['image/webp'] = self::_check_webp();
							
		return substr(md5($request_uri.json_encode($extra)), 0, 12);
	}
	
	
	
	public static function _set_product_popularity($product_id, $popularity_view = POPULARITY_VIEW){
		if (empty($_SESSION['products_popularity']['viewed'][$product_id])) {
			$_data = array (
				'product_id' => $product_id,
				'viewed' => 1,
				'total' => $popularity_view
			);		
			db_query("INSERT INTO ?:product_popularity ?e ON DUPLICATE KEY UPDATE viewed = viewed + 1, total = total + ?i", $_data, $popularity_view);		
			$_SESSION['products_popularity']['viewed'][$product_id] = true;		
			return true;
		}		
		return false;
	}
	public static function _add_product_to_recently_viewed($product_id, $max_list_size = MAX_RECENTLY_VIEWED)
	{
		$added = false;	
		if (!empty($_SESSION['recently_viewed_products'])) {
			$is_exist = array_search($product_id, $_SESSION['recently_viewed_products']);			
			if ($is_exist !== false) {				
				unset($_SESSION['recently_viewed_products'][$is_exist]);				
				$_SESSION['recently_viewed_products'] = array_values($_SESSION['recently_viewed_products']);
			}
			array_unshift($_SESSION['recently_viewed_products'], $product_id);
			$added = true;
		} else {
			$_SESSION['recently_viewed_products'] = array($product_id);
		}	
		if (count($_SESSION['recently_viewed_products']) > $max_list_size) {
			array_pop($_SESSION['recently_viewed_products']);
		}	
		return $added;
	}
	
	private static function _store_session_params($params, $controller, $mode){
		if (!empty($params['items_per_page'])){
			$_SESSION['items_per_page'] = $params['items_per_page'];	
		}
		if (!empty($params['sort_by'])){
			$_SESSION['sort_by'] = $params['sort_by'];	
		}
		if (!empty($params['sort_order'])){
			$_SESSION['sort_order'] = $params['sort_order'];	
		}
		fn_set_hook('fpc_store_session_params', $params, $controller, $mode);	
	}	
	
	public static function _check_turbo_third_addons(){	
		if (!empty(Registry::get('addons.csc_last_modified')) && Registry::get('addons.csc_last_modified.status')=="A"){
			return false;	
		}		
		return true;	
	}
	private static function _check_allow_save_from_third_addons(&$allow){
		//AB:antibot compatibility
		if (function_exists('fn_ab__ab_is_bot')){			
			$bot=fn_ab__ab_is_bot();
			if ($bot !='N'){
				$allow = false;				
			}
		}		
	}
	private static function _check_controller_availability_third_addons(&$available){
		if(Registry::get('addons.sd_geoip_maxmind.status')=="A" && empty($_SESSION['geoip'])){
			$available=false;
		}	
	}	
	
	private static function _get_cache_path_third_addons(&$data, $params, $controller, $mode){
		/*AB SEO filters compatibility*/
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
			$data['HTTP_X_REQUESTED_WITH']=$_SERVER['HTTP_X_REQUESTED_WITH'];
		}					
		if (Registry::get('addons.ab__seo_filters.status')=="A" && !empty($params['features_hash']) && !empty($_SERVER['REDIRECT_URL']) && !defined('AJAX_REQUEST')){
			$data['filters_path']= $_SERVER['REDIRECT_URL'];														
		}
		if (function_exists('fn_ab__ab_is_bot')){			
			$data['bot']=fn_ab__ab_is_bot();			
		}
		/*AB:UNITHEME2*/
		if (empty($data['layout']) && Registry::get('addons.abt__unitheme2.status')=="A"){			
			$data['layout'] = self::_get_products_layout($params);					
		}
		/*WEBP SUPPORT*/
		$data['image/webp'] = self::_check_webp();
	}
	
	private static function _get_cache_availibility_third_addons(&$allow){
		/*Disable if Twigmo is runing*/
		if (!empty($_SESSION['twg_state'])){
			$allow=false;
		}				
		/*AB SEO filters compatibility*/
		if (Registry::get('addons.ab__seo_filters.status')=="A" && defined('AJAX_REQUEST') && Registry::get('runtime.controller')=="categories"){
			$allow=false;										
		}
		
		/* Age verification */
		if (Registry::get('addons.age_verification.status')=="A"){
			$controller = Registry::get('runtime.controller');
			$mode = Registry::get('runtime.mode');			
			if ($controller=="categories"  && $mode=="view" && !empty($_REQUEST['category_id'])){
				list ($result, $category_id) = fn_age_verification_category_check($_REQUEST['category_id']);
				if ($result){
					$allow=false;	
				}				
			}			
			if ($controller=="products"  && $mode=="view" && !empty($_REQUEST['product_id'])){
				$data = db_get_row("SELECT product_id, age_verification, age_limit FROM ?:products WHERE product_id = ?i", $_REQUEST['product_id']);
				if ($data['age_verification'] == 'Y') {
					$age = !empty($_SESSION['auth']['age']) ? $_SESSION['auth']['age'] : 0;
					if (!$age) {
						$type = 'form';
					} else {
						if ($age < $data['age_limit']) {
							$type = 'deny';
						}
					} 
				}
				if (!isset($type)) {
					$data = db_get_array("SELECT * FROM ?:products_categories WHERE product_id = ?i", $data['product_id']);
					foreach ($data as $record) {
						list ($type, $object_id) = fn_age_verification_category_check($record['category_id']);
						$object = 'category_descriptions';
						if ($type === false) {
							break;
						}
					}
				}
				if (isset($type) && $type !== false) {
					$allow=false;
				}										 	
			}										
		}
		/* Age verification */	
	}
	private static function _check_webp(){
		static $webp;
		if (!isset($webp)){	
			if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false){
				foreach(Registry::get('addons') as $addon_code=>$addon){
					if (strpos($addon_code, 'webp') !== false && $addon['status']=="A"){
						$webp = true;
					}
				}			
			}
		}
		return $webp;
	}		
}
