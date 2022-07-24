<?php

use Tygh\Registry;

if($mode == 'manage') {
	list($list,$links) = fn_get_brand_categories();

	Tygh::$app['view']->assign(array(
        'list' => $list,
        'search' => $links
    ));

} elseif ($mode == 'update' || $mode == 'add') {
	$data = fn_get_brand_category_data($_REQUEST['id']);

	if($mode == 'update' && !$_REQUEST['id']) {
		// return array(CONTROLLER_STATUS_DENIED);
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// fn_print_die($_REQUEST);
		$id = fn_update_brand_category($_REQUEST);
		$id = ($id!=0) ? $id : $_REQUEST['brand_data']['id'];


		return array(CONTROLLER_STATUS_REDIRECT,'brand_categories.update&id=' . $id);
	}
	

	Tygh::$app['view']->assign('data',$data);

} elseif ($mode == 'delete') {

	fn_brand_category_delete($_REQUEST['id']);

	return array(CONTROLLER_STATUS_REDIRECT,'brand_categories.manage');

} elseif ($mode == 'm_delete') {

	fn_brand_category_delete($_REQUEST['item_ids']);

	return array(CONTROLLER_STATUS_REDIRECT,'brand_categories.manage');

}