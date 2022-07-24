<?php


if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
   // 'pre_place_order',
  //  'place_suborders',
   // 'place_order'
   // 'shippings_get_company_shipping_ids',
   // 'checkout_place_orders_pre_route'

     'get_order_info'

    ,'place_order_post'
    //,'cp_shipping_sent_by_marketplace'
);