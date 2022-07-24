<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\ContextMenu\Items\GroupItem;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied!');

$controller = Registry::get('runtime.controller');
$mode = Registry::get('runtime.mode');

/** @var array $schema */
// Add-on vendor_data_premoderation
unset($schema['items']['vendor_data_premoderation.product_approval']);

if (
    $controller !== 'products'
    || $mode !== 'master_products'
) {
    return $schema;
}

if (Registry::get('runtime.company_id')) {
    unset($schema['items']);
    $schema['items']['actions'] = [
        'name'     => ['template' => 'actions'],
        'type'     => GroupItem::class,
        'items'    => [
            'sell'           => [
                'name'     => ['template' => 'master_products.sell_selected'],
                'dispatch' => 'products.m_sell_master_product',
                'form'     => 'manage_products_form',
                'position' => 10,
            ],
            'export'           => [
                'name'     => ['template' => 'export_selected'],
                'dispatch' => 'products.export_range.master',
                'form'     => 'manage_products_form',
                'position' => 20,
            ],
        ],
        'position' => 10,
    ];
} else {
    $schema['items']['price']['name'] = ['template' => 'price'];
    $schema['items']['actions']['items']['export']['dispatch'] = 'products.export_range.master';
}

return $schema;
