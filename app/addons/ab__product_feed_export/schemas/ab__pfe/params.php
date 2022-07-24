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
$schema['ab__product_feed_export']['abpfe_p1'] = [
'name' => __('ab__pfe.params.p1'),
'tooltip' => __('ab__pfe.params.p1.tooltip'),
'default' => 'utf-8',
];
$schema['ab__product_feed_export']['abpfe_p2'] = [
'name' => __('ab__pfe.params.p2'),
'tooltip' => __('ab__pfe.params.p2.tooltip'),
'default' => '',
];
$schema['ab__product_feed_export']['csv_delim'] = [
'name' => __('ab__pfe.params.csv_delim'),
'tooltip' => __('ab__pfe.params.csv_delim.tooltip'),
'default' => ';',
];
$schema['ab__product_feed_export']['csv_enc'] = [
'name' => __('ab__pfe.params.csv_enc'),
'tooltip' => __('ab__pfe.params.csv_enc.tooltip'),
'default' => '\'',
];
return $schema;
