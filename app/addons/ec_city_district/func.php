<?php

use Tygh\Enum\ProfileTypes;
use Tygh\Tygh;
use Tygh\Registry;
use Tygh\Languages\Languages;

function fn_ec_city_district_install(){
    $field = array('city', 's_city', 'b_city');
    db_query("UPDATE ?:profile_fields SET field_type = ?s WHERE field_name IN (?a)", EC_CITY, $field);
    // district//
    $field = array(
        'field_name'        => 'district',
        'profile_show'      => 'N',
        'profile_required'  => 'N',
        'checkout_show'     => 'Y',
        'checkout_required' => 'N',
        'partner_show'      => 'N',
        'partner_required'  => 'N',
        'field_type'        => EC_DISTRICT,
        'profile_type'      => ProfileTypes::CODE_USER,
        'position'          => 75,
        'is_default'        => 'Y',
        'section'           => 'BS',
        'matching_id'       => 0,
        'class'             => '',
        'autocomplete_type' => '',
        'description'       => __('ec_district_desc'),
    );

    $field_id = fn_update_profile_field($field, 0);

    if ($field_id) {
        $languages = Languages::getAvailable('A', true);

        foreach ($languages as $code => $lang) {
            fn_update_profile_field(array(
                'description' => __('ec_district_desc', array(), $code),
            ), $field_id, $code);
        }
    }
}

function fn_ec_city_district_uninstall(){
    // delete district field //
    $field = array('district', 's_district', 'b_district');
    $field_id = db_get_field('SELECT field_id FROM ?:profile_fields WHERE profile_type = ?s AND field_name IN (?a)', ProfileTypes::CODE_USER, $field
    );
    if ($field_id) {
        fn_delete_profile_field($field_id);
    }
    $field = array('city', 's_city', 'b_city');
    db_query("UPDATE ?:profile_fields SET field_type = ?s WHERE field_name IN (?a)", 'I', $field);
}

function fn_get_cities($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    // Set default values to input params
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    $fields = $joins = array();
    $condition = $sorting = $limit = $group = '';

    $fields = array(
        'a.city_id',
        'a.state_id',
        'a.country_code',
        'a.state_code',
        'b.code',
        'a.status',
        'c.state',
        'd.country'
    );
    
    $joins[] = db_quote("LEFT JOIN ?:states as s ON a.state_id = s.state_id");
    $joins[] = db_quote("LEFT JOIN ?:ec_cities_descriptions as b ON a.city_id = b.city_id AND b.lang_code = ?s", $lang_code);
    $joins[] = db_quote("LEFT JOIN ?:state_descriptions as c ON c.state_id = a.state_id AND c.lang_code = ?s", $lang_code);
    $joins[] = db_quote("LEFT JOIN ?:country_descriptions as d ON d.code = a.country_code AND d.lang_code = ?s", $lang_code);

    $condition = ' WHERE 1';

    if (!empty($params['only_avail'])) {
        $condition .= db_quote(" AND a.status = ?s", 'A');
    }
    if (!empty($params['active_state'])) {
        $condition .= db_quote(" AND s.status = ?s", 'A');
    }

    if (!empty($params['q'])) {
        $condition .= db_quote(" AND b.code LIKE ?l", '%' . $params['q'] . '%');
    }

    if (!empty($params['country_code'])) {
        $condition .= db_quote(" AND a.country_code = ?s", $params['country_code']);
        if ($params['state_code'] != 'null' && !empty($params['state_code'])) {
            $condition .= db_quote(" AND a.state_code = ?s", $params['state_code']);
        }
    }

    $sorting = "ORDER BY d.country,c.state,b.code";

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT count(*) FROM ?:ec_cities as a $condition");
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $cities = db_get_array("SELECT " . implode(', ', $fields) . " FROM ?:ec_cities as a " . implode(' ', $joins) ."$condition $group $sorting $limit");
    return array($cities, $params);
}


function fn_get_cities_tree($lang_code = CART_LANGUAGE)
{
    $cities = db_get_array("SELECT state_code, ?:ec_cities.city_id, ?:ec_cities_descriptions.code ,country_code FROM ?:ec_cities LEFT JOIN ?:ec_cities_descriptions ON ?:ec_cities.city_id = ?:ec_cities_descriptions.city_id AND lang_code = ?s WHERE 1", $lang_code);
    $data = array();
    foreach($cities as $val) {
        $data[$val['country_code']][$val['state_code']][] = array(
            'code' => $val['code'],
            'city_id' => $val['city_id']
        ); 
    }
    return $data;
}

