<?php
use Tygh\Registry;
use Tygh\Tools\SecurityHelper;
use Tygh\Languages\Languages;
use Tygh\Tools\Url;
use Tygh\Enum\SiteArea;
use Tygh\Enum\UserTypes;

if (!defined('BOOTSTRAP')) {die('Access denied');}

/* Hooks */

//fn.cart.php fn_get_orders()



function fn_an_one_get_ecl_comission($product_id){


    if(Registry::get('addons.ec_vendor_cost.status') =="A") {

        $commission_per = Registry::get('addons.ec_vendor_cost.default_commission');
        $ec_commission_per = db_get_field("SELECT ec_commission FROM ?:products WHERE product_id = ?i", $product_id);
        $company_id = db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i", $product_id);
        $category_id = db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = ?s", $product_id, 'M');
        $x = 0;
        if (!empty($ec_commission_per) && floatval($ec_commission_per) > 0) {
            $x = $ec_commission_per;
        }
        $x += db_get_field("SELECT ec_commission FROM ?:companies WHERE company_id = ?i", $company_id);
        $x += db_get_field("SELECT ec_commission FROM ?:categories WHERE category_id = ?i", $category_id);
        $x += fn_ec_get_product_brand_markup($product_id);
        $commission_per = $x ? $x : $commission_per;

        return $commission_per;
    }
    else{
        return 0;
    }

}

function fn_an_one_order_vendor_plans_calculate_commission_for_payout_post($order_info, $company_data, &$payout_data){

   // fn_print_r($order_info);
   // fn_print_r($total);
    //fn_print_r($surcharge_to_commission);
    //fn_print_r($payout_data);

    $amount_for_marketplace =0;
    $percent_folor_marketplace =0;
    $different_by_prices =0;
    $order_subtotal = $order_info['subtotal'];
    $items_subtotal = 0;

    if(!empty($order_info['products'])){

        foreach ($order_info['products'] as $k_item => $product) {
            $items_subtotal = $items_subtotal + $product['subtotal'];
            $ecl_comission = fn_an_one_get_ecl_comission($product['product_id']);
             //fn_print_r('$ecl_comission',$ecl_comission);
            $product_subtotal = $product['subtotal'];

            if($ecl_comission > 0 ){
                $price_before_comission = 0;
                $price_before_comission = ($product_subtotal * $ecl_comission)/ (100 + $ecl_comission);
                $different_by_prices = $different_by_prices + ($product_subtotal-$price_before_comission);
            }


        }
    }


    $new_comission =0;

    $different_by_prices = round($different_by_prices,2);

    if($different_by_prices > 0){
        //calc comission percent for vendor balance

        $new_comission = ($different_by_prices * 100)/$order_subtotal;

        $new_comission = round($new_comission,2);

        $payout_data['commission'] =$new_comission;
        $payout_data['commission_amount'] = $order_subtotal - $different_by_prices;
    }

    //fn_print_r($items_subtotal);
    //fn_print_r($order_subtotal);
    //fn_print_r($different_by_prices);
   // exit('dfdfdfdfdf');

}

function fn_an_one_order_an_change_order_status_parent_place_order($order_id, $status_to, $status_from, $force_notification, $place_order, $child_order_id, $child_status_to, $change_child_status, $child_status_from){
    $par_order_id = db_get_field("SELECT parent_order_id FROM ?:orders WHERE order_id =?i",$order_id);
    if($par_order_id) {
        $order_id  = $par_order_id;
        $order_info = fn_get_order_info($order_id, true);
        $edp_data = fn_generate_ekeys_for_edp(array('status_from' => $status_from, 'status_to' => $status_to), $order_info);


        $force_notification[UserTypes::CUSTOMER] = true;


        $status_id = strtolower($status_to);

        //if store want to send email to INCOMPLETE status, we should change status to ORDER.
        if($status_id == STATUS_INCOMPLETED_ORDER){
            $status_id ='o';

            db_query("UPDATE ?:orders SET `status` = ?s WHERE order_id =?i","O",$order_id);
        }

        /** @var \Tygh\Notifications\EventDispatcher $event_dispatcher */
        $event_dispatcher = Tygh::$app['event.dispatcher'];

        /** @var \Tygh\Notifications\Settings\Factory $notification_settings_factory */
        $notification_settings_factory = Tygh::$app['event.notification_settings.factory'];
        $notification_rules = $notification_settings_factory->create($force_notification);

        $res =  $event_dispatcher->dispatch(
            "order.status_changed.{$status_id}",
            ['order_info' => $order_info],
            $notification_rules,
            new OrderProvider($order_info)
        );

        //fn_print_r('child dispatch change');
       // fn_print_r($res);

        if ($edp_data) {
            $notification_rules = fn_get_edp_notification_rules($force_notification, $edp_data);
            $event_dispatcher->dispatch(
                'order.edp',
                ['order_info' => $order_info, 'edp_data' => $edp_data],
                $notification_rules,
                new OrderProvider($order_info, $edp_data)
            );
        }
    }
}


