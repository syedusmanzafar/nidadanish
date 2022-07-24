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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'm_delete') {
        if (isset($_REQUEST['search_ids'])) {
            foreach ($_REQUEST['search_ids'] as $v) {
                fn_cp_delete_search_history($v);
            }
        }
        fn_set_notification('N', __('notice'), __('text_search_words_have_been_deleted'));
    }
    
    if ($mode == 'clear') {
        $condition = '';
        if (!empty($_REQUEST['company_id'])) {
            $condition .= db_quote(' AND ?:cp_search_history.company_id = ?i', $_REQUEST['company_id']);
        }
        if (!empty($_REQUEST['storefront_id'])) {
            $condition .= db_quote(' AND ?:cp_search_history.storefront_id = ?i', $_REQUEST['storefront_id']);
        }
        db_query(
            'DELETE ?:cp_search_history, ?:cp_search_history_clicks FROM ?:cp_search_history'
            . ' LEFT JOIN ?:cp_search_history_clicks ON ?:cp_search_history_clicks.search_id = ?:cp_search_history.search_id'
            . ' WHERE 1 ?p', $condition
        );
        fn_set_notification('N', __('notice'), __('text_search_words_have_been_deleted'));
    }

    return array(CONTROLLER_STATUS_OK, 'cp_search_history.manage?section=all');
}

if ($mode == 'manage') {
    $params = $_REQUEST;
    $params['section'] = !empty($params['section']) ? $params['section'] : '';

    $history = $search = array();
    if ($params['section'] == 'all') {
        list($history, $search) = fn_cp_get_search_history($params, Registry::get('settings.Appearance.admin_elements_per_page'));

    } elseif ($params['section'] == 'phrase_group') {
        list($history, $search) = fn_cp_get_group_search_history($params, Registry::get('settings.Appearance.admin_elements_per_page'));

    } elseif ($params['section'] == 'product_clicks' ) {
        list($history, $search) = fn_cp_get_products_search_history($params, Registry::get('settings.Appearance.admin_elements_per_page'));
        
    }
    
    Registry::get('view')->assign('search', $search);
    Registry::get('view')->assign('history', $history);

    $sections = array(
        'all' => array(
            'title' => __('cp_all_search_history'),
            'href' => 'cp_search_history.manage?section=all'
        ),
        'phrase_group' => array(
            'title' => __('cp_search_phrases_group'),
            'href' => 'cp_search_history.manage?section=phrase_group'
        ),
        'product_clicks' => array(
            'title' => __('cp_product_clicks'),
            'href' => 'cp_search_history.manage?section=product_clicks'
        )
    );

    Registry::set('navigation.dynamic.sections', $sections);

    $section = !empty($_REQUEST['section']) ? $_REQUEST['section'] : '';
    Registry::set('navigation.dynamic.active_section', $section);
    
    if (fn_allowed_for('MULTIVENDOR:ULTIMATE')) {
        $storefront_id = !empty($_REQUEST['storefront_id'])
            ? (int) $_REQUEST['storefront_id']
            : 0;
        Tygh::$app['view']->assign('selected_storefront_id', $storefront_id);
    }

} elseif ($mode == 'delete') {

    fn_cp_delete_search_history($_REQUEST['search_id']);
    fn_set_notification('N', __('notice'), __('text_search_word_has_been_deleted'));

} elseif ($mode == 'info') {
    if (!empty($_REQUEST['phrases'])) {
        if (!empty($_REQUEST['product_id'])) {
            $company_id = fn_cp_live_search_get_company_id();
            $phrases = db_get_fields(
                'SELECT DISTINCT history.search FROM ?:cp_search_history as history'
                . ' LEFT JOIN ?:cp_search_history_clicks as clicks ON history.search_id = clicks.search_id'
                . ' WHERE clicks.product_id = ?i AND history.company_id = ?i', $_REQUEST['product_id'], $company_id
            );
            Registry::get('view')->assign('search_phrases', $phrases);
        }

    } else {
        $product_ids = array();
        if (!empty($_REQUEST['search_id'])) {
            $product_ids = fn_cp_get_search_product_clicks($_REQUEST['search_id']);
        } elseif (!empty($_REQUEST['search'])) {
            $product_ids = fn_cp_get_search_phrase_product_clicks($_REQUEST['search']);
        }

        if (!empty($product_ids)) {
            list($products) = fn_get_products(array('pid' => $product_ids));
                
            $gather_params = array(
                'get_icon' => true,
                'get_detailed' => true,
                'get_additional' => false,
                'get_options'=> false,
                'get_taxed_prices' => false,
                'get_extra' => false,
                'detailed_params' => false
            );
            
            fn_gather_additional_products_data($products, $gather_params);
            Registry::get('view')->assign('products', $products);
        }
    }
}
/*
if ($mode == 'generate_history') {
    $rec_count = 10000000;
    $company_id = fn_get_runtime_company_id();
    for ($i = 0; $i < $rec_count; $i++) {
        $data = array(
            'search' => fn_cp_random_string(rand(3, 7)),
            'timestamp' => TIME - rand(0, 1000000),
            'search_type' => $i % 3 == 0 ? 'S' : 'L',
            'result' => rand(0, 11),
            'company_id' => $company_id,
            'lang_code' => CART_LANGUAGE
        );
        if (empty($data['search'])) {
            continue;
        }
        db_query('INSERT INTO ?:cp_search_history ?e', $data);
    }
    fn_print_die('Success!');
}
if ($mode == 'generate_history_clicks') {

    $product_ids = db_get_fields('SELECT product_id FROM ?:products WHERE status = ?s', 'A');
    $p_count = count($product_ids) - 1;
    $rec_count = 100000;
    $company_id = fn_get_runtime_company_id();
    for ($i = 0; $i < $rec_count; $i++) {
        $data = array(
            'search_id' => rand($rec_count*10, $rec_count*1000),
            'product_id' => $product_ids[rand(0, $p_count)],
        );
        db_query('INSERT INTO ?:cp_search_history_clicks ?e', $data);
    }
    fn_print_die('Success!');
}
function fn_cp_random_string($count = 10)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    $str = '';
    $chars_count = strlen($chars) - 1;
    for ($i = 0; $i < $count; $i++) {
        $k = rand(0, $chars_count);
        $str .= $chars[$k];
        fn_print_r($str, $k, $chars[$k]);
    }
    return $str;
}
*/
