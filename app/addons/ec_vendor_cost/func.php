<?php
/**
 * Ecarter Technologies Pvt. Ltd.
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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_settings_variants_addons_ec_vendor_cost_wholesale_usergroup(){
    $user_groups = fn_get_simple_usergroups('C', false);
    return $user_groups;
}
/**
 * Update product data (running after fn_update_product() function)
 *
 * @param array   $product_data Product data
 * @param int     $product_id   Product integer identifier
 * @param string  $lang_code    Two-letter language code (e.g. 'en', 'ru', etc.)
 * @param boolean $create       Flag determines if product was created (true) or just updated (false).
 */
function fn_ec_vendor_cost_update_product_post($product_data, $product_id, $lang_code, $create){
    $product_data['product_id'] = $product_id;
    fn_ec_vendor_cost_get_custom_price_data($product_data, $auth = []);
}
/**
 * Hook Hane   $product_id Product identifier
 * @param int   $amount     Amount of products, required to get wholesale price
 * @param array $auth       Array of user authentication data (e.g. uid, usergroup_ids, etc.)
 * @param float $price
 */
function fn_ec_vendor_cost_get_product_price_post($product_id, $amount, $auth, &$price){
    // fn_print_r($price);

    if (!in_array(Registry::get('addons.ec_vendor_cost.wholesale_usergroup'), $auth['usergroup_ids']) || $amount < 2){
        $price = db_get_field("SELECT ec_retail_price FROM ?:products WHERE product_id = ?i", $product_id);
    }else{
        $price = db_get_field("SELECT ec_wholesale_price FROM ?:products WHERE product_id = ?i", $product_id);
    }
    // fn_print_r($price);

}

/**
 * Hook Handler: Particularize product data
 *
 * @param array   $product_data List with product fields
 * @param mixed   $auth         Array with authorization data
 * @param boolean $preview      Is product previewed by admin
 * @param string  $lang_code    2-letter language code (e.g. 'en', 'ru', etc.)
 */
function fn_ec_vendor_cost_get_product_data_post(&$product_data, $auth, $preview, $lang_code){
    $product_data = fn_ec_vendor_cost_get_custom_price_data($product_data, $auth);
}

/**
 * Changes additional params for selecting products
 *
 * @param array  $params    Product search params
 * @param array  $fields    List of fields for retrieving
 * @param array  $sortings  Sorting fields
 * @param string $condition String containing SQL-query condition possibly prepended with a logical operator (AND or OR)
 * @param string $join      String with the complete JOIN information (JOIN type, tables and fields) for an SQL-query
 * @param string $sorting   String containing the SQL-query ORDER BY clause
 * @param string $group_by  String containing the SQL-query GROUP BY field
 * @param string $lang_code Two-letter language code (e.g. 'en', 'ru', etc.)
 * @param array  $having    HAVING condition
 */
function fn_ec_vendor_cost_get_products($params, &$fields, $sortings, $condition, $join, $sorting, $group_by, $lang_code, $having){
    $fields['ec_wholesale_price'] = 'products.ec_wholesale_price';
    $fields['ec_retail_price'] = 'products.ec_retail_price';
    $fields['ec_list_price'] = 'products.ec_list_price';
}

/**
 * Hook Handler:  Changes selected products
 *
 * @param array  $products  Array of products
 * @param array  $params    Product search params
 * @param string $lang_code Language code
 */
function fn_ec_vendor_cost_get_products_post(&$products, $params, $lang_code){
    $auth = Tygh::$app['session']['customer_auth'];
    if ((defined("ORDER_MANAGEMENT") || Registry::get('runtime.mode') == 'get_products_list' || Registry::get('runtime.mode') == 'cart_list') && $auth){
        $auth = Tygh::$app['session']['customer_auth'];
    }else{
        $auth = Tygh::$app['session']['auth'];
    }
    foreach($products as &$product) {
        $product = fn_ec_vendor_cost_get_custom_price_data($product, $auth);
    }    
}

