<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}
use Tygh\Registry;

function fn_facebook_feed_product_addon_install()
{
    if (db_get_row("SHOW COLUMNS FROM `?:privileges` WHERE `Field`='group_id'")) {
        db_query("REPLACE INTO ?:privileges (privilege, is_default, section_id, group_id, is_view) VALUES ('manage_pinta_facebook_feed', 'Y', 'addons', 'pinta_facebook_feed', 'N')");
        db_query("REPLACE INTO ?:privileges (privilege, is_default, section_id, group_id, is_view) VALUES ('view_pinta_facebook_feed', 'Y', 'addons', 'pinta_facebook_feed', 'Y')");
    } else {
        db_query("REPLACE INTO ?:privileges (privilege, is_default, section_id) VALUES ('manage_pinta_facebook_feed', 'Y', 'addons')");
        db_query("REPLACE INTO ?:privileges (privilege, is_default, section_id) VALUES ('view_pinta_facebook_feed', 'Y', 'addons')");
    }
    db_query("REPLACE INTO ?:usergroup_privileges (usergroup_id, privilege) VALUES (4, 'manage_pinta_facebook_feed')");
    db_query("REPLACE INTO ?:usergroup_privileges (usergroup_id, privilege) VALUES (4, 'view_pinta_facebook_feed')");

    $file = file('https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt');
    if (!$file) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $file = explode(PHP_EOL, $output);
    }
    foreach ($file as $f) {

        if (!strstr($f, '#')) {
            $buffer = explode("-", $f);
            $google_category_name = htmlentities(trim($buffer[1]));
            $google_category_id = trim($buffer[0]);
            $google_category_parent = 0;
            if (count($buffer1 = explode(">", $buffer[1])) > 1) {
                $google_category_parent = db_get_field("SELECT google_category_id FROM ?:facebook_feed_google_category WHERE google_category_name = ?s", htmlentities(trim($buffer1[0])));
            }

            $data = array('google_category_id' => $google_category_id, 'google_category_name' => $google_category_name, 'google_category_parent' => $google_category_parent);
            db_query('INSERT INTO ?:facebook_feed_google_category ?e', $data);
        }
    }
}

function fn_facebook_feed_get_company_id()
{
    if(Registry::get('runtime.company_data.company_id') == 0){
        return 0;
    }else{
        return Registry::get('runtime.company_data.company_id');
    }
}

function fn_facebook_feed_get_settings_for_feed($company_id = 0)
{

    return db_get_field("SELECT setting FROM ?:facebook_feed_settings WHERE company_id = ?i", $company_id);
}

function fn_facebook_feed_update_settings_for_feed($company_id,$data)
{
    if(empty($company_id)){
        $company_id = 0;
    }
    if(empty(db_get_field("SELECT setting FROM ?:facebook_feed_settings WHERE company_id = ?i", $company_id))){
        return db_query('INSERT INTO ?:facebook_feed_settings (`company_id`,`setting`) VALUES (?i, ?s)',$company_id, $data['setting']);
    } else {
        return db_query('UPDATE ?:facebook_feed_settings SET `setting`=?s WHERE `company_id`=?i', $data['setting'],$company_id);
    }
}

function fn_get_google_main_categories()
{

    $data = [];
    $query = db_get_array('SELECT * FROM ?:facebook_feed_google_category WHERE google_category_parent = ?i', 0);

    if (!empty($query)) {
        foreach ($query as $q) {
            $data[$q['google_category_id']] = $q;
        }
    }
    return $data;
}

