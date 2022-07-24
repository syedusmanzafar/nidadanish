<?php

use Tygh\Registry;
use Tygh\Addons\CpStatusChain\Models\OrderLogger;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


function fn_sc_united_shipping_shippings_get_company_shipping_ids($company_id, $shipping_ids){
    //проверка включен ли модуль. какой  тарифный план. и тогда дергаем айдишники. СУПЕР
    if( $company_id > 0  && empty($shipping_ids)){

    }
}

function fn_sc_united_shipping_place_order($order_id, $action, $order_status, $cart, $auth){

}


function fn_sc_united_shipping_pre_place_order(array $cart, $allow, array $product_groups){



}


function fn_sc_united_shipping_place_suborders($cart, $suborder_cart, $key_group){

}


function fn_sc_place_order_for_shipping_vendor($order_id, $parent_order_id, $total, $vendor_id){

    $cscart_order_data = array();
    $cscart_order_data['user_id'] =0;
    $cscart_order_data['firstname'] =    $cscart_order_data['firstname'] = __("sc_united_shipping_test_vendor_order_name");
    $cscart_order_data['lastname'] =    $cscart_order_data['lastname'] = '';
    $cscart_order_data['b_firstname'] =    $cscart_order_data['s_firstname'] = __("sc_united_shipping_test_vendor_order_name");
    $cscart_order_data['b_lastname'] =    $cscart_order_data['s_lastname'] = '';
    $cscart_order_data['b_state'] =    $cscart_order_data['s_state'] = '';
    $cscart_order_data['b_city'] =    $cscart_order_data['s_city'] = '';
    $cscart_order_data['b_address'] =    $cscart_order_data['s_address'] =  '';
    $cscart_order_data['b_country'] =    $cscart_order_data['s_country'] = '';
    $cscart_order_data['b_zipcode'] =    $cscart_order_data['s_zipcode'] =  '';
    $cscart_order_data['b_phone'] =    $cscart_order_data['s_phone'] = '';
    $cscart_order_data['email'] = __("sc_united_shipping_test_vendor_email_order");
    $cscart_order_data['company_id'] = $vendor_id;


    $user_id = db_get_field("SELECT user_id FROM ?:users WHERE sc_united_use_vendor =?s","Y");

    if(empty($user_id)){
        $user_id = db_get_field('SELECT user_id FROM ?:users WHERE is_root=?s AND company_id=?i ORDER BY user_id ASC', 'Y', 0);
    }

    $cscart_order_data['user_id'] = $user_id;
    $cscart_order_data['payment_id'] =   '';
    $cscart_order_data['shipping_ids'] = '';
    $cscart_order_data['total'] = $total;
    $cscart_order_data['subtotal'] =$total;
    $cscart_order_data['discount'] = 0;
    $cscart_order_data['shipping_cost'] = 0;
    $cscart_order_data['notes'] = '';
    $cscart_order_data['status'] = 'O';
    $date = time();
    $cscart_order_data['timestamp'] = $date;

    $cscart_order_data['parent_order_id'] = $parent_order_id;

    $cscart_order_data['is_sc_united_ship_order'] = 'Y';


    $order_id = db_query("INSERT INTO ?:orders ?e", $cscart_order_data);



}

