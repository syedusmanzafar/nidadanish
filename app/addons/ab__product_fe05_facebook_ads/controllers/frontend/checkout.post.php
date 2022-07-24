<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2021   *
* / /_\ | | _____  _| |_/ /_ __ __ _ _ __   __| |_ _ __   __ _   | |_ ___  __ _ _ __ ___   *
* |  _  | |/ _ \ \/ / ___ \ '__/ _` | '_ \ / _` | | '_ \ / _` |  | __/ _ \/ _` | '_ ` _ \  *
* | | | | |  __/>  <| |_/ / | | (_| | | | | (_| | | | | | (_| |  | ||  __/ (_| | | | | | | *
* \_| |_/_|\___/_/\_\____/|_|  \__,_|_| |_|\__,_|_|_| |_|\__, |  \___\___|\__,_|_| |_| |_| *
*                                                         __/ |                            *
*                                                        |___/                             *
* ---------------------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license and accept    *
* to the terms of the License Agreement can install and use this program.                  *
* ---------------------------------------------------------------------------------------- *
* website: https://cs-cart.alexbranding.com                                                *
*   email: info@alexbranding.com                                                           *
*******************************************************************************************/
if (!defined('BOOTSTRAP')) { die('Access denied'); }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}
if ($mode == 'checkout') {
$cart = Tygh::$app['session']['cart'];
if (defined('AJAX_REQUEST') || empty($cart['products'])) {
return;
}
$data = fn_ab__pfe05_get_array_by_products($cart['products']);
fn_ab__pfe05_send_event('InitiateCheckout', $data);
} elseif ($mode == 'complete') {
$order_info = Tygh::$app['view']->getTemplateVars('order_info');
if (empty($order_info['products']) || !empty(Tygh::$app['session']['ab__pfe05_sent_orders_ids'][$order_info['order_id']])) {
return;
}
$data = fn_ab__pfe05_get_array_by_products($order_info['products']);
fn_ab__pfe05_send_event('Purchase', $data);
Tygh::$app['session']['ab__pfe05_sent_orders_ids'][$order_info['order_id']] = true;
}