function fn_ec_get_product_brand_markup($product_id = 0, $key= 'ec_commission'){
    $feature_id = fn_ec_get_brand_feature();
    $commission_per = 0;
    if($feature_id){
        $variant_id = db_get_field("SELECT variant_id FROM ?:product_features_values WHERE product_id = ?i AND feature_id = ?i", $product_id, $feature_id);
        if ($variant_id){
            $commission_per = db_get_field("SELECT $key FROM ?:product_feature_variants WHERE variant_id = ?i AND feature_id = ?i", $variant_id, $feature_id);
        }
    }
    return $commission_per;
}
function fn_ec_get_brand_feature(){
    static $feature_id = null;
    if(!$feature_id){
        $feature_id = db_get_field("SELECT feature_id FROM ?:product_features WHERE feature_style = ?s AND feature_type = ?s", 'brand', 'E');
    } 
    return $feature_id;
}

function fn_ec_vendor_cost_vendor_plans_calculate_commission_for_payout_post($order_info, $company_data, &$payout_data) {
    if (AREA == "C" || empty($order_info['order_id'])){
        if ($payout_data['order_amount'] > 0){
            $commission = 0;
            $_SESSION['ec_calcualate_payout'] = true;
            foreach($order_info['products'] as $product_data) {
                $price = fn_get_product_price($product_data['product_id'], $product_data['amount'], $_SESSION['auth']);
                $commission +=  (($product_data['price']-$price)*$product_data['amount']);
            }
            unset($_SESSION['ec_calcualate_payout']);
            $payout_data['commission_amount'] += $commission; 
            $payout_data['commission'] += $commission; 
            $payout_data['extra']['commission_amount'] = $payout_data['commission_amount'];
            $payout_data['extra']['commission'] = $payout_data['commission'];
            $payout_data['extra']['fixed_commission'] += $commission;
            $payout_data['marketplace_profit'] += $commission;
            $payout_data['extra']['percent_commission'] = ceil(($payout_data['marketplace_profit']/$order_info['total'])*100);
        }
    }
}

function fn_ec_vendor_cost_get_custom_price_product($_product_data) {
    $product_id = $_product_data['product_id'];
    $product_data = db_get_row("SELECT list_price, company_id, ec_commission_wholesale,ec_commission, product_id  FROM ?:products WHERE product_id = ?i", $product_id);
    $product_data['main_category'] = db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = ?s", $product_id, 'M');
    $product_data['price'] = !isset($_product_data['price'])?$_product_data['price']:db_get_field("SELECT price FROM ?:product_prices WHERE product_id = ?i AND lower_limit = ?i", $product_id, 1);
    $product_data = array_merge($_product_data, $product_data);
    $ec_commission_wholesale_per = $product_data['ec_commission_wholesale'];
    $price = $product_data['price'];
    $list_price = $product_data['list_price'];
    $ec_commission_per = $product_data['ec_commission'];
    $commission_per = Registry::get('addons.ec_vendor_cost.default_commission');
    $x = 0;
    if (!empty($ec_commission_per) && floatval($ec_commission_per)> 0.001){
        $x = $commission_per = $product_data['ec_commission'];
    }
    $x += db_get_field("SELECT ec_commission FROM ?:companies WHERE company_id = ?i", $product_data['company_id']);
    $x += db_get_field("SELECT ec_commission FROM ?:categories WHERE category_id = ?i", $product_data['main_category']);
    $x += fn_ec_get_product_brand_markup($product_data['product_id']);
    $commission_per = $x>0.1 ? $x : $commission_per;
    $retail_price = $price + ceil($price*$commission_per/100);
    $list_price = $list_price + ceil($list_price*$commission_per/100);
    if($retail_price >= 500) {
        $retail_price= ceil($retail_price / 500) * 500;
    }
    if($list_price >= 500) {
        $list_price= ceil($list_price / 500) * 500;
    }

    $commission_wholesale_per = Registry::get('addons.ec_vendor_cost.default_commission_wholesale');
    $ec_commission_wholesale_per = db_get_field("SELECT ec_commission_wholesale FROM ?:products WHERE product_id = ?i", $product_id);
    $y = 0;
    if (!empty($ec_commission_wholesale_per) && floatval($ec_commission_wholesale_per)> 0.01){
        $y = $ec_commission_wholesale_per;
    }
    $y += db_get_field("SELECT ec_commission_wholesale FROM ?:companies WHERE company_id = ?i", $product_data['company_id']);
    $y += db_get_field("SELECT ec_commission_wholesale FROM ?:categories WHERE category_id = ?i", $product_data['main_category']);
    $y += fn_ec_get_product_brand_markup($product_id, 'ec_commission_wholesale');
    $commission_wholesale_per = $y>0.1 ? $y : $commission_wholesale_per;
    $wholesale_price = $price + ceil($price*$commission_wholesale_per/100);
    if($wholesale_price >= 500) {
        $wholesale_price= ceil($wholesale_price / 500) * 500;
    }
    return array($retail_price, $wholesale_price, $list_price);
}

