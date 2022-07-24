<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update'){
        // fn_print_r($_REQUEST);
        // $destination_data = $_REQUEST['destination_data'];
        // $countries = $destination_data['countries'];
        // $state_ids = $destination_data['states'];
        // $cities = $destination_data['cities'];
        // $districts = $destination_data['districts'];
        // foreach($state_ids as $key=>$state_id){
        //     if (!db_get_field("SELECT state_id FROM ?:states WHERE state_id = ?i AND country_code IN (?a) AND status = ?s", $state_id, $countries, 'A')){
        //         unset($_REQUEST['destination_data']['states'][$key]);
        //     }
        // }
        // foreach($destination_data['cities'] as $key=>$city){
        //     if (!db_get_field("SELECT city_id FROM ?:ec_cities WHERE city_id = ?i AND state_id IN (?n)", $city, $_REQUEST['destination_data']['states'])){
        //         unset($_REQUEST['destination_data']['cities'][$key]);
        //     }
        // }
        // foreach($districts as $key=>$district){
        //     if (!db_get_field("SELECT ?:ec_district.district_id FROM ?:ec_district WHERE ?:ec_district.city_id IN (?n) AND ?:ec_district.district_id = ?i", $_REQUEST['destination_data']['cities'], $district)){
        //         unset($_REQUEST['destination_data']['districts'][$key]);
        //     }
        // }

        // fn_print_die($_REQUEST); 
    }
}