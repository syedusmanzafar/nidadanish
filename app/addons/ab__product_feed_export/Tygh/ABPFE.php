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
namespace Tygh;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
use Tygh\Enum\ProductTracking;
use Tygh\Enum\ProductFeatures;
use Tygh\Addons\ProductVariations\Product\Type\Type;
class ABPFE
{
private static $_features = [];
public static function get_default_templates($name = '')
{
$schema = fn_get_schema('ab__pfe', 'templates');
if (!empty($schema) && is_array($schema)) {
$tmps = [];
$path_tmp = Registry::get('config.dir.design_backend') . 'templates/addons';
foreach ($schema as $object => $templates) {
foreach ($templates as $id => $template) {
$tmps[$id]['name'] = $template['name'];
$tmps[$id]['path'] = "{$path_tmp}/{$object}/templates/{$id}.tplx";
}
}
if (empty($name)) {
return $tmps;
}
return (file_exists($tmps[$name]['path'])) ? fn_get_contents($tmps[$name]['path']) : __('ab__pfe.erorrs.template_is_absent');
}
return false;
}
public static function get_default_params()
{
$schema = fn_get_schema('ab__pfe', 'params');
if (!empty($schema) && is_array($schema)) {
return array_reverse($schema);
}
return false;
}
public static function get_extensions_list()
{
$schema = fn_get_schema('ab__pfe', 'extensions');
if (!empty($schema) && is_array($schema)) {
return $schema;
}
return false;
}
public static function get_templates($params = [], $items_per_page = 0)
{
fn_set_hook('ab__pfe_get_templates_pre', $params, $items_per_page);
$default_params = [
'template_id' => 0,
'page' => 1,
'items_per_page' => $items_per_page,
'limit' => 0,
'status' => ['A', 'D'],
];
$params = array_merge($default_params, $params);
$fields = [
'?:ab__pfe_templates.template_id',
'?:ab__pfe_templates.template',
'?:ab__pfe_templates.name',
'?:ab__pfe_templates.position',
'?:ab__pfe_templates.status',
];
$sortings = [
'status_position_name' => [
'?:ab__pfe_templates.status',
'?:ab__pfe_templates.position',
'?:ab__pfe_templates.name',
],
];
$join = $condition = $limit = '';
$condition .= db_quote(' AND ?:ab__pfe_templates.status IN (?a)', $params['status']);
if (!empty($params['template_id'])) {
$condition .= db_quote(' AND ?:ab__pfe_templates.template_id IN (?n)', $params['template_id']);
}
fn_set_hook('ab__pfe_get_templates', $params, $fields, $sortings, $join, $condition, $limit);
$sorting = db_sort($params, $sortings, 'status_position_name', 'asc');
$limit = '';
if (!empty($params['items_per_page'])) {
$params['total_items'] = db_get_field('SELECT COUNT(DISTINCT(?:ab__pfe_templates.template_id)) FROM ?:ab__pfe_templates ?p WHERE 1 ?p', $join, $condition);
$limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
}
$data = db_get_hash_array('SELECT ' . implode(',', $fields) . ' FROM ?:ab__pfe_templates ?p WHERE 1 ?p ?p ?p', 'template_id', $join, $condition, $sorting, $limit);
fn_set_hook('ab__pfe_get_templates_post', $params, $data);
if (empty($data)) {
return [[], $params];
}
if (!empty($params['limit']) && $params['limit'] == 1) {
return array_shift($data);
}
return [$data, $params];
}
public static function update_template($data, $id = 0)
{
if (empty($data)) {
return false;
}
fn_set_hook('ab__pfe_update_template_pre', $data, $id);
$d = [
'name' => trim(fn_generate_name($data['name'])),
'status' => $data['status'],
'position' => intval($data['position']),
'template' => $data['template'],
];
if (!empty($id)) {
db_query('UPDATE ?:ab__pfe_templates SET ?u WHERE template_id = ?i', $d, $id);
} else {
$id = db_query('INSERT INTO ?:ab__pfe_templates ?e', $d);
}
fn_set_hook('ab__pfe_update_template_post', $data, $id);
return $id;
}
public static function delete_templates($template_ids)
{
if (!empty($template_ids)) {
fn_set_hook('ab__pfe_delete_templates_pre', $template_ids);
$deleted = false;
$datafeeds = db_get_field('SELECT COUNT(*) FROM ?:ab__pfe_datafeeds WHERE template_id IN (?n)', $template_ids);
if (empty($datafeeds)) {
$deleted = true;
db_query('DELETE FROM ?:ab__pfe_templates WHERE template_id IN (?n)', $template_ids);
} else {
fn_set_notification('W', __('warning'), __('ab__pfe.error.template_delete'));
}
fn_set_hook('ab__pfe_delete_templates_post', $template_ids, $deleted);
}
}
public static function get_datafeeds($params = [], $items_per_page = 0)
{
fn_set_hook('ab__pfe_get_datafeeds_pre', $params, $items_per_page);
$default_params = [
'page' => 1,
'items_per_page' => $items_per_page,
'limit' => 0,
];
$params = array_merge($default_params, $params);
$fields = [
'?:ab__pfe_datafeeds.*',
'?:ab__pfe_datafeed_status.timestamp',
'?:ab__pfe_datafeed_status.total_products',
'?:ab__pfe_datafeed_status.loaded_products',
];
$sortings = [
'status_position_name' => [
'?:ab__pfe_datafeeds.status',
'?:ab__pfe_datafeeds.position',
'?:ab__pfe_datafeeds.name',
],
'timestamp' => '?:ab__pfe_datafeed_status.timestamp',
];
$join = $condition = $limit = '';
if (!empty($params['status'])) {
$condition .= db_quote(' AND ?:ab__pfe_datafeeds.status IN (?a)', $params['status']);
}
if (!empty($params['datafeed_id'])) {
$condition .= db_quote(' AND ?:ab__pfe_datafeeds.datafeed_id IN (?n)', $params['datafeed_id']);
}
if (!empty($params['auto_generate'])) {
$condition .= db_quote(' AND ?:ab__pfe_datafeeds.auto_generate IN (?a) ', $params['auto_generate']);
}
if (!empty($params['storefront_id'])) {
$condition .= db_quote(' AND ?:ab__pfe_datafeeds.storefront_id = ?i', $params['storefront_id']);
}
if (!empty($params['company_id'])) {
$condition .= db_quote(' AND ?:ab__pfe_datafeeds.company_id = ?i', $params['company_id']);
}
$join .= ' LEFT JOIN ?:ab__pfe_datafeed_status ON ?:ab__pfe_datafeed_status.datafeed_id = ?:ab__pfe_datafeeds.datafeed_id';
fn_set_hook('ab__pfe_get_datafeeds', $params, $fields, $sortings, $join, $condition, $limit);
$sorting = db_sort($params, $sortings, 'status_position_name', 'asc');
$limit = '';
if (!empty($params['items_per_page'])) {
$params['total_items'] = db_get_field('SELECT COUNT(DISTINCT(?:ab__pfe_datafeeds.datafeed_id)) FROM ?:ab__pfe_datafeeds ?p WHERE 1 ?p', $join, $condition);
$limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
}
$data = db_get_hash_array('SELECT ' . implode(',', $fields) . ' FROM ?:ab__pfe_datafeeds ?p WHERE 1 ?p ?p ?p', 'datafeed_id', $join, $condition, $sorting, $limit);
if (empty($data)) {
return [[], $params];
}

$formatter = Tygh::$app['formatter'];

$storefront_repository = Tygh::$app['storefront.repository'];
foreach ($data as &$datafeed) {
$datafeed['params'] = unserialize($datafeed['params']);
$datafeed['features_conditions'] = unserialize($datafeed['features_conditions']);
$datafeed['stop_words_search_fields'] = unserialize($datafeed['stop_words_search_fields']);
$datafeed['last_update_date'] = ($datafeed['timestamp'] && $datafeed['loaded_products'] > $datafeed['total_products']) ? $formatter->asDatetime($datafeed['timestamp']) : '';
if (fn_allowed_for('MULTIVENDOR')) {
$storefront = $storefront_repository->findById($datafeed['storefront_id']);
if ($storefront->getCompanyIds()) {
$company_ids = array_intersect($storefront->getCompanyIds(), explode(',', $datafeed['included_companies']));
$datafeed['included_companies'] = implode(',', $company_ids);
}
}
}
fn_set_hook('ab__pfe_get_datafeeds_post', $params, $data);
return [$data, $params];
}
public static function delete_datafeeds($datafeed_ids)
{
if (!empty($datafeed_ids)) {
fn_set_hook('ab__pfe_delete_datafeeds', $datafeed_ids);
db_query('DELETE FROM ?:ab__pfe_datafeeds WHERE datafeed_id IN (?n)', $datafeed_ids);
$items = fn_ab__pfe_get_features_names(['datafeed_id' => $datafeed_ids]);
if (!empty($items)) {
fn_ab__pfe_delete_feature_names(array_keys($items));
}
self::reset_datafeed_status($datafeed_ids);
}
}
public static function update_datafeed($data, $id = 0)
{
if (empty($data)) {
return false;
}
fn_set_hook('ab__pfe_update_datafeed_pre', $data, $id);
$d = [
'status' => $data['status'],
'position' => intval($data['position']),
'name' => trim($data['name']),
'company_id' => empty($data['company_id']) ? 0 : intval($data['company_id']),
'storefront_id' => intval($data['storefront_id']),
'filename' => fn_generate_name(trim($data['filename'])),
'ext' => trim($data['ext']),
'template_id' => intval($data['template_id']),
'lang_code' => $data['lang_code'],
'currency_code' => $data['currency_code'],
'brand_id' => intval($data['brand_id']),
'max_images' => intval($data['max_images']),
'images_full_size' => (in_array($data['images_full_size'], ['Y', 'N'])) ? $data['images_full_size'] : 'Y',
'use_watermark' => (!empty($data['use_watermark']) && in_array($data['use_watermark'], ['Y', 'N'])) ? $data['use_watermark'] : 'N',
'export_variations' => (!empty($data['export_variations']) && in_array($data['export_variations'], ['Y', 'N'])) ? $data['export_variations'] : 'N',
'promotions_apply' => (!empty($data['promotions_apply']) && in_array($data['promotions_apply'], ['Y', 'N'])) ? $data['promotions_apply'] : 'N',
'auto_generate' => (!empty($data['auto_generate']) && in_array($data['auto_generate'], ['Y', 'N'])) ? $data['auto_generate'] : 'N',
'generate_before_download' => (!empty($data['generate_before_download']) && in_array($data['generate_before_download'], ['Y', 'N'])) ? $data['generate_before_download'] : 'N',
'login' => trim($data['login']),
'password' => trim($data['password']),
'amount_from' => intval($data['amount_from']),
'amount_to' => floatval($data['amount_to']),
'price_from' => floatval($data['price_from']),
'price_to' => floatval($data['price_to']),
'only_in_stock' => (in_array($data['only_in_stock'], ['Y', 'N'])) ? $data['only_in_stock'] : 'N',
'only_with_description' => (in_array($data['only_with_description'], ['Y', 'N'])) ? $data['only_with_description'] : 'N',
'only_with_images' => (in_array($data['only_with_images'], ['Y', 'N'])) ? $data['only_with_images'] : 'N',
'stop_words' => trim($data['stop_words']),
'stop_words_search_fields' => empty($data['stop_words_search_fields']) ? '' : serialize($data['stop_words_search_fields']),
'included_categories' => $data['included_categories'],
'included_subcategories' => $data['included_subcategories'],
'features_conditions' => empty($data['features_conditions']) ? '' : serialize($data['features_conditions']),
'included_products' => $data['included_products'],
'included_companies' => empty($data['included_companies']) ? '' : $data['included_companies'],
'excluded_categories' => $data['excluded_categories'],
'excluded_subcategories' => $data['excluded_subcategories'],
'excluded_products' => $data['excluded_products'],
'params' => serialize($data['params']),
'output_to_display' => (in_array($data['output_to_display'], ['Y', 'N'])) ? $data['output_to_display'] : 'N',
];
$d['filename'] = str_replace('.', '', $d['filename']);
if (!empty($id)) {
db_query('UPDATE ?:ab__pfe_datafeeds SET ?u WHERE datafeed_id = ?i', $d, $id);
} else {
$id = db_query('INSERT INTO ?:ab__pfe_datafeeds ?e', $d);
}
fn_set_hook('ab__pfe_update_datafeed_post', $data, $id);
return $id;
}
public static function get_datafeed_categories($datafeed)
{
if (empty($datafeed)) {
return false;
}
$res = [];
if (empty($datafeed['included_categories'])) {
$included_categories = db_get_fields('SELECT category_id FROM ?:categories WHERE status = ?s AND company_id = ?i', 'A', $datafeed['company_id']);
} else {
$included_categories = (array) static::_get_categories_and_subcategories($datafeed['included_categories'], $datafeed['included_subcategories']);
}
$excluded_categories = (array) static::_get_categories_and_subcategories($datafeed['excluded_categories'], $datafeed['excluded_subcategories']);
$diff = array_diff($included_categories, $excluded_categories);
if (!empty($diff)) {
$fields = [
'c.category_id',
'c.parent_id',
'cd.category',
];
$join = 'INNER JOIN ?:category_descriptions AS cd ON (c.category_id = cd.category_id)';
$conditions = db_quote('c.category_id IN (?n) AND cd.lang_code = ?s', $diff, $datafeed['lang_code']);
$conditions .= fn_get_company_condition('c.company_id');
fn_set_hook('ab__pfe_get_categories', $datafeed, $fields, $join, $conditions);
$res = db_get_hash_array('SELECT ' . implode(',', $fields) . ' FROM ?:categories AS c ?p
WHERE ?p
ORDER BY c.parent_id, c.position, c.category_id', 'category_id', $join, $conditions);
foreach ($res as &$r) {
$r['category'] = htmlspecialchars($r['category']);
}
}
return $res;
}
private static function _get_categories_and_subcategories($categories = '', $subcats = 'N')
{
$res = [];
if (strlen(trim($categories))) {
$cats = explode(',', $categories);
$res = $cats;
if (!empty($cats) && is_array($cats)) {
if ($subcats == 'Y') {
foreach ($cats as $category_id) {
$subcats = db_get_fields('SELECT a.category_id
FROM ?:categories AS a
LEFT JOIN ?:categories AS b ON (b.category_id = ?i and b.status = ?s)
WHERE a.id_path LIKE CONCAT( b.id_path, ?l )
UNION
SELECT c.category_id
FROM ?:categories AS c
WHERE c.category_id = ?i AND c.status = ?s', $category_id, 'A', '/%', $category_id, 'A');
$res = array_merge($res, $subcats);
}
}
}
$res = array_unique($res);
}
return $res;
}

public static function init_tmp_datafeed_table($datafeed, $params)
{
$fields = [
'p.product_id',
'p.product_code',
'p.list_price',
'p.zero_price_action',
'p.amount',
'p.min_qty',
'p.tracking',
'p.is_edp',
'p.avail_since',
'"" AS category_ids',
'"" AS brand',
];
$condition = $join = $category_join = $group = $limit = '';
if (fn_allowed_for('ULTIMATE') && Registry::get('runtime.company_id')) {
$join .= db_quote(' LEFT JOIN ?:ult_product_prices AS shared_prices ON shared_prices.product_id = p.product_id AND shared_prices.lower_limit = 1 AND shared_prices.usergroup_id = 0 AND shared_prices.company_id = ?i', Registry::get('runtime.company_id'));
$fields[] = 'IF(shared_prices.product_id IS NOT NULL, shared_prices.price, pp.price) AS price';
$join .= db_quote(' LEFT JOIN ?:ult_product_descriptions AS shared_descr ON shared_descr.product_id = p.product_id AND shared_descr.lang_code = ?s AND shared_descr.company_id = ?i', $datafeed['lang_code'], Registry::get('runtime.company_id'));
$fields[] = 'IF(shared_descr.product_id IS NOT NULL, shared_descr.product, pd.product) AS product';
$fields[] = 'IF(shared_descr.product_id IS NOT NULL, shared_descr.short_description, pd.short_description) AS short_description';
$fields[] = 'IF(shared_descr.product_id IS NOT NULL, shared_descr.full_description, pd.full_description) AS full_description';
} else {
$fields[] = 'pp.price';
$fields[] = 'pd.product';
$fields[] = 'pd.short_description';
$fields[] = 'pd.full_description';
}
if (fn_allowed_for('MULTIVENDOR')) {
if ($params['storefront']->getCompanyIds()) {
$condition .= db_quote(' AND p.company_id IN (?n)', $params['storefront']->getCompanyIds());
}
if (!empty($datafeed['included_companies'])) {
$condition .= db_quote(' AND p.company_id IN (?n)', explode(',', $datafeed['included_companies']));
}
}
$condition .= db_quote(' AND p.status = ?s', 'A');
if (floatval($datafeed['price_from'])) {
$condition .= db_quote(' AND pp.price >= ?d', floatval($datafeed['price_from']));
}
if (floatval($datafeed['price_to'])) {
$condition .= db_quote(' AND pp.price <= ?d', floatval($datafeed['price_to']));
}
if ($datafeed['only_in_stock'] == 'Y') {
$condition .= db_quote(' AND (p.zero_price_action <> ?s OR pp.price > 0)', 'R');
if (Registry::get('settings.General.inventory_tracking') == 'Y' && Registry::get('settings.General.allow_negative_amount') != 'Y') {
$condition .= db_quote(' AND ((p.amount > 0 AND (p.min_qty IS NULL OR p.amount >= p.min_qty)) OR p.tracking = ?s OR p.is_edp = ?s)', ProductTracking::DO_NOT_TRACK, 'Y');
}
}
if (intval($datafeed['amount_from']) > 0) {
$condition .= db_quote(' AND p.amount >= ?i', intval($datafeed['amount_from']));
}
if (intval($datafeed['amount_to']) > 0) {
$condition .= db_quote(' AND p.amount <= ?i', intval($datafeed['amount_to']));
}
if ($datafeed['only_with_description'] == 'Y') {
$condition .= db_quote(' AND (pd.full_description <> "" OR pd.short_description <> "")');
}
if (!empty($datafeed['excluded_products'])) {
$condition .= db_quote(' AND p.product_id NOT IN (?p)', $datafeed['excluded_products']);
}
if (!empty($datafeed['stop_words']) && !empty($datafeed['stop_words_search_fields'])) {
$stop_words = explode(',', $datafeed['stop_words']);
$stop_words_cond = [];
if (!empty($stop_words)) {
foreach ($stop_words as $stop_word) {
if (!empty($datafeed['stop_words_search_fields']['pname'])) {
$stop_words_cond[] = db_quote('pd.product NOT LIKE ?s', '%' . trim($stop_word) . '%');
}
if (!empty($datafeed['stop_words_search_fields']['pshort'])) {
$stop_words_cond[] = db_quote('pd.short_description NOT LIKE ?s', '%' . trim($stop_word) . '%');
}
if (!empty($datafeed['stop_words_search_fields']['pfull'])) {
$stop_words_cond[] = db_quote('pd.full_description NOT LIKE ?s', '%' . trim($stop_word) . '%');
}
if (!empty($datafeed['stop_words_search_fields']['pkeywords'])) {
$stop_words_cond[] = db_quote('pd.meta_keywords NOT LIKE ?s', '%' . trim($stop_word) . '%');
}
}
}
if (!empty($stop_words_cond)) {
$condition .= db_quote(' AND (?p)', implode(' AND ', $stop_words_cond));
}
}
$join .= db_quote(' INNER JOIN ?:product_descriptions AS pd ON (p.product_id = pd.product_id AND pd.lang_code = ?s)', $datafeed['lang_code']);
$join .= db_quote(' INNER JOIN ?:product_prices AS pp ON (p.product_id = pp.product_id AND pp.lower_limit = 1 AND pp.usergroup_id = 0)');
if (!empty($params['categories'])) {
$category_join = db_quote(' INNER JOIN ?:products_categories AS pc ON pc.product_id = p.product_id AND pc.category_id IN (?n)', array_keys($params['categories']));
$category_join .= db_quote(' INNER JOIN ?:categories ON pc.category_id = ?:categories.category_id AND ?:categories.status IN (?a) ?p', ['A', 'H'], fn_get_company_condition('?:categories.company_id'));
}
if ($datafeed['only_with_images'] == 'Y') {
$join .= db_quote(' INNER JOIN ?:images_links as il ON (p.product_id = il.object_id AND il.object_type = ?s AND il.type = ?s AND il.pair_id IS NOT NULL)', 'product', 'M');
}
if (!empty($datafeed['features_conditions'])) {
foreach ($datafeed['features_conditions'] as $k => $c) {
$feature = self::_get_feature_data($c['condition_element']);
if (empty($feature)) {
continue;
}
$alias = 'pfv_' . intval($k);
$field = self::_get_feature_condition_field($feature);
$feature_cond = self::_make_feature_condition("$alias.$field", $c['operator'], $c['value']);
$variation_condition = ($params['allow_export_variations'] && $feature['purpose'] == 'group_variation_catalog_item') ? " OR {$alias}.product_id = p.parent_product_id" : '';
$join .= db_quote(" INNER JOIN ?:product_features_values AS {$alias} ON {$alias}.feature_id = ?i AND ({$alias}.product_id = p.product_id {$variation_condition}) AND {$alias}.lang_code = ?s AND {$feature_cond} ", $c['condition_element'], $datafeed['lang_code']);
}
}
if (!empty($datafeed['included_products'])) {
$condition .= db_quote(' AND p.product_id IN (?n)', explode(',', $datafeed['included_products']));
}
if (fn_allowed_for('MULTIVENDOR') && !empty($datafeed['included_companies'])) {
$condition .= db_quote(' AND p.company_id IN (?n)', explode(',', $datafeed['included_companies']));
}
if (Registry::get('addons.product_variations.status') === 'A') {
$allowed_types = [Type::PRODUCT_TYPE_SIMPLE];
if ($params['allow_export_variations']) {
$allowed_types[] = Type::PRODUCT_TYPE_VARIATION;
}
$fields[] = 'p.product_type';
$fields[] = 'p.parent_product_id';
$fields[] = 'pvgp.group_id AS variation_group_id';
$join .= db_quote(' LEFT JOIN ?:product_variation_group_products AS pvgp ON p.product_id = pvgp.product_id');
$condition .= db_quote(' AND p.product_type IN (?a)', $allowed_types);
}
if (!empty($params['extra_fields'])) {
$fields = array_merge($fields, $params['extra_fields']);
}
if (!empty($params['extra_join'])) {
$join .= ' ' . $params['extra_join'];
}
if (!empty($params['extra_condition'])) {
$condition .= ' ' . $params['extra_condition'];
}
$group .= db_quote('GROUP BY p.product_id');
fn_set_hook('ab__pfe_get_items', $datafeed, $params, $fields, $join, $condition, $group, $limit);
$fields = array_unique($fields);
$table_name = 'ab__pfe_' . $datafeed['datafeed_id'] . '_temp';
db_query('DROP TABLE IF EXISTS ?:?f', $table_name);
$add_columns_query_parts = [];
foreach ($fields as $field) {
$field = strtolower($field);
if (strpos($field, ' as ') !== false) {
$parts = explode(' as ', $field);
$field = end($parts);
} elseif (strpos($field, '.') !== false) {
$parts = explode('.', $field);
$field = end($parts);
}
$add_columns_query_parts[] = (trim($field) == 'product_id') ? $field . ' mediumint(8) unsigned NOT NULL' : $field . ' text';
}
$add_columns_query_parts[] = 'PRIMARY KEY (`product_id`)';
db_query('CREATE TABLE ?:?f (?p) DEFAULT CHARSET=utf8', $table_name, implode(',', $add_columns_query_parts));
$total_rows = db_query('INSERT ?:?f SELECT ?p FROM ?:products p ?p WHERE 1 ?p ?p ORDER BY p.product_id ASC', $table_name, implode(',', $fields), $category_join . $join, $condition, $group);
db_query('UPDATE ?:?f AS data
LEFT JOIN (SELECT product_id, GROUP_CONCAT(IF(link_type = ?s, CONCAT(category_id, ?s), category_id)) AS category_ids FROM ?:products_categories GROUP BY product_id) AS temp ON temp.product_id = data.product_id
SET data.category_ids = temp.category_ids', $table_name, 'M', 'M');
if (!empty($datafeed['brand_id'])) {
db_query('UPDATE ?:?f AS data
LEFT JOIN ?:product_features_values AS pfv ON (data.product_id = pfv.product_id AND pfv.feature_id = ?i AND pfv.lang_code = ?s)
INNER JOIN ?:product_feature_variant_descriptions as pfvd ON (pfv.variant_id = pfvd.variant_id AND pfvd.lang_code = ?s)
SET data.brand = pfvd.variant', $table_name, $datafeed['brand_id'], $datafeed['lang_code'], $datafeed['lang_code']);
}
db_query('UPDATE ?:ab__pfe_datafeed_status SET total_products = ?i WHERE datafeed_id = ?i', $total_rows, $datafeed['datafeed_id']);

fn_set_hook('ab__pfe_get_items_after_table_fill', $datafeed, $params, $table_name);
return $table_name;
}
public static function get_datafeed_items($datafeed, $params)
{
static $storefront = null;
if ($storefront === null) {
$storefront = \Tygh::$app['storefront.repository']->findById($datafeed['storefront_id']);
}
if (empty($datafeed) || empty($params) || empty($storefront)) {
return [];
}
fn_set_hook('ab__pfe_get_items_pre', $datafeed, $params);
$default_params = [
'storefront' => $storefront,
'categories' => [],
'min_product_id' => 0,
'items_per_page' => 200,
'get_images' => false,
'get_features' => false,
'feature_output_style' => 'array',
'datafeed_id' => $datafeed['datafeed_id'],
'allow_export_variations' => Registry::get('addons.product_variations.status') == 'A' ? $datafeed['export_variations'] == 'Y' : false,
'is_ajax' => defined('AJAX_REQUEST'),
];
$params = array_merge($default_params, $params);
static $table_names = [];
if (empty($table_names[$datafeed['datafeed_id']])) {
if (!empty($params['count_only'])) {
ob_get_clean();
self::_print_notification(__('ab__pfe.error.old_template'), 'E', $params['is_ajax']);
exit;
}
$table_names[$datafeed['datafeed_id']] = self::init_tmp_datafeed_table($datafeed, $params);
return self::get_datafeed_items($datafeed, $params);
}
if ($params['is_ajax']) {
$current_content = ob_get_clean();
static $comet_step = 0;
static $comet_progress = 0;
if ($comet_step === 0) {
$total_products = db_get_field('SELECT total_products FROM ?:ab__pfe_datafeed_status WHERE datafeed_id = ?i', $datafeed['datafeed_id']);
$comet_step = ($total_products > 0) ? round(min(1, $params['items_per_page'] / $total_products) * 100, 2) : 1;
fn_set_progress('parts', ceil(100 / $comet_step));
}
fn_set_progress('echo', $comet_progress . '%');
$comet_progress = min(100, $comet_progress + $comet_step);
ob_start();
echo($current_content);
}
$products = db_get_hash_array('SELECT * FROM ?:?f WHERE product_id > ?i LIMIT ?i', 'product_id', $table_names[$datafeed['datafeed_id']], $params['min_product_id'], $params['items_per_page']);
if (empty($products)) {
return [];
}
$storefront_protocol = fn_get_storefront_protocol(null, $storefront->storefront_id);
if ($params['get_images'] && !empty($datafeed['max_images'])) {
$product_ids = array_keys($products);
$main_pairs = fn_get_image_pairs($product_ids, 'product', 'M', false, true, $datafeed['lang_code']);
$additional_pairs = ($datafeed['max_images'] < 2) ? [] : fn_get_image_pairs($product_ids, 'product', 'A', false, true, $datafeed['lang_code']);
$thumbnail_width = Registry::get('settings.Thumbnails.product_details_thumbnail_width');
$thumbnail_height = Registry::get('settings.Thumbnails.product_details_thumbnail_height');
foreach ($products as $key => $product) {
if (empty($main_pairs[$product['product_id']])) {
if (empty($product['parent_product_id']) || empty($main_pairs[$product['parent_product_id']])) {
continue;
}
$product_id = $product['parent_product_id'];
} else {
$product_id = $product['product_id'];
}
$images = empty($additional_pairs[$product_id]) ? $main_pairs[$product_id] : array_merge($main_pairs[$product_id], $additional_pairs[$product_id]);
$count = 0;
foreach ($images as $image) {
if ($datafeed['images_full_size'] != 'Y') {
$image['detailed'] = fn_image_to_display($image, $thumbnail_width, $thumbnail_height);
}
list($products[$key]['images'][]) = explode('?', $image['detailed']['image_path']);
if (++$count == $datafeed['max_images']) {
break;
}
}
}
}
foreach ($products as $product_id => $product) {
if (empty($product['main_category']) && !empty($product['category_ids'])) {
list($product['category_ids'], $product['main_category']) = fn_convert_categories($product['category_ids']);
}
if (empty($params['categories'][$product['main_category']])) {
foreach ($product['category_ids'] as $category_id) {
if (!empty($params['categories'][$category_id])) {
$product['category_id'] = $category_id;
break;
}
}
} else {
$product['category_id'] = $product['main_category'];
}
$products[$product_id] = $product;
}
if ($params['get_features']) {
static::_get_product_features($products, $params, $datafeed['lang_code'], [$datafeed['brand_id']]);
}
if ($datafeed['promotions_apply'] == 'Y') {
fn_gather_additional_products_data($products, [
'get_options' => false,
'get_taxed_prices' => false,
'detailed_params' => false,
]);
}
if (fn_allowed_for('ULTIMATE')) {
$company_url = '&company_id=' . $datafeed['company_id'];
} else {
$company_url = '&storefront_id=' . $datafeed['storefront_id'];
}
foreach ($products as $product_id => $product) {
if ($product['base_price'] > $product['price']) {
$product['list_price'] = $product['base_price'];
}
$product['url'] = fn_url('products.view?product_id=' . $product_id . $company_url, 'C', $storefront_protocol, $datafeed['lang_code']);
$product['product'] = htmlspecialchars($product['product']);
$product['brand'] = htmlspecialchars($product['brand']);
$product['price'] = fn_format_price_by_currency($product['price'], CART_PRIMARY_CURRENCY, $datafeed['currency_code']);
$product['list_price'] = fn_format_price_by_currency($product['list_price'], CART_PRIMARY_CURRENCY, $datafeed['currency_code']);
$products[$product_id] = $product;
}
db_query('UPDATE ?:ab__pfe_datafeed_status SET loaded_products = loaded_products + ?i WHERE datafeed_id = ?i', $params['items_per_page'], $datafeed['datafeed_id']);
fn_set_hook('ab__pfe_get_items_post', $datafeed, $params, $products);
return $products;
}
private static function _make_feature_condition($field, $operator, $value)
{
if ($operator === 'eq') {
return db_quote("$field = ?s", $value);
} elseif ($operator === 'neq') {
return "$field <> $value";
} elseif ($operator === 'lte') {
return "$field <= $value";
} elseif ($operator === 'lt') {
return "$field < $value";
} elseif ($operator === 'gte') {
return "$field >= $value";
} elseif ($operator === 'gt') {
return "$field > $value";
} elseif ($operator === 'in') {
return db_quote("$field IN (?a)", explode(',', $value));
} elseif ($operator === 'nin') {
return db_quote("$field NOT IN (?a)", explode(',', $value));
}
return '0';
}
private static function _get_feature_data($feature_id)
{
if (empty(self::$_features[$feature_id])) {
self::$_features[$feature_id] = db_get_row('SELECT * FROM ?:product_features WHERE feature_id = ?i', $feature_id);
}
return self::$_features[$feature_id];
}
private static function _get_feature_condition_field($feature)
{
if (strpos(ProductFeatures::TEXT_SELECTBOX . ProductFeatures::NUMBER_SELECTBOX . ProductFeatures::EXTENDED . ProductFeatures::MULTIPLE_CHECKBOX, $feature['feature_type']) !== false) {
$field = 'variant_id';
} elseif (strpos(ProductFeatures::NUMBER_FIELD . ProductFeatures::DATE, $feature['feature_type']) !== false) {
$field = 'value_int';
} else {
$field = 'value';
}
return $field;
}
private static function _get_product_features(&$products, $params, $lang_code, $exclude_ids = [])
{
if (!empty($products)) {
fn_set_hook('ab__pfe_get_features', $products, $params, $lang_code, $exclude_ids);
static $features_names = null;
if ($features_names === null) {
$items = fn_ab__pfe_get_features_names([
'datafeed_id' => $params['datafeed_id'],
], $lang_code);
$features_names = [];
foreach ($items as $item) {
$features_names[$item['category_id']][$item['feature_id']] = $item['name'];
}
}
foreach ($products as $product_id => &$product) {
list($features) = fn_get_product_features([
'ab__pfe' => true,
'product_id' => $product_id,
'existent_only' => true,
'statuses' => 'A',
'variants' => true,
'variants_selected_only' => true,
'exclude_group' => true,
'exclude_feature_ids' => $exclude_ids,
], 0, $lang_code);
$product['features'] = [];
$variant_names = [];
if (!empty($features)) {
foreach ($features as $feature) {
if (!empty($features_names[$product['category_id']]) && !empty($features_names[$product['category_id']][$feature['feature_id']])) {
$feature['description'] = $features_names[$product['category_id']][$feature['feature_id']];
$feature['overrided'] = true;
} else {
$feature['description'] = empty($feature['filter']) ? preg_replace('/\s\([^)]+\)/', '', $feature['description']) : $feature['filter'];
$feature['overrided'] = false;
}
$value = static::_get_feature_value($feature);
if (!empty($value)) {
$feature['description'] = htmlspecialchars($feature['description']);
$value = htmlspecialchars($value);
if ($params['feature_output_style'] == 'string') {
$product['features'][$feature['feature_id']] = $feature['description'] . ': ' . $value;
} else {
$product['features'][$feature['feature_id']] = [
'name' => $feature['description'],
'value' => $value,
'variant_id' => empty($feature['variant_id']) ? '' : $feature['variant_id'],
'overrided' => $feature['overrided'],
];
}
if ($feature['purpose'] === 'group_variation_catalog_item') {
$variant_names[] = $value;
}
}
}
if ($params['feature_output_style'] == 'string') {
$product['features'] = implode('; ', $product['features']);
}
if (!empty($variant_names) && $params['allow_export_variations']) {
$product['product'] = sprintf('%s (%s)', $product['product'], implode(', ', $variant_names));
}
}
fn_set_hook('ab__pfe_get_product_features', $product, $features, $params, $lang_code, $exclude_ids);
}
fn_set_hook('ab__pfe_get_features_post', $products, $params, $lang_code, $exclude_ids);
}
}
private static function _get_feature_value($feature)
{
$value = '';
if ($feature['prefix']) {
$value .= $feature['prefix'];
}
if ($feature['feature_type'] == ProductFeatures::DATE) {
$value .= fn_date_format($feature['value_int'], Registry::get('settings.Appearance.date_format'));
} elseif (in_array($feature['feature_type'], [ProductFeatures::MULTIPLE_CHECKBOX, ProductFeatures::TEXT_SELECTBOX, ProductFeatures::NUMBER_SELECTBOX, ProductFeatures::EXTENDED])) {
$variants = [];
foreach ($feature['variants'] as $variant) {
$variants[] = empty($variant['variant']) ? $variant['value'] : $variant['variant'];
}
if (!empty($variants)) {
$value .= implode(', ', $variants);
}
} elseif ($feature['feature_type'] == ProductFeatures::SINGLE_CHECKBOX && $feature['value'] == 'Y') {
$value .= __('ab__pfe.checked');
} elseif ($feature['feature_type'] == ProductFeatures::NUMBER_FIELD) {
$value .= floatval($feature['value_int']);
} elseif ($feature['feature_type'] == ProductFeatures::TEXT_FIELD) {
$value .= $feature['value'];
} else {
return false;
}
if ($feature['suffix']) {
$value .= $feature['suffix'];
}
return $value;
}
public static function send_datafeed($datafeed, $filename, $ext)
{
$file = static::get_datafeed_filename($datafeed);
$extensions = static::get_extensions_list();
if (file_exists($file)) {
if (!empty($extensions[$ext]) && !empty($extensions[$ext]['header'])) {
header($extensions[$ext]['header']);
$disposition = (!empty($datafeed['output_to_display']) && $datafeed['output_to_display'] == 'Y') ? 'inline' : 'attachment';
header('Content-Disposition: ' . $disposition . '; filename="' . $filename . '.' . $ext . '"');
}
if ($fd = fopen($file, 'rb')) {
while (!feof($fd)) {
print fread($fd, 1024);
}
fclose($fd);
}
} else {
header('Content-Type: text/plain;charset=utf-8');
fn_echo(__('ab__pfe.file_is_not_generated'));
}
}
public static function get_datafeed_filename($datafeed, $as_url = false)
{
if ($as_url) {
$storefront = \Tygh::$app['storefront.repository']->findById($datafeed['storefront_id']);
$storefront_url = fn_get_storefront_protocol(null, $storefront->storefront_id) . '://' . $storefront->url;
if (Registry::get('addons.seo.status') == 'A') {
$url = $storefront_url . '/ab__pfe_' . $datafeed['datafeed_id'] . '_' . $datafeed['filename'] . '.' . $datafeed['ext'];
} else {
$url = $storefront_url . "/?dispatch=ab__pfe_feed.get&datafeed_id={$datafeed['datafeed_id']}&filename={$datafeed['filename']}&ext={$datafeed['ext']}";
}
return $url;
}
return fn_get_files_dir_path($datafeed['company_id']) . 'ab__product_feed_export/ab__pfe_' . $datafeed['datafeed_id'] . '_' . $datafeed['filename'] . '.' . $datafeed['ext'];
}
private static function _datafeed_to_file($d, $datafeed)
{
$file = static::get_datafeed_filename($datafeed);
fn_put_contents($file, trim($d));
}
public static function generate_datafeed($datafeed)
{
$res = false;
if (empty($datafeed)) {
return $res;
}
if (defined('AJAX_REQUEST')) {
fn_set_progress('title', __('ab__pfe.comet.title'));
fn_set_progress('echo', __('ab__pfe.comet.init_table'), false);
}
Registry::set('ab__pfe_datafeed', $datafeed);
$in_progress = db_get_row('SELECT * FROM ?:ab__pfe_datafeed_status WHERE loaded_products < total_products AND total_products > 0 AND datafeed_id = ?i', $datafeed['datafeed_id']);
if (!empty($in_progress) && ($in_progress['timestamp'] + SECONDS_IN_HOUR) > TIME) {
self::_print_notification(__('ab__pfe.datafeed.generation_in_progress', [
'[time]' => fn_date_format($in_progress['timestamp'], Registry::get('settings.Appearance.date_format') . ' ' . Registry::get('settings.Appearance.time_format')),
'[loaded]' => $in_progress['loaded_products'],
'[total]' => $in_progress['total_products'],
]), 'W', defined('AJAX_REQUEST'));
return false;
}
db_replace_into('ab__pfe_datafeed_status', [
'datafeed_id' => $datafeed['datafeed_id'],
'timestamp' => TIME,
'total_products' => 0,
'loaded_products' => 0,
]);
Tygh::$app['view']->assign('datafeed', $datafeed);
$template = ABPFE::get_templates([
'template_id' => $datafeed['template_id'],
'limit' => 1,
]);
if (!empty($datafeed['params']) && is_array($datafeed['params'])) {
foreach ($datafeed['params'] as $k => $v) {
Tygh::$app['view']->assign($k, $v);
}
}
$vars = [];
fn_set_hook('ab__pfe_extra_vars', $vars);
Tygh::$app['view']->assign('vars', $vars);
$categories = ABPFE::get_datafeed_categories($datafeed);
Tygh::$app['view']->assign('categories', $categories);
if (!empty($categories)) {
$d = Tygh::$app['view']->fetch('string:' . $template['template']);
if (!empty($d)) {
static::_datafeed_to_file($d, $datafeed);
$res = true;
}
} else {
fn_print_r('select categories in datafeed');
}
Registry::set('ab__pfe_datafeed', false);
db_query('DROP TABLE IF EXISTS ?:ab__pfe_' . $datafeed['datafeed_id'] . '_temp');
return $res;
}
public static function reset_datafeed_status($datafeed_ids)
{
db_query('DELETE FROM ?:ab__pfe_datafeed_status WHERE datafeed_id IN (?n)', $datafeed_ids);
}
private static function _print_notification($message, $type = 'E', $is_ajax = false)
{
if ($is_ajax) {
$title = ($type === 'E') ? __('error') : __('warning');
fn_set_notification($type, $title, $message);
} else {
fn_print_r($message);
}
}
}
