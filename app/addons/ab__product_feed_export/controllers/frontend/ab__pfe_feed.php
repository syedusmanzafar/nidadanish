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
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
use Tygh\ABPFE;
use Tygh\Registry;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return true;
}
if ($mode == 'get') {
if (!empty($_REQUEST['datafeed_id']) && !empty($_REQUEST['filename']) && !empty($_REQUEST['ext'])) {
list($datafeeds) = ABPFE::get_datafeeds([
'datafeed_id' => $_REQUEST['datafeed_id'],
'storefront_id' => Tygh::$app['storefront']->storefront_id,
]);
if (empty($datafeeds[$_REQUEST['datafeed_id']]) || $datafeeds[$_REQUEST['datafeed_id']]['status'] !== 'A') {
return [CONTROLLER_STATUS_NO_PAGE];
}
$status = true;
$df = $datafeeds[$_REQUEST['datafeed_id']];
if (!empty($df['login']) && !empty($df['password'])) {
if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])
|| trim($df['login']) != trim($_SERVER['PHP_AUTH_USER'])
|| trim($df['password']) != trim($_SERVER['PHP_AUTH_PW'])
) {
$status = false;
}
}
if ($status) {
if ($df['generate_before_download'] === 'Y') {
ABPFE::generate_datafeed($df);
}
ABPFE::send_datafeed($df, $_REQUEST['filename'], $_REQUEST['ext']);
} else {
header('WWW-Authenticate: Basic realm="' . $_SERVER['SERVER_NAME'] . '"');
header('HTTP/1.0 401 Unauthorized');
die('Not authorized!');
}
}
exit;
}
