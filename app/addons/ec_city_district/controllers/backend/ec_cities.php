<?php


use Tygh\Registry;
use Tygh\Languages\Languages;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $suffix = '';
    
    if($mode == 'update') {
        $city_data = $_REQUEST['city_data'];
        if(isset($city_data['state_code'])) {
            $state_id = db_get_field('SELECT state_id FROM ?:states WHERE code = ?s AND country_code = ?s',$city_data['state_code'],$city_data['country_code']);
            if($state_id) {
                if(is_array($city_data['code'])) {
                    foreach($city_data['code'] as $key => $item) {
                        if(!empty($item)) {
                            $data = array(
                                'state_code' => $city_data['state_code'],
                                'country_code' => $city_data['country_code'],
                                'state_id' => $state_id,
                                'status' => "A"
                            );
                            $city_id = db_query('INSERT INTO ?:ec_cities ?e', $data);
                            if ($city_id) {
                                foreach (Languages::getAll() as $lang_code => $_v) {
                                    $data = array(
                                        'city_id' => $city_id,
                                        'code' => $item,
                                        'lang_code' => $lang_code
                                    );
                                    db_query('INSERT INTO ?:ec_cities_descriptions ?e', $data);
                                }
                            }
                        }
                    }
                }
            }  else {
                fn_set_notification('W',__('warning'),__('state_is_missing'));                
            }
        } else {
            fn_set_notification('W',__('warning'),__('state_is_missing'));
        }
        $suffix = '&country_code='.$city_data['country_code'].'&state_code='.$city_data['state_code'];
    }
    
    if($mode == 'delete') {
        if(!empty($_REQUEST['city_id'])) {            
            db_query('DELETE FROM ?:ec_cities WHERE city_id = ?i', $_REQUEST['city_id']);
            db_query('DELETE FROM ?:ec_cities_descriptions WHERE city_id = ?i', $_REQUEST['city_id']);
            db_query('DELETE FROM ?:ec_district WHERE city_id = ?i', $_REQUEST['city_id']);
        }
        $suffix = '&country_code='.$_REQUEST['country_code'].'&state_code='.$_REQUEST['state_code'];
    }

    if($mode == 'm_delete') {

        if(!empty($_REQUEST['city_ids'])) {
            db_query('DELETE FROM ?:ec_cities WHERE city_id IN (?a)', $_REQUEST['city_ids']);
        }
        $suffix = '&country_code='.$_REQUEST['country_code'].'&state_code='.$_REQUEST['state_code'];
    }


    if($mode == 'm_update') {
        foreach($_REQUEST['states'] as $city_id => $city) {
            $data = array(
                'code' => $city['code'] 
            );
            db_query('UPDATE ?:ec_cities_descriptions SET ?u WHERE city_id = ?i', $data, $city_id);
        }
        $suffix = '&country_code='.$_REQUEST['country_code'].'&state_code='.$_REQUEST['state_code'];
    }

    return array(CONTROLLER_STATUS_OK,'ec_cities.manage'.$suffix);
}


if ($mode == 'manage') {

    $params = $_REQUEST;

    if (empty($params['country_code'])) {
        $params['country_code'] = Registry::get('settings.General.default_country');

        if (empty($params['state_code'])) {
            $params['state_code'] = Registry::get('settings.General.default_state');
        }
    }

    list($cities, $search) = fn_get_cities($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

    Tygh::$app['view']->assign('cities', $cities);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('countries', fn_get_simple_countries(false, DESCR_SL));
    Tygh::$app['view']->assign('states', fn_get_all_states());
    
    if(isset($params['state_code'])) {
        $list = fn_get_country_states($params['country_code']);
        if(!empty($list)) {
            $valiadate_state_as_a_option = 'Y';
        } else {
            $valiadate_state_as_a_option = 'N';            
        }
    } else {
        $valiadate_state_as_a_option = 'N';
    }

    Tygh::$app['view']->assign('allow_add_city', $valiadate_state_as_a_option);  
}

?>