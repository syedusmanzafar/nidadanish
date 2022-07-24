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
    if ($product_id && !empty($product_data['price'])){ 
        $price = $product_data['price'];
        $commission_per = Registry::get('addons.ec_vendor_cost.default_commission');
        $ec_commission_per = db_get_field("SELECT ec_commission FROM ?:products WHERE product_id = ?i", $product_id);
        $company_id =db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i", $product_id);
        $category_id =db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = ?s", $product_id, 'M');
        $x = 0;
        if (!empty($ec_commission_per) && floatval($ec_commission_per)> 0){
            $x = $ec_commission_per;
        }
        $x += db_get_field("SELECT ec_commission FROM ?:companies WHERE company_id = ?i", $company_id);
        $x += db_get_field("SELECT ec_commission FROM ?:categories WHERE category_id = ?i", $category_id );
        $x += fn_ec_get_product_brand_markup($product_id);
        $commission_per = $x ? $x : $commission_per;
        $price = $price + ceil($price*$commission_per/100);
        if($price >= 500) {
            $price= ceil($price / 500) * 500;
        }else {
            $price= 500;
        }
        db_query("UPDATE ?:products SET ec_retail_price = ?d WHERE product_id = ?i", $price, $product_id);


        if (Registry::get('addons.ec_vendor_cost.wholesale_usergroup')){
            $price = $product_data['price'];
            $commission_per = Registry::get('addons.ec_vendor_cost.default_commission_wholesale');
            $ec_commission_wholesale_per = db_get_field("SELECT ec_commission_wholesale FROM ?:products WHERE product_id = ?i", $product_id);
            $x = 0;
            if (!empty($ec_commission_wholesale_per) && floatval($ec_commission_wholesale_per)> 0){
                $x = $ec_commission_wholesale_per;
            }
            $x += db_get_field("SELECT ec_commission_wholesale FROM ?:companies WHERE company_id = ?i", $company_id);
            $x += db_get_field("SELECT ec_commission_wholesale FROM ?:categories WHERE category_id = ?i", $category_id);
            $x += fn_ec_get_product_brand_markup($product_id, 'ec_commission_wholesale');
            $commission_per = $x ? $x : $commission_per;
            $price = $price + ceil($price*$commission_per/100);
            if($price >= 500) {
                $price= ceil($price / 500) * 500;
            }else {
                $price= 500;
            }
            $lower_limit = db_get_field("SELECT lower_limit FROM ?:product_prices WHERe product_id = ?i AND usergroup_id = ?i", $product_id, Registry::get('addons.ec_vendor_cost.wholesale_usergroup'));
            $_data = array(
                'product_id' => $product_id,
                'price' => $price,
                'lower_limit' => $lower_limit?$lower_limit:2,
                'usergroup_id' => Registry::get('addons.ec_vendor_cost.wholesale_usergroup')
            );
            db_query("REPLACE INTO ?:product_prices ?e", $_data);
            db_query("UPDATE ?:products SET ec_wholesale_price = ?d WHERE product_id = ?i", $price, $product_id);
        }
    }
}
/**
 * Hook Hane   $product_id Product identifier
 * @param int   $amount     Amount of products, required to get wholesale price
 * @param array $auth       Array of user authentication data (e.g. uid, usergroup_ids, etc.)
 * @param float $price
 */
