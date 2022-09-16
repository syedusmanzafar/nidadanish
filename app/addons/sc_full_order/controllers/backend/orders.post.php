<?php


use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'delete') {
        $children_ids = db_get_fields('SELECT order_id FROM ?:orders WHERE parent_order_id = ?i ORDER BY order_id ASC', $_REQUEST['order_id']);
        
        if (!empty($children_ids)) {
            foreach ($children_ids as $id) {
                /*
                db_query("DELETE FROM ?:order_data WHERE order_id = ?i", $id);
                db_query("DELETE FROM ?:order_details WHERE order_id = ?i", $id);
                db_query("DELETE FROM ?:orders WHERE order_id = ?i", $id);
                db_query("DELETE FROM ?:product_file_ekeys WHERE order_id = ?i", $id);
                db_query("DELETE FROM ?:profile_fields_data WHERE object_id = ?i AND object_type='O'", $id);
                db_query("DELETE FROM ?:order_docs WHERE order_id = ?i", $id);

                $shipment_ids = db_get_fields('SELECT shipment_id FROM ?:shipment_items WHERE order_id = ?i GROUP BY shipment_id', $id);
                fn_delete_shipments($shipment_ids);
                */

                fn_delete_order($id);

            }
            
        }      
    }

    if ($mode == 'update_status') {

        $order_info = fn_get_order_info($_REQUEST['id']);
        $old_status = $order_info['status'];
        $new_status = $_REQUEST['status'];

        if ($order_info['parent_order_id'] == 0 && $order_info['is_parent_order'] == 'Y') {
            //db_query('UPDATE ?:orders SET status = ?s WHERE parent_order_id = ?i ', $status_to,$order_info['order_id']);

            //также сменим статус самого заказа парента
           // $res = db_query('UPDATE ?:orders SET status = ?s WHERE order_id = ?i ', $new_status,$order_info['order_id']);

        }
    }

}



if ($mode == 'manage') {

    $orders = Tygh::$app['view']->getTemplateVars('orders');

    if(empty($orders)){
        return false;
    }

    //is_sc_united_ship_order
    //$new_orders[$order['from_order']]['suborders'][] =$order;

    foreach ($orders as $key => $order){
        if(!empty($order['is_parent_order']) && $order['is_parent_order'] == 'Y' ){
            $childs = db_get_array("SELECT * FROM ?:orders WHERE parent_order_id =?i",$order['order_id']);
            $orders[$key]['childs'] = $childs;
        }
    }
    Tygh::$app['view']->assign('orders', $orders);
}
elseif ($mode == 'details') {
    $_REQUEST['order_id'] = empty($_REQUEST['order_id']) ? 0 : $_REQUEST['order_id'];
    $additinal_orders = db_get_array('SELECT * FROM ?:orders WHERE parent_order_id = ?i', $_REQUEST['order_id']);
    Tygh::$app['view']->assign('additinal_orders', $additinal_orders);

    $order_info= Tygh::$app['view']->getTemplateVars('order_info');
    if(!empty($order_info['cp_is_need_return_parent_flag'])){
        $order_info['is_parent_order'] = "Y";
    }
    Tygh::$app['view']->assign('order_info', $order_info);


}