<?php
/*****************************************************************************
*                                                        © 2013 Cart-Power   *
*           __   ______           __        ____                             *
*          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
*      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
*     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
*    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
*                                                                            *
*                                                                            *
* -------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license *
* and  accept to the terms of the License Agreement can install and use this *
* program.                                                                   *
* -------------------------------------------------------------------------- *
* website: https://store.cart-power.com                                      *
* email:   sales@cart-power.com                                              *
******************************************************************************/

use Tygh\Registry;
use Tygh\Navigation\LastView;
use Tygh\Settings;
use Tygh\Http;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

define('CP_SEARCH_CACHE_TABLE', Registry::get('config.table_prefix') . 'cp_search_cache');
define('CP_SEARCH_INDEX_TABLE', Registry::get('config.table_prefix') . 'cp_search_index');

//
// Hooks
//

function fn_cp_live_search_products_sorting(&$sorting, $simple_mode)
{
    if (AREA != 'C') {
        return;
    }
    $sorting['cp_search_weight'] = array(
        'description' => __('sort_by_cp_search_weight_desc'),
        'default_order' => 'desc',
        'asc' => false
    );
    $sorting['cp_category_group'] = array(
        'description' => __('sort_by_cp_category_group_asc'),
        'default_order' => 'asc'
    );
}

function fn_cp_live_search_get_products_pre(&$params, $items_per_page, $lang_code)
{
    if (AREA != 'C') {
        return;
    }
    
    if (!empty($params['q']) && fn_string_not_empty($params['q'])
        || !empty($params['search_q']) && fn_string_not_empty($params['search_q'])
    ) {
        if (!empty($params['search_q'])) {
            $params['q'] = '';
        }

        $settings = Registry::get('addons.cp_live_search');
        
        $use_boost = !empty($settings['use_boost']) && $settings['use_boost'] == 'Y' ? true : false;
        $default_params = array (
            'match' => $settings['search_by_exact_phrase'],
            'pname' => 'Y',
            'pcode_from_q' => $settings['search_by_product_code'],
            'search_by_product_code' => $settings['search_by_product_code'],            
            'pshort' => $use_boost ? 'N' : $settings['search_in_short_description'],
            'pfull' => $use_boost ? 'N' : $settings['search_in_full_description'],
            'pkeywords' => $use_boost ? 'N' : $settings['search_in_keywords'],
            'search_in_features' => $use_boost ? 'N' : $settings['searchinfeatures'],
            'search_in_options' => $use_boost ? 'N' : $settings['searchinoptions'],
            'search_by_categories' => $use_boost ? 'N' : $settings['search_by_categories'],
            'search_by_vendors' => $use_boost ? 'N' : $settings['search_by_vendors'],
            'name_without_symbols' => !empty($settings['ignore_symbols']),
            'use_boost' => $use_boost,
            'page' => isset($_REQUEST['page']) ? $_REQUEST['page'] : 1,
            'lang_code' => $lang_code
        );

        // override standart search params
        $params = array_merge($params, $default_params);
        if ($settings['use_cache'] == 'Y') { // for dont join descr1
            if (empty($params['custom_extend']) || in_array('description', $params['custom_extend'])
                || in_array('product_name', $params['custom_extend'])
            ) {
                $params['custom_extend'] = array('prices', 'categories');
                if (!empty($params['sort_by']) && $params['sort_by'] == 'popularity') {
                    $params['custom_extend'][] = 'popularity';
                }
            }
        } elseif (!empty($params['cp_live_search'])) {
            $sort_by = !empty($params['sort_by']) ? $params['sort_by'] : '';
            if (empty($sort_by)) {
                $default_sorting = fn_get_default_products_sorting();
                $sort_by = $default_sorting['sort_by'];
            }
            $params['extend'] = !empty($params['extend']) ? $params['extend'] : array();
            if ($sort_by == 'popularity' && !in_array('popularity', $params['extend'])) {
                $params['extend'][] = 'popularity';
            }
        }

        // Separate common products by vendors
        if (Registry::get('addons.master_products.status') == 'A'
            && (!empty($settings['separate_common_products']) && $settings['separate_common_products'] == 'Y'
                || !empty($params['company_id']))
        ) {
            $params['separate_common_products'] = true;
        } 
    }
}

function fn_cp_live_search_additional_fields_in_search($params, $fields, $sortings, $condition, $join, $sorting, $group_by, &$tmp, $piece, $having)
{
    if (AREA != 'C') {
        return;
    }

    // Search by categories for no-cache search
    if (!empty($params['q']) && fn_string_not_empty($params['q'])) {
        $q = fn_cp_ls_prepare_phrase($params['q']);

        if (!empty($params['search_by_categories'])
            && ($params['search_by_categories'] == 'in_categories' || $params['search_by_categories'] == 'in_categories_paths')
        ) {
            $cids = db_get_fields(
                'SELECT category_id FROM ?:category_descriptions WHERE category LIKE ?l AND lang_code = ?s',
                "%$q%", $params['lang_code']
            );
            if (!empty($cids)) {
                $categories = array();
                foreach($cids as $cid) {
                    $categories[] = $cid;
                    if ($params['search_by_categories'] == 'in_categories_paths') {
                        $_ids = db_get_fields(
                            'SELECT a.category_id FROM ?:categories as a LEFT JOIN ?:categories as b ON b.category_id IN (?n)'
                            . ' WHERE a.id_path LIKE CONCAT(b.id_path, "/%")', $cids
                        );
                        $categories = fn_array_merge($categories, $_ids, false);
                    }
                }
                
                if (!empty($categories)) {   
                    $tmp .= db_quote(' OR products_categories.category_id IN (?n)', $categories);
                }
            }
        }
        if (fn_allowed_for('MULTIVENDOR') && $params['search_by_vendors'] == 'Y') {
            $tmp .= db_quote(' OR companies.company LIKE ?l', "%$q%");
        }

        fn_cp_ls_prepare_condition_by_synonym($piece, $tmp);
    }
}

function fn_cp_live_search_get_products(&$params, &$fields, &$sortings, &$condition, &$join, &$sorting, &$group_by, $lang_code, $having) 
{
    if (AREA != 'C') {
        return;
    }

    $company_id = Registry::get('runtime.company_id');
    
    if (isset($params['search_q']) && fn_string_not_empty($params['search_q'])) { // using cache
        
        $fields['product'] = 'descr1.product as product';
        if (!empty($params['cp_simple_search'])) {
            $fields['short_description'] = 'descr1.short_description';
            $fields['full_description'] = 'descr1.full_description';
        }
        
        $sortings['cp_search_weight'] = 'cp_search_weight';
        
        $params['q'] = $params['search_q'];
        $phrase = fn_cp_ls_prepare_phrase($params['search_q']);

        $table_name = fn_cp_get_search_cache_table($company_id, $lang_code);
        if (empty($table_name)) { // Cache table not exists
            return;
        }

        // Because of the cs-cart bug, need to change the name of product_id, so it does not connect directly
        $join .= db_quote(' INNER JOIN (SELECT *, product_id as prod_id FROM ?p) as descr1 ON products.product_id = descr1.prod_id', $table_name);
        
        // Order by weight 
        $weight = '';
        if ($params['sort_by'] == 'cp_search_weight') {
            $q = $phrase;
            if (!empty($q)) {
                $weight_array = array();
                $rules = db_get_array('SELECT * FROM ?:cp_search_weight_rules');
                if (!empty($rules)) {
                    foreach($rules as $rule) {
                        $rule_field = 'descr1.' . $rule['field'];
                        $weight_array[] = db_quote(
                            "IF($rule_field LIKE ?l, ?i, IF($rule_field LIKE ?l, ?i, IF($rule_field LIKE ?l, ?i, IF($rule_field LIKE ?l, ?i, 0))))",
                            $q, $rule['none'], "$q%", $rule['before'], "%$q", $rule['after'], "%$q%", $rule['any']
                        );
                    }
                }
                if (!empty($weight_array)) { 
                    $fields[] = implode(' + ', $weight_array) . ' AS cp_search_weight';
                }
            }
        }
                
        if ($params['match'] == 'any') {
            $pieces = fn_explode(' ', $phrase);
            $search_type = ' OR ';
        } elseif ($params['match'] == 'all') {
            $pieces = fn_explode(' ', $phrase);
            $search_type = ' AND ';
        } elseif ($params['match'] == 'strict_all') {
            $pieces = fn_explode(' ', $phrase);
            $search_type = ' OR ';
        } else {
            $pieces = array($phrase);
            $search_type = '';
        }
        
        array_walk($pieces, 'fn_cp_ls_prepare_phrase');
        $pieces = array_filter($pieces, function ($piece) {
            return !empty($piece);
        });
        
        $params['search_by_vendors'] = (fn_allowed_for('MULTIVENDOR') && $params['search_by_vendors'] == 'Y') ? 'Y' : 'N';
        
        // Conditions
        $search_fields = array('descr1.search_words');
        $extra_fields = array(
            'pname' => array('descr1.product'),
            'pshort' => array('descr1.short_description'),
            'pfull' => array('descr1.full_description'),
            'pkeywords' => array('descr1.meta_keywords', 'descr1.meta_description'),
            'search_in_features' => array('descr1.features'),
            'search_in_options' => array('descr1.options'),
            'search_by_product_code' => array('descr1.product_code', 'descr1.product_code_combinations'),
            'name_without_symbols' => array('descr1.name_without_symbols'),
            'search_by_vendors' => array('companies.company')
        );
        
        if ($params['match'] == 'strict_all') {
            foreach ($search_fields as $s_field) {
                $tmp = array();
                foreach ($pieces as $piece) {
                    $tmp[] = db_quote('?p LIKE ?l', $s_field, "%$piece%");
                }
                $tmp_str = implode(' AND ', $tmp);
                $_condition[] = '(' . $tmp_str . ')';
            }
            foreach ($extra_fields as $e_param => $e_fields) {
                if (empty($params[$e_param]) || $params[$e_param] != 'Y') {
                    continue;
                }
                foreach ($e_fields as $e_field) {
                    $tmp = array();
                    foreach ($pieces as $piece) {
                        $tmp_str = db_quote('?p LIKE ?l', $e_field, "%$piece%");
                        fn_cp_ls_prepare_condition_by_synonym($piece, $tmp_str);
                        $tmp[] = $tmp_str;
                    }
                    $_condition[] = '(' . implode(' AND ', $tmp) . ')';
                }
            }
        } else {
            foreach ($pieces as $piece) {
                $tmp = array();
                foreach ($search_fields as $s_field) {
                    $tmp[] = db_quote('?p LIKE ?l', $s_field, "%$piece%");
                }
                foreach ($extra_fields as $e_param => $e_fields) {
                    if (empty($params[$e_param]) || $params[$e_param] != 'Y') {
                        continue;
                    }
                    foreach ($e_fields as $e_field) {
                        $tmp[] = db_quote('?p LIKE ?l', $e_field, "%$piece%");
                    }
                }
                $tmp_str = implode(' OR ', $tmp);
                fn_cp_ls_prepare_condition_by_synonym($piece, $tmp_str);
                
                $_condition[] = '(' . $tmp_str . ')';
            }
        }
        $_cond = implode($search_type, $_condition);

        // Search by categories
        if (!empty($params['search_by_categories'])
            && ($params['search_by_categories'] == 'in_categories' || $params['search_by_categories'] == 'in_categories_paths')
        ) {
            $cids = db_get_fields(
                'SELECT category_id FROM ?:category_descriptions WHERE category LIKE ?l AND lang_code = ?s',
                '%' . $phrase . '%', $lang_code
            );
            
            if (!empty($cids)) {
                $categories = array();
                foreach($cids as $cid) {
                    $categories[] = $cid;
                    if ($params['search_by_categories'] == 'in_categories_paths') {
                        $_ids = db_get_fields(
                            'SELECT a.category_id FROM ?:categories as a LEFT JOIN ?:categories as b ON b.category_id IN (?n)'
                            . ' WHERE a.id_path LIKE CONCAT(b.id_path, "/%")', $cids
                        );
                        $categories = fn_array_merge($categories, $_ids, false);
                    }
                }
                
                if (!empty($categories)) {   
                    $_cond .= db_quote(' OR ?:products_categories.category_id IN (?n)', $categories);
                    $join .= db_quote(' LEFT JOIN ?:products_categories ON ?:products_categories.product_id = descr1.product_id');
                }
            }
        }

        // Search phrase products
        if (!empty($_cond)) {
            $condition .= db_quote(' AND (?p)', $_cond);
        } 
        
        $condition .= db_quote(' AND NOT FIND_IN_SET(?s, descr1.stop_words)', $phrase);
        
    } elseif (!empty($params['q'])) {
    
        $phrase = fn_cp_ls_prepare_phrase($params['q']);
        
        if (!empty($params['extend']) && !empty($phrase)
            && (in_array('product_name', $params['extend']) || in_array('description', $params['extend']))
        ) {
            $condition .= db_quote(' AND NOT FIND_IN_SET(?s, descr1.stop_words)', $phrase);
        }
    }

    if (!empty($params['search_q']) || !empty($params['q'])) {
        $q = !empty($params['search_q']) ? $params['search_q'] : $params['q'];

        if (!empty($params['cp_live_search'])
            && in_array('categories', $params['extend'])
            && Registry::get('addons.cp_live_search.show_product_category') == 'group'
            && !empty($sortings[$params['sort_by']]) && !is_array($sortings[$params['sort_by']])
        ) {
            $sortings['cp_category_group'] = array('?:categories.category_id', $sortings[$params['sort_by']]);
            $params['sort_by'] = 'cp_category_group';
            if (!empty($params['sort_order']) && $params['sort_order'] != 'asc') {
                $params['sort_order'] = 'asc' . $params['sort_order'];
            }
        }
        
        if (!empty($params['use_boost'])) {
            $index_table = fn_cp_get_search_index_table($company_id, $lang_code);
            if (!empty($index_table)) { // Cache table not exists
                $search_indexes = fn_cp_ls_parse_for_index(array($q));
                if (!empty($search_indexes)) {
                    $index_products = db_get_fields('SELECT product_ids FROM ?p WHERE id IN (?a)', $index_table, $search_indexes);
                    $find_product_ids = array();
                    foreach ($index_products as $product_ids) {
                        $product_ids = !empty($product_ids) ? explode(',', $product_ids) : array();
                        $find_product_ids = array_merge($find_product_ids, $product_ids);
                    }
                    $condition .= db_quote(' AND products.product_id IN (?n)', array_unique($find_product_ids));
                }
            }
        }

        // Separate common products by vendors
        if (!empty($params['separate_common_products']) && strpos($condition, 'products.master_product_id = 0') !== false) {
            $condition = str_replace(
                'AND products.master_product_id = 0',
                'AND (products.master_product_offers_count = 0 OR products.master_product_id != 0)',
                $condition
            );
        }
    }
}

