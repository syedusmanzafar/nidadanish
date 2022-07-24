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
    die('Access denied');
}

/**
 * Function runs on installation of add-on.
 *
 * @return void
 */
function fn_ec_payna_payment_install()
{
    $data = [
        'processor' => 'Payna Payment Gateway (In-context)',
        'processor_script' => 'ec_payna.php',
        'processor_template' => 'addons/ec_payna_payment/views/orders/components/payments/ec_payna.tpl',
        'admin_template' => 'ec_payna.tpl',
        'callback' => 'Y',
        'type' => 'P',
        'addon' => 'ec_payna_payment',
    ];
    db_query('INSERT INTO ?:payment_processors ?e', $data);
}

/**
 * Function runs on unstallation of add-on.
 *
 * @return null
 */
function fn_ec_payna_payment_uninstall()
{
    $condition = [
        'addon' => 'ec_payna_payment',
    ];
    db_query('DELETE FROM ?:payment_processors WHERE ?w', $condition);
}

function fn_ec_payna_payment_send_otp($data)
{
    $cart = Tygh::$app['session']['cart'];
    $addons_settings = Registry::get('addons.ec_payna_payment');
    $price = fn_format_price_by_currency($cart['total']);
    $currency = CART_SECONDARY_CURRENCY;
    $client = new SoapClient('http://62.240.55.2:6187/BCDUssd/payna.asmx?wsdl');
    $params = [
        'Mobile' => (string) $addons_settings['mobile'],
        'Pin' => (string) $addons_settings['pin'],
        'Cmobile' => $data['phone'],
        'Amount' => $price,
        'PW' => $addons_settings['password'],
    ];
    $_SESSION['EC_Cmobile'] = $data['phone'];
    $_SESSION['EC_Amount'] = $price;
    $_SESSION['EC_Total'] = $price;
    $response = $client->__soapCall('DoPTrans', [$params]);

    return $response;
}

function fn_ec_payna_payment_confirm_otp($data)
{
    $addons_settings = Registry::get('addons.ec_payna_payment');
    $client = new SoapClient('http://62.240.55.2:6187/BCDUssd/payna.asmx?wsdl');
    $params = [
        'Mobile' => $addons_settings['mobile'],
        'Pin' => $data['pin'],
        'sessionID' => $_SESSION['EC_sessionID'],
        'PW' => $addons_settings['password'],
    ];
    $response = $client->__soapCall('OnlineConfTrans', [$params]);

    return $response;
}

/**
 * Provide token and handle errors for checkout with In-Context checkout.
 *
 * @param array $cart   Cart data
 * @param array $auth   Authentication data
 * @param array $params Request parameters
 */
function fn_ec_payna_payment_checkout_place_orders_pre_route(&$cart, $auth, $params)
{
    // $cart = empty($cart) ? [] : $cart;
    // $payment_id = (empty($params['payment_id']) ? $cart['payment_id'] : $params['payment_id']);
    // $processor_data = fn_get_processor_data($payment_id);
    // if (!empty($processor_data['processor_script']) && $processor_data['processor_script'] == 'ec_payna.php' &&
    //     isset($params['in_context_order'])) {
    //     // parent order has the smallest identifier of all the processed orders
    //     $order_id = min($cart['processed_order_id']);
    //     $addons_settings = Registry::get('addons.ec_payna_payment');
    //     if (!empty($addons_settings['mobile']) && !empty($addons_settings['pin']) && !empty($addons_settings['password'])) {
    //         // set token for in-context checkout
    //         Tygh::$app['ajax']->assign('success_url', fn_url("payment_notification.success&payment=ec_payna&order_id=$order_id"));
    //         Tygh::$app['ajax']->assign('cancel_url', fn_url("payment_notification.cancel&payment=ec_payna&order_id=$order_id"));
    //         Tygh::$app['ajax']->assign('token', $order_id);
    //     } else {
    //         Tygh::$app['ajax']->assign('error', true);
    //     }
    //     exit;
    // }
}

function fn_settings_variants_addons_ec_payna_payment_order_status()
{
    return fn_get_simple_statuses(STATUSES_ORDER);
}
