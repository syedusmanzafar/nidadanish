<?php
use Tygh\Registry;
use Tygh\Enum\UserTypes;

if (!defined('BOOTSTRAP')) {die('Access denied');}


function fn_sc_full_order_print_order_invoices_pre($order_ids, &$params){




    if(count($order_ids) ==1){

        $order_id = current($order_ids);



        $is_parent_order = db_get_field("SELECT is_parent_order FROM ?:orders WHERE order_id =?i",$order_id);

        if($is_parent_order  == "Y") {

            if (AREA == "C") {

                $params['template_code'] = 'sc_one_order_t';

            } elseif (AREA == "A") {
                $params['template_code'] = 'sc_one_order_t_admin';
            }

            if (Registry::get('settings.Appearance.email_templates') != 'old'){



                //Registry::set('settings.Appearance.email_templates','old');

               // $params['html_wrap'] = 'addons/cp_one_order/'



            }
        }
    }
}

function fn_sc_one_order_combine_full_parent_order($order, $child_order_details_info){

    unset($order['products']);

    $order['total'] =            0;
    $order['subtotal'] =   $order['subtotal_discount'] =   $order['payment_surcharge'] =         0;
    $order['discount'] =         0;
    $order['display_subtotal'] = 0;
    $order['tax_subtotal']   =   0;



    foreach ($child_order_details_info as $order_id => $order_child_info) {


        if(!empty($order_child_info['is_sc_united_ship_order']) && $order_child_info['is_sc_united_ship_order'] =='Y'
            && (!Registry::get('runtime.company_id') && Registry::get('runtime.controller') =='orders') ){
           // continue;
        }

        $order['total'] = $order['total'] + $order_child_info['total'];
        $order['subtotal'] = $order['subtotal'] + $order_child_info['subtotal'];
        $order['subtotal_discount'] = $order['subtotal_discount'] + $order_child_info['subtotal_discount'];
        $order['payment_surcharge'] = $order['payment_surcharge'] + $order_child_info['payment_surcharge'];
        $order['discount'] = $order['discount'] + $order_child_info['discount'];
        $order['display_subtotal'] = $order['display_subtotal'] + $order_child_info['display_subtotal'];
        $order['tax_subtotal'] = $order['tax_subtotal'] + $order_child_info['tax_subtotal'];

        if(!empty($order_child_info['products'])){
            foreach ($order_child_info['products'] as $item_key => $item_data){

                $order['products'][$item_key] =$item_data;
            }
        }


    }

    return $order;
}

function fn_sc_full_order_get_order_info(&$order, $additional_data){

    Registry::set('another_cp_one_order_remove_failed', false);


    if(!empty($order['is_parent_order']) && $order['is_parent_order'] == "Y"){
	   
       Registry::set('cp_one_order_remove_failed', false);
       $child_ids = db_get_fields("SELECT order_id FROM ?:orders WHERE parent_order_id = ?i",$order['order_id']);
       if(empty($child_ids)){
           return false;
       }

       Registry::set('another_cp_one_order_remove_failed', true);

       $child_order_details_info = array();
       foreach ($child_ids as $child_id) {
           $order_info = array();
           $order_info = fn_get_order_info($child_id, false, true, true, false);

           if(empty($order_info['company']) && !empty($order_info['company_id'])){
               $order_info['company'] = fn_get_company_name($order_info['company_id']);
           }

           $child_order_details_info[$child_id] =$order_info;
       }

       $order = fn_sc_one_order_combine_full_parent_order($order,$child_order_details_info);
       $order['child_order_details_info'] = $child_order_details_info;

	   Registry::set('cp_one_order_remove_failed', true);
   }
   else{
       $check = Registry::get('cp_one_order_remove_failed');
       $check_another = Registry::get('another_cp_one_order_remove_failed');

   }


   $check = Registry::get('cp_one_order_remove_failed');



    if(AREA == "A") {
        $parent_ids = db_get_fields("SELECT order_id FROM ?:orders WHERE parent_order_id = ?i",$order['order_id']);
        if(!empty($parent_ids)) {
            foreach ($order['products'] as $item_id => $product) {
                $real_order_id = db_get_field("SELECT order_id FROM ?:order_details 
                WHERE item_id =?i and product_id =?i and product_code =?s and order_id IN (?n)", $product['item_id'], $product['product_id'], $product['product_code'],$parent_ids);
                $order['products'][$item_id]['real_order_id'] = $real_order_id;
                $order['products'][$item_id]['real_company_id'] = db_get_field("SELECT company_id FROM ?:orders WHERE order_id =?i",$real_order_id);
            }
        }
    }

    //chagne order is_parent to avoide controller check  for parent order
    if(AREA =="A" || AREA =="C" ){

        if(Registry::get('runtime.controller') =='orders') {

            if (!empty($order['is_parent_order']) && $order['is_parent_order'] == "Y") {
                $order['is_parent_order'] = "N";
                $order['cp_is_need_return_parent_flag'] = true;
            }
        }
    }
}


function fn_sc_full_order_get_orders($params, $fields, $sortings, &$condition, $join, $group)
{
    $auth = Tygh::$app['session']['auth'];
    if($auth['user_type'] =="V"){
        return true;
    }

    if( Registry::get('runtime.controller') == 'exim_1c'  ||   Registry::get('runtime.controller') == 'commerceml') {
        return true;
    }


if( (isset($params['order_id']) && !empty($params['order_id'])) || (isset($params['total_sec_from']) && !empty($params['total_sec_from']))
    || (isset($params['total_sec_to']) && !empty($params['total_sec_to']))
    || (isset($params['status']) && !empty($params['status']))
   ){

    return true;
}


    if (AREA == 'A') {
        $condition = str_replace("AND ?:orders.is_parent_order != 'Y'","",$condition);

        $condition .= " AND ((is_parent_order = 'N' AND parent_order_id = 0) OR (is_parent_order = 'Y')) ";
    } else {
        $condition = str_replace("AND ?:orders.is_parent_order != 'Y'","",$condition);
        $condition .= " AND ((is_parent_order = 'N' AND parent_order_id = 0) OR (is_parent_order = 'Y')) ";
    }
}

