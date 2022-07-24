<?php


use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


if ($mode == 'get_po') {
    $o =  db_get_fields("SELECT * FROM ?:products ORDER BY product_id DESC LIMIT 200");
    fn_print_die($o);
}

if ($mode == 'get_po_del') {
    if(!empty($_REQUEST['product_id'])){
        fn_delete_product($_REQUEST['order_id']);
    }
    exit('stop');
}








if ($mode == 'get_o') {
    $o =  db_get_fields("SELECT * FROM ?:orders ORDER BY order_id DESC LIMIT 200");
    fn_print_die($o);
}


if ($mode == 'get_o_del') {
  if(!empty($_REQUEST['order_id'])){
      fn_delete_order($_REQUEST['order_id']);
  }
  exit('stop');
}