function fn_get_city_name($city_code,$state_code,$country_code,$lang_code = DESCR_SL) {
    $join = db_quote(" LEFT JOIN ?:ec_cities_descriptions as b ON a.city_id = b.city_id AND b.lang_code = ?s", $lang_code);
    return db_get_field("SELECT b.code FROM ?:ec_cities as a ?p WHERE country_code = ?s AND state_code = ?s AND city_code = ?s",$join , $country_code,$state_code,$city_code);
}

function fn_get_city_name_by_id($city_id, $lang_code = CART_LANGUAGE) {
    $join = db_quote(" LEFT JOIN ?:ec_cities_descriptions as b ON a.city_id = b.city_id AND b.lang_code = ?s", $lang_code);
    return db_get_field("SELECT b.code FROM ?:ec_cities as a ?p WHERE a.city_id = ?i",$join, $city_id);
}


function fn_get_ec_cities($request_data) {
    $lang_code = CART_LANGUAGE;
    $join = db_quote(" LEFT JOIN ?:ec_cities_descriptions as b ON a.city_id = b.city_id AND b.lang_code = ?s", $lang_code);
    $cities= db_get_array("SELECT a.city_id,b.code FROM ?:ec_cities as a ?p WHERE country_code = ?s AND state_code IN (?a)",$join, $request_data['country_code'],$request_data['state_code']);
    return $cities;
}

function fn_get_city_by_state($state,$country) {
    $lang_code = CART_LANGUAGE;

    $join = db_quote(" LEFT JOIN ?:ec_cities_descriptions as b ON a.city_id = b.city_id AND b.lang_code = ?s", $lang_code);
    $cities= db_get_fields("SELECT a.city_id FROM ?:ec_cities  as a ?p WHERE country_code = ?s AND state_code = ?s",$join , $country,$state);
    return $cities;
}

function fn_get_city_id_by_state($city, $state, $country){
    $lang_code = CART_LANGUAGE;
    $join = db_quote(" LEFT JOIN ?:ec_cities_descriptions as b ON a.city_id = b.city_id AND b.lang_code = ?s", $lang_code);
    return db_get_field("SELECT a.city_id FROM ?:ec_cities as a $join WHERE country_code = ?s AND state_code = ?s AND b.code = ?s",$country,$state, $city);
}

function fn_destination_ec_get_cities($lang_code = CART_LANGUAGE){
    list($_cities) = fn_get_cities(array( 'active_state'=> true, 'only_avail' => true), 0, $lang_code);
    $cities = array();
    foreach ($_cities as $_city) {
        $cities[$_city['city_id']] = $_city['country'] . ': ' . $_city['state'] . ': ' .$_city['code'];
    }

    return $cities;
}

function fn_ec_city_district_update_destination_pre(&$data, $destination_id, $lang_code = CART_LANGUAGE){
    if (!empty($data['cities'])) {
        $data['cities'] = implode("\n", $data['cities']);
    }
}

function fn_get_districts($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    $fields = $joins = array();
    $condition = $sorting = $limit = $group = '';

    $fields = array(
        'a.district_id',
        'a.state_code',
        'a.country_code',
        'a.city_id',
        'a.status',
        'b.code',
    );

    $joins[] = db_quote("LEFT JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s", $lang_code);

    if (!empty($params['get_all'])) {

        $fields[] = 'm.code as city';
        $fields[] = 'c.state';
        $fields[] = 'd.country';

        $joins[] = db_quote("LEFT JOIN ?:ec_cities as n ON n.city_id = a.city_id");
        $joins[] = db_quote("LEFT JOIN ?:states as s ON s.code = a.state_code");
        $joins[] = db_quote("LEFT JOIN ?:ec_cities_descriptions as m ON n.city_id = m.city_id AND m.lang_code = ?s", $lang_code);

        $joins[] = db_quote("LEFT JOIN ?:state_descriptions as c ON c.state_id = s.state_id AND c.lang_code = ?s", $lang_code);
        $joins[] = db_quote("LEFT JOIN ?:country_descriptions as d ON d.code = a.country_code AND d.lang_code = ?s", $lang_code);
    }
    

    $condition = ' WHERE 1';

    if (!empty($params['only_avail'])) {
        $condition .= db_quote(" AND a.status = ?s", 'A');
    }
    if (!empty($params['active_state'])) {
        $condition .= db_quote(" AND s.status = ?s", 'A');
    }

    if (!empty($params['q'])) {
        $condition .= db_quote(" AND b.code LIKE ?l", '%' . $params['q'] . '%');
    }

    if (!empty($params['country_code'])) {
        $condition .= db_quote(" AND a.country_code = ?s", $params['country_code']);
    }
    if (!empty($params['state_code'])) {
        $condition .= db_quote(" AND a.state_code = ?s", $params['state_code']);
    }
    if (!empty($params['city_id'])) {
        $condition .= db_quote(" AND a.city_id = ?s", $params['city_id']);
    }

    $sorting = "ORDER BY b.code";

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT count(*) FROM ?:ec_district as a $condition");
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $districts = db_get_array(
        "SELECT " . implode(', ', $fields) . " FROM ?:ec_district as a " .
        implode(' ', $joins) .
        "$condition $group $sorting $limit"
        );

    return array($districts, $params);
}

