<?php


use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if ($mode == 'update_statusddddd') {

        $order_info = fn_get_order_info($_REQUEST['id']);
        $old_status = $order_info['status'];

        $new_status = $_REQUEST['status'];

        //fn_print_die($order_info['parent_order_id']);

        if ($order_info['parent_order_id'] == 0 && $order_info['is_parent_order'] == 'Y' && $new_status !== STATUS_PARENT_ORDER) {
            //db_query('UPDATE ?:orders SET status = ?s WHERE parent_order_id = ?i ', $status_to,$order_info['order_id']);

            //также сменим статус самого заказа парента
            $res = db_query('UPDATE ?:orders SET `status` = ?s WHERE order_id = ?i ', $new_status,$order_info['order_id']);

            //fn_print_r($res);

        }

        //if (fn_change_order_status($_REQUEST['id'], $_REQUEST['status'], '', fn_get_notification_rules($_REQUEST))) {

        //}
    }

    return;
}

if ($mode == 'details') {


    $auth = Tygh::$app['session']['auth'];
    //$ccc = Registry::get('config.current_url');

    if($auth['user_type'] =="V"){
        return true;
    }


    if (!empty($_REQUEST['order_id'])) {
        $parent_order_id = db_get_field('SELECT parent_order_id FROM ?:orders WHERE order_id = ?i ', $_REQUEST['order_id']);
        
        if (!empty($parent_order_id) && empty($_REQUEST['an_show_order'])) {
            return array(CONTROLLER_STATUS_REDIRECT, 'orders.details?order_id=' . $parent_order_id);
        }
    }

}