function fn_ec_vendor_cost_get_custom_price_data($product_data, $auth){
    if (AREA == 'C' || defined("ORDER_MANAGEMENT") || Registry::get('runtime.mode') == 'get_products_list' || Registry::get('runtime.mode') == 'cart_list' ||  Registry::get('runtime.mode') == 'build_queue'){
        if (!empty($product_data['ec_retail_price']))
            $product_data['base_price'] = $product_data['price'] = $product_data['ec_retail_price'];
        if ((!empty($product_data['ec_wholesale_price']) && in_array(Registry::get('addons.ec_vendor_cost.wholesale_usergroup'), $auth['usergroup_ids']))){
            $product_data['base_price'] =  $product_data['price'] = $product_data['ec_wholesale_price'];
        }
        // if (!empty($product_data['ec_list_price']))
        //     $product_data['list_price'] = $product_data['ec_list_price'];
    }elseif (AREA == 'A') {
        list($retail_price, $wholesale_price, $list_price) = fn_ec_vendor_cost_get_custom_price_product($product_data);
        if ($retail_price > 0 && $wholesale_price > 0){
            $product_id = $product_data['product_id'];
            $lower_limit = db_get_field("SELECT lower_limit FROM ?:product_prices WHERe product_id = ?i AND usergroup_id = ?i", $product_id, Registry::get('addons.ec_vendor_cost.wholesale_usergroup'));
            $_data = array(
                'product_id' => $product_id,
                'price' => $wholesale_price,
                'lower_limit' => $lower_limit>=2?$lower_limit:2,
                'usergroup_id' => Registry::get('addons.ec_vendor_cost.wholesale_usergroup')
            );
            if (isset($product_data['ec_wholesale_price']) && $wholesale_price != $product_data['ec_wholesale_price'])
                db_query("REPLACE INTO ?:product_prices ?e", $_data);
            if (isset($product_data['ec_wholesale_price']) && isset($product_data['ec_retail_price']) && ($wholesale_price != $product_data['ec_wholesale_price'] || $retail_price != $product_data['ec_retail_price']))
                db_query("UPDATE ?:products SET ec_wholesale_price = ?d, ec_retail_price = ?d, ec_list_price = ?d WHERE product_id = ?i", $wholesale_price, $retail_price, $list_price, $product_id);
            $product_data['ec_calculated_wholesale_price'] = $wholesale_price;
            $product_data['ec_calculated_price'] = $retail_price; 
        }
    }
    if (!empty($product_data['prices']) && !empty($product_data['ec_wholesale_price'])){
        foreach($product_data['prices'] as &$price_ltd){
            if ($price_ltd['usergroup_id'] == Registry::get('addons.ec_vendor_cost.wholesale_usergroup')){
                $price_ltd['price'] = $product_data['ec_wholesale_price'];
            }
        }
    }
    return $product_data;
}