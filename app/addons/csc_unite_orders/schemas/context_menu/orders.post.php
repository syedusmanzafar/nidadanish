<?php
use Tygh\ContextMenu\Items\DividerItem;
use Tygh\ContextMenu\Items\ComponentItem;
use Tygh\ContextMenu\Items\GroupItem;

defined('BOOTSTRAP') or die('Access denied!');
$schema['items']['actions']['items']['cuo.unite_orders'] = 
	[
		'name'     => ['template' => 'cuo.unite_orders'],
        'dispatch' => 'cuo.unite',
        'position' => 50
	];
return $schema;