function fn_an_order_check_and_change($order_id,$status_from,$status_to,$is_parent,$parent_order_id){


return true;
    if($is_parent){
        $child_order_ids = db_get_fields("SELECT order_id FROM ?:orders WHERE parent_order_id  = ?i", $order_id);
        db_query("UPDATE ?:order_details SET an_status =?s WHERE order_id  IN (?n)", $status_to, $child_order_ids);
        db_query("UPDATE ?:order_details SET an_status =?s WHERE order_id  =?i", $status_to, $order_id);

        foreach ($child_order_ids as $child_id) {
            $_res = fn_change_order_status($child_id, $status_from, $status_to);
        }

    }
    else{
        $child_order_id_date = db_get_array("SELECT order_id,status FROM ?:orders WHERE parent_order_id  = ?i and order_id != ?i", $parent_order_id,$order_id);
        db_query("UPDATE ?:order_details SET an_status =?s WHERE order_id  =?i", $status_to, $order_id);

        if($child_order_id_date){
            $no_need_change_parent = false;
            foreach ($child_order_id_date as $item_data) {
                if($item_data['status'] != $status_to ){
                    $no_need_change_parent = true;
                }
            }

            if(!$no_need_change_parent){

                db_query("UPDATE ?:order_details SET an_status =?s WHERE order_id  =?i", $status_to, $parent_order_id);
                db_query("UPDATE ?:orders SET status = ?s WHERE order_id = ?i ",$status_to,$parent_order_id);
            }
        }
    }
}

//fn.cart.php fn_change_order_status()
//при изменении статуса любого заказа из группы заказов, статус будет меняться у всей группы.
function fn_an_one_order_change_order_status($status_to, $status_from, $order_info, &$force_notification, $order_statuses, $place_order)
{
    //проверим не было бы смены заказа парента
    //fn_print_r('change_order_status');
   // fn_print_r($order_info['order_id']);
    //fn_print_r($status_from );
    //fn_print_r($status_to);

    if(!empty($_REQUEST['id'])){
        $order_infow = fn_get_order_info($_REQUEST['id']);
        $new_status = $_REQUEST['status'];

        if ($order_infow['parent_order_id'] == 0 && $order_infow['is_parent_order'] == 'Y' && $new_status !== STATUS_PARENT_ORDER) {
            $res = db_query('UPDATE ?:orders SET `status` = ?s WHERE order_id = ?i ', $new_status,$order_infow['order_id']);
        }
    }

    if ($order_info['parent_order_id'] > 0) {
        //lemuria
        $force_notification[UserTypes::CUSTOMER] = false;
        //db_query('UPDATE ?:orders SET status = ?s WHERE order_id = ?i ', $status_to, $order_info['parent_order_id']);
        //db_query('UPDATE ?:orders SET status = ?s WHERE parent_order_id = ?i ',  $status_to, $order_info['parent_order_id']);

       // fn_an_order_check_and_change($order_info['order_id'],$status_from,$status_to,false,$order_info['parent_order_id']);


    } elseif ($order_info['parent_order_id'] == 0 && $order_info['is_parent_order'] == 'Y' && $status_to !== STATUS_PARENT_ORDER) {
        // db_query('UPDATE ?:orders SET status = ?s WHERE parent_order_id = ?i ', $status_to,$order_info['order_id']);
        //также сменим статус самого заказа парента
        //проверим не было бы смены заказа парента
        $res = db_query('UPDATE ?:orders SET status = ?s WHERE order_id = ?i ', $status_to,$order_info['order_id']);


       // fn_an_order_check_and_change($order_info['order_id'],$status_from,$status_to,true,0);

        //fn_print_r($res);
    }
}

