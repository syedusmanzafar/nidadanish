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
use Tygh\Settings;
foreach (glob(Registry::get('config.dir.addons') . '/ab__product_feed_export/ab__functions/fn.*.php') as $functions) {
require_once $functions;
}
function fn__pfe_get_offers($datafeed, $params)
{
static $last_product_ids = [];
if (empty($last_product_ids[$datafeed['datafeed_id']])) {
$last_product_ids[$datafeed['datafeed_id']] = 0;
}
$params['min_product_id'] = $last_product_ids[$datafeed['datafeed_id']];
$products = ABPFE::get_datafeed_items($datafeed, $params);
if (!empty($products) && is_array($products)) {
$last_product_ids[$datafeed['datafeed_id']] = end($products)['product_id'];
}
return $products;
}
function fn_ab__pfe_is_valid_cron_key($cron_key)
{
return (strlen($cron_key) >= 10 && strlen($cron_key) <= 20);
}
function fn_ab__pfe_get_cron_key()
{
$storefront = Tygh::$app['storefront'];
$company_id = fn_allowed_for('ULTIMATE') ? $storefront->getCompanyIds()[0] : 0;
$cron_key = trim(Settings::instance([
'company_id' => $company_id,
'storefront_id' => $storefront->storefront_id,
])->getValue('cron_key', 'ab__product_feed_export'));
return fn_ab__pfe_is_valid_cron_key($cron_key) ? $cron_key : '';
}
function fn_ab__pfe_check_key($cron_key)
{
return fn_ab__pfe_is_valid_cron_key($cron_key) && $cron_key === fn_ab__pfe_get_cron_key();
}
function fn_ab__product_feed_export_ab__pfe_autogenerate()
{
$p = [
'status' => ['A'],
'auto_generate' => ['Y'],
];
list($datafeeds) = ABPFE::get_datafeeds($p);
if (!empty($datafeeds)) {
foreach ($datafeeds as $datafeed) {
ABPFE::generate_datafeed($datafeed);
}
}
}
function fn_ab__product_feed_export_get_product_features(&$fields, &$join, &$condition, $params)
{
if (!empty($params['ab__pfe'])) {
$fields[] = '?:product_filter_descriptions.filter';
$join .= ' LEFT JOIN ?:product_filters ON pf.feature_id = ?:product_filters.feature_id';
$join .= ' LEFT JOIN ?:product_filter_descriptions ON ?:product_filters.filter_id = ?:product_filter_descriptions.filter_id AND ?:product_filter_descriptions.lang_code = ?:product_features_descriptions.lang_code';
if (!empty($params['exclude_feature_ids'])) {
$condition .= db_quote(' AND pf.feature_id NOT IN (?n)', $params['exclude_feature_ids']);
}
}
}
function fn_ab__product_feed_export_is_need_watermark_post($object_type, $is_detailed, $company_id, &$result)
{
$datafeed = Registry::get('ab__pfe_datafeed');
if ($object_type == 'product' && !empty($datafeed) && !empty($datafeed['use_watermark'])) {
$result = $datafeed['use_watermark'] == 'Y';
}
}
function fn_ab__pfe_get_features_names($params, $lang_code = CART_LANGUAGE)
{
$fields = [
'abcd__f.*',
'abcd__fd.*',
];
$condition = '';
if (!empty($params['category_id'])) {
$condition .= db_quote(' AND abcd__f.category_id IN (?n)', (array) $params['category_id']);
}
if (!empty($params['feature_id'])) {
$condition .= db_quote(' AND abcd__f.feature_id IN (?n)', (array) $params['feature_id']);
}
if (!empty($params['item_id'])) {
$condition .= db_quote(' AND abcd__f.item_id = ?i', $params['item_id']);
}
if (!empty($params['datafeed_id'])) {
$condition .= db_quote(' AND abcd__f.datafeed_id IN (?n)', (array) $params['datafeed_id']);
}
$items = db_get_hash_array('SELECT ?p FROM ?:ab__pfe_features_names AS abcd__f
LEFT JOIN ?:ab__pfe_feature_name_descriptions AS abcd__fd ON abcd__f.item_id = abcd__fd.item_id AND abcd__fd.lang_code = ?s
WHERE 1 ?p', 'item_id', implode(',', $fields), $lang_code, $condition);
return $items;
}
function fn_ab__pfe_update_feature_name($item_data, $item_id, $lang_code = DESCR_SL)
{
if (empty($item_id)) {
$item_id = db_query('INSERT INTO ?:ab__pfe_features_names ?e', $item_data);
} else {
db_query('UPDATE ?:ab__pfe_features_names SET ?u WHERE item_id = ?i', $item_data, $item_id);
}
$item_data['lang_code'] = $lang_code;
$item_data['item_id'] = $item_id;
db_replace_into('ab__pfe_feature_name_descriptions', $item_data);
return $item_id;
}
function fn_ab__pfe_delete_feature_names($items_ids)
{
db_query('DELETE FROM ?:ab__pfe_features_names WHERE item_id IN (?n)', $items_ids);
db_query('DELETE FROM ?:ab__pfe_feature_name_descriptions WHERE item_id IN (?n)', $items_ids);
return true;
}
function fn_ab__product_feed_export_delete_category_post($category_id, $recurse, $category_ids)
{
$items = fn_ab__pfe_get_features_names(['category_id' => $category_ids]);
if (!empty($items)) {
fn_ab__pfe_delete_feature_names(array_keys($items));
}
return true;
}
function fn_ab__product_feed_export_delete_feature_post($feature_id, $variant_ids)
{
$items = fn_ab__pfe_get_features_names(['feature_id' => $feature_id]);
if (!empty($items)) {
fn_ab__pfe_delete_feature_names(array_keys($items));
}
return true;
}
