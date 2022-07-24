<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$_REQUEST['destination_id'] = empty($_REQUEST['destination_id']) ? 0 : $_REQUEST['destination_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') return;

if ($mode === 'update') {
    $destination_data = Tygh::$app['view']->getTemplateVars('destination_data');
       
    $destination_data['cities'] = db_get_hash_single_array("SELECT a.city_id as city_id, CONCAT(d.country, ': ',s.state,': ', b.code) as city FROM ?:ec_cities as a LEFT JOIN ?:ec_cities_descriptions as b ON a.city_id = b.city_id AND b.lang_code = ?s LEFT JOIN ?:destination_elements as c ON c.element_type = 'T' AND c.element = a.city_id LEFT JOIN ?:country_descriptions as d ON d.code = a.country_code AND d.lang_code = ?s LEFT JOIN ?:state_descriptions as s ON s.state_id = a.state_id AND s.lang_code = ?s WHERE c.destination_id = ?i", array('city_id', 'city'), DESCR_SL, DESCR_SL, DESCR_SL, $_REQUEST['destination_id']);
    $all_cities = fn_destination_ec_get_cities(DESCR_SL);
    if (!empty($destination_data['cities'])) {
        foreach($destination_data['cities'] as $k => $val){
            if(array_key_exists($k, $all_cities)){
                unset($all_cities[$k]);
            }
        }
    }
    $destination_data['districts'] = db_get_hash_single_array("SELECT x.district_id as district_name, CONCAT(d.country, ': ',s.state,': ',z.code,': ', x.code) as district FROM ?:ec_district as a LEFT JOIN ?:ec_district_descriptions as x ON x.district_id = a.district_id AND x.lang_code = ?s LEFT JOIN ?:destination_elements as c ON c.element_type = 'X' AND c.element = x.district_id LEFT JOIN ?:country_descriptions as d ON d.code = a.country_code AND d.lang_code = ?s LEFT JOIN ?:states as r ON r.code = a.state_code LEFT JOIN ?:state_descriptions as s ON s.state_id = r.state_id AND s.lang_code = ?s LEFT JOIN ?:ec_cities as y ON y.city_id = a.city_id LEFT JOIN ?:ec_cities_descriptions as z ON y.city_id = z.city_id AND z.lang_code = ?s WHERE c.destination_id = ?i", array('district_name', 'district'),DESCR_SL, DESCR_SL, DESCR_SL, DESCR_SL, $_REQUEST['destination_id']);
    $all_districts = fn_destination_ec_get_districts(DESCR_SL);
    if (!empty($destination_data['districts'])) {
        foreach($destination_data['districts'] as $k => $val){
            if(array_key_exists($k, $all_districts)){
                unset($all_districts[$k]);
            }
        }
    }
    Tygh::$app['view']->assign('cities', $all_cities);
    Tygh::$app['view']->assign('districts', $all_districts);
    Tygh::$app['view']->assign('destination_data', $destination_data);

} elseif ($mode === 'add') {
    $_all_cities = $all_cities = fn_destination_ec_get_cities(DESCR_SL);
    Tygh::$app['view']->assign('cities', $all_cities);
    $_all_descticts = $all_districts = fn_destination_ec_get_districts(DESCR_SL);
    Tygh::$app['view']->assign('districts', $all_districts);
}