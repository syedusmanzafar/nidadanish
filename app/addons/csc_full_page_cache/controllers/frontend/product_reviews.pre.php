<?php
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {	
	if ($mode == 'add' && !empty($_REQUEST['product_review_data']['product_id'])){
		$product_id = $_REQUEST['product_review_data']['product_id'];
	}	
    if ($mode == 'vote' && !empty($_REQUEST['product_review_id'])) {
		$product_id = db_get_field("SELECT product_id FROM ?:product_reviews WHERE product_review_id=?i", $_REQUEST['product_review_id']);
    }
	if (!empty($product_id)){
		list($category_ids, $company_id) = fn_cfpc_get_product_extra_data($product_id);			
		fn_csc_full_page_cache_delete_cache_by_category_id($category_ids, true);
		fn_csc_full_page_cache_delete_cache_by_company_id($company_id, true);						
		fn_csc_full_page_cache_delete_cache_by_product_id($product_id, false);				
	}
}