function fn_an_one_order_change_order_status_child_order($order_id, $status_to, $status_from, &$force_notification, $place_order, $child_order_id, $child_status_to, $change_child_status, $child_status_from){


    $check = Registry::get('an_was_send_parent_notice');

    //fn_print_r('fn_an_one_order_change_order_status_child_order');
    //fn_print_r($check);
    //fn_print_r('********************');

    //fn_print_die('dfdfdf');

    if(!$check){

        $par_order_id = db_get_field("SELECT parent_order_id FROM ?:orders WHERE order_id =?i",$order_id);
        if($par_order_id) {
            $order_id  = $par_order_id;
            $order_info = fn_get_order_info($order_id, true);
            $edp_data = fn_generate_ekeys_for_edp(array('status_from' => $status_from, 'status_to' => $status_to), $order_info);


            $force_notification[UserTypes::CUSTOMER] = true;


            $status_id = strtolower($status_to);

            /** @var \Tygh\Notifications\EventDispatcher $event_dispatcher */
            $event_dispatcher = Tygh::$app['event.dispatcher'];

            /** @var \Tygh\Notifications\Settings\Factory $notification_settings_factory */
            $notification_settings_factory = Tygh::$app['event.notification_settings.factory'];
            $notification_rules = $notification_settings_factory->create($force_notification);

           $res =  $event_dispatcher->dispatch(
                "order.status_changed.{$status_id}",
                ['order_info' => $order_info],
                $notification_rules,
                new OrderProvider($order_info)
            );

          // fn_print_r('child dispatch change');
           //fn_print_r($res);

            if ($edp_data) {
                $notification_rules = fn_get_edp_notification_rules($force_notification, $edp_data);
                $event_dispatcher->dispatch(
                    'order.edp',
                    ['order_info' => $order_info, 'edp_data' => $edp_data],
                    $notification_rules,
                    new OrderProvider($order_info, $edp_data)
                );
            }
        }
    }

    Registry::set('an_was_send_parent_notice',true);

    $force_notification = array();
    $force_notification[UserTypes::CUSTOMER] = false;

}

function fn_an_one_order_combine_full_parent_order($order,$child_order_details_info){


    //fn_print_r($order);

    //fn_print_die($child_order_details_info);

    unset($order['products']);

    $order['total'] =            0;
    $order['subtotal'] =   $order['subtotal_discount'] =   $order['payment_surcharge'] =         0;
    $order['discount'] =         0;
    $order['display_subtotal'] = 0;
    $order['tax_subtotal']   =   0;



    foreach ($child_order_details_info as $order_id => $order_child_info) {

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

function fn_an_one_order_get_order_info(&$order, $additional_data){

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
           $child_order_details_info[$child_id] =$order_info;
       }

       $order = fn_an_one_order_combine_full_parent_order($order,$child_order_details_info);
	   Registry::set('cp_one_order_remove_failed', true);
   }
   else{
       $check = Registry::get('cp_one_order_remove_failed');

       $check_another = Registry::get('another_cp_one_order_remove_failed');

       //fn_print_r($check);
       //fn_print_r($check_another);

       if(!empty($order['products'])  && !$check && !$check_another){
           foreach ($order['products'] as $item => $product) {
               if(in_array($product['an_status'],array("F","I","D"))){
                   $order['total'] = $order['total'] - $product['subtotal'];

                   $order['subtotal'] = $order['subtotal'] - $product['subtotal'];
               }
           }

           $order['display_subtotal'] = $order['subtotal'];

       }
   }

   //fn_print_r($order['subtotal']);

   $check = Registry::get('cp_one_order_remove_failed');

   if(!empty($order['products']) && $check){
       foreach ($order['products'] as $item => $product) {
           if(in_array($product['an_status'],array("F","I","D"))){
              // $order['total'] = $order['total'] - $product['subtotal'];
           }
       }
   }

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
}


