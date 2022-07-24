<?php


use Tygh\Registry;
use Tygh\Languages\Languages;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $suffix = '';
    
    if($mode == 'update') {
        $district_data = $_REQUEST['district_data'];

        // fn_print_r($_REQUEST);
        if (!empty($district_data['country_code']) && !empty($district_data['state_code']) && $district_data['state_code'] != 'null' && !empty($district_data['city_id'])) {
            if(is_array($district_data['code'])) {
                foreach($district_data['code'] as $key => $item) {
                    if(!empty($item)) {
                        $data = array(
                            'state_code' => $district_data['state_code'],
                            'country_code' => $district_data['country_code'],
                            'city_id' => $district_data['city_id'],
                            'status' => "A"
                        );
                        $district_id = db_query('INSERT INTO ?:ec_district ?e', $data);
                        if ($district_id) {
                            foreach (Languages::getAll() as $lang_code => $_v) {
                                $data = array(
                                    'district_id' => $district_id,
                                    'code' => $item,
                                    'lang_code' => $lang_code
                                );
                                db_query('INSERT INTO ?:ec_district_descriptions ?e', $data);
                            }
                        }
                    }
                }
            }
        } else {
            fn_set_notification('W',__('warning'),__('ec_country_is_missing'));
        }
    }
    
    if($mode == 'delete') {

        if(!empty($_REQUEST['district_id'])) {
            db_query('DELETE FROM ?:ec_district WHERE district_id = ?i', $_REQUEST['district_id']);
            db_query('DELETE FROM ?:ec_district_descriptions WHERE district_id = ?i', $_REQUEST['district_id']);
        }        
    }

    if($mode == 'm_delete') {

        if(!empty($_REQUEST['district_ids'])) {
            db_query('DELETE FROM ?:ec_district WHERE district_id IN (?a)', $_REQUEST['district_ids']);
            db_query('DELETE FROM ?:ec_district_descriptions WHERE district_id IN (?a)', $_REQUEST['district_ids']);
        }
    }

    if($mode == 'm_update') {
        foreach($_REQUEST['districts'] as $district_id => $district) {
            db_query('UPDATE ?:ec_district_descriptions SET ?u WHERE district_id = ?i AND lang_code = ?s', $district, $district_id, DESCR_SL);
        }
    }

    return array(CONTROLLER_STATUS_OK,'ec_district.manage'.$suffix);
}


if ($mode == 'manage') {
    
    $params = $_REQUEST;
    $cities = fn_get_cities_tree();

    if (empty($params['country_code'])) {
        $params['country_code'] = Registry::get('settings.General.default_country');
    }
    if (empty($params['state_code'])) {
        $params['state_code'] = Registry::get('settings.General.default_state');
    }
    if(empty($params['city_id']) && !empty($cities[$params['country_code']][$params['state_code']])){
        $params['city_id'] =  $cities[$params['country_code']][$params['state_code']][0]['city_id'];
    }

    $city_name = '';
    if($params['city_id']){
        $city_name = db_get_field("SELECT code FROM ?:ec_cities WHERE city_id = ?i", $params['city_id']);
    }
    list($districts, $search) = fn_get_districts($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

    Tygh::$app['view']->assign('districts', $districts);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('countries', fn_get_simple_countries(false, DESCR_SL));
    Tygh::$app['view']->assign('states', fn_get_all_states());
    Tygh::$app['view']->assign('cities', $cities);    
    Tygh::$app['view']->assign('city_name', $city_name);    

    // fn_print_die(fn_get_simple_countries(false, DESCR_SL),fn_get_all_states(), fn_get_cities_tree());
}

?>