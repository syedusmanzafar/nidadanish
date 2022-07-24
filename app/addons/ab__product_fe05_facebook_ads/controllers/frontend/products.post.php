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
if ($mode == 'search') {
if (isset($_REQUEST['q']) && fn_string_not_empty($_REQUEST['q'])) {
fn_ab__pfe05_send_event('Search', array(
'search_string' => $_REQUEST['q']
));
}
} elseif ($mode == 'view' || $mode == 'quick_view') {
if ($mode == 'view' && defined('AJAX_REQUEST')) {
return;
}
$product = Tygh::$app['view']->getTemplateVars('product');
if (empty($product)) {
return;
}
$data = array(
'content_name' => $product['product'],
'content_ids' => array($product['product_id']),
'content_type' => 'product',
'currency' => CART_PRIMARY_CURRENCY,
'value' => $product['price'],
);
fn_ab__pfe05_send_event('ViewContent', $data);
}
