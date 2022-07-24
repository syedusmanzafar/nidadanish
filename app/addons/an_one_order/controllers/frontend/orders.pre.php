<?php


use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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