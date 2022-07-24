<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

function fn_an_payment_location_prepare_checkout_payment_methods(&$cart, $auth, &$payment_groups){

  if(isset($cart['user_data']['s_state'])){
    $state = $cart['user_data']['s_state'];
    foreach ($payment_groups as $tab => $payment_group){

        if(!empty($payment_group)){
            foreach ($payment_group as $pay_key => $item) {
                $payment_id = $item['payment_id'];
                $states_list = db_get_field('SELECT states_list FROM ?:payments WHERE payment_id = ?i', $payment_id);

                if(!empty($states_list)) {
                    $states_list = explode(',', $states_list);
                }
                else{
                    $states_list =array();
                }


                if(!empty($states_list) &&  !in_array($state,$states_list)){
                    unset($payment_groups[$tab][$pay_key]);

                    if(isset($cart['payment_id']) && $payment_id == $cart['payment_id']){

                        unset($cart['payment_id']);
                    }
                }

            }
        }
    }
  }

}


function fn_an_payment_location_update_payment_pre(&$payment_data, $payment_id, $lang_code, $certificate_file, $certificates_dir, $can_remove_offline_payment_params){
    if (!empty($payment_data['states_list'])) {
        $payment_data['states_list'] = implode(',', $payment_data['states_list']);
    } else {
        $payment_data['states_list'] = '';
    }
}

function fn_an_get_country_states($country_code, $payment_id, $avail_only = true, $lang_code = CART_LANGUAGE)
{

    $condition ='';

        $condition = db_quote(" and a.country_code = ?s", "TZ");

    $states_list = db_get_field('SELECT states_list FROM ?:payments WHERE payment_id = ?i', $payment_id);

   // fn_print_r($states_list);

    if (!empty($states_list)) {
        foreach ($states_list as $states) {
            if (!is_array($states)) {
                $states = explode(',', $states);
            }
            foreach ($states as $state) {
                $condition .= db_quote(' AND a.code != ?s', $state);
            }
        }

        if ($avail_only) {
            $condition .= db_quote(" AND a.status = ?s", 'A');
        }
    }






    return db_get_hash_single_array("SELECT a.code, b.state FROM ?:states as a LEFT JOIN ?:state_descriptions as b ON b.state_id = a.state_id AND b.lang_code = ?s WHERE 1= 1 ?p ORDER BY b.state", array('code', 'state'), $lang_code, $condition);
}