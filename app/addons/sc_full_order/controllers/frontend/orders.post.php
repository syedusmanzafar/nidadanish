<?php

use Tygh\Registry;

if ($mode == 'search') {

    $orders = Tygh::$app['view']->getTemplateVars('orders');

    if(empty($orders)){
        return false;
    }

    //is_sc_united_ship_order
    //$new_orders[$order['from_order']]['suborders'][] =$order;

    foreach ($orders as $key => $order){
        if(!empty($order['is_parent_order']) && $order['is_parent_order'] == 'Y' ){
            $childs = db_get_array("SELECT * FROM ?:orders WHERE parent_order_id =?i and is_sc_united_ship_order != ?s",$order['order_id'],"Y");
            $orders[$key]['childs'] = $childs;
        }
    }
    Tygh::$app['view']->assign('orders', $orders);
}


if ($mode == 'details') {
    $order_info = Tygh::$app['view']->getTemplateVars('order_info');
    if (!empty($order_info['cp_is_need_return_parent_flag'])) {
        $order_info['is_parent_order'] = "Y";
    }

    Tygh::$app['view']->assign('order_info', $order_info);

}