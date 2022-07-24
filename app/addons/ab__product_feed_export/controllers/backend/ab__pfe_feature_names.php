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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
fn_trusted_vars('item_data');
if ($mode == 'update') {
$id = fn_ab__pfe_update_feature_name($_REQUEST['item_data'], $_REQUEST['item_id'], DESCR_SL);
return [CONTROLLER_STATUS_OK, 'categories.update?item_id=' . $_REQUEST['item_id'] . '&category_id=' . $_REQUEST['category_id'] . '&selected_section=ab__pfe_feature_names'];
}
return;
}
if ($mode == 'update') {
if (!empty($_REQUEST['item_id'])) {
$items = fn_ab__pfe_get_features_names([
'item_id' => $_REQUEST['item_id'],
], DESCR_SL);
Tygh::$app['view']->assign('ab__pfe_feature_name', reset($items));
list($features) = fn_get_product_features([
'category_ids' => [$_REQUEST['category_id']],
'exclude_group' => true,
'statuses' => 'A',
], 0, DESCR_SL);
Tygh::$app['view']->assign('ab__pfe_features', $features);
list($datafeeds) = ABPFE::get_datafeeds([
'company_id' => fn_get_runtime_company_id(),
]);
Tygh::$app['view']->assign('ab__pfe_datafeeds', $datafeeds);
}
} elseif ($mode == 'delete') {
if (!empty($_REQUEST['item_id'])) {
fn_ab__pfe_delete_feature_names([$_REQUEST['item_id']]);
return [CONTROLLER_STATUS_OK, $_REQUEST['return_url'] . '&selected_section=ab__pfe_feature_names'];
}
}