function fn_cp_live_search_delete_product_post($product_id, $product_deleted) 
{
    if ($product_deleted == true) {
        $tables = fn_cp_get_all_search_cache_tables();
        foreach ($tables as $table) {
            $exists = db_get_field('SELECT product_id FROM ?p WHERE product_id = ?i', $table, $product_id);
            if (!empty($exists)) {
                db_query('DELETE FROM ?p WHERE product_id = ?i', $table, $product_id);
            }
        }
    }
    return true;
}

function fn_cp_live_search_update_product_post($product_data, $product_id, $lang_code, $create) 
{
    if (!empty($product_data['skip_caching'])) {
        return true;
    }

    $is_ult = (fn_allowed_for('MULTIVENDOR') != true && count(fn_get_all_companies_ids()) > 1) ? true : false;

    $company_ids = array();
    if (!fn_allowed_for('MULTIVENDOR')) {
        if ($is_ult) {
            $company_ids = db_get_fields(
                'SELECT company_id FROM ?:ult_product_descriptions WHERE product_id = ?i AND lang_code = ?s',
                $product_id, $lang_code
            );
        }
        if (empty($company_ids)) {
            $company_ids = db_get_fields('SELECT company_id FROM ?:products WHERE product_id = ?i', $product_id);
        }
    } else {
        $company_ids = array(0);
    }
    if ($create == true) {
        $lang_codes = array_keys(fn_get_translation_languages());
        foreach ($company_ids as $company_id) {
            foreach ($lang_codes as $lang) {
                fn_cp_update_search_cache_product($product_id, $company_id, $lang, '', $is_ult);  
                fn_cp_ls_update_products_indexes(array($product_id), $company_id, $lang, '', $is_ult);  
            }
        }
    } else {
        foreach ($company_ids as $company_id) {
            fn_cp_update_search_cache_product($product_id, $company_id, $lang_code, '', $is_ult);
            fn_cp_ls_update_products_indexes(array($product_id), $company_id, $lang_code, '', $is_ult);  
        }
    }
    
    return true;
}

function fn_cp_live_search_get_product_feature_data_before_select(&$fields, $join, $condition, $feature_id, $get_variants, $get_variant_images, $lang_code)
{
    $fields[] = '?:product_features.cp_ls_use';
}

function fn_cp_live_search_dispatch_before_display()
{
    if (AREA != 'C') {
        return;
    }
    // Styles settings
    $styles = fn_cp_live_search_get_style_settings();
    Registry::get('view')->assign('cp_ls_styles', $styles);

    // Search motivation
    $dispatch = Registry::get('runtime.controller') . '.' . Registry::get('runtime.mode');
    
    $settings = Registry::get('addons.cp_live_search');

    if (!empty($settings['use_sm']) && $settings['use_sm'] == 'Y') {

        $check_display = array(
            'index.index' => 'sm_home',
            'checkout.cart' => 'sm_cart',
            'checkout.checkout' => 'sm_checkout',
            'profiles.update' => 'sm_profile',
            'profiles.add' => 'sm_profile',
            'pages.view' => 'sm_pages',
            'categories.view' => 'sm_categories',
            'products.view' => 'sm_products'
        );

        // Dont use search motivation if page disabled in settings
        if (isset($check_display[$dispatch]) && !array_key_exists($check_display[$dispatch], $settings['show_sm'])) {
            return;
        }

        $company_id = Registry::get('runtime.company_id');

        $search_motivation = '';
        if ($dispatch == 'categories.view') {
            $category = Registry::get('view')->getTemplateVars('category_data');
            if (!empty($category['cp_search_motivation'])) {
                $search_motivation = $category['cp_search_motivation'];
            }
        }

        if (empty($search_motivation)) {
            $storefront_id = 0;
            if (fn_allowed_for('MULTIVENDOR:ULTIMATE')) {
                $storefront_id = Tygh::$app['storefront']->storefront_id;
            }
            $search_motivation = fn_cp_live_search_get_search_motivation('D', $storefront_id, $company_id, CART_LANGUAGE);
        }

        if (!empty($search_motivation)) {
            $search_motivation = explode("\n", $search_motivation);
            $search_motivation = array_merge(array(''), $search_motivation);
            Registry::get('view')->assign('cp_search_motivation', json_encode($search_motivation));
        }
    }    
}

function fn_cp_live_search_update_category_post($category_data, $category_id, $lang_code)
{
    if (isset($category_data['cp_search_motivation'])) {
        $data = array(
            'object_type' => 'C',
            'object_id' => $category_id,
            'company_id' => !empty($category_data['company_id']) ? $category_data['company_id'] : 0,
            'lang_code' => $lang_code,
            'content' => $category_data['cp_search_motivation']
        );
        fn_cp_live_search_update_search_motivation($data);
    }
}

function fn_cp_live_search_get_category_data_post($category_id, $field_list, $get_main_pair, $skip_company_condition, $lang_code, &$category_data)
{
    $company_id = !empty($category_data['company_id']) ? $category_data['company_id'] : 0;
    $category_data['cp_search_motivation'] = fn_cp_live_search_get_search_motivation('C', $category_id, $company_id, $lang_code);
}

function fn_cp_live_search_get_companies($params, $fields, $sortings, &$condition, $join, $auth, $lang_code, $group)
{
    if (!empty($params['cp_search_q']) && fn_string_not_empty($params['cp_search_q'])) {
        $q = fn_cp_ls_prepare_phrase($params['cp_search_q']);
        $condition .= db_quote(' AND ?:companies.company LIKE ?l', "%$q%");
    }
}

//
// Main functions
//