function fn_get_google_main_category($company_id = 0)
{
    $data = [];
    $query = db_get_array('SELECT ?:facebook_feed_category.*, ?:facebook_feed_google_category.google_category_name 
                            FROM ?:facebook_feed_category 
                            LEFT JOIN ?:facebook_feed_google_category ON ?:facebook_feed_category.google_category_id = ?:facebook_feed_google_category.google_category_id 
                            WHERE ?:facebook_feed_category.company_id = ?i', $company_id);

    if (!empty($query)) {
        foreach ($query as $q) {
            $data[$q['category_id']] = $q;
        }
    }
    return $data;
}

/**
 * Choosing a product_id from the selected categories
 * @param int $company_id
 * @return array
 */
function fn_get_products_in_categoryes_facebook($company_id = 0)
{
    $products = array();
    $data = db_get_fields('SELECT category_id FROM ?:facebook_feed_category ');
    try {
        if (!empty($data)) {
            if ($company_id > 0) {
                $join = db_quote(' LEFT JOIN ?:products ON ?:products.product_id = ?:products_categories.product_id');
                $condition = db_quote('?:products.company_id = ?i', $company_id);

                $products = db_get_array('SELECT ?:products_categories.product_id,?:products_categories.category_id as main_category 
                                          FROM ?:products_categories ?p WHERE ?:products_categories.category_id IN (?a) AND ?p AND ?:products.status = "A" GROUP BY ?:products_categories.product_id', $join, $data, $condition);
            } else {
                $products = db_get_array('SELECT ?:products.product_id,?:products_categories.category_id as main_category 
                                            FROM ?:products_categories 
                                              LEFT JOIN ?:products ON ?:products.product_id = ?:products_categories.product_id
                                            WHERE ?:products_categories.category_id IN (?a) AND ?:products.status = "A" GROUP BY ?:products_categories.product_id', $data);

            }
        }
    } catch (\Exception $e){
    }
    return (array('0' => $products));

}


/**
 * Set and get data in Memcached
 * @param string $type name type data
 * @param int $id
 * @param string $lang_id
 * @param string$func_name
 * @param object $memcached
 * @param bool $auth
 * @return array|bool|mixed|string
 */
function fn_facebook_feed_memcached($type, $id, $lang_id, $func_name,$memcached,$auth = false,$company_id = 0)
{
    $result = array();
    if($memcached !== false){
        $mc = $memcached;
        $name_key = $type . '-' . $id . "-" . $lang_id."-".$company_id;
        $result = $mc->get($name_key);
    } else {
        $result = false;
    }

    if (!$result) {
        if ($func_name == 'fn_get_product_data') {
            $result = fn_get_product_data($id, $auth, $lang_id, '', true, true, true, true, false, true, false,true);
            fn_gather_additional_product_data($result, true, true);

        } elseif ($func_name == 'fn_get_products') {
                $result = fn_get_products( array('company_id' => $id), 0, $lang_id);
        } elseif ($func_name == 'fn_get_product_options') {
            $result = fn_get_product_options($id, $lang_id);
        } elseif ($func_name == 'fn_get_category_data') {
            $result = fn_get_category_data($id, $lang_id, $field_list = '', false);
        } elseif ($func_name == 'fn_get_google_category_') {
              $result = fn_get_google_category_($id,$company_id);
        }

        if(($memcached !== false) && ($result !== false)){
            $mc->set($name_key, $result);
        }
    }
    return $result;
}
function fn_facebook_feed_get_product_feature($product_id,$lang_code){
    return db_get_array('SELECT * 
                            FROM ?:product_features_values pfv
                            LEFT JOIN ?:product_feature_variant_descriptions pfvd ON pfvd.variant_id = pfv.variant_id AND pfvd.lang_code = pfv.lang_code
                            WHERE pfv.product_id = ?i AND pfvd.lang_code = ?s ', $product_id, $lang_code);

}
function fn_get_google_category_($category_id,$company_id = 0)
{
    return db_get_row('SELECT * FROM ?:facebook_feed_category WHERE category_id = ?i AND company_id = ?i', $category_id, $company_id);
}
function fn_get_google_category_name($category_id)
{
    return db_get_field('SELECT google_category_name FROM ?:facebook_feed_google_category WHERE google_category_id = ?i ', $category_id);
}
function fn_get_options_id_from_setting($option_name)
{

    return db_get_fields('SELECT option_id FROM ?:product_options_descriptions WHERE option_name = ?s', $option_name);
}
function fn_facebook_feed_get_options_id_from_setting($option_name,$type = 'options')
{
    if($type == 'options'){
        return db_get_fields('SELECT option_id FROM ?:product_options_descriptions WHERE option_name = ?s', $option_name);
    } else {
        return db_get_fields('SELECT feature_id AS option_id FROM ?:product_features_descriptions WHERE description = ?s GROUP BY feature_id', $option_name);
    }
}
function fn_get_list_category($category_tree)
{
    $result = [];

    foreach ($category_tree as $i) {
        if (!empty($i['subcategories'])) {
            foreach ($i['subcategories'] as $e) {
                $result[] = get_item_list($e);
                if (!empty($e['subcategories'])) {
                    foreach ($e['subcategories'] as $elem) {
                        $result[] = get_item_list($elem);
                        if (!empty($elem['subcategories'])) {
                            foreach ($elem['subcategories'] as $item) {
                                $result[] = get_item_list($item);
                            }
                        } else {
                            // $result[] = get_item_list($elem);
                        }
                    }
                } else {
                    // $result[] = get_item_list($e);
                }
            }
        } else {
             $result[] = get_item_list($i);
        }

    }
    return $result;
}

function get_item_list($data)
{
    $result = [
        "category_id" => $data['category_id']
        , "category" => $data['category']
        , 'company_id' => isset($data['company_id']) ? $data['company_id'] : false
        , "status" => $data['status']
        , 'level' => $data['level']
    ];
    return $result;
}

function get_facebook_feed_get_all_features($lang_code = CART_LANGUAGE)
{
    $query = db_get_array('SELECT description AS option_name FROM ?:product_features_descriptions WHERE lang_code = ?s GROUP BY description ORDER BY description ASC ', $lang_code);
    return $query;
}


function fn_facebook_feed_get_company_categories($company_id)
{
    return db_get_fields('SELECT cd.category_id 
FROM ?:products_categories pc
         LEFT JOIN ?:products p ON p.product_id = pc.product_id
         LEFT JOIN ?:category_descriptions cd ON cd.category_id = pc.category_id
WHERE p.company_id = ?i
GROUP BY pc.category_id',$company_id);
}
function fn_facebook_feed_get_categories_str($data){
    fn_print_die($data);
}