function fn_an_one_order_get_orders($params, $fields, $sortings, &$condition, $join, $group)
{
    $auth = Tygh::$app['session']['auth'];
    //$ccc = Registry::get('config.current_url');

    if($auth['user_type'] =="V"){
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



//fn.cart.php fn_place_suborders()
//fn_set_hook('cp_place_suborders', $cart, $suborder_cart, $action, $order_id); - НОВЫЙ ХУК!!
//при редактировании родительского заказа, изменения идут и в дочерние
function fn_an_one_order_an_place_suborders($cart, &$suborder_cart, $action, $parent_order_id)
{


    //fn_print_r($suborder_cart);

    //    if (isset($cart['shipping_cost']) && $cart['shipping_cost'] == 0 && isset($suborder_cart['shipping_cost']) && $suborder_cart['shipping_cost'] > 0) {
    if (isset($suborder_cart['shipping_cost']) && $suborder_cart['shipping_cost'] > 0) {
        $suborder_cart['total'] -= $suborder_cart['shipping_cost'];
        $suborder_cart['shipping_cost'] = 0;        
    }

    /*
    static $number_suborder_cart = 0;

    if ($number_suborder_cart == 0) {
        if (isset($cart['shipping_cost']) && $cart['shipping_cost'] != 0) {
            $suborder_cart['total'] -= $suborder_cart['shipping_cost'];
            $suborder_cart['total'] += $cart['shipping_cost'];
            $suborder_cart['shipping_cost'] = $cart['shipping_cost'];
            $number_suborder_cart++;
        }       
    } else {
        if (isset($suborder_cart['shipping_cost']) && $suborder_cart['shipping_cost'] != 0) {
            $suborder_cart['total'] -= $suborder_cart['shipping_cost'];
            $suborder_cart['shipping_cost'] = 0;
        }        
    }
    */

    if ($action == 'save' && !empty($suborder_cart['order_id'])) {
        if (empty(Tygh::$app['session']['children_orders_id'][$parent_order_id])) {

            $order_data = db_get_row('SELECT is_parent_order, parent_order_id FROM ?:orders WHERE order_id = ?i ', $parent_order_id);

            if ($order_data['is_parent_order'] == 'Y' && $order_data['parent_order_id'] == 0) {
                Tygh::$app['session']['children_orders_id'][$parent_order_id] = db_get_fields('SELECT order_id FROM ?:orders WHERE parent_order_id = ?i ORDER BY order_id ASC', $parent_order_id);
                $suborder_cart['order_id'] = array_shift(Tygh::$app['session']['children_orders_id'][$parent_order_id]);
            }

        } elseif (isset(Tygh::$app['session']['children_orders_id'][$parent_order_id]) && count(Tygh::$app['session']['children_orders_id'][$parent_order_id]) > 0) {
            $suborder_cart['order_id'] = array_shift(Tygh::$app['session']['children_orders_id'][$parent_order_id]);
        }
    }
}


function fn_an_one_order_get_store_locations_for_shipping_before_select($destination_id, $fields, $joins, &$conditions)
{
    if (isset($conditions['company_id'])) {
        unset($conditions['company_id']);
    }
}

function fn_an_one_order_shippings_get_company_shipping_ids($company_id, &$shipping_ids)
{
    $shipping_ids = db_get_fields("SELECT shipping_id FROM ?:shippings WHERE status = ?s", 'A');
}

function fn_an_one_order_calculate_cart_taxes_pre(&$cart, $cart_products, &$product_groups, $calculate_taxes, $auth)
{
    $first_product_group = reset($cart['product_groups']);

    if (isset($first_product_group['chosen_shippings'])) {
        $chosen_shipping = reset($first_product_group['chosen_shippings']);

        $chosen_shipping_id = $chosen_shipping['shipping_id'];

        if (isset($first_product_group['shippings'][$chosen_shipping_id]['rate'])) {
            $cart['shipping_cost'] = $first_product_group['shippings'][$chosen_shipping_id]['rate'];
            $cart['display_shipping_cost'] = $cart['shipping_cost'];
        }
        
        $cart['chosen_shipping'] = [];
        foreach ($cart['product_groups'] as $group_key => $group) {
            $cart['chosen_shipping'][$group_key] = $chosen_shipping_id;
            $cart['product_groups'][$group_key]['chosen_shippings'] = [];
            $cart['product_groups'][$group_key]['chosen_shippings'][] = $chosen_shipping;
        }

        $cart['shipping'] = [];
        $cart['shipping'][$chosen_shipping_id] = $chosen_shipping;      

        $shipping_cost = 0;
        foreach ($cart['product_groups'] as $key => $product_group) {
            foreach ($product_group['shippings'] as $shipping_id => $shipping) {
                if ($shipping['rate_info']['rate_value']['C']) {
                    foreach ($shipping['rate_info']['rate_value']['C'] as $rate_value) {
                        if ($cart['subtotal'] >= $rate_value['amount']) {
                            $shipping_cost = $rate_value['value'];
                        }
                    }

                    $cart['product_groups'][$key]['shippings'][$shipping_id]['rate'] = $shipping_cost;
                }
            }
            $cart['product_groups'][$key]['chosen_shippings'][0]['rate'] = $cart['product_groups'][$key]['shippings'][$chosen_shipping_id]['rate'];
        }

        $product_groups = $cart['product_groups']; 

        if (!empty($cart['shipping'][$chosen_shipping_id]['rate_info']['rate_value']['C'])) {
            $rate_value = $cart['shipping'][$chosen_shipping_id]['rate_info']['rate_value']['C'];

            $shipping_cost = 0;
            foreach ($rate_value as $key => $value) {
                if ($cart['subtotal'] >= $value['amount']) {
                    $shipping_cost = $value['value'];
                }
            }

            $cart['shipping'][$chosen_shipping_id]['rate'] = $shipping_cost;
            $cart['shipping_cost'] = $shipping_cost;
            $cart['display_shipping_cost'] = $cart['shipping_cost'];
        }
    }
}


/* Hooks end */








