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
$schema['central']['ab__addons']['items']['ab__product_feed_export']['attrs'] = ['class' => 'is-addon'];
$schema['central']['ab__addons']['items']['ab__product_feed_export']['href'] = 'ab__pfe.help';
$schema['central']['ab__addons']['items']['ab__product_feed_export']['position'] = 3;
$schema['central']['ab__addons']['items']['ab__product_feed_export']['subitems']['ab__pfe.settings'] = [
'href' => 'addons.update&addon=ab__product_feed_export',
'alt' => 'addons.update&addon=ab__product_feed_export',
'position' => 0,
];
$schema['central']['ab__addons']['items']['ab__product_feed_export']['subitems']['ab__pfe.datafeeds'] = [
'href' => 'ab__pfe_datafeeds.manage',
'alt' => 'ab__pfe_datafeeds.manage,ab__pfe_datafeeds.update',
'position' => 10,
];
$schema['central']['ab__addons']['items']['ab__product_feed_export']['subitems']['ab__pfe.templates'] = [
'href' => 'ab__pfe_templates.manage',
'alt' => 'ab__pfe_templates.manage,ab__pfe_templates.update',
'position' => 20,
];
$schema['central']['ab__addons']['items']['ab__product_feed_export']['subitems']['ab__pfe.demodata'] = [
'href' => 'ab__pfe.demodata',
'alt' => 'ab__pfe.demodata',
'position' => 30,
];
$schema['central']['ab__addons']['items']['ab__product_feed_export']['subitems']['ab__pfe.help'] = [
'href' => 'ab__pfe.help',
'alt' => 'ab__pfe.help',
'position' => 100,
];
return $schema;