function fn_cp_rebuild_search_cache($company_ids, $lang_codes, $set_notification = false)
{ 
    $count = 0;
    $is_ult = (fn_allowed_for('MULTIVENDOR') != true && count(fn_get_all_companies_ids()) > 1) ? true : false;
    if (fn_allowed_for('MULTIVENDOR')) {
        $company_ids = array(0);
    }
    
    $sort_param = array();
    $tables = array();
    foreach ($company_ids as $company_id) {
        foreach ($lang_codes as $lang_code) {
            $m = $company_id . '-' . $lang_code;
            
            list($c_join, $c_condition) = fn_cp_get_cache_products_query_params($company_id, $lang_code, $is_ult);
            
            $products_count = db_get_field(
                'SELECT COUNT(?:products.product_id) FROM ?:products ?p WHERE 1 ?p', $c_join, $c_condition
            );
            
            $table_name = fn_cp_get_search_cache_table($company_id, $lang_code);
            if (empty($table_name)) {
                $table_name = fn_cp_generate_cache_table_name($company_id, $lang_code);
                fn_cp_create_cache_table_name($table_name);
            }
            
            $cached_count = db_get_field('SELECT COUNT(product_id) FROM ?p', $table_name);
            
            $tables[$m] = array(
                'company_id' => $company_id,
                'lang_code' => $lang_code,
                'products_count' => $products_count,
                'cached_count' => $cached_count,
                'table_name' => $table_name
            );
            
            $sort_param[$m] = $products_count - $cached_count;
            
            $count += $products_count;
        }
    }
    
    arsort($sort_param);
    
    if (!empty($set_notification)) {   
        fn_set_progress('step_scale', $count);
    }
    
    $k = 1;
    foreach ($sort_param as $m => $not_cached_count) {
        $company_id = $tables[$m]['company_id'];
        $lang_code = $tables[$m]['lang_code'];
        $table_name = $tables[$m]['table_name'];
        
        list($c_join, $c_condition) = fn_cp_get_cache_products_query_params($company_id, $lang_code, $is_ult);

        $exists_product_ids = db_get_fields('SELECT DISTINCT(product_id) FROM ?p ORDER BY cache_timestamp ASC', $table_name);
        if (!empty($exists_product_ids)) {
            $c_condition .= db_quote(' AND ?:products.product_id NOT IN (?n)', $exists_product_ids);
        }

        $product_ids = db_get_fields('SELECT DISTINCT(?:products.product_id) FROM ?:products ?p WHERE 1 ?p', $c_join, $c_condition);
        
        foreach ($product_ids as $product_id) {
            fn_cp_update_search_cache_product($product_id, $company_id, $lang_code, $table_name, $is_ult);
            
            if (!empty($set_notification)) {
                fn_set_progress('echo', 'Product ' . $product_id . '-' . $m . ' is cached (total: ' . $k . ')<br/>');
            } else {
                fn_echo('Processed: ' . $product_id . ' '  . $m . '<br/>');
            }
            $k++;
        }

        if (!empty($exists_product_ids)) {
            foreach ($exists_product_ids as $product_id) {
                fn_cp_update_search_cache_product($product_id, $company_id, $lang_code, $table_name, $is_ult);
                
                if (!empty($set_notification)) {
                    fn_set_progress('echo', 'Product ' . $product_id . '-' . $m . ' is cached (total: ' . $k . ')<br/>');
                } else {
                    fn_echo('Processed: ' . $product_id . ' '  . $m . '<br/>');
                }
                $k++;
            }
        }

        fn_cp_modify_search_cache_table('OPTIMIZE', $table_name);
    }
    
    if (!empty($set_notification)) {
        fn_set_notification('N', __('notice'), __('done'));
    }

    return true;
}

function fn_cp_get_cache_products_query_params($company_id, $lang_code, $is_ult = false)
{
    $c_join = $c_condition = '';

    $c_join .= db_quote(' LEFT JOIN ?:product_descriptions as descr ON ?:products.product_id = descr.product_id AND descr.lang_code = ?s', $lang_code);
    $c_join .= db_quote(' LEFT JOIN ?:languages ON ?:languages.lang_code = descr.lang_code');
    $c_join .= db_quote(' LEFT JOIN ?:products_categories ON ?:products_categories.product_id = ?:products.product_id');
    $c_join .= db_quote(' LEFT JOIN ?:categories ON ?:products_categories.category_id = ?:categories.category_id');
    
    $c_condition .= db_quote(
        ' AND ?:products.status = ?s AND ?:languages.status = ?s AND ?:categories.status = ?s', 'A', 'A', 'A'
    );

    if ($is_ult) {
        $c_join .= db_quote(
            ' LEFT JOIN ?:ult_product_descriptions as ult_descr ON ?:products.product_id = ult_descr.product_id AND ult_descr.lang_code = ?s', $lang_code
        );
        $c_condition .= db_quote(' AND (?:products.company_id = ?i OR ult_descr.company_id = ?i)', $company_id, $company_id);
    } else {
        if (!fn_allowed_for('MULTIVENDOR')) {
            $c_condition .= db_quote(' AND ?:products.company_id = ?i', $company_id);
        }
    }

    return array($c_join, $c_condition);
}

function fn_cp_update_search_cache_product($product_id, $company_id, $lang_code, $table_name = '', $is_ult = false)
{
    $delimeter = '|';
    $product = db_get_row('SELECT * FROM ?:products WHERE product_id = ?i', $product_id);

    $product_descr = db_get_row(
        'SELECT * FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s', $product_id, $lang_code
    );

    if ($is_ult) {
        $ult_product_descr = db_get_row(
            'SELECT * FROM ?:ult_product_descriptions WHERE product_id = ?i AND company_id = ?i AND lang_code = ?s',
            $product_id, $company_id, $lang_code
        );
        $product_descr = array_merge($product_descr, $ult_product_descr);
    }
    
    if (empty($product_descr)) {
        return false;
    }

    $product = array_merge($product, $product_descr);

    $options = fn_get_product_options(array($product_id), $lang_code);
    $exceptions = fn_get_product_exceptions($product_id, true);
    $optns = array();
    if (!empty($options[$product_id])) {
        foreach($options[$product_id] as $option) {
            $excns = array();
            if (!empty($exceptions)) {
                foreach($exceptions as $ex) {
                    if (!empty($ex[$option['option_id']])) {
                        $excns[] = $ex[$option['option_id']];
                    }
                }
            }
            if (!empty($option['variants'])) {
                foreach($option['variants'] as $var) {
                    if (!in_array($var['variant_id'], $excns)) {
                        $optns[] = trim($var['variant_name']);
                    }
                }
            }
        }
    }
    $options = implode($delimeter, $optns);
    
    $features = db_get_fields(
        'SELECT vd.variant FROM ?:product_feature_variant_descriptions as vd'
        . ' INNER JOIN ?:product_features_values as v ON v.variant_id = vd.variant_id AND vd.lang_code = ?s'
        . ' LEFT JOIN ?:product_features as pf ON pf.feature_id = v.feature_id'
        . ' WHERE v.product_id = ?i AND pf.status = ?s AND pf.cp_ls_use= ?s GROUP by vd.variant_id',
        $lang_code, $product['product_id'], 'A', 'Y'
    );
    
    $features_value = db_get_fields(
        'SELECT v.value FROM ?:product_features_values as v'
        . ' LEFT JOIN ?:product_features as pf ON pf.feature_id = v.feature_id'
        . ' WHERE v.lang_code = ?s AND v.product_id = ?i AND pf.status = ?s AND pf.cp_ls_use = ?s',
        $lang_code, $product['product_id'], 'A', 'Y'
    );  

    $combination_data = '';
    $comb_res = array();
    if ($product['tracking'] == 'O') {
        $comb_res = db_get_fields('SELECT product_code FROM ?:product_options_inventory WHERE product_id =?i',$product['product_id']); 
        if (!empty($comb_res)) {
            foreach ($comb_res as $c_key => $d) {
                if (empty($d)) {
                    unset($comb_res[$c_key]);
                }
            }
        }
    }

    if (!empty($comb_res)) {
        $combination_data = implode($delimeter, $comb_res);
    }

    $features = array_merge($features, $features_value);
    foreach ($features as $kk_f => $vv_f) {
        if (empty($vv_f)) {
            unset($features[$kk_f]);
        }
    }

    $features = implode($delimeter, $features);    
    $price = fn_get_product_price($product['product_id'], 1, Tygh::$app['session']['auth']);
    $popularity = db_get_field('SELECT total FROM ?:product_popularity WHERE product_id = ?i', $product['product_id']);
    $cache_data = array(
        'product_id' => $product['product_id'],
        'product' => $product['product'],
        'name_without_symbols' => fn_cp_get_product_name_without_symbols($product['product']),
        'short_description' => !empty($product['short_description']) ? $product['short_description'] : '',
        'full_description' => !empty($product['full_description']) ? $product['full_description'] : '',
        'meta_keywords' => !empty($product['meta_keywords']) ? $product['meta_keywords'] : '',
        'meta_description' => !empty($product['meta_description']) ? $product['meta_description'] : '',
        'lang_code' => $lang_code,
        'company_id' => $product['company_id'],
        'options' => $options,
        'features' => $features,
        'search_words' => !empty($product['search_words']) ? $product['search_words'] : '',
        'stop_words' => !empty($product['stop_words']) ? $product['stop_words'] : '',
        'product_code' => !empty($product['product_code']) ? $product['product_code'] : '',
        'timestamp' => $product['timestamp'],
        'status' => $product['status'],
        'product_code_combinations' => $combination_data,
        'popularity' => !empty($popularity) ? $popularity : 0,
        'price' => $price,
        'status' => !empty($product['status']) ? $product['status'] : 'D',
        'cache_timestamp' => time()
    );

    $table_name = !empty($table_name) ? $table_name : fn_cp_get_search_cache_table($company_id, $lang_code);
    if (!empty($table_name)) {
        db_query('REPLACE INTO ?p ?e', $table_name, $cache_data);
        return true;
    }

    return false;
}


function fn_cp_get_search_cache_table($company_id, $lang_code)
{
    $table_name = fn_cp_generate_cache_table_name($company_id, $lang_code);
    $result = db_get_field('SHOW TABLES LIKE ?l', $table_name);
    return !empty($result) ? $table_name : false;
}

function fn_cp_get_all_search_cache_tables()
{
    return db_get_fields('SHOW TABLES LIKE ?l', CP_SEARCH_CACHE_TABLE . '%');
}

function fn_cp_generate_cache_table_name($company_id, $lang_code)
{
    if (fn_allowed_for('MULTIVENDOR') == true) {
        $table_name = CP_SEARCH_CACHE_TABLE . '_' . strtolower($lang_code);
    } else {
        $table_name = CP_SEARCH_CACHE_TABLE . '_' . $company_id . '_' . strtolower($lang_code);
    }
    return $table_name;
}