function fn_ec_vendor_cost_get_product_price_post($product_id, $amount, $auth, &$price){
    if (isset($_SESSION['ec_calcualate_payout'])){
        return;
    }
    if (AREA == 'C' || defined("ORDER_MANAGEMENT")){
        if (in_array(Registry::get('addons.ec_vendor_cost.wholesale_usergroup'), $auth['usergroup_ids']) && $amount >= 2){
            $commission_per = Registry::get('addons.ec_vendor_cost.default_commission_wholesale');
            $ec_commission_wholesale_per = db_get_field("SELECT ec_commission_wholesale FROM ?:products WHERE product_id = ?i", $product_id);
            $company_id =db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i", $product_id);
            $category_id =db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = ?s", $product_id, 'M');
            $x = 0;
            if (!empty($ec_commission_wholesale_per) && floatval($ec_commission_wholesale_per)> 0){
                $x = $ec_commission_wholesale_per;
            }
            $x += db_get_field("SELECT ec_commission_wholesale FROM ?:companies WHERE company_id = ?i", $company_id);
            $x += db_get_field("SELECT ec_commission_wholesale FROM ?:categories WHERE category_id = ?i", $category_id );
            $x += fn_ec_get_product_brand_markup($product_id, 'ec_commission_wholesale');
            $commission_per = $x ? $x : $commission_per;
            $price = $price + ceil($price*$commission_per/100);
            if($price >= 500) {
                $price= ceil($price / 500) * 500;
            }else {
                $price= 500;
            }
        }else{
            $commission_per = Registry::get('addons.ec_vendor_cost.default_commission');
            $ec_commission_per = db_get_field("SELECT ec_commission FROM ?:products WHERE product_id = ?i", $product_id);
            $company_id =db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i", $product_id);
            $category_id =db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = ?s", $product_id, 'M');
            $x = 0;
            if (!empty($ec_commission_per) && floatval($ec_commission_per)> 0){
                $x = $ec_commission_per;
            }
            $x += db_get_field("SELECT ec_commission FROM ?:companies WHERE company_id = ?i", $company_id);
            $x += db_get_field("SELECT ec_commission FROM ?:categories WHERE category_id = ?i", $category_id );
            $x += fn_ec_get_product_brand_markup($product_id);
            $commission_per = $x ? $x : $commission_per;
            $price = $price + ceil($price*$commission_per/100);
            if($price >= 500) {
                $price= ceil($price / 500) * 500;
            }else {
                $price= 500;
            }
        }
    }

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
    if ((AREA == 'A' &&  !Registry::get('runtime.company_id')) ||  ((AREA == 'C' || defined("ORDER_MANAGEMENT")) && in_array(Registry::get('addons.ec_vendor_cost.wholesale_usergroup'), $auth['usergroup_ids']))){
        $price = &$product_data['price'];
        $list_price = $product_data['list_price'];
        $commission_per = Registry::get('addons.ec_vendor_cost.default_commission_wholesale');
        $x = 0;
        if (!empty($product_data['ec_commission_wholesale']) && floatval($product_data['ec_commission_wholesale'])>0){
            $x = $commission_per = $product_data['ec_commission_wholesale'];
        }
        $x += db_get_field("SELECT ec_commission_wholesale FROM ?:companies WHERE company_id = ?i", $product_data['company_id']);
        $x += db_get_field("SELECT ec_commission_wholesale FROM ?:categories WHERE category_id = ?i", $product_data['main_category']);
        $x += fn_ec_get_product_brand_markup($product_data['product_id'], 'ec_commission_wholesale');
        $commission_per = $x ? $x : $commission_per;
        $selling_price = $price + ceil($price*$commission_per/100);
        $list_price = $list_price + ceil($list_price*$commission_per/100);
        if($selling_price >= 500) {
            $selling_price= ceil($selling_price / 500) * 500;
        }else {
            $selling_price= 500;
        }
        if($list_price >= 500) {
            $list_price= ceil($list_price / 500) * 500;
        }else {
            $list_price= 500;
        }
        if ((AREA == 'C' || defined("ORDER_MANAGEMENT")) && in_array(Registry::get('addons.ec_vendor_cost.wholesale_usergroup'), $auth['usergroup_ids'])){
            $product_data['price'] = $selling_price;
            $product_data['base_price'] = $selling_price;
            $product_data['list_price'] = $list_price;
            return;
        }else{
            $product_data['ec_calculated_wholesale_price'] = $selling_price;
        }
        if (!empty($product_data['prices'])){
            foreach($product_data['prices'] as &$price_ltd){
                if ($price_ltd['usergroup_id'] == Registry::get('addons.ec_vendor_cost.wholesale_usergroup')){
                    $price_ltd['price'] = $selling_price;
                }
            }
        }
    }
    if (!Registry::get('runtime.company_id') || AREA == 'C' || defined("ORDER_MANAGEMENT")){
        $price = &$product_data['price'];
        $list_price = $product_data['list_price'];
        $commission_per = Registry::get('addons.ec_vendor_cost.default_commission');
        $x = 0;
        if (!empty($product_data['ec_commission']) && floatval($product_data['ec_commission'])>0){
            $x = $commission_per = $product_data['ec_commission'];
        }
        $x += db_get_field("SELECT ec_commission FROM ?:companies WHERE company_id = ?i", $product_data['company_id']);
        $x += db_get_field("SELECT ec_commission FROM ?:categories WHERE category_id = ?i", $product_data['main_category']);
        $x += fn_ec_get_product_brand_markup($product_data['product_id']);
        $commission_per = $x ? $x : $commission_per;
        $selling_price = $price + ceil($price*$commission_per/100);
        $list_price = $list_price + ceil($list_price*$commission_per/100);
        if($selling_price >= 500) {
            $selling_price= ceil($selling_price / 500) * 500;
        }else {
            $selling_price= 500;
        }
        if($list_price >= 500) {
            $list_price= ceil($list_price / 500) * 500;
        }else {
            $list_price= 500;
        }
        if (AREA == 'C' || defined("ORDER_MANAGEMENT")){
            $product_data['price'] = $selling_price;
            $product_data['base_price'] = $selling_price;
            $product_data['list_price'] = $list_price;
        }else
            $product_data['ec_calculated_price'] = $selling_price;
        
    }
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
    if (!Registry::get('runtime.company_id') || AREA == 'C'){
        foreach($products as &$product_data) {
            $product_id = $product_data['product_id'];
            $product_data['ec_price'] = $price = $product_data['price'];
            $list_price = isset($product_data['list_price'])?$product_data['list_price']:0;
            $commission_per = Registry::get('addons.ec_vendor_cost.default_commission');
            $ec_commission_per = db_get_field("SELECT ec_commission FROM ?:products WHERE product_id = ?i", $product_data['product_id']);
            $x = 0;
            if (!empty($ec_commission_per) && floatval($ec_commission_per)> 0){
                $x = $ec_commission_per;
            }
            if (!empty($product_data['company_id']))
                $x += db_get_field("SELECT ec_commission FROM ?:companies WHERE company_id = ?i", $product_data['company_id']);
            if (!empty($product_data['main_category']))
                $x += db_get_field("SELECT ec_commission FROM ?:categories WHERE category_id = ?i", $product_data['main_category']);
            $x += fn_ec_get_product_brand_markup($product_data['product_id']);
            $commission_per = $x ? $x : $commission_per;
            $selling_price = $price + ceil($price*$commission_per/100);
            if($selling_price >= 500) {
                $selling_price= ceil($selling_price / 500) * 500;
            }else {
                $selling_price= 500;
            }
            if ($list_price > 0){
                if($list_price >= 500) {
                    $list_price= ceil($list_price / 500) * 500;
                }else {
                    $list_price= 500;
                }
            }
            if (!in_array(Registry::get('addons.ec_vendor_cost.wholesale_usergroup'), $auth['usergroup_ids']))
            if (AREA == 'C' || defined("ORDER_MANAGEMENT") || Registry::get('runtime.mode') == 'get_products_list' || Registry::get('runtime.mode') == 'cart_list'){
                $product_data['price'] = $selling_price;
                $product_data['base_price'] = $selling_price;
                if ($list_price)
                    $product_data['list_price'] = $list_price;
            }else
                $product_data['ec_calculated_price'] = $selling_price;
            if (AREA == 'A')
                db_query("UPDATE ?:products SET ec_retail_price = ?d WHERE product_id = ?i", $selling_price, $product_id);
        }
    }

    if ((AREA == 'A' &&  !Registry::get('runtime.company_id')) || ((AREA == 'C' || defined("ORDER_MANAGEMENT") || Registry::get('runtime.mode') == 'get_products_list' || Registry::get('runtime.mode') == 'cart_list') && in_array(Registry::get('addons.ec_vendor_cost.wholesale_usergroup'), $auth['usergroup_ids']))){
        foreach($products as &$product_data) {
            $product_id = $product_data['product_id'];
            $price = isset($product_data['ec_price'])? $product_data['ec_price']:$product_data['price'];
            $list_price = isset($product_data['list_price'])?$product_data['list_price']:0;
            $commission_per = Registry::get('addons.ec_vendor_cost.default_commission_wholesale');
            $ec_commission_wholesale_per = db_get_field("SELECT ec_commission_wholesale FROM ?:products WHERE product_id = ?i", $product_data['product_id']);
            $x = 0;
            if (!empty($ec_commission_wholesale_per) && floatval($ec_commission_wholesale_per)> 0){
                $x = $ec_commission_wholesale_per;
            }
            if (!empty($product_data['company_id']))
                $x += db_get_field("SELECT ec_commission_wholesale FROM ?:companies WHERE company_id = ?i", $product_data['company_id']);
            if (!empty($product_data['main_category']))
                $x += db_get_field("SELECT ec_commission_wholesale FROM ?:categories WHERE category_id = ?i", $product_data['main_category']);
            $x += fn_ec_get_product_brand_markup($product_data['product_id'], 'ec_commission_wholesale');
            $commission_per = $x ? $x : $commission_per;
            $selling_price = $price + ceil($price*$commission_per/100);
            if($selling_price >= 500) {
                $selling_price= ceil($selling_price / 500) * 500;
            }else {
                $selling_price= 500;
            }
            if ($list_price > 0){
                if($list_price >= 500) {
                    $list_price= ceil($list_price / 500) * 500;
                }else {
                    $list_price= 500;
                }
            }
            if ((AREA == 'C' || defined("ORDER_MANAGEMENT") || Registry::get('runtime.mode') == 'get_products_list' || Registry::get('runtime.mode') == 'cart_list') && in_array(Registry::get('addons.ec_vendor_cost.wholesale_usergroup'), $auth['usergroup_ids'])){
                $product_data['price'] = $selling_price;
                $product_data['base_price'] = $selling_price;
                if ($list_price)
                    $product_data['list_price'] = $list_price;
            }else
                $product_data['ec_calculated_wholesale_price'] = $selling_price;

            if (AREA == 'A'){
                $lower_limit = db_get_field("SELECT lower_limit FROM ?:product_prices WHERe product_id = ?i AND usergroup_id = ?i", $product_id, Registry::get('addons.ec_vendor_cost.wholesale_usergroup'));
                $_data = array(
                    'product_id' => $product_id,
                    'price' => $selling_price,
                    'lower_limit' => $lower_limit?$lower_limit:2,
                    'usergroup_id' => Registry::get('addons.ec_vendor_cost.wholesale_usergroup')
                );
                db_query("REPLACE INTO ?:product_prices ?e", $_data);
                db_query("UPDATE ?:products SET ec_wholesale_price = ?d WHERE product_id = ?i", $selling_price, $product_id);
            }
        }
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