function fn_sc_find_needed_order_id($processed_order_ids, $item_id_order, $product_id, $company_id){

    $order_ids_data = db_get_row("SELECT o.order_id,o.parent_order_id FROM ?:orders as o LEFT JOIN ?:order_details as od ON od.order_id = o.order_id 
                              WHERE od.item_id =?i and od.product_id =?i and o.parent_order_id != ?i and o.order_id in (?n)  ",$item_id_order,$product_id,0,$processed_order_ids);

    return $order_ids_data;
}





function fn_sc_united_shipping_place_order_post($cart,
                                                $auth,
                                                $action,
                                                $issuer_id,
                                                $parent_order_id,
                                                $order_id,
                                                $order_status,
                                                $short_order_data,
                                                $notification_rules){

    if($parent_order_id > 0){


        $child_orders = db_get_array("SELECT shipping_cost, order_id FROM ?:orders WHERE parent_order_id =?i",$parent_order_id);

        if($child_orders){

            foreach ($child_orders as $data){




                if($data['shipping_cost'] >= 0 ){

                    //проверим а есть ли вообще продавец для заказа-доставки
                    $vendor_id = db_get_field("SELECT company_id FROM ?:companies WHERE sc_united_use_vendor =?s","Y");
                    //проверим не был ли УЖЕ размещен заказ для единой доставки
                    $check_exist_united_ship_order = db_get_field("SELECT order_id FROM ?:orders WHERE parent_order_id =?i and is_sc_united_ship_order = ?s", $parent_order_id, "Y");


                    db_query("UPDATE ?:orders SET shipping_cost =?i WHERE order_id =?i", 0, $data['order_id']);
                    db_query("UPDATE ?:orders SET total = total - ?i WHERE order_id =?i",$data['shipping_cost'],$data['order_id']);

                    if ($vendor_id && empty($check_exist_united_ship_order)) {

                        $res = fn_sc_place_order_for_shipping_vendor($order_id,$parent_order_id, $data['shipping_cost'],$vendor_id);

                    }


                }

            }
        }

    }


}



function fn_sc_united_shipping_checkout_place_orders_pre_route($cart, $auth, $params){



    if(!empty($cart['product_groups'])){

        //выясним кому какой заказ принадлежит
        //  processed_order_id
        foreach ($cart['product_groups'] as $group_key => $product_group){
            $company_id = $product_group['company_id'];
            $products_order_ids = array();
            $product_id = 0;
            $item_id_order =0;

            if(!empty($product_group['products'])){
                foreach ($product_group['products'] as $item_id => $product) {
                    $product_id = $product['product_id'];
                    $item_id_order =$item_id;

                    break;
                }
            }

            if(!empty($product_group['shipping_by_marketplace'])) {
                $order_ids_data = fn_sc_find_needed_order_id($cart['processed_order_id'],$item_id_order,$product_id,$company_id);

                if(!empty($order_ids_data['order_id'])){
                    //check shipping_total
                    $shipping_total = db_get_field("SELECT shipping_cost FROM ?:orders WHERE order_id =?i",$order_ids_data['order_id']);

                    if($shipping_total >= 0 ){

                        //проверим а есть ли вообще продавец для заказа-доставки
                        $vendor_id = db_get_field("SELECT company_id FROM ?:companies WHERE sc_united_use_vendor =?s","Y");
                        //проверим не был ли УЖЕ размещен заказ для единой доставки
                        $check_exist_united_ship_order = db_get_field("SELECT order_id FROM ?:orders WHERE parent_order_id =?i and is_sc_united_ship_order = ?s", $order_ids_data['parent_order_id'], "Y");
                        if ($vendor_id && empty($check_exist_united_ship_order)) {
                            db_query("UPDATE ?:orders SET shipping_cost =?i WHERE order_id =?i", 0, $order_ids_data['order_id']);
                            db_query("UPDATE ?:orders SET total = total - ?i WHERE order_id =?i",$shipping_total,$order_ids_data['order_id']);
                            $res = fn_sc_place_order_for_shipping_vendor($order_ids_data['order_id'],$order_ids_data['parent_order_id'], $shipping_total,$vendor_id);

                        }
                    }
                }
            }
        }
    }

    //все что нужно это проверить флаг shipping_by_marketplace и цену

}





function fn_sc_united_shipping_get_order_info(&$order, $additional_data)
{
$company_id = Registry::get('runtime.company_id');
    if ($company_id > 0) {
        if(!empty($order['parent_order_id'])){
            $check_exist_united_ship_order = db_get_field("SELECT order_id FROM ?:orders WHERE parent_order_id =?i and is_sc_united_ship_order = ?s", $order['parent_order_id'], "Y");
        }

        if (!empty($order['shipping']) && !empty($check_exist_united_ship_order)) {
            foreach ($order['shipping'] as $k => $sh_data){
                $order['shipping'][$k]['group_name'] = fn_get_company_name($order['company_id']);
            }
        }
    }
    if(AREA =="C" && $order['is_parent_order'] == "Y"){
        $check_exist_united_ship_order = db_get_field("SELECT order_id FROM ?:orders WHERE parent_order_id =?i and is_sc_united_ship_order = ?s", $order['order_id'], "Y");

        if($check_exist_united_ship_order){
            $order['sc_united_ship_order'] = db_get_row("SELECT * FROM ?:orders WHERE order_id =?i",$check_exist_united_ship_order);
        }
    }
}



function fn_sc_united_shipping_cp_shipping_sent_by_marketplace($company_id, $shipping, &$do_zero_price_shipping){

    if(Registry::get('addons.order_fulfillment.status') =='A'){


        $plan_id  = db_get_field("SELECT plan_id FROM ?:companies WHERE company_id = ?i",$company_id);
        $is_fulfillment_by_marketplace = db_get_field("SELECT is_fulfillment_by_marketplace FROM ?:vendor_plans WHERE plan_id =?i",$plan_id);

        //fn_print_r($shipping);
        // fn_print_r($company_id);
        // fn_print_r($is_fulfillment_by_marketplace);

        if(!isset($shipping['company_id'])){
            $shipping = fn_get_shipping_info((int) $shipping['shipping_id']);
        }

        //если доставка НЕ принадлжеит продавцу а маркетплейсу. но при этом продавец на тарифном плане где НЕТ фулфилмента
        if (isset($shipping['company_id']) && empty($shipping['company_id']) && $is_fulfillment_by_marketplace != "Y") {
            $do_zero_price_shipping = false;
        }

        //вернуть false если нет спец. тарифав плане
    }
}