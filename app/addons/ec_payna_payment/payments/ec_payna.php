<?php
/**
 * Ecarter Technologies Pvt. Ltd.
 *
 * This source file is part of a commercial software. Only users who have purchased a valid license through
 * https://www.ecarter.co and accepted to the terms of the License Agreement can install this product.
 *
 * @category   Add-ons
 *
 * @copyright  Copyright (c) 2020 Ecarter Technologies Pvt. Ltd.. (https://www.ecarter.co)
 * @license    https://ecarter.co/legal/license-agreement/   License Agreement
 *
 * @version    $Id$
 */

use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    require './../../../payments/init_payment.php';
}

/*
 * @var array $order_info
 * @var array $processor_data
 * @var string $mode
 */

if (defined('PAYMENT_NOTIFICATION')) {
    $order_id = (!empty($_REQUEST['order_id'])) ? $_REQUEST['order_id'] : 0;
    if ($mode == 'success') {
        $order_info = fn_get_order_info($order_id);
        $processor_data = fn_get_processor_data($order_info['payment_id']);
        if ($order_info['status'] != $processor_data['processor_params']['s_order_status']) {
            $pp_response['order_status'] = 'I';
            $pp_response['reason_text'] = __('text_transaction_cancelled');
            fn_finish_payment($order_id, $pp_response);
        }
        fn_order_placement_routines('route', $order_id, false);
    }
    if ($mode == 'failed') {
        $order_info = fn_get_order_info($order_id);
        $processor_data = fn_get_processor_data($order_info['payment_id']);
        if ($order_info['status'] != $processor_data['processor_params']['f_order_status']) {
            $pp_response['order_status'] = 'I';
            $pp_response['reason_text'] = __('text_transaction_cancelled');
            fn_finish_payment($order_id, $pp_response);
        }
        fn_order_placement_routines('route', $order_id, false);
    }
} else {
    $payment_timeout = !empty($processor_data['processor_params']['payment_timeout'])?$processor_data['processor_params']['payment_timeout']:30;
    $curl = curl_init();
    curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.payna.co.tz/payment/auth/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{"email": "'.$processor_data['processor_params']['api_username'].'", "password": "'.$processor_data['processor_params']['api_password'].'"}  ',
    CURLOPT_HTTPHEADER => [
        'Authorization: '.$processor_data['processor_params']['api_key'],
        'Content-Type: application/json',
    ],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    // fn_print_r('First', $response);
    // file_put_contents('first1_response'.TIME.'.json', json_encode(array("response" => $response)));

    if (!empty($response)) {
        $response = json_decode($response, true);
        if (!empty($response['success'])) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.payna.co.tz/client/request',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{"reference": "ND'.TIME.$order_info['order_id'].'", "msisdn": "'.str_replace("_", '',$order_info['payment_info']['msisdn']).'", "amount": "'.intval($order_info['total']).'", "channel": "'.$order_info['payment_info']['channel'].'"}',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer '.$response['token'],
                    'Content-Type: application/json',
                ],
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            // fn_print_die('Second', $response);
            // file_put_contents('first_response.'.TIME.'.json', json_encode(array("response" => $response)));
            if (!empty($response)) {
                $payment_result = json_decode($response, true);
                if (!empty($payment_result['success'])) {
                    fn_echo("<div style='margin:0 auto; text-align:center;height:90vh;display:flex;flex-direction:column;align-items:center;justify-content:center;'>");
                    fn_echo("<img src='images/spinner.gif'/><br/>");
                    fn_echo(__("ec_payna_payment.your_payment_request_send_to_your_number_please_keep_your_phone_activated", ['[phone]'=> str_replace("_", '',$order_info['payment_info']['msisdn'])]));
                    fn_echo("</div>");
                    while ((time() - TIME) < $payment_timeout) {
                        sleep(10);
                        $_oinfo = fn_get_order_short_info($order_info['order_id']);
                        // fn_print_r(time(), TIME, $_oinfo);
                        // file_put_contents('first1_response23'.TIME.'.json', json_encode($_oinfo));

                        if ($_oinfo['status'] == $processor_data['processor_params']['s_order_status']) {
                            fn_create_payment_form(fn_url('payment_notification.success&payment=ec_payna&order_id='.$order_info['order_id']), null, '', 'GET');
                            exit;
                            
                        } elseif ($_oinfo['status'] == $processor_data['processor_params']['f_order_status']) {
                            fn_create_payment_form(fn_url('payment_notification.failed&payment=ec_payna&order_id='.$order_info['order_id']), null, '', 'GET');
                            exit;
                        }
                    }
                    // die;
                } elseif (!empty($response['error_code'])) {
                    $pp_response['reason_text'] = !empty($response['message']) ? $response['message'] : fn_get_payna_payment_error_code($response['error_code']);
                }
            }
        }elseif (!empty($response['error_code'])) {
            $pp_response['reason_text'] = !empty($response['message']) ? $response['message'] : fn_get_payna_payment_error_code($response['error_code']);
        }
    }
}

function fn_get_payna_payment_error_code($code)
{
    $errors = [
        100 => 'Trouble communicating with Vodacom M-Pesa',
        1000 => 'Failed to authenticate with MNO',
        1001 => 'Duplicate transaction request',
        1010 => 'MSISDN is missing in incorrect formatted',
        1020 => 'Reference number is missing',
        1021 => 'Biller account code does not exists',
        1030 => 'Amount is required',
        1032 => 'Amount required must be an integer',
        5001 => 'Email is missing',
        5002 => 'Password is missing',
        5005 => 'Wrong credentials when requesting token',
    ];

    return isset($errors[$code]) ? $errors[$code] : '';
}