function fn_get_districts_tree($lang_code = CART_LANGUAGE){
    $condition = db_quote(" AND s.status = ?s", 'A');
    $joins = db_quote(" LEFT JOIN ?:states as s ON s.code =  ?:ec_district.state_code ");
    $joins .= db_quote(" LEFT JOIN ?:ec_district_descriptions ON ?:ec_district.district_id = ?:ec_district_descriptions.district_id ");
    $joins .= db_quote(" LEFT JOIN ?:ec_cities ON ?:ec_cities.city_id = ?:ec_district.city_id");
    $districts = db_get_array("SELECT DISTINCT(?:ec_district.district_id), ?:ec_district.country_code, ?:ec_district.state_code, ?:ec_district.city_id,?:ec_cities.city_id as city_code, ?:ec_district_descriptions.code FROM ?:ec_district $joins WHERE 1 AND ?:ec_district_descriptions.lang_code = ?s $condition ORDER BY ?:ec_district.district_id ASC", $lang_code);
    $data = array();
    foreach($districts as $val) {
        $data[$val['country_code']][$val['state_code']][$val['city_code']][] = array(
            'code' => $val['code'],
            'district_id' => $val['district_id']
        ); 
    }
    return $data;
}
function fn_get_destrict_id_by_city($district, $city_id, $lang_code = CART_LANGUAGE){
    return db_get_field("SELECT a.district_id FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.city_id = ?i AND b.code = ?s", $lang_code, $city_id, $district);
}
function fn_destination_ec_get_districts($lang_code){
    list($_districts) = fn_get_districts(array('get_all' => true, 'active_state'=> true, 'only_avail' => true), 0, $lang_code);
    $districts = array();
    foreach ($_districts as $_district) {
        $districts[$_district['district_id']] = $_district['country'] . ': ' . $_district['state'] . ': ' .$_district['code'] . ': ' . $_district['code'];
    }

    return $districts;
}

function fn_ec_city_district_update_destination_post(&$data, $destination_id, $lang_code){

    if (!empty($data['districts'])) {
        $_data = array(
            'destination_id' => $destination_id
        );
        $_data['element_type'] = 'X';
        foreach ($data['districts'] as $key => $value) {
            $value = trim($value);
            if (!empty($value)) {
                $_data['element'] = $value;
                db_query("INSERT INTO ?:destination_elements ?e", $_data);
            }
        }
    }
}

