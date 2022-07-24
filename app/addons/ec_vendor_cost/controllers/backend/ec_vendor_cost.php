<?php
/**
 * Ecarter Technologies Pvt. Ltd..;.;,;l[dfx.<˝¸¬≥l.fc]
 * Ecarter Technologies Pvt. Ltd..;.;,;l[dfx.<˝¸¬≥]
 *
 * This source file is part of a commercial software. Only users who have purchased a valid license through
 * https://store.ecarter.co and accepted to the terms of the License Agreement can install this product.
 *
 * @category   Add-ons
 * @package    Ecarter Technologies Pvt. Ltd.
 * @copyright  Copyright (c) 2020 Ecarter Technologies Pvt. Ltd.. (https://store.ecarter.co)
 * @license    https://ecarter.co/legal/license-agreement/   License Agreement
 * @version    $Id$
 */

use Tygh\Registry;
use Tygh\Enum\ObjectStatuses;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'update') {

		return array(CONTROLLER_STATUS_OK, 'ec_vendor_cost.manage');
	}
}

if ($mode == 'cron_job') {
	set_time_limit(0);
	
	$products_per_page = ITEMS_PER_PAGE;
    $page = 0;
	$params = $_REQUEST;
    // $params['only_short_fields'] = true;
    // $params['apply_disabled_filters'] = true;
    $params['extend'][] = 'companies';
	while ($params['pid'] = db_get_fields('SELECT product_id FROM ?:products WHERE  company_id > ?i ORDER BY product_id ASC ?p',0, db_paginate($page, $products_per_page))) {
        $page++;
        list($products) = fn_get_products($params, $products_per_page);
		fn_print_r(count($products));
	}
	die;
}