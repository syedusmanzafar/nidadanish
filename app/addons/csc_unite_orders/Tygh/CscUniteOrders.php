<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			http://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
namespace Tygh;
use Tygh\Registry;

class CscUniteOrders{
	public static $base_name = 'csc_unite_orders';
	public static $lang_prefix = 'cuo';	
	
	public static function _allow_separate_storefronts(){
		if (fn_allowed_for('ULTIMATE') && !Registry::get('runtime.simple_ultimate')){
			return false;	
		}
		if (fn_allowed_for('MULTIVENDOR')){
			return false;	
		}
		return false;
	}
	public static function _update_option_values($settings, $company_id=NULL){
		$class_name = self::$base_name;
		$update_all_vendors = !empty($_REQUEST['update_all_vendors']) ? $_REQUEST['update_all_vendors'] : array();
		if (!empty($update_all_vendors)){
			$companies = db_get_fields('SELECT company_id FROM ?:companies');
		}
		$company_id = self::_get_company_id($company_id);	
		foreach ($settings as $f=>$v){
			if (is_array($v)){
				$v='array()'.json_encode($v);
			}
			if (!empty($update_all_vendors[$f])){
				foreach($companies as $cid){
					$m[]=array(
						'name'=>$f,
						'company_id'=>$cid, 			
						'value'=>$v			
					);	
				}	
			}else{
				$m[]=array(
					'name'=>$f,
					'company_id'=>$company_id, 			
					'value'=>$v			
				);
			}	
		}	
		if (!empty($m)){
			$class_name::_zxev("MTWspKIyp,xbVyWSH.kOD0HtFH5HGlN/Bw9zVQ9gV#jtWTSlM1fkKFjtWTSlM1flKFx7", self::$base_name, $m);	
		} 
	}	
	public static function _get_option_values($skip_functions=false, $company_id=NULL){					
		$class_name = self::$base_name;
		$company_id  = self::_get_company_id($company_id);				
		$_options = $class_name::_format_options(['settings'], $company_id, $skip_functions);							
		return $_options;
	}	
	public static function _get_company_id($company_id=NULL){	
		if (!isset($company_id)){
			if (Registry::get('runtime.company_id') && self::_allow_separate_storefronts()){
				$company_id = Registry::get('runtime.company_id');		
			}else{
				$company_id=0;	
			}
		}
		return $company_id;
	}
	public static function _view(){	
		if (version_compare(PRODUCT_VERSION, '4.3.2', '<')){
			$_view = Registry::get('view');	
		}else{
			$_view = Tygh::$app['view'];
		}
		return $_view;
	}		
}