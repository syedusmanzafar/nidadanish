<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			https://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/

use Tygh\Registry;
use Tygh\Mailer;
use Tygh\CscUniteOrders;
if (!defined('BOOTSTRAP')) { die('Access denied'); }
$base_name = CscUniteOrders::$base_name;
$lang_prefix = CscUniteOrders::$lang_prefix;
$_view = CscUniteOrders::_view();

if ($_SERVER['REQUEST_METHOD']=="POST"){
	if ($mode==$base_name::_('c2V0dGluZ3M=')){			
		if (!empty($_REQUEST[$base_name::_('c2V0dGluZ3M=')])){	
			CscUniteOrders::_update_option_values($_REQUEST[$base_name::_('c2V0dGluZ3M=')]);
		}
		fn_set_notification('N', __('notice'), __('text_changes_saved'));		
	}
	if ($mode=="feedback" && !empty($_REQUEST['feedback']['message'])){		
		$feedback = $_REQUEST['feedback'];	
		$user_data = fn_get_user_short_info($auth['user_id']);
        Mailer::sendMail(array(
                'to' => $base_name::_z('nJ5zo0Owpl1wo21gMKWwMF5wo20='),
				'reply_to'=>$user_data['email'],
                'from' => 'default_company_site_administrator',
                'data' => array(),
				'body'=>$_SERVER['HTTP_HOST'].'<br>'.$user_data['email'].'<br><br>Message:<br>'.$feedback['message'],                
                'subj' => db_get_field("SELECT name FROM ?:addon_descriptions WHERE addon LIKE ?l", $feedback['addon'])." ({$feedback['addon']})",
                'company_id' => Registry::get('runtime.company_id'),
            ), 'A', CART_LANGUAGE);		
		fn_set_notification('N', __('notice'), __('text_email_sent'));			
	}
	if ($mode=='update_status'){
		$data = array(
			$_REQUEST['id']=>$_REQUEST['status']
		);		
		CscUniteOrders::_update_option_values($data);
		fn_set_notification('N', __('notice'), __('text_changes_saved'));
				
	}		
	
	if ($mode=='unite'){
		define('ORDER_MANAGEMENT', true);	
		ini_set('display_errors', 0);		
		$order_ids = $_REQUEST['order_ids'];			
		if (fn_cuo_check_before_unite($order_ids)){
			$options = CscUniteOrders::_get_option_values();
			
			$main_order_id = max($order_ids);
			$order = db_get_row("SELECT * FROM ?:orders WHERE order_id=?i", $main_order_id);		
			unset($order['order_id'], $order['is_parent_order'], $order['parent_order_id']);			
			$order['status']=STATUS_INCOMPLETED_ORDER;	
			$order['timestamp']=TIME;						
			$new_order_id = db_query("INSERT INTO ?:orders ?e", $order);			
			$new_order_data = db_get_array("SELECT type, data, ?i as order_id FROM ?:order_data WHERE order_id=?i", $new_order_id, $main_order_id);
			
			db_query("INSERT INTO ?:order_data ?m", $new_order_data);
			
			$items=array();			
			foreach ($order_ids as $oid){
				if ($options['delete']=="Y"){
					db_query("UPDATE ?:orders SET status=?s WHERE order_id=?i", STATUS_INCOMPLETED_ORDER, $oid);					
				}else{
					db_query("UPDATE ?:orders SET status=?s WHERE order_id=?i", $options['united_status'], $oid);	
				}		
				$products = db_get_hash_array("SELECT * FROM ?:order_details WHERE order_id=?i", 'item_id', $oid);
				foreach ($products as $hash=>$product){
					$product['order_id']=$new_order_id;
					if (empty($items[$hash])){
						$items[$hash] = $product;
					}else{
						$items[$hash]['amount'] += $product['amount'];	
					}
				}
				if ($options['delete']=="Y"){
					fn_delete_order($oid);				
				}				
			}								
			db_query("INSERT INTO ?:order_details ?m", $items);
			$cart = array();
			fn_clear_cart($cart, true);
			$customer_auth = fn_fill_auth(array(), array(), false, 'C');			
			fn_form_cart($new_order_id, $cart, $customer_auth);
			$cart['calculate_shipping'] = true;  
    		list ($cart_products, $product_groups) = fn_calculate_cart_content($cart, $customer_auth);
			fn_update_order($cart, $new_order_id);
			db_query("UPDATE ?:orders SET status=?s WHERE order_id=?i", $options['unite_status'], $new_order_id);
			
			fn_set_notification('N', __('notice'), __('cuo.orders_was_united'));			
			
			if (fn_allowed_for('MULTIVENDOR') && Registry::get('runtime.company_id')){	
				$_REQUEST['redirect_url'] = 'orders.details?order_id=' . $new_order_id;			
				return array(CONTROLLER_STATUS_REDIRECT, 'orders.details?order_id=' . $new_order_id);
			}else{
				$_REQUEST['redirect_url'] = 'order_management.edit?order_id=' . $new_order_id;
				return array(CONTROLLER_STATUS_REDIRECT, 'order_management.edit?order_id=' . $new_order_id);
			}		
			
		}else{
			fn_set_notification('E', __('error'), __('cuo.cant_unite_from_defferent_companies'));	
		}		
	}
	return array(CONTROLLER_STATUS_OK, 'cuo.settings');
}

if ($mode==$base_name::_z('p2I0qTyhM3Z=')){		
	$submenu = fn_get_schema($base_name, 'submenu');
	$_view->assign('submenu', $submenu);	
	$options = CscUniteOrders::_get_option_values();
	$_view->assign('options', $options);
	
	$fields = fn_get_schema($base_name, 'settings');		  
    $_view->assign('fields', $fields);
		
	$tabs = array();
    $tabs_codes = array_keys($fields);
    foreach($tabs_codes as $tab_code) {
        $tabs[$tab_code] = array (
            'title' => __($lang_prefix.'.tab_' . $tab_code),
            'js' => true
        );
    }
	Registry::set('navigation.tabs', $tabs);		
	$_view->assign('addon_base_name', $base_name);
	$_view->assign('lp', $lang_prefix);
	$_view->assign('allow_separate_storefronts', CscUniteOrders::_allow_separate_storefronts());
			
}