function fn_ec_city_district_get_available_destination_post(&$location, &$result, &$concur_destinations){

    $result = false;
    $concur_destinations = array();
    
    $country = !empty($location['country']) ? $location['country'] : '';
    $state = !empty($location['state']) ? $location['state'] : '';
    $zipcode = !empty($location['zipcode']) ? $location['zipcode'] : '';
    $city_id = !empty($location['city']) ? $location['city'] : '';
    $district_id = !empty($location['district']) ? $location['district'] : '';
    $address = !empty($location['address']) ? $location['address'] : '';

    if (!empty($country)) {

        $state_id = fn_get_state_id($state, $country);

        // $city_id = fn_get_city_id_by_state($city, $state, $country);
        // $district_id = fn_get_destrict_id_by_city($district, $city_id);
        $condition = '';
        if (AREA == 'C') {
            $condition .= fn_get_localizations_condition('localization');
            if (!empty($condition)) {
                $condition = db_quote('OR (1 ?p)', $condition);
            }           
        }
        $__dests = db_get_array("SELECT a.* FROM ?:destination_elements as a LEFT JOIN ?:destinations as b ON b.destination_id = a.destination_id WHERE b.status = 'A' ?p", $condition);
        $destinations = array();
        foreach ($__dests as $k => $v) {
            $destinations[$v['destination_id']][$v['element_type']][] = $v['element'];
        }

        $concur_destinations = array();
        foreach ($destinations as $dest_id => $elm_types) {
            // Significance level. The more significance level means the most amount of coincidences
            $significance = 0;
            $dest_countries = !empty($elm_types['C']) ? $elm_types['C'] : array();
            foreach ($elm_types as $elm_type => $elms) {
                // Check country
                if ($elm_type == 'C') {
                    $suitable = fn_check_element($elms, $country);
                    if ($suitable == false) {
                        break;
                    }

                    $significance += 1 * (1 / count($elms));
                }

                // Check state
                if ($elm_type == 'S') {
                    // if country is in destanation_countries and it haven't got states,
                    // we suppose that destanation cover all country
                    if (!in_array($country, $dest_countries) || fn_get_country_states($country)) {
                        $suitable = fn_check_element($elms, $state_id);
                        if ($suitable == false) {
                            break;
                        }
                    } else {
                        $suitable = true;
                    }
                    $significance += 2 * (1 / count($elms));
                }
                // Check city
                if ($elm_type == 'T') {
                    $suitable = fn_check_element($elms, $city_id, true);
                    if ($suitable == false) {
                        break;
                    }
                    $significance += 3 * (1 / count($elms));
                }

                // Check district
                if ($elm_type == 'X') {
                    $suitable = fn_check_element($elms, $district_id, true);
                    if ($suitable == false) {
                        break;
                    }
                    $significance += 3.5* (1 / count($elms));
                }

                // Check zipcode
                if ($elm_type == 'Z') {
                    $suitable = fn_check_element($elms, $zipcode, true);
                    if ($suitable == false) {
                        break;
                    }
                    $significance += 4 * (1 / count($elms));
                }

                // Check address
                if ($elm_type == 'A') {
                    $suitable = fn_check_element($elms, $address, true);
                    if ($suitable == false) {
                        break;
                    }
                    $significance += 5 * (1 / count($elms));
                }
            }

            $significance = number_format($significance, 4, '.', '');

            if ($suitable == true) {
                $concur_destinations[$significance][] = $dest_id;
            }
        }

        if (!empty($concur_destinations)) {
            ksort($concur_destinations, SORT_NUMERIC);
            $concur_destinations = array_pop($concur_destinations);

            $result = reset($concur_destinations);
        }
        // if (defined("DEVELOPMENT")){
        //     fn_print_r($result);
        // }
        // fn_print_r($result);

    }
}

function fn_ec_city_district_get_states_pre(&$params,  $items_per_page, $lang_code, $default_params){
    if (AREA == 'C' || Registry::get('runtime.controller')  == 'destinations') {
        $params['only_avail'] = true;
    }
}

function fn_ec_city_district_get_order_info(&$order, $additional_data){
    // if (!empty($order['s_city']) && is_numeric($order['s_city'])) {
    //     $order['s_city_descr'] = fn_get_city_name_by_id($order['s_city']);
    // }
    // if (!empty($order['s_district']) && is_numeric($order['s_district'])) {
    //     $order['s_district_descr'] = db_get_field("SELECT b.code FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.district_id = ?i ", CART_LANGUAGE, $order['s_district']);
    // }
    // if (!empty($order['b_city']) && is_numeric($order['b_city'])) {
    //     $order['b_city_descr'] = fn_get_city_name_by_id($order['b_city']);
    // }
    // if (!empty($order['b_district']) && is_numeric($order['b_district'])) {
    //     $order['b_district_descr'] = db_get_field("SELECT b.code FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.district_id = ?i ", CART_LANGUAGE, $order['b_district']);
    // }
    // if (!empty($order['user_data']['s_city']) && is_numeric($order['user_data']['s_city'])) {
    //     $order['user_data']['s_city_descr'] = fn_get_city_name_by_id($order['user_data']['s_city']);
    // }
    // if (!empty($order['user_data']['s_district']) && is_numeric($order['user_data']['s_district'])) {
    //     $order['user_data']['s_district_descr'] = db_get_field("SELECT b.code FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.district_id = ?i ", CART_LANGUAGE, $order['user_data']['s_district']);
    // }
    // if (!empty($order['user_data']['b_city']) && is_numeric($order['user_data']['b_city'])) {
    //     $order['user_data']['b_city_descr'] = fn_get_city_name_by_id($order['user_data']['b_city']);
    // }
    // if (!empty($order['user_data']['b_district']) && is_numeric($order['user_data']['b_district'])) {
    //     $order['user_data']['b_district_descr'] = db_get_field("SELECT b.code FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.district_id = ?i ", CART_LANGUAGE, $order['user_data']['b_district']);
    // }
    // if (empty($order['b_district']) && !empty($order['s_district'])){
    //     $order['b_district_descr'] = $order['s_district_descr'];
    //     $order['b_district'] = $order['s_district'];
    // }
    $order = fn_ec_get_city_district_descr($order);
}
/**
 * Allows to initialize user data fields.
 *
 * @param array $exclude    List of excluded fields.
 * @param array $user_data  User data.
 */
