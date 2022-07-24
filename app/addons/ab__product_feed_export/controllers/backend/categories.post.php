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
use Tygh\Registry;
use Tygh\ABPFE;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}
if ($mode == 'update') {
Registry::set('navigation.tabs.ab__pfe_feature_names', [
'title' => __('ab__pfe_feature_names'),
'js' => true,
]);
if (!empty($_REQUEST['category_id'])) {
list($features) = fn_get_product_features([
'category_ids' => [$_REQUEST['category_id']],
'exclude_group' => true,
'statuses' => 'A',
], 0, DESCR_SL);
Tygh::$app['view']->assign('ab__pfe_features', $features);
$items = fn_ab__pfe_get_features_names([
'category_id' => $_REQUEST['category_id'],
], DESCR_SL);
Tygh::$app['view']->assign('ab__pfe_feature_names', $items);
}
list($datafeeds) = ABPFE::get_datafeeds([
'company_id' => fn_get_runtime_company_id(),
]);
Tygh::$app['view']->assign('ab__pfe_datafeeds', $datafeeds);
}
