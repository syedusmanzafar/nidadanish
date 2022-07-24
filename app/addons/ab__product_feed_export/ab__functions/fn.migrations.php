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
use Tygh\Settings;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}

function fn_ab__product_feed_export_install()
{
$objects = [
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'ext',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD ext VARCHAR(5) NOT NULL DEFAULT \'\' AFTER filename',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'company_id',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD company_id INT(10) unsigned NOT NULL DEFAULT 0 AFTER datafeed_id',
'add_sql' => ['ALTER TABLE ?:ab__pfe_datafeeds ADD KEY `company_id` (`company_id`)'],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'storefront_id',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD storefront_id INT(11) unsigned NOT NULL DEFAULT 0 AFTER company_id',
'add_sql' => ['ALTER TABLE ?:ab__pfe_datafeeds ADD KEY `storefront_id` (`storefront_id`)'],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'images_full_size',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD images_full_size CHAR(1) NOT NULL DEFAULT \'Y\' AFTER max_images',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'use_watermark',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD use_watermark CHAR(1) NOT NULL DEFAULT \'N\' AFTER images_full_size',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'export_variations',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD export_variations CHAR(1) NOT NULL DEFAULT \'N\' AFTER use_watermark',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'promotions_apply',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD promotions_apply CHAR(1) NOT NULL DEFAULT \'N\' AFTER export_variations',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'features_conditions',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD features_conditions text CHARACTER SET utf8 NOT NULL AFTER included_subcategories',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'output_to_display',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD output_to_display CHAR(1) NOT NULL DEFAULT \'N\'',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'included_products',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD `included_products` text NOT NULL AFTER included_subcategories',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'included_companies',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD `included_companies` text NOT NULL AFTER included_products',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'amount_from',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD amount_from MEDIUMINT(8) NOT NULL DEFAULT 0 AFTER price_to',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'amount_to',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD amount_to MEDIUMINT(8) NOT NULL DEFAULT 0 AFTER amount_from',
'add_sql' => [],
],
[
'table' => '?:ab__pfe_datafeeds',
'field' => 'generate_before_download',
'sql' => 'ALTER TABLE ?:ab__pfe_datafeeds ADD generate_before_download CHAR(1) NOT NULL DEFAULT \'N\' AFTER amount_to',
'add_sql' => [],
],
];
if (!empty($objects) && is_array($objects)) {
foreach ($objects as $object) {
$fields = db_get_fields('DESCRIBE ' . $object['table']);
if (!empty($fields) && is_array($fields)) {
$is_present_field = false;
foreach ($fields as $f) {
if ($f == $object['field']) {
$is_present_field = true;
break;
}
}
if (!$is_present_field) {
db_query($object['sql']);
if (!empty($object['add_sql'])) {
foreach ($object['add_sql'] as $sql) {
db_query($sql);
}
}
}
}
}
}
}

function fn_ab__pfe_install_cron_key()
{
$new_value = fn_generate_password(15);
Settings::instance()->updateValue('cron_key', $new_value, 'ab__product_feed_export');
}

function fn_ab__pfe_install_3_8_0__3_7_3()
{
$default_storefront = Tygh::$app['storefront.repository']->findDefault();
$datafeeds = db_get_array('SELECT datafeed_id, company_id FROM ?:ab__pfe_datafeeds WHERE storefront_id = 0');
if (empty($datafeeds)) {
return;
}
if (fn_allowed_for('MULTIVENDOR')) {
$datafeeds_ids = array_column($datafeeds, 'datafeed_id');
db_query('UPDATE ?:ab__pfe_datafeeds SET storefront_id = ?i WHERE datafeed_id IN (?n)', $default_storefront->storefront_id, $datafeeds_ids);
} else {
foreach ($datafeeds as $datafeed) {
$storefront = Tygh::$app['storefront.repository']->findByCompanyId($datafeed['company_id']);
$storefront_id = empty($storefront) ? $default_storefront->storefront_id : $storefront->storefront_id;
db_query('UPDATE ?:ab__pfe_datafeeds SET storefront_id = ?i WHERE datafeed_id = ?i', $storefront_id, $datafeed['company_id']);
}
}
}