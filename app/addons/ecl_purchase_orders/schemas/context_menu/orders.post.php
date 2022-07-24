<?php


defined('BOOTSTRAP') or die('Access denied!');

$schema['items']['print']['items']['print'] = [

    'name'     => ['template' => 'print_purchase_order'],
    'dispatch' => 'orders.purchase_orders',
    'data'     => [
        'action_class' => 'cm-new-window',
    ],
    'position' => 30,
];

return $schema;