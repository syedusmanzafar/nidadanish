<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}
// file_put_contents('payment_response1_'.TIME.'.txt', json_encode($_REQUEST));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'payna_ipn') {
        $response = file_get_contents('php://input');
        $res = json_decode($response, true);
        // $logging_data = array();
        // $logging_data['response'] = $response;
        
        // if (isset($_REQUEST)) {
        //     $logging_data['data'] = var_export($_REQUEST, true);
        // }
        // fn_log_event('requests', 'http', $logging_data);
        // file_put_contents('payment_response_'.TIME.'.txt', json_encode($res));
        if (!empty($res['reference'])) {
            $res['reference'] = substr($res['reference'], 12);
            $order_info = fn_get_order_info($res['reference']);
            $processor_data = fn_get_processor_data($order_info['payment_id']);
            $pp_response = $res;

            if ($res && $res['action'] == 'PAYMENT') {
                // $pp_response['action']
                unset($res['code']);
                $pp_response['order_status'] = $processor_data['processor_params']['s_order_status'];
                fn_change_order_status($order_info['order_id'], 'P');
                $pp_response['reason_text'] = '';
            } else {
                $pp_response['order_status'] = $processor_data['processor_params']['f_order_status'];
                fn_change_order_status($order_info['order_id'], 'F');
            }
            // file_put_contents('payment_responseq_'.TIME.'.txt', json_encode($pp_response));
           
            fn_finish_payment($order_info['order_id'], $pp_response, false);
        }

        // file_put_contents('test_response_'.TIME.'.txt', $response);

        // file_put_contents('test_'.TIME.'.json', json_encode($_REQUEST));
    }
    exit;
}
