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
$schema['ab__pfe_datafeeds']['modes']['manage']['vendor_only'] = true;
$schema['ab__pfe_datafeeds']['modes']['manage']['use_company'] = true;
$schema['ab__pfe_datafeeds']['modes']['manual_generate']['vendor_only'] = true;
$schema['ab__pfe_datafeeds']['modes']['manual_generate']['use_company'] = true;
$schema['ab__pfe_datafeeds']['modes']['update']['use_company'] = true;
$schema['ab__pfe_datafeeds']['modes']['generate']['use_company'] = true;
$schema['ab__pfe_datafeeds']['page_title'] = 'ab__pfe.datafeeds';
$schema['ab__pfe']['modes']['demodata']['vendor_only'] = true;
$schema['ab__pfe']['modes']['demodata']['use_company'] = true;
return $schema;