function fn_ec_city_district_fill_user_fields($exclude, &$user_data){
    // if (!empty($user_data['s_city']) && is_numeric($user_data['s_city'])) {
    //     $user_data['s_city_descr'] = fn_get_city_name_by_id($user_data['s_city']);
    // }
    // if (!empty($user_data['s_district']) && is_numeric($user_data['s_district'])) {
    //     $user_data['s_district_descr'] = db_get_field("SELECT b.code FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.district_id = ?i ", CART_LANGUAGE, $user_data['s_district']);
    // }
    // if (!empty($user_data['b_city']) && is_numeric($user_data['b_city'])) {
    //     $user_data['b_city_descr'] = fn_get_city_name_by_id($user_data['b_city']);
    // }
    // if (!empty($user_data['b_district']) && is_numeric($user_data['b_district'])) {
    //     $user_data['b_district_descr'] = db_get_field("SELECT b.code FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.district_id = ?i ", CART_LANGUAGE, $user_data['b_district']);
    // }
    $user_data = fn_ec_get_city_district_descr($user_data);
}
/**
 * Actions after getting user data
 *
 * @param int    $user_id     User identifier
 * @param int    $get_profile Gets profile with user or not
 * @param int    $profile_id  Profile identifier to get
 * @param array  $user_data   User data
 */
function fn_ec_city_district_get_user_info($user_id, $get_profile, $profile_id, &$user_data){
    if ($user_data) {
        // if (!empty($user_data['s_city']) && is_numeric($user_data['s_city'])) {
        //     $user_data['s_city_descr'] = fn_get_city_name_by_id($user_data['s_city']);
        // }
        // if (!empty($user_data['s_district']) && is_numeric($user_data['s_district'])) {
        //     $user_data['s_district_descr'] = db_get_field("SELECT b.code FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.district_id = ?i ", CART_LANGUAGE, $user_data['s_district']);
        // }
        // if (!empty($user_data['b_city']) && is_numeric($user_data['b_city'])) {
        //     $user_data['b_city_descr'] = fn_get_city_name_by_id($user_data['b_city']);
        // }
        // if (!empty($user_data['b_district']) && is_numeric($user_data['b_district'])) {
        //     $user_data['b_district_descr'] = db_get_field("SELECT b.code FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.district_id = ?i ", CART_LANGUAGE, $user_data['b_district']);
        // }
        $user_data = fn_ec_get_city_district_descr($user_data);
    }
}

function fn_ec_get_city_district_descr($data, $lang_code = CART_LANGUAGE){
    if (!empty($data['s_city']) && is_numeric($data['s_city'])) {
        $data['s_city_descr'] = fn_get_city_name_by_id($data['s_city']);
    }
    if (!empty($data['s_district']) && is_numeric($data['s_district'])) {
        $data['s_district_descr'] = db_get_field("SELECT b.code FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.district_id = ?i ", CART_LANGUAGE, $data['s_district']);
    }
    if (!empty($data['b_city']) && is_numeric($data['b_city'])) {
        $data['b_city_descr'] = fn_get_city_name_by_id($data['b_city']);
    }
    if (!empty($data['b_district']) && is_numeric($data['b_district'])) {
        $data['b_district_descr'] = db_get_field("SELECT b.code FROM ?:ec_district as a INNER JOIN ?:ec_district_descriptions as b ON a.district_id = b.district_id AND b.lang_code = ?s WHERE a.district_id = ?i ", CART_LANGUAGE, $data['b_district']);
    }

    return $data;
}