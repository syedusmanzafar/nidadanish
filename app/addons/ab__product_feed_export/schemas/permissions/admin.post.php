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
$schema['ab__pfe_templates']['permissions']
= $schema['ab__pfe_datafeeds']['permissions']
= $schema['ab__pfe']['permissions']
= ['GET' => 'ab__pfe.view', 'POST' => 'ab__pfe.manage'];
$schema['ab__pfe_datafeeds']['modes']['generate']['permissions'] = 'ab__pfe.manage';
$schema['ab__pfe_datafeeds']['modes']['reset_status']['permissions'] = 'ab__pfe.manage';
$schema['tools']['modes']['update_status']['param_permissions']['table']['ab__pfe_templates'] = 'ab__pfe.manage';
$schema['tools']['modes']['update_status']['param_permissions']['table']['ab__pfe_datafeeds'] = 'ab__pfe.manage';
return $schema;
