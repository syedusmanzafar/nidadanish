<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}


function fn_an_shipping_vendor_get_available_shippings($company_id, $fields, $join, &$condition, $get_service_params){


    if(AREA =="C"){

        $condition = '('.$condition.') OR ('.db_quote("a.an_is_main = ?s","Y").')';
    }

}