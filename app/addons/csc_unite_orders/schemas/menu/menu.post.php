<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			https://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/
if (!defined('BOOTSTRAP')) { die('Access denied'); }
$schema['top']['addons']['items']['csc_addons']['type']='title';
$schema['top']['addons']['items']['csc_addons']['href']='cuo.settings';
$schema['top']['addons']['items']['csc_addons']['position']='1000';
$schema['top']['addons']['items']['csc_addons']['title']=__("cuo.csc_addons");

$schema['top']['addons']['items']['csc_addons']['subitems']['csc_unite_orders'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'cuo.settings',	
    'position' => 800
);

return $schema;
