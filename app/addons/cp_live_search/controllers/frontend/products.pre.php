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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (!empty($_REQUEST['new_r'])) {
    $_REQUEST = $_REQUEST['new_r'];
}

if ($mode == 'cp_live_search') {
    if (!empty($_REQUEST['q'])) {
        $q = fn_cp_ls_replace_search_symbols($_REQUEST['q']);
        $_REQUEST['q'] = $q;
        
        if (defined('AJAX_REQUEST')) {
            Registry::get('ajax')->assign('highlight', $q);
        }
        
        $settings = Registry::get('addons.cp_live_search');

        $company_id = Registry::get('runtime.company_id');
        $lang_code = CART_LANGUAGE;

        $params = array (
            'q' => $q,
            'match' => $settings['search_by_exact_phrase'],
            'pname' => 'Y',
            'pcode_from_q' => 'Y',
            'search_by_product_code' => $settings['search_by_product_code'],
            'pshort' => $settings['search_in_short_description'],
            'pfull' => $settings['search_in_full_description'],
            'pkeywords' => $settings['search_in_keywords'],
            'search_in_features' => $settings['searchinfeatures'],
            'search_in_options' => $settings['searchinoptions'],
            'search_by_categories' => $settings['search_by_categories'],
            'page' => isset($_REQUEST['page']) ? $_REQUEST['page'] : 1,
        );
        
        if (fn_allowed_for('MULTIVENDOR') && !empty($_REQUEST['company_id'])) {
            $params['company_id'] = $_REQUEST['company_id'];
        }

        $params['cp_live_search'] = true;
        
        $table_exists = fn_cp_get_search_cache_table($company_id, $lang_code);
        if ($settings['use_cache'] == 'Y' && !empty($table_exists)) {
            $params['q'] = '';
            $params['search_q'] = $q;
            
            if ($settings['order_results'] == 'weight') {
                $params['sort_by'] = 'cp_search_weight';
                $params['sort_order'] = 'desc';
            } elseif ($settings['order_results'] == 'newest') {
                $params['sort_by'] = 'timestamp';
                $params['sort_order'] = 'desc';
            } elseif ($settings['order_results'] == 'price_asc') {
                $params['sort_by'] = 'price';
                $params['sort_order'] = 'asc';
            } elseif ($settings['order_results'] == 'price_desc') {
                $params['sort_by'] = 'price';
                $params['sort_order'] = 'desc';            
            } elseif ($settings['order_results'] == 'product') {
                $params['sort_by'] = 'product';
                $params['sort_order'] = 'asc';
            } else {
                $params['sort_by'] = 'popularity';
                $params['sort_order'] = 'desc';
            }
        }

        // Featured products
        if ($settings['show_f_products_result'] == 'Y') {
            $f_product_ids = fn_cp_get_phrase_featured_products($q);
            if (!empty($f_product_ids)) {
                $params['exclude_pid'] = $f_product_ids;

                if (empty($_REQUEST['load_more'])) {
                    $f_products_limit = !empty($settings['f_products_amount']) ? $settings['f_products_amount'] : 5;

                    $f_params = array('item_ids' => implode(',', $f_product_ids));
                    list($f_products) = fn_get_products($f_params, $f_products_limit, $lang_code);
                }
            }
        }

        list($products, $search) = fn_get_products($params, $settings['items_limit'], $lang_code);

        if ($settings['show_product_category'] == 'each' || $settings['show_product_category'] == 'group') {
            $main_categories = array();
            $_products = !empty($f_products) ? array_merge($products, $f_products) : $products;

            foreach ($_products as $product) {
                if (!empty($product['main_category']) && !in_array($product['main_category'], $main_categories)) {
                    $main_categories[] = $product['main_category'];
                }
            }
            if (!empty($main_categories)) {
                if ($settings['show_product_category'] == 'group') {

                    $category_names = fn_cp_live_search_get_category_names($main_categories);
                    Registry::get('view')->assign('category_names', $category_names);

                } elseif ($settings['show_product_category'] == 'each') {

                    $category_labels = Tygh::$app['session']['category_labels'];
                    $colors = array();
                    if (!empty($category_labels)) {
                        if ($settings['show_category_labels'] == 'assigned' && !empty($settings['category_labels_colors'])) {
                            $colors = explode(',', $settings['category_labels_colors']);
                            $correct_category_ids = array();
                            foreach ($category_labels as $l_category_id => $label) {
                                if (in_array($label['color'], $colors)) {
                                    $correct_category_ids[] = $l_category_id;
                                }
                            }
                            $main_categories = array_diff($main_categories, $correct_category_ids);
                        } else {
                            $main_categories = array_diff($main_categories, array_keys($category_labels));
                        }
                    }

                    if (!empty($main_categories)) {
                        $new_category_labels = fn_cp_live_search_get_category_labels($main_categories, $colors);
                        $category_labels = fn_array_merge($category_labels, $new_category_labels);
                    }

                    Tygh::$app['session']['category_labels'] = $category_labels;

                    Registry::get('view')->assign('category_labels', $category_labels);
                }
            }
        }

        $gather_params = array(
            'get_icon' => true,
            'get_detailed' => true,
            'get_additional' => false,
            'get_options'=> true,
            'get_taxed_prices' => false,
            'get_extra' => false,
            'detailed_params' => false,
            'get_taxed_prices' => true
        );
        
        fn_gather_additional_products_data($products, $gather_params);

        // Featured products in result output
        if (!empty($f_products)) {
            fn_gather_additional_products_data($f_products, $gather_params);

            Registry::get('view')->assign('f_products', $f_products);
        }
        
        if (empty($_REQUEST['load_more'])) {
            $cpv1 = ___cp('YnJhbmRz');
            $cpv2 = ___cp('c3BlbGxlcg');
            $cpv3 = ___cp('Y2F0ZWdvcmllcw');
            $cpv4 = ___cp('dmVuZG9ycw');
            $cpv5 = ___cp('bWVhbl93b3Jkcw');
            $cpv6 = ___cp('c2hvd19icmFuZHXfcmVzdWx0');
            $cpv7 = ___cp('c2hvd19jYPRlZ29yaWVzP3Jlc3VsdA');
            $cpv8 = ___cp('c2hvd192ZW5kb3JzP3Jlc3VsdA');
            if ($settings['use_suggestions'] != 'none') {
                $items = array();
                if ($settings['use_suggestions'] == 'search_phrases' || $settings['use_suggestions'] == 'auto') {
                    $items = fn_cp_get_phrase_suggestions($q);
                }
                if ($settings['use_suggestions'] == 'search_results' || $settings['use_suggestions'] == 'auto' && empty($items)) {
                    $items = fn_cp_get_history_suggestions($q, $company_id, $lang_code, $settings);
                }
                if (!empty($items)) {
                    $items = array_slice($items, 0, $settings['suggestions_amount']);
                    Registry::get('view')->assign('suggestions', $items);
                }
            }
            if ($settings[$cpv6] == 'Y') {
                $items = call_user_func(___cp('Zm5fY3BfbGl2ZV9zZWFyY2hfZ2V0P3XlYPJjaF9icmFuZHM'), $q, $lang_code, $settings);
                if (!empty($items)) {
                    Registry::get('view')->assign($cpv1, $items);
                }
            }
            if ($settings[$cpv7] == 'Y') {
                $items = call_user_func(___cp('Zm5fY3BfbGl2ZV9zZWFyY2hfZ2V0P3XlYPJjaF9jYPRlZ29yaWVz'), $q, $lang_code, $settings);
                if (!empty($items)) {
                    Registry::get('view')->assign($cpv3, $items);
                }
            }
            if ($settings[$cpv8] == 'Y') {
                $items = call_user_func(___cp('Zm5fY3BfbGl2ZV9zZWFyY2hfZ2V0P3XlYPJjaF92ZW5kb3Jz'), $q, $lang_code, $settings);
                if (!empty($items)) {
                    Registry::get('view')->assign($cpv4, $items);
                }
            }
            if (!empty($settings[$cpv2])) {
                $items = call_user_func(___cp('Zm5fY3BfbHXfZ2V0P3XwZWxsZPJfd29yZHM'), $q, $lang_code, $settings);
                if (!empty($items)) {
                    Registry::get('view')->assign($cpv5, $items);
                }
            }
            if (!empty($settings['show_items'])) {
                $items = fn_cp_ls_get_search_show_items($q, $lang_code, $settings);
                if (!empty($items)) {
                    Registry::get('view')->assign('show_items', $items);
                }
            }
        }

        $search_id = '';
        if (!empty($_REQUEST['search_id'])) {
            $search_id = $_REQUEST['search_id'];
        } else {
            $search_id = fn_cp_add_search_history($q, 'L', !empty($search['total_items']) ? $search['total_items'] : 0);
        }
        Registry::get('view')->assign('search_id', $search_id);

        $search_input_id = !empty($_REQUEST['search_input_id']) ? $_REQUEST['search_input_id'] : '';
        Registry::get('view')->assign('search_input_id', $search_input_id);
        Registry::get('view')->assign('live_result', $products);
        Registry::get('view')->assign('search', $search);

        if (!empty($_REQUEST['load_more'])) {
            Registry::get('view')->display('addons/cp_live_search/components/products_list.tpl');
        } else {
            Registry::get('view')->display('addons/cp_live_search/components/result.tpl');
        }
    }
   exit;
    
} elseif ($mode == 'search') {

    if (empty($_REQUEST['supplier_id']) && !empty($_REQUEST['q'])
        && (!empty($_REQUEST['search_performed']) || !empty($_REQUEST['features_hash']))
    ) {
        $params = $_REQUEST;

        $settings = Registry::get('addons.cp_live_search');

        $company_id = Registry::get('runtime.company_id');
        $lang_code = CART_LANGUAGE;

        $q = fn_cp_ls_replace_search_symbols($params['q']);
        $_REQUEST['q'] = $q;

        $table_exists = fn_cp_get_search_cache_table($company_id, $lang_code);
        if ($settings['use_cache'] == 'Y' && !empty($table_exists) && !empty($q)) {
            $params = array(
                'match' => $settings['search_by_exact_phrase'],
                'pname' => 'Y',
                'search_by_product_code' => $settings['search_by_product_code'],
                'pshort' => $settings['search_in_short_description'],
                'pfull' =>  $settings['search_in_full_description'],
                'pkeywords' => $settings['search_in_keywords'],
                'search_in_features' => $settings['searchinfeatures'],
                'search_in_options' => $settings['searchinoptions'],
                'extend' => array('description'),
                'search_by_categories' => $settings['search_by_categories'],
                'page' => !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1,
                'items_per_page' => !empty($_REQUEST['items_per_page']) ? $_REQUEST['items_per_page'] : Registry::get('settings.Appearance.products_per_page')
            );

            $params = array_merge($_REQUEST, $params);

            $params['q'] = '';
            $params['search_q'] = $q;
            $params['cp_simple_search'] = true;

            if (empty($params['sort_by'])) {
                if ($settings['order_results'] == 'weight') {
                    $params['sort_by'] = 'cp_search_weight';
                    $params['sort_order'] = 'desc';
                } elseif ($settings['order_results'] == 'newest') {
                    $params['sort_by'] = 'timestamp';
                    $params['sort_order'] = 'desc';
                } elseif ($settings['order_results'] == 'price_asc') {
                    $params['sort_by'] = 'price';
                    $params['sort_order'] = 'asc';
                } elseif ($settings['order_results'] == 'price_desc') {
                    $params['sort_by'] = 'price';
                    $params['sort_order'] = 'desc';            
                } elseif ($settings['order_results'] == 'product') {
                    $params['sort_by'] = 'product';
                    $params['sort_order'] = 'asc';
                } else {
                    $params['sort_by'] = 'popularity';
                    $params['sort_order'] = 'desc';
                }
            }

            list($products, $search) = fn_get_products($params, $params['items_per_page'], $lang_code);
            
            fn_gather_additional_products_data($products, array(
                'get_icon' => true,
                'get_detailed' => true,
                'get_additional' => true,
                'get_options'=> true
            ));
            
            if (!empty($products)) {
                Tygh::$app['session']['continue_url'] = Registry::get('config.current_url');
            }

            $selected_layout = fn_get_products_layout($params);
            Registry::get('view')->assign('products', $products);
            Registry::get('view')->assign('search', $search);
            Registry::get('view')->assign('selected_layout', $selected_layout);

            //will prevent execution of default controller
            $_REQUEST['ls_search_performed'] = !empty($_REQUEST['search_performed']) ? $_REQUEST['search_performed'] : '';
            $_REQUEST['ls_features_hash'] =  !empty($_REQUEST['features_hash']) ? $_REQUEST['features_hash'] : '';
            $_REQUEST['search_performed'] = $_REQUEST['features_hash'] = '';
        
        } else {
            if (!empty($settings['search_by_exact_phrase']) && in_array($settings['search_by_exact_phrase'], array('all', 'any', 'exact_phrase'))) {
                $_REQUEST['match'] = $settings['search_by_exact_phrase'];
            }
        }
    }
}