function fn_cp_create_cache_table_name($table_name)
{
    $query = "CREATE TABLE IF NOT EXISTS `$table_name` (
        `product_id` int(11) unsigned NOT NULL DEFAULT 0,
        `product` varchar(255) NOT NULL DEFAULT '',
        `name_without_symbols` varchar(255) NOT NULL DEFAULT '',
        `short_description` mediumtext NOT NULL DEFAULT '',
        `full_description` mediumtext NOT NULL DEFAULT '',
        `meta_keywords` varchar(255) NOT NULL DEFAULT '',
        `options` text NOT NULL DEFAULT '',
        `features` text NOT NULL DEFAULT '',
        `search_words` text NOT NULL DEFAULT '',
        `stop_words` mediumtext NOT NULL DEFAULT '',
        `product_code` varchar(32) NOT NULL DEFAULT '',
        `meta_description` varchar(255) NOT NULL DEFAULT '',
        `price` decimal(12,2) NOT NULL DEFAULT '0.00',
        `timestamp` int(11) unsigned NOT NULL DEFAULT 0,
        `status` char(1) NOT NULL DEFAULT 'D',
        `popularity` int(11) NOT NULL DEFAULT 0,
        `product_code_combinations` text NOT NULL DEFAULT '',
        `cache_timestamp` int(11) unsigned NOT NULL DEFAULT 0,
        PRIMARY KEY (`product_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    db_query($query);
}


function fn_cp_modify_search_cache_table($command = 'REPAIR', $table = '', $company_id = 0, $lang_code = CART_LANGUAGE, $set_notification = false)
{
    $allowed_commands = array('REPAIR', 'OPTIMIZE', 'TRUNCATE', 'DROP');
    if (!in_array($command, $allowed_commands)) {
        return false;
    }

    $table = !empty($table) ? $table : fn_cp_get_search_cache_table($company_id, $lang_code);
    if (empty($table)) {
        return false;
    }

    db_query('?p TABLE ?p', $command, $table);

    return true;
}

function fn_cp_get_product_name_without_symbols($product_name)
{
    $name = '';
    $symbols = Registry::get('addons.cp_live_search.ignore_symbols');
    if (!empty($symbols)) {
        $symbols = str_replace(' ', '', $symbols);
        if (empty($symbols)) {
            return '';
        }
        $symbols = preg_quote($symbols, '/');
        $name = preg_replace('/[' . $symbols .']/', '', $product_name);
    }

    return $name;
}

function fn_cp_ls_replace_search_symbols($str)
{
    $symbols = trim(Registry::get('addons.cp_live_search.q_ignore'));
    if (!empty($symbols)) {
        $symbols = preg_quote(str_replace(' ', '', $symbols), '/');
        $str = preg_replace('/[' . $symbols .']/', ' ', $str);
    }
    return rtrim(trim($str), '\\');
}

function fn_cp_live_search_update_product_pre(&$product_data, $product_id, $lang_code, $can_update) 
{
    if (!empty($product_data['stop_words'])) {
        $product_data['stop_words'] = explode(',', $product_data['stop_words']);
        $product_data['stop_words'] = array_map('fn_cp_ls_prepare_phrase', $product_data['stop_words']);
        $product_data['stop_words'] = implode(',', $product_data['stop_words']);
    } 
    return true;
}

function fn_cp_live_search_export_stop_words($stop_words)
{
    if (!empty($stop_words)) {
        $stop_words = explode(',', $stop_words);
        $stop_words = array_map('fn_cp_ls_prepare_phrase', $stop_words);
        $stop_words = implode(',', $stop_words);
    }
    
    return $stop_words;
}

function fn_cp_get_search_condition($search_phrase, $params, $search_field = 'search')
{
    if (!fn_string_not_empty($search_phrase)) {
        return '';
    }

    $condition = '';
    $search_phrase = fn_cp_ls_prepare_phrase($search_phrase);
    if ($params['match'] == 'any') {
        $pieces = fn_explode(' ', $search_phrase);
        $search_type = ' OR ';
    } elseif ($params['match'] == 'all') {
        $pieces = fn_explode(' ', $search_phrase);
        $search_type = ' AND ';
    } else {
        $pieces = array($search_phrase);
        $search_type = '';
    }

    $_condition = array();
    foreach ($pieces as $p_key => $piece) {
        if (strlen($piece) == 0) {
            continue;
        }
        $_condition[] .= db_quote($search_field . ' LIKE ?l', '%' . $piece . '%');
    }

    if (!empty($_condition)) {
        $condition .= ' AND ('  . implode($search_type, $_condition) . ')';
    }

    return $condition;
}

function fn_cp_get_search_history($params, $items_per_page = 0) 
{
    $params = LastView::instance()->update('search_history', $params);
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
        'match' => 'all'
    );
    $params = array_merge($default_params, $params);

    $fields = array (
        'history.*'
    );

    $sortings = array (
        'search_id' => 'history.search_id',
        'search' => 'history.search',
        'timestamp' => 'history.timestamp',
        'search_type' => 'history.search_type',
        'result' => 'history.result'
    );

    $condition = $_condition = $join = '';

    $company_id = Registry::get('runtime.company_id');
    if (!empty($company_id)) {
        $condition .= db_quote(' AND history.company_id = ?i', $company_id);
    }
    if (!empty($params['storefront_id'])) {
        $condition .= db_quote(' AND history.storefront_id = ?i', $params['storefront_id']);
    }
    if (isset($params['search'])) {
        $condition .= fn_cp_get_search_condition($params['search'], $params, 'search');
    }
    if (!empty($params['search_type'])) {
        $condition .= db_quote(' AND search_type = ?s', $params['search_type']);
    }    
    if (!empty($params['period']) && $params['period'] != 'A') {
        list($params['period_from'], $params['period_to']) = fn_create_periods($params);
        $condition .= db_quote(' AND (timestamp >= ?i AND timestamp <= ?i)', $params['period_from'], $params['period_to']);
    }
    if (!empty($params['product_id'])) {
        $join .= db_quote(' LEFT JOIN ?:cp_search_history_clicks as clicks ON history.search_id = clicks.search_id');
        $condition .= db_quote(' AND clicks.product_id = ?i', $params['product_id']);
    }

    $limit = '';
    $sorting = db_sort($params, $sortings, 'timestamp', 'desc');

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field('SELECT COUNT(search_id) FROM ?:cp_search_history as history ?p WHERE 1 ?p', $join, $condition);
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }
    
    $history = db_get_array(
        'SELECT ?p FROM ?:cp_search_history as history ?p WHERE 1 ?p ?p ?p',
        implode(', ', $fields), $join, $condition, $sorting, $limit
    );

    if (!empty($history) && empty($params['product_id'])) {
        foreach ($history as $key => $history_item) {
            $product_ids = db_get_fields('SELECT product_id FROM ?:cp_search_history_clicks WHERE search_id = ?i', $history_item['search_id']);
            $history[$key]['product_clicks'] = count($product_ids);
            $history[$key]['product_ids'] = $product_ids;
        }
    }

    LastView::instance()->processResults('history', $history, $params);
    
    return array($history, $params);
}

function fn_cp_get_group_search_history($params, $items_per_page = 0) 
{
    $params = LastView::instance()->update('search_history', $params);
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
        'match' => 'all'
    );
    $params = array_merge($default_params, $params);
    
    $condition = $join = $group = '';

    $company_id = Registry::get('runtime.company_id');
    if (!empty($company_id)) {
        $condition .= db_quote(' AND history.company_id = ?i', $company_id);
    }

    if (isset($params['search'])) {
        $condition .= fn_cp_get_search_condition($params['search'],$params, 'search');
    }

    if (!empty($params['product_id'])) {
        $join .= db_quote(' LEFT JOIN ?:cp_search_history_clicks as clicks ON history.search_id = clicks.search_id');
        $condition .= db_quote(' AND clicks.product_id = ?i', $params['product_id']);
    }

    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field('SELECT COUNT(DISTINCT(search)) FROM ?:cp_search_history as history ?p WHERE 1 ?p', $join, $condition);
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }

    $sortings = array (
        'search' => 'history.search',
        'count' => 'count'
    );
    $sorting = db_sort($params, $sortings, 'count', 'desc');
    
    $history = db_get_array(
        'SELECT history.search, COUNT(history.search) as count FROM ?:cp_search_history as history ?p'
        . ' WHERE 1 ?p GROUP BY search ?p ?p', $join, $condition, $sorting, $limit
    );

    foreach ($history as $key => $history_item) {
        $search_ids = db_get_fields('SELECT search_id FROM ?:cp_search_history WHERE search = ?s', $history_item['search']);
        $history[$key]['product_clicks'] = db_get_field('SELECT COUNT(search_id) FROM ?:cp_search_history_clicks WHERE search_id IN (?n)', $search_ids);
    }

    LastView::instance()->processResults('history', $history, $params);
    
    return array($history, $params);
}

function fn_cp_get_products_search_history($params, $items_per_page = 0, $lang_code = DESCR_SL) 
{
    $params = LastView::instance()->update('search_history', $params);
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
        'match' => 'all'
    );
    $params = array_merge($default_params, $params);

    $condition = $join = $group = '';
    $company_id = Registry::get('runtime.company_id');
    if (!empty($company_id)) {
        $join .= db_quote(' LEFT JOIN ?:cp_search_history as history ON history.search_id = clicks.search_id AND history.company_id = ?i', $company_id);
    }

    if (isset($params['search'])) {
        $join .= db_quote(' LEFT JOIN ?:product_descriptions as descr ON descr.product_id = clicks.product_id AND descr.lang_code = ?s', $lang_code);
        $condition .= fn_cp_get_search_condition($params['search'], $params, 'product');
    }

    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field(
            'SELECT COUNT(DISTINCT(clicks.product_id)) FROM ?:cp_search_history_clicks as clicks ?p WHERE 1 ?p',
            $join, $condition
        );
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }

    $sortings = array (
        'product_clicks' => 'product_clicks'
    );

    $sorting = db_sort($params, $sortings, 'product_clicks', 'desc');

    $history = db_get_array(
        'SELECT clicks.product_id, COUNT(clicks.product_id) as product_clicks FROM ?:cp_search_history_clicks as clicks ?p'
        . ' WHERE 1 ?p GROUP BY product_id ?p ?p', $join, $condition, $sorting, $limit
    );

    foreach ($history as $key => $history_item) {
        $search_ids = db_get_fields('SELECT search_id FROM ?:cp_search_history_clicks WHERE product_id = ?i', $history_item['product_id']);
        $history[$key]['phrases_count'] = db_get_field('SELECT COUNT(DISTINCT search) FROM ?:cp_search_history WHERE search_id IN (?n)', $search_ids);
        $history[$key]['product'] = fn_get_product_name($history_item['product_id'], $lang_code);
        $history[$key]['main_pair'] = fn_get_image_pairs($history_item['product_id'], 'product', 'M', true, true, $lang_code);
    }

    LastView::instance()->processResults('history', $history, $params);
    
    return array($history, $params);
}

function fn_cp_add_search_history($q = '', $type = 'L', $result = 0) 
{
    if (Registry::get('addons.cp_live_search.record_search_history') != 'Y') {
        return false;
    }

    $q = trim($q);
    $letters_count = Registry::get('addons.cp_live_search.letters_to_start');
    if (empty($q) || strlen($q) < $letters_count) {
        return false;
    }
    
    $storefront_id = 0;
    if (fn_allowed_for('MULTIVENDOR:ULTIMATE')) {
        $storefront_id = Tygh::$app['storefront']->storefront_id;
    }
      
    $search_history = array(
        'search' => $q,
        'timestamp' => TIME,
        'search_type' => $type,
        'result' => !empty($result) ? $result : 0,
        'storefront_id' => $storefront_id,
        'company_id' => Registry::get('runtime.company_id'),
        'lang_code' => CART_LANGUAGE
    );
      
    $search_id = db_query('INSERT INTO ?:cp_search_history ?e', $search_history);

    return $search_id;
}

function fn_cp_add_search_history_click($search_id, $product_id) 
{     
    $search_history = array(
        'search_id' => $search_id,
        'product_id' => $product_id
    );
    db_query('REPLACE INTO ?:cp_search_history_clicks ?e', $search_history);
}

function fn_cp_delete_search_history($search_id = 0) 
{
    if (!empty($search_id)) {
        db_query('DELETE FROM ?:cp_search_history WHERE search_id = ?i', $search_id);
        db_query('DELETE FROM ?:cp_search_history_clicks WHERE search_id = ?i', $search_id);
    }
    return true;
}

function fn_cp_get_search_product_clicks($search_id) 
{     
    return db_get_fields('SELECT product_id FROM ?:cp_search_history_clicks WHERE search_id = ?i', $search_id);
}

function fn_cp_get_search_phrase_product_clicks($search_phrase) 
{     
    return db_get_fields(
        'SELECT product_id FROM ?:cp_search_history_clicks as clicks'
        . ' LEFT JOIN ?:cp_search_history as history ON history.search_id = clicks.search_id'
        . ' WHERE history.search = ?s', $search_phrase
    );
}

function fn_cp_get_search_phrase_product_ids($search_phrase) 
{     
    return db_get_fields(
        'SELECT product_id FROM ?:cp_search_phrase_products'
        . ' WHERE search = ?s ORDER BY position ASC', $search_phrase
    );
}

//
// Search phrases
//

function fn_cp_update_search_phrases($phrase_id = 0, $data = array())
{
    $data['company_id'] = !empty($data['company_id']) ? $data['company_id'] : fn_cp_live_search_get_company_id();
    $data['lang_code'] = DESCR_SL;

    $is_update = true;
    if (!empty($phrase_id)) {
        db_query('UPDATE ?:cp_search_phrases SET ?u WHERE phrase_id = ?i', $data, $phrase_id);
    } else {
        $phrase_id = db_query('INSERT INTO ?:cp_search_phrases ?e', $data);
        $is_update = false;
    }

    // Update phrases products
    if (isset($data['product_ids'])) {
        fn_cp_set_search_phrase_products($phrase_id, $data['product_ids'], $is_update);
    }

    // Update search phrases
    if (!empty($data['searchs'])) {
        $searchs = is_array($data['searchs']) ? $data['searchs'] : explode(',', $data['searchs']);
        fn_cp_set_search_phrase_searchs($phrase_id, $searchs, $is_update);
    }

    return $phrase_id;
}

function fn_cp_get_search_phrase($phrase_id)
{
    $phrase = db_get_row('SELECT * FROM ?:cp_search_phrases WHERE phrase_id = ?i', $phrase_id);

    $phrase['product_ids'] = db_get_fields(
        'SELECT product_id FROM ?:cp_search_phrase_products WHERE phrase_id = ?i', $phrase['phrase_id']
    );

    $phrase['searchs'] = fn_cp_get_search_phrase_searchs($phrase['phrase_id']);

    return $phrase;
}

function fn_cp_get_search_phrases($params, $items_per_page = 0, $lang_code = DESCR_SL)
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
        'match' => 'all'
    );
    $params = array_merge($default_params, $params);

    $fields = array (
        'phrases.*'
    );

    $sortings = array (
        'phrase_id' => 'phrases.phrase_id',
        'status' => 'phrases.status',
        'priority' => 'phrases.priority'
    );

    $condition = $join = '';

    $condition .= db_quote(' AND phrases.lang_code = ?s', $lang_code);

    $params['company_id'] = !empty($params['company_id']) ? $params['company_id'] : fn_cp_live_search_get_company_id();
    if (!empty($params['company_id'])) {
        $condition .= db_quote(' AND phrases.company_id = ?i', $params['company_id']);
    }

    if (!empty($params['search'])) {
        $join .= db_quote(' LEFT JOIN ?:cp_search_phrase_searchs as phrase_seachs ON phrases.phrase_id = phrase_seachs.phrase_id');
        $condition .= db_quote(' AND phrase_seachs.search LIKE ?l', '%' . $params['search'] . '%');
    }

    $limit = '';
    $sorting = db_sort($params, $sortings, 'priority', 'desc');

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field('SELECT COUNT(*) FROM ?:cp_search_phrases as phrases ?p WHERE 1 ?p', $join, $condition);
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }
    
    $search_phrases = db_get_array(
        'SELECT ?p FROM ?:cp_search_phrases as phrases ?p WHERE 1 ?p ?p ?p',
        implode(', ', $fields), $join, $condition, $sorting, $limit
    );

    if (!empty($search_phrases)) {
        foreach ($search_phrases as $key => $phrase) {
            $search_phrases[$key]['searchs'] = implode(', ', fn_cp_get_search_phrase_searchs($phrase['phrase_id']));
        }
    }

    return array($search_phrases, $params);
}

function fn_cp_delete_search_phrase($phrase_id)
{
    db_query('DELETE FROM ?:cp_search_phrases WHERE phrase_id = ?i', $phrase_id);
    db_query('DELETE FROM ?:cp_search_phrase_products WHERE phrase_id = ?i', $phrase_id);
    db_query('DELETE FROM ?:cp_search_phrase_searchs WHERE phrase_id = ?i', $phrase_id);
}

function fn_cp_get_search_phrase_searchs($phrase_id)
{
    return db_get_fields(
        'SELECT search FROM ?:cp_search_phrase_searchs WHERE phrase_id = ?i', $phrase_id
    );
}

function fn_cp_set_search_phrase_searchs($phrase_id, $searchs = array(), $is_update = true)
{
    if ($is_update) {
        db_query('DELETE FROM ?:cp_search_phrase_searchs WHERE phrase_id = ?s', $phrase_id);
    }
    $search_data = array('phrase_id' => $phrase_id);
    foreach ($searchs as $search) {
        $search_data['search'] = fn_cp_ls_prepare_phrase($search);
        if (!empty($search_data['search'])) {
            db_query('REPLACE INTO ?:cp_search_phrase_searchs ?e', $search_data);
        }
    }
}

function fn_cp_set_search_phrase_products($phrase_id, $product_ids = array(), $is_update = true)
{
    if ($is_update) {
        db_query('DELETE FROM ?:cp_search_phrase_products WHERE phrase_id = ?s', $phrase_id);
    }
    if (!empty($product_ids)) {
        asort($product_ids); // sort by priority
        
        $prod_data = array('phrase_id' => $phrase_id);
        foreach ($product_ids as $product_id => $position) {
            $prod_data['product_id'] = $product_id;
            $prod_data['position'] = $position;
            db_query('REPLACE INTO ?:cp_search_phrase_products ?e', $prod_data);
        }
    }
}

function fn_cp_get_phrase_featured_products($phrase, $lang_code = DESCR_SL)
{
    return db_get_fields(
        'SELECT phrase_products.product_id FROM ?:cp_search_phrase_products as phrase_products'
        . ' LEFT JOIN ?:cp_search_phrases as phrases ON phrase_products.phrase_id = phrases.phrase_id'
        . ' LEFT JOIN ?:cp_search_phrase_searchs as phrase_searchs ON phrase_searchs.phrase_id = phrases.phrase_id'
        . ' WHERE phrases.status = ?s AND phrases.lang_code = ?s AND phrase_searchs.search = ?s'
        . ' ORDER BY phrases.priority DESC, phrase_products.position ASC', 'A', $lang_code, $phrase
    );
}

function fn_cp_get_phrase_suggestions($phrase, $lang_code = DESCR_SL)
{
    $company_id = fn_cp_live_search_get_company_id();
    $phrases_suggestions = db_get_fields(
        'SELECT phrases.suggestions FROM ?:cp_search_phrases as phrases'
        . ' LEFT JOIN ?:cp_search_phrase_searchs as phrase_searchs ON phrase_searchs.phrase_id = phrases.phrase_id'
        . ' WHERE phrases.status = ?s AND phrases.lang_code = ?s AND company_id = ?i AND phrase_searchs.search = ?s'
        . ' ORDER BY phrases.priority DESC', 'A', $lang_code, $company_id, $phrase
    );
    $all_suggestions = array();
    foreach ($phrases_suggestions as $suggestions) {
        $suggestions = explode("\n", $suggestions);
        $all_suggestions = array_merge($all_suggestions, $suggestions);
    }
    $display_suggestions = array();
    if (!empty($all_suggestions)) {
        foreach ($all_suggestions as $suggestion) {
            $suggestion = trim($suggestion);
            if (strcasecmp($suggestion, $phrase) != 0) {
                $display_suggestions[] = $suggestion;
            }
        }
    }
    return $display_suggestions;
}

function fn_cp_get_history_suggestions($q, $company_id, $lang_code = DESCR_SL, $settings = array())
{
    $condition = db_quote(
        ' AND search LIKE (?l) AND search != ?s AND company_id = ?i AND lang_code = ?s',
        "$q%", $q, $company_id, $lang_code
    );
    $group_by = db_quote(' GROUP BY search');
    $items_count = !empty($settings['suggestions_amount']) ? $settings['suggestions_amount'] : 5;
    $limit = !empty($items_count) ? db_quote('LIMIT 0, ?i', $items_count) : '';
    if (AREA == 'C') {
        $condition .= ' AND result > 0';
        $char_count = Registry::get('addons.cp_live_search.h_suggestions_letters');
        if (!empty($char_count)) {
            $condition .= db_quote(' AND CHAR_LENGTH(search) >= ?i', $char_count);
        }
        $requests_count = Registry::get('addons.cp_live_search.h_suggestions_requests');
    }
    $sorting = 'ORDER BY count DESC';
    $searchs = db_get_array(
        'SELECT search, COUNT(search) as count FROM ?:cp_search_history WHERE 1 ?p ?p ?p ?p',
        $condition, $group_by, $sorting, $limit
    );
    if (empty($searchs)) {
        return array();
    }
    $suggestions = array();
    foreach ($searchs as $search) {
        if (!empty($requests_count) && $search['count'] < $requests_count) {
            continue;
        }
        $suggestions[] = $search['search'];
    }
    return $suggestions;
}

//
// Search synonyms
//

function fn_cp_update_search_synonym($synonym_id = 0, $data = array())
{
    if (empty($data['variants'])) {
        return false;
    }

    $data['company_id'] = !empty($data['company_id']) ? $data['company_id'] : fn_cp_live_search_get_company_id();
    $data['status'] = !empty($data['status']) ? $data['status'] : 'A';
    $data['lang_code'] = !empty($data['lang_code']) ? $data['lang_code'] : DESCR_SL;
    $data['value'] = trim($data['value']);

    $is_update = true;
    if (!empty($synonym_id)) {
        db_query('UPDATE ?:cp_search_synonyms SET ?u WHERE synonym_id = ?i', $data, $synonym_id);
    } else {
        $synonym_id = db_query('INSERT INTO ?:cp_search_synonyms ?e', $data);
        $is_update = false;
    }
 
    // Update synonym variants
    $variants = is_array($data['variants']) ? $data['variants'] : explode(',', $data['variants']);
    fn_cp_set_synonym_variants($synonym_id, $variants, $is_update);

    return $synonym_id;
}

function fn_cp_get_search_synonyms($params, $items_per_page = 0, $lang_code = DESCR_SL)
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
        'match' => 'all'
    );
    $params = array_merge($default_params, $params);

    $fields = array(
        'synonyms.*'
    );

    $sortings = array (
        'synonym_id' => 'synonyms.synonym_id',
        'value' => 'synonyms.value',
        'status' => 'synonyms.status'
    );

    $condition = $join = '';

    $condition .= db_quote(' AND synonyms.lang_code = ?s', $lang_code);

    $params['company_id'] = !empty($params['company_id']) ? $params['company_id'] : fn_cp_live_search_get_company_id();
    if (!empty($params['company_id'])) {
        $condition .= db_quote(' AND synonyms.company_id = ?i', $params['company_id']);
    }

    if (!empty($params['search'])) {
        $p_search = $params['search'];
        $join .= db_quote(' LEFT JOIN ?:cp_search_synonym_variants as variants ON synonyms.synonym_id = variants.synonym_id');
        $condition .= db_quote(' AND (variants.variant LIKE ?l OR synonyms.value LIKE ?l)', "%$p_search%", "%$p_search%");
    }

    $limit = '';
    $sorting = db_sort($params, $sortings, 'synonym_id', 'desc');

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field(
            'SELECT COUNT(*) FROM ?:cp_search_synonyms as synonyms ?p WHERE 1 ?p', $join, $condition
        );
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }
    
    $search_synonyms = db_get_hash_array(
        'SELECT ?p FROM ?:cp_search_synonyms as synonyms ?p WHERE 1 ?p ?p ?p',
        'synonym_id', implode(', ', $fields), $join, $condition, $sorting, $limit
    );

    if (!empty($search_synonyms)) {
        foreach ($search_synonyms as $synonym_id => $synonym) {
            $search_synonyms[$synonym_id]['variants'] = fn_cp_get_synonym_variants($synonym_id);
        }
    }

    return array($search_synonyms, $params);
}

function fn_cp_delete_search_synonym($synonym_id)
{
    db_query('DELETE FROM ?:cp_search_synonyms WHERE synonym_id = ?i', $synonym_id);
    db_query('DELETE FROM ?:cp_search_synonym_variants WHERE synonym_id = ?i', $synonym_id);
}

function fn_cp_get_synonym_variants($synonym_id)
{
    return db_get_fields(
        'SELECT variant FROM ?:cp_search_synonym_variants WHERE synonym_id = ?i', $synonym_id
    );
}

function fn_cp_set_synonym_variants($synonym_id, $variants = array(), $is_update = true)
{
    if ($is_update) {
        db_query('DELETE FROM ?:cp_search_synonym_variants WHERE synonym_id = ?s', $synonym_id);
    }
    $variant_data = array('synonym_id' => $synonym_id);
    foreach ($variants as $variant) {
        $variant_data['variant'] = fn_cp_ls_prepare_phrase($variant);
        if (!empty($variant_data['variant'])) {
            db_query('REPLACE INTO ?:cp_search_synonym_variants ?e', $variant_data);
        }
    }
}

function fn_cp_check_synonym_variant_exists($variant, $company_id = 0, $lang_code = CART_LANGUAGE)
{
    $company_id = !empty($company_id) ? $company_id : fn_cp_live_search_get_company_id();
    $variant = db_get_field(
        'SELECT synonyms.variant FROM ?:cp_search_synonyms as synonyms'
        . ' LEFT JOIN ?:cp_search_synonym_variants as vars ON synonyms.synonym_id = vars.synonym_id'
        . ' WHERE synonyms.vars.variant LIKE ?l AND synonyms.company_id = ?i AND synonyms.lang_code = ?s',
        $variant, $company_id, $lang_code, 'A'
    );
    return !empty($variant) ? true : false;
}

function fn_cp_get_synonym_search_value($q, $company_id = 0, $lang_code = CART_LANGUAGE, $partial_search = false)
{
    static $values = array();
    if (!isset($values[$q])) {
        $search_q = $partial_search ? $q . '%' : $q;
        $company_id = !empty($company_id) ? $company_id : fn_cp_live_search_get_company_id();
        $values[$q] = db_get_field(
            'SELECT synonyms.value FROM ?:cp_search_synonyms as synonyms'
            . ' LEFT JOIN ?:cp_search_synonym_variants as vars ON synonyms.synonym_id = vars.synonym_id'
            . ' WHERE vars.variant LIKE ?l AND synonyms.company_id = ?i AND synonyms.lang_code = ?s AND synonyms.status = ?s',
            $search_q, $company_id, $lang_code, 'A'
        );
    }
    return $values[$q];
}

function fn_cp_ls_prepare_condition_by_synonym($piece, &$tmp)
{
    $partial_search = Registry::get('addons.cp_live_search.search_synonym_part') == 'Y';
    $synonym_value = fn_cp_get_synonym_search_value($piece, 0, CART_LANGUAGE, $partial_search);
    if (!empty($synonym_value)) {
        if ($partial_search) {
            $syn_tmp = str_replace("%$piece%", "%$synonym_value%", $tmp);
            $tmp = '(' . $tmp . ' OR ' . $syn_tmp . ')';
        } else {
            $tmp = str_replace("%$piece%", "%$synonym_value%", $tmp);
        }
    }
}

//
// Objects search
//

function fn_cp_live_search_get_search_brands($q, $lang_code = DESCR_SL, $settings = array())
{
    if (empty($settings['brand_id'])) {
        return array();
    }
    $items_count = !empty($settings['brands_amount']) ? $settings['brands_amount'] : 5;
    $limit = db_quote('LIMIT 0, ?i', $items_count);
    $brands = db_get_hash_array(
        'SELECT descr.variant_id, descr.variant FROM ?:product_feature_variant_descriptions as descr'
        . ' LEFT JOIN ?:product_feature_variants as vars ON vars.variant_id = descr.variant_id'
        . ' WHERE vars.feature_id = ?i AND descr.variant LIKE ?l AND descr.lang_code = ?s ?p',
        'variant_id', $settings['brand_id'], "%$q%", $lang_code, $limit
    );
    return $brands;
}

function fn_cp_live_search_get_search_categories($q, $lang_code = DESCR_SL, $settings = array())
{
    $items_count = !empty($settings['categories_amount']) ? $settings['categories_amount'] : 5;
    $show_path = ($settings['show_categories_path'] == 'Y') ? true : false;
    $limit = db_quote('LIMIT 0, ?i', $items_count);
    $join = db_quote(
        ' LEFT JOIN ?:category_descriptions as descr ON cats.category_id = descr.category_id AND lang_code = ?s', $lang_code
    );
    $condition = db_quote(' AND descr.category LIKE ?l AND cats.status = ?s', "%$q%", 'A');
    $condition .= fn_get_company_condition('cats.company_id');
    if (Registry::get('settings.General.show_products_from_subcategories') == 'Y') {
        $join .= db_quote(' LEFT JOIN ?:categories as c_childs ON c_childs.id_path LIKE CONCAT(cats.id_path, ?s)', '/%');
        $condition .= db_quote(' AND (cats.product_count > 0 OR c_childs.product_count > 0 AND c_childs.status != ?s)', 'D');
    } else {
        $condition .= db_quote(' AND cats.product_count > 0');
    }
    $categories = db_get_hash_array(
        'SELECT DISTINCT descr.category, descr.category_id, cats.id_path FROM ?:categories as cats ?p'
        . ' WHERE 1 ?p ?p', 'category_id', $join, $condition, $limit
    );
    
    if ($show_path) {
        $path_category_ids = array();
        foreach ($categories as $category_id => &$category) {
            $category['path'] = explode('/', $category['id_path']);
            $path_category_ids = array_merge($path_category_ids, $category['path']);
        }
        $path_category_ids = array_unique($path_category_ids);
        $path_categories = fn_cp_live_search_get_category_names($path_category_ids, $lang_code);
        
        foreach ($categories as $category_id => &$category) {
             $path = '';
             if (!empty($category['path'])) {
                foreach ($category['path'] as $parent_cat_id) {
                    if (!empty($path)) {
                        $path .= ' / ';
                    }
                    $path .= $path_categories[$parent_cat_id];
                }
             }
             $category['category'] = !empty($path) ? $path : $category['category'];
        }
    }
    return $categories;
}

function fn_cp_live_search_get_search_vendors($q, $lang_code = DESCR_SL, $settings = array())
{
    if (!fn_allowed_for('MULTIVENDOR')) {
        return array();
    }
    $items_count = !empty($settings['vendors_amount']) ? $settings['vendors_amount'] : 5;
    $limit = db_quote('LIMIT 0, ?i', $items_count);
    $join = '';
    $condition = db_quote('AND ?:companies.status = ?s AND ?:companies.company LIKE ?l', 'A', "%$q%");

    if (version_compare(PRODUCT_VERSION, '4.10', '>=')) {
        $storefront = Tygh::$app['storefront'];
        if (!empty($storefront)) {
            $st_company_ids = $storefront->getCompanyIds();
            if (!empty($st_company_ids)) {
                $condition .= db_quote(' AND ?:companies.company_id IN (?n)', $st_company_ids);
            }
        }
    }

    $vendors = db_get_hash_array(
        'SELECT ?:companies.company_id, ?:companies.company FROM ?:companies ?p WHERE 1 ?p ?p',
        'company_id', $join, $condition, $limit
    );
    return $vendors;
}

function fn_cp_ls_get_search_show_items($q, $lang_code = DESCR_SL, $settings = array())
{
    $show_items = array();
    $show_items_schema = fn_get_schema('cp_live_search', 'show_items');
    foreach ($settings['show_items'] as $item => $value) {
        if (empty($show_items_schema[$item])) {
            continue;
        }
        $obj_config = $show_items_schema[$item];
        if (empty($obj_config['get_function'])) {
            continue;
        }
        $objects = call_user_func($obj_config['get_function'], $q, $lang_code, $settings);
        if (empty($objects)) {
            continue;
        }
        foreach ($objects as &$object) {
            $object['link'] = fn_cp_ls_generate_link($object, $obj_config);
        }
        $show_items[$item] = array(
            'title' => $obj_config['title'],
            'items' => $objects
        );
    }
    return $show_items;
}

//
// Weights logic
//

function fn_cp_live_search_get_search_fields()
{
    $fields = array(
        'product' => __('product_name'),
        'product_code' => __('cp_ls_product_code'),
        'short_description' => __('short_description'),
        'full_description' => __('full_description'),
        'meta_keywords' => __('meta_keywords'),
        'meta_description' => __('meta_description'),
        'features' => __('features'),
        'options' => __('options')
    );
    
    fn_set_hook('cp_live_search_get_search_fields', $fields);
    
    return $fields;
}

function fn_cp_live_search_get_weight_rules()
{
    return db_get_array('SELECT * FROM ?:cp_search_weight_rules');
}

function fn_cp_live_search_update_weight_rules($params)
{
    if (empty($params['fields'])) {
        return false;
    }

    db_query('DELETE FROM ?:cp_search_weight_rules');

    $rules = array();
    foreach($params['fields'] as $field) {
        if (empty($field['field'])) {
            continue;
        }
        $rules[$field['field']] = $field;
    }
    foreach($rules as $rule) {
        db_query('REPLACE INTO ?:cp_search_weight_rules ?e', $rule);
    }

    return true;
}

function fn_cp_live_search_get_cache_info($company_id, $is_ult = false)
{
    $cache_info = array();
    $lang_codes = array_keys(fn_get_translation_languages());
    if (!fn_allowed_for('MULTIVENDOR')) {
        $company_ids = empty($company_id) ? fn_get_all_companies_ids() : array($company_id);
    } else {
        $company_ids = array(0);
    }

    foreach ($company_ids as $company_id) {
        $cache_info[$company_id]['total_cached_strings'] = 0;
        $cache_info[$company_id]['company_name'] = !fn_allowed_for('MULTIVENDOR') ? fn_get_company_name($company_id) : '';
        $cache_info[$company_id]['cron_command'] = fn_cp_live_search_get_cron_command($company_id);

        foreach ($lang_codes as $lang_code) {

            list($c_join, $c_condition) = fn_cp_get_cache_products_query_params($company_id, $lang_code, $is_ult);
            $cache_info[$company_id]['total_products'][$lang_code] = db_get_field(
                'SELECT COUNT(DISTINCT(?:products.product_id)) FROM ?:products ?p  WHERE 1 ?p', $c_join, $c_condition
            );

            $table_name = fn_cp_get_search_cache_table($company_id, $lang_code);
            if (empty($table_name)) {
                continue;
            }
            $cache_info[$company_id]['cached_products'][$lang_code] = db_get_field('SELECT COUNT(DISTINCT(product_id)) FROM ?p', $table_name);
            $cache_info[$company_id]['total_cached_strings'] += $cache_info[$company_id]['cached_products'][$lang_code];
        }
    }
    
    return $cache_info;
}



function fn_cp_live_search_get_category_labels($category_ids, $colors = array(), $lang_code = CART_LANGUAGE)
{
    $category_labels = db_get_hash_array(
        'SELECT category_id, category FROM ?:category_descriptions WHERE category_id IN (?n) AND lang_code = ?s',
        'category_id', $category_ids, $lang_code
    );

    $last_limit = 200; // 0-255, if less is darker (for random generating)
    $i = 0;
    if (!empty($colors)) {
        $colors_count = count($colors);
    }
    foreach ($category_labels as $key => $label) {
        if (!empty($colors)) {
            if ($i >= $colors_count) {
                $i = 0;
            }
            $category_labels[$key]['color'] = $colors[$i];
            $i++;
        } else {
            $category_labels[$key]['color'] = sprintf('#%02X%02X%02X', rand(0, $last_limit), rand(0, $last_limit), rand(0, $last_limit));
        }
        
    }
    
    return $category_labels;
}

function fn_cp_live_search_get_category_names($category_ids, $lang_code = CART_LANGUAGE)
{
    return db_get_hash_single_array(
        'SELECT category_id, category FROM ?:category_descriptions WHERE category_id IN (?n) AND lang_code = ?s',
        array('category_id', 'category'), $category_ids, $lang_code
    );
}

function fn_cp_live_search_update_style_settings($settings, $params = array())
{
    if (fn_allowed_for('MULTIVENDOR:ULTIMATE') && !empty($params['storefront_id'])) {
        Settings::instance()->updateValue('style_settings', serialize($settings), 'cp_live_search', false, null, true, $params['storefront_id']);
    } else {
        Settings::instance()->updateValue('style_settings', serialize($settings), 'cp_live_search');
    }
}

function fn_cp_live_search_get_style_settings($lang_code = DESCR_SL, $params = array())
{
    if (fn_allowed_for('MULTIVENDOR:ULTIMATE') && !empty($params['storefront_id'])) {
        $settings = Settings::instance()->getValues('cp_live_search', 'ADDON', false, null, $params['storefront_id']);
    } else {
        $settings = Settings::instance()->getValues('cp_live_search', 'ADDON');
    }
    
    $style_settings = array();
    if (!empty($settings['display_options']['style_settings'])) {
        $style_settings = unserialize($settings['display_options']['style_settings']);
    } elseif (!empty($settings['style_settings'])) {
        $style_settings = unserialize($settings['style_settings']);
    }
    
    $default_settings = fn_cp_live_search_get_default_styles();
    $style_settings = array_merge($default_settings, $style_settings);
    
    return $style_settings;
}

function fn_cp_live_search_get_default_styles()
{
    $default_styles = array(
        'background' => array(
            'descr' => __('cp_ls_background'),
            'color' => '#ffffff',
            'hover_color' => '#f3f3f3'
        ),
        'header_background' => array(
            'descr' => __('cp_ls_headers_background'),
            'color' => '#efefef',
            'hover_color' => '#efefef'
        ),
        'popup_titles' => array(
            'descr' => __('cp_ls_popup_titles'),
            'color' => '#434343',
            'hover_color' => '#434343'
        ),
        'add_to_cart' => array(
            'descr' => __('cp_btn_add_to_cart'),
            'color' => '#808080',
            'hover_color' => '#b06c07'
        ),
        'add_to_wishlist' => array(
            'descr' => __('cp_btn_add_to_wishlist'),
            'color' => '#808080',
            'hover_color' => '#980d29'
        ),
        'add_to_compare' => array(
            'descr' => __('cp_btn_add_to_comparison_list'),
            'color' => '#1c1c1c',
            'hover_color' => '#3060ab'
        ),
        'load_more' => array(
            'descr' => __('cp_btn_load_more'),
            'color' => '#26323e',
            'hover_color' => '#192129'
        ),
        'view_all' => array(
            'descr' => __('cp_btn_view_all'),
            'color' => '#ffffff',
            'hover_color' => '#f3f3f3'
        ),
        'view_all_text' => array(
            'descr' => __('cp_btn_view_all_text'),
            'color' => '#26323e',
            'hover_color' => '#192129'
        ),
        'product_name' => array(
            'descr' => __('product_name'),
            'color' => '#313030',
            'hover_color' => '#313030'
        ),
        'product_code' => array(
            'descr' => __('sku'),
            'color' => '#000000',
            'hover_color' => '#000000'
        ),
        'product_price' => array(
            'descr' => __('price'),
            'color' => '#000000',
            'hover_color' => '#000000'
        ),
        'list_price' => array(
            'descr' => __('list_price'),
            'color' => '#000000',
            'hover_color' => '#000000'
        )
    );

    return $default_styles;
}

// Search motivation

function fn_cp_live_search_update_search_motivation($data)
{
    if (!empty($data['object_type'])) {
        db_query('REPLACE INTO ?:cp_search_motivation ?e', $data);
    }
}

function fn_cp_live_search_get_search_motivation($object_type = 'D', $object_id = 0, $company_id = 0, $lang_code = DESCR_SL)
{
    $content = db_get_field(
        'SELECT content FROM ?:cp_search_motivation'
        . ' WHERE object_type = ?s AND object_id = ?i AND company_id = ?s AND lang_code = ?s',
        $object_type, $object_id, $company_id, $lang_code
    );

    return $content;
}

// Search indexes
function fn_cp_ls_create_product_indexes($company_id, $lang_codes = CART_LANGUAGE, $set_notification = false)
{
    $is_ult = (fn_allowed_for('MULTIVENDOR') != true && count(fn_get_all_companies_ids()) > 1) ? true : false;
    
    $storage_key = 'cp_index_generate_' . $type . '_' . $company_id;
    $script_continue = fn_get_storage_data($storage_key);
    if (!empty($script_continue)) {
        list($continue_lang, $continue_pos) = explode('_', $script_continue);
    }

    foreach ($lang_codes as $lang_num => $lang_code) {
        if (isset($continue_lang) && $continue_lang != $lang_code) {
            continue;
        }
        
        list($c_join, $c_condition) = fn_cp_get_cache_products_query_params($company_id, $lang_code, $is_ult);
        $products_count = db_get_field(
            'SELECT COUNT(DISTINCT(?:products.product_id)) FROM ?:products ?p WHERE 1 ?p', $c_join, $c_condition
        );
        
        if (!empty($set_notification)) {
            $count = $products_count * (count($lang_codes) - $lang_num);
            $count = isset($continue_pos) ? $count - $continue_pos : $count;
            fn_set_progress('step_scale', (int) $scale_count / $step);
        }
        
        $table_name = fn_cp_get_search_index_table($company_id, $lang_code);
        if (empty($table_name)) {
            $table_name = fn_cp_generate_index_table_name($company_id, $lang_code);
            fn_cp_create_index_table($table_name);
        }
        
        if (!isset($continue_pos)) {
            fn_cp_modify_search_cache_table('TRUNCATE', $table_name);
        }
           
        $k = 0;
        $step = 3000;
        while ($k < $products_count) {
            if (isset($continue_pos) && $continue_pos > $k) {
                $k += $step;
                continue;
            }
            fn_set_storage_data($storage_key, $lang_code . '_' . $k);

            if (!empty($set_notification)) {
                $next = $k + $step;
                if (defined('AJAX_REQUEST')) {
                    fn_set_progress('echo', "Products $k - $next $lang_code are indexed<br/>");
                } else {
                    fn_echo("Process: $k - $next $lang_code is indexed<br/>");
                }
            }
        
            $limit = "LIMIT $k, $step";
            $product_ids = db_get_fields('SELECT DISTINCT(?:products.product_id) FROM ?:products ?p WHERE 1 ?p ?p', $c_join, $c_condition, $limit);
            fn_cp_ls_update_products_indexes($product_ids, $company_id, $lang_code, $table_name, $is_ult);
            
            $k += $step;
        }
        fn_set_storage_data($storage_key, '');
    }
    
    if (!empty($set_notification)) {
        fn_set_notification('N', __('notice'), __('done'));
    }

    return true;
}

function fn_cp_ls_update_products_indexes($product_ids, $company_id, $lang_code, $table_name = '', $is_ult = false)
{
    if (empty($table_name)) {
        $table_name = fn_cp_get_search_index_table($company_id, $lang_code);
        if (empty($table_name)) {
            return;
        }
    }

    $index_products = array();
    foreach ($product_ids as $product_id) {
        $indexes = fn_cp_ls_get_product_indexes($product_id, $company_id, $lang_code, $is_ult);
        foreach ($indexes as $i_val) {
            if (empty($index_products[$i_val]) || !in_array($product_id, $index_products[$i_val])) {
                $index_products[$i_val][] = $product_id;
            }
        }
    }

    if (!empty($index_products)) {
        $exists_indexes = db_get_hash_single_array(
            'SELECT id, product_ids FROM ?p WHERE id IN (?a)',
            array('id', 'product_ids'), $table_name, array_keys($index_products)
        );
        
        foreach ($index_products as $index => $product_ids) {
            if (empty($product_ids)) {
                continue;
            }
            if (!empty($exists_indexes[$index])) {
                $product_ids = array_merge(explode(',', $exists_indexes[$index]), $product_ids);
                $product_ids = array_unique($product_ids);
            }
            sort($product_ids);
            $data = array(
                'id' => $index,
                'product_ids' => implode(',', $product_ids)
            );
            db_query('REPLACE INTO ?p ?e', $table_name, $data);
        }
    }
}

function fn_cp_ls_get_product_indexes($product_id, $company_id, $lang_code = CART_LANGUAGE, $is_ult = false)
{
    $product = db_get_row(
        'SELECT products.product_code, products.product_type, products.tracking, descr.product, descr.search_words FROM ?:products as products'
        . ' LEFT JOIN ?:product_descriptions as descr ON products.product_id = descr.product_id AND descr.lang_code = ?s'
        . ' WHERE products.product_id = ?i', $lang_code, $product_id
    );

    if (empty($product)) {
        return false;
    }

    if ($is_ult) {
        $product_ult = db_get_row(
            'SELECT product, search_words FROM ?:ult_product_descriptions WHERE product_id = ?i AND company_id = ?i AND lang_code = ?s',
            $product_id, $company_id, $lang_code
        );
        $product = array_merge($product, $product_ult);
    }

    $parse_array = array();
    $parse_array[] = !empty($product['product_code']) ? $product['product_code'] : '';
    $parse_array[] = !empty($product['product']) ? $product['product'] : '';

    $comb_res = array();
    if ($product['tracking'] == 'O') {
        $comb_res = db_get_fields('SELECT product_code FROM ?:product_options_inventory WHERE product_id =?i', $product_id); 
    }

    $search_words = !empty($product['search_words']) ? explode(',', $product['search_words']) : array();
    $parse_array = array_merge($parse_array, $search_words, $comb_res);

    $indexes = fn_cp_ls_parse_for_index($parse_array);
    
    return $indexes;
}

function fn_cp_ls_parse_for_index($parse_array)
{
    static $use_mbstring = null;
    $use_mbstring = isset($use_mbstring) ? $use_mbstring : extension_loaded('mbstring');
    $indexes = array();
    foreach ($parse_array as $str) {
        $str = preg_replace('/[^\w\.\+\s]/', '', $str);
        $str = $use_mbstring ? mb_strtolower($str) : strtolower($str);
        $parsed_str = explode(' ', $str);
        foreach ($parsed_str as $val) {
            $val = trim($val);
            if (strlen($val) >= 2) {
                $indexes[] = $use_mbstring ? mb_substr($val, 0, 2) : substr($val, 0, 2);
            }
        }
    }
    return $indexes;
}

function fn_cp_get_search_index_table($company_id, $lang_code)
{
    $table_name = fn_cp_generate_index_table_name($company_id, $lang_code);
    $result = db_get_field('SHOW TABLES LIKE ?l', $table_name);
    return !empty($result) ? $table_name : false;
}

function fn_cp_get_all_search_index_tables()
{
    return db_get_fields('SHOW TABLES LIKE ?l', CP_SEARCH_INDEX_TABLE . '%');
}

function fn_cp_generate_index_table_name($company_id, $lang_code)
{
    if (fn_allowed_for('MULTIVENDOR') == true) {
        $table_name = CP_SEARCH_INDEX_TABLE . '_' . strtolower($lang_code);
    } else {
        $table_name = CP_SEARCH_INDEX_TABLE . '_' . $company_id . '_' . strtolower($lang_code);
    }
    return $table_name;
}

function fn_cp_create_index_table($table_name)
{
    $query = "CREATE TABLE IF NOT EXISTS `$table_name` (
        `id` varchar(4) NOT NULL DEFAULT '',
        `product_ids` text NOT NULL DEFAULT '',
        PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    db_query($query);
}

//
// Spellers
//

function fn_cp_ls_get_speller_words($q, $lang_code = CART_LANGUAGE, $settings = array())
{
    if (empty($settings['speller']) || $settings['speller'] == 'none') {
        return array();
    }
    $speller = $settings['speller'];
    $words = array();
    if ($speller == 'yandex') {
        $params_str = '';
        $texts = explode(' ', $q);
        foreach ($texts as $text) {
            $params_str .= '&text=' . trim($text);
        }
        $lang = in_array($lang_code, array('en', 'ru', 'uk')) ? $lang_code : 'en';
        $params_str .= '&lang=' . $lang;

        Http::$logging = false;
        $response = Http::post(
            'https://speller.yandex.net/services/spellservice.json/checkTexts?' . $params_str,
            array(), array('timeout' => 5)
        );
        $response = json_decode($response, true);
        if (!empty($response)) {
            foreach ($response as $result) {
                if (!empty($result[0]['s'])) {
                    $words = array_merge($words, $result[0]['s']);
                }
            }
        }
    }
    if (!empty($settings['spell_amount'])) {
        $words = array_slice($words, 0, $settings['spell_amount']);
    }
    return $words;
}

//
// Common functions
//

function fn_cp_live_search_get_company_id()
{
    $company_id = Registry::get('runtime.company_id');
    if (!fn_allowed_for('MULTIVENDOR') && empty($company_id)) {
        $company_id = fn_get_default_company_id();
    }
    return $company_id;
}

function fn_cp_ls_prepare_phrase($phrase)
{
    return rtrim(trim($phrase), '\\');
}

function fn_cp_ls_generate_link($object, $config)
{
    if (empty($config['link']) || empty($config['link_params'])) {
        return array();
    }
    $link = $config['link'];
    foreach ($config['link_params'] as $param_pl => $param_field) {
        $value = !empty($object[$param_field]) ? $object[$param_field] : '';
        $link = str_replace('[' . $param_pl . ']', $value, $link);
    }
    return fn_url($link);
}

//
// addon.xml functions
//

function fn_settings_variants_addons_cp_live_search_brand_id()
{
    $default = array('0' => __('none'));
    $brands = db_get_hash_single_array(
        'SELECT descr.feature_id, descr.description FROM ?:product_features_descriptions as descr'
        . ' LEFT JOIN ?:product_features as features ON features.feature_id = descr.feature_id'
        . ' WHERE features.feature_type = ?s AND lang_code = ?s',
        array('feature_id', 'description'), 'E', CART_LANGUAGE
    );
    return fn_array_merge($default, $brands);
}

function fn_install_cp_live_search()
{
    // cache tables will create automaticaly on rebuild

    $style_settings = fn_cp_live_search_get_default_styles();
    foreach ($style_settings as $key => $style) {
        unset($style_settings[$key]['descr']);
    }
    fn_cp_live_search_update_style_settings($style_settings);

    if (version_compare(PRODUCT_VERSION, '4.10', '>=')) {
        $privileges = array('manage_cp_live_search', 'view_cp_live_search');
        db_query('UPDATE ?:privileges SET group_id = ?s WHERE privilege IN (?a)', 'cp_live_search', $privileges);
        db_query('UPDATE ?:privileges SET is_view = ?s WHERE privilege = ?s', 'Y', 'view_cp_live_search');
    }
}

function fn_uninstall_cp_live_search()
{
    $tables = fn_cp_get_all_search_cache_tables();
    foreach ($tables as $table) {
        db_query('DROP TABLE ?p', $table);
    }
    
    $i_tables = fn_cp_get_all_search_index_tables();
    foreach ($i_tables as $table) {
        db_query('DROP TABLE ?p', $table);
    }
}

function fn_cp_live_search_get_cron_command($company_id = 0, $for_all = false)
{
    $php = 'php ' . DIR_ROOT .'/index.php --dispatch=cp_search_cache.generate';
    $curl = 'curl ' . fn_url('cp_search_cache.generate', 'C');
    
    if (isset($company_id)) {
        $php .= ' --switch_company_id=' . $company_id;
        $curl .= '&company_id=' . $company_id;
    }
    if (!empty($for_all)) {
        $php .= ' --all=' . $for_all;
        $curl .= '&all=' . $for_all;
    }

    $access_key = Registry::get('addons.cp_live_search.cron_password');
    $php .= ' --access_key=' . $access_key;
    $curl .= '&access_key=' . $access_key;

    return $php . '<br />' . $curl;
}

function fn_cp_live_search_get_index_cron_info()
{
    $company_id = Registry::get('runtime.company_id');
    $simple_ult = Registry::get('runtime.simple_ultimate');
    if (!empty($company_id) || $simple_ult || fn_allowed_for('MULTIVENDOR')) {
        return __('cp_ls_index_cron_info', array(
            '[dir]' => Registry::get('config.dir.root'),
            '[url]' => Registry::get('config.current_location'),
            '[company_id]' => !empty($simple_ult) ? fn_get_default_company_id() : $company_id,
            '[cron_key]' => Registry::get('addons.cp_live_search.cron_password')
        ));
    } else {
        return __('cp_ls_index_select_store');
    }
}

function fn_cp_live_search_get_cache_link_info()
{
    return __('cp_ls_cache_link_info', array(
        '[link]' => fn_url('cp_search_cache.rebuild', 'A')
    ));
}

function fn_cp_live_search_get_sm_link_info()
{
    return __('cp_ls_sm_link_info', array(
        '[link]' => fn_url('cp_live_search.motivation_update', 'A')
    ));
}
