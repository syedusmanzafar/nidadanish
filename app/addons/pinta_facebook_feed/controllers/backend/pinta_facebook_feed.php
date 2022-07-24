<?php

use Tygh\Registry;
use Tygh\Languages\Languages;
use Tygh\BlockManager\Block;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {
    if ($mode == 'autocomplete') {
        if (defined('AJAX_REQUEST') ) {
            if(isset($_REQUEST['q']) && !empty($_REQUEST['q'])){
                $and = ' AND google_category_name LIKE "%'.(string)htmlentities($_REQUEST['q']).'%"';
            } else {
                $and = '';
            }
            $results = db_get_array('SELECT * FROM ?:facebook_feed_google_category WHERE google_category_parent = ?i '.$and, $_REQUEST['google_main_category_id']);
            foreach ($results as $result) {
                $list[] = array(
                    'value' => html_entity_decode($result['google_category_name'], ENT_QUOTES, 'UTF-8'),
                    'label' => html_entity_decode($result['google_category_name'], ENT_QUOTES, 'UTF-8'),
                    'google_category_id' => $result['google_category_id'],
                );
            }
            Registry::get('ajax')->assign('autocomplete', $list);
            exit();
        }
    }
    if ($mode == 'clear_category') {
        $company_id = isset($_REQUEST['company_id'])? $_REQUEST['company_id'] : 0;

        $result = db_query('DELETE FROM ?:facebook_feed_category WHERE category_id = ?i AND company_id = ?i', $_REQUEST['category_id'],$company_id);
        Registry::get('ajax')->assign('clear_category', $result);
        exit();
    }

    if ($mode == 'save_main_google_category') {

        $company_id = isset($_REQUEST['company_id'])? $_REQUEST['company_id'] : 0;

        db_query('DELETE FROM ?:facebook_feed_category WHERE category_id = ?i AND company_id = ?i', $_REQUEST['category_id'], $company_id);

        if ($_REQUEST['google_main_category_id'] != 0) {

            $data = array('google_main_category_id'=> $_REQUEST['google_main_category_id'], 'category_id'=>$_REQUEST['category_id'],'company_id'=> $company_id );
           $result = db_query('INSERT INTO ?:facebook_feed_category ?e ', $data);
            Registry::get('ajax')->assign('clear_category', $result);
        }
        exit();
    }
    if ($mode == 'memcached_flush') {
        if (class_exists('Memcached')) {
            $mc = new Memcached();
            $mc->addServer("127.0.0.1", 11211);
            $status = $mc->getStats();
            if ($status !== false) {
                $mc->flush();
            }
        }
        return array(CONTROLLER_STATUS_OK, 'pinta_facebook_feed.manage');
    }
    if ($mode == 'save_google_category') {
        $company_id = isset($_REQUEST['company_id'])? $_REQUEST['company_id'] : 0;

        $result = db_query('UPDATE ?:facebook_feed_category SET google_category_id = ?i WHERE category_id = ?i AND company_id= ?i', $_REQUEST['google_category_id'], $_REQUEST['category_id'],$company_id);
        Registry::get('ajax')->assign('clear_category', $result);

        exit();
    }

    if ($mode == 'update') {
        $data = array('setting'=> json_encode($_REQUEST));
        $company_id = isset($_REQUEST['feed_companies'])? $_REQUEST['feed_companies'] : 0;

        fn_facebook_feed_update_settings_for_feed($company_id,$data);
        return array(CONTROLLER_STATUS_OK, 'pinta_facebook_feed.manage&company_id='.$company_id);
    }

    if ($mode == 'feed_update' ) {

        if ($_REQUEST["feed_update"]) {
            if (file($_REQUEST["feed_update"])) {
                $file = file($_REQUEST["feed_update"]);
                foreach ($file as $f) {
                    if (!strstr($f, '#')){
                        $buffer = explode("-", $f);
                        $google_category_name = htmlentities(trim($buffer[1]));
                        $google_category_id = trim($buffer[0]);
                        $google_category_parent = 0;
                        if (count($buffer1= explode(">", $buffer[1]))>1){
                            $google_category_parent = db_get_field("SELECT google_category_id FROM ?:facebook_feed_google_category WHERE google_category_name = ?s", htmlentities(trim($buffer1[0])));
                        }

                        $data = array('google_category_id'=> $google_category_id, 'google_category_name'=>$google_category_name, 'google_category_parent'=>$google_category_parent);
                        db_query('INSERT INTO ?:facebook_feed_google_category ?e', $data);


                    }
                }
                fn_set_notification('N', __('congratulations'), __('feed_updated'));
                return array(CONTROLLER_STATUS_OK, 'pinta_facebook_feed.manage');

            } else {
                fn_set_notification('E', __('error'), __('wrong_file'));

                return array(CONTROLLER_STATUS_OK, 'pinta_facebook_feed.manage');
            }

        } else {
            fn_set_notification('E', __('error'), __('input_not_empty'));

            return array(CONTROLLER_STATUS_OK, 'pinta_facebook_feed.manage');
        }


    }

}

if ($mode == 'manage') {
    $pinta_facebook_feed = array();
    $params = array();
    $company_id = (isset($_REQUEST['company_id']) && !empty($_REQUEST['company_id']))? $_REQUEST['company_id'] : 0;
    if ($pinta_facebook_feed['setting'] = json_decode(fn_facebook_feed_get_settings_for_feed($company_id))) {
        $pinta_facebook_feed['languages_setting'] = $pinta_facebook_feed['setting']->feed_language;
        $pinta_facebook_feed['currency_setting'] = $pinta_facebook_feed['setting']->feed_currency;
        $pinta_facebook_feed['color_setting'] = $pinta_facebook_feed['setting']->feed_color;
        $pinta_facebook_feed['size_setting'] = $pinta_facebook_feed['setting']->feed_size;
        $pinta_facebook_feed['pattern_setting'] = $pinta_facebook_feed['setting']->feed_pattern;
        $pinta_facebook_feed['material_setting'] = $pinta_facebook_feed['setting']->feed_material;
        $pinta_facebook_feed['upload_without_img_setting'] = $pinta_facebook_feed['setting']->feed_upload_without_img;
        $pinta_facebook_feed['turn_off_categories_setting'] = $pinta_facebook_feed['setting']->feed_turn_off_categories;
        $pinta_facebook_feed['companies_setting'] = $pinta_facebook_feed['setting']->feed_companies;

        $pinta_facebook_feed['feed_maping'] = $pinta_facebook_feed['setting']->feed_maping;
        $pinta_facebook_feed['mapping_title'] = $pinta_facebook_feed['setting']->feed_mapping_title;
        $pinta_facebook_feed['mapping_description'] = $pinta_facebook_feed['setting']->feed_mapping_description;
        $pinta_facebook_feed['mapping_image_link'] = $pinta_facebook_feed['setting']->feed_mapping_image_link;
        $pinta_facebook_feed['mapping_brand'] = $pinta_facebook_feed['setting']->feed_mapping_brand;
        $pinta_facebook_feed['mapping_availability'] = $pinta_facebook_feed['setting']->feed_mapping_availability;
        $pinta_facebook_feed['google_main_category'] = fn_get_google_main_category($company_id);
    } else {
        $pinta_facebook_feed['feed_maping'] = "options";
        $pinta_facebook_feed['google_main_category'] = fn_get_google_main_category(0);
        $pinta_facebook_feed['companies_setting'] = $company_id;
    }
    //var_dump( $pinta_facebook_feed['google_main_category']);die;
    $pinta_facebook_feed['languages'] = Languages::getAll();
    $pinta_facebook_feed['currency'] = fn_get_currencies_list();
    $pinta_facebook_feed['companies'] = fn_get_companies($params, $auth, 0);
    if(!isset($pinta_facebook_feed['feed_maping']) || $pinta_facebook_feed['feed_maping'] == 'options'){
        $pinta_facebook_feed['options'] = fn_get_product_global_options();
    } else {
        $pinta_facebook_feed['options'][0] = get_facebook_feed_get_all_features($pinta_facebook_feed['languages_setting']);
    }
    $pinta_facebook_feed['google_main_categories'] = fn_get_google_main_categories();

   //fn_print_die( $pinta_facebook_feed['google_main_categories']);
    $pinta_facebook_feed['mapping_fields'] = [
        'Title',
        'Description',
        'Image link',
        'Brand',
        'Availability'
    ];
    if (class_exists('Memcached')) {
        $mc = new Memcached();
        $mc->addServer("127.0.0.1", 11211);
        $status = $mc->getStats();
        if ($status === false) {
            $memcached = false;
        } else {
            $memcached = true;
        }
    } else {
        $memcached = false;
    }
    if(!isset($_GET['company_id']) || ($_GET['company_id'] == 0) ){
        $pinta_facebook_feed['vendor'] = false;
    }else{
        $pinta_facebook_feed['vendor'] = true;
        $company_name = strtolower(
            str_replace(' ', '_', Registry::get('runtime.company_data.company'))
        );
        $pinta_facebook_feed['company_name'] = $company_name;
    }

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        $link = "https";
    else
        $link = "http";
    $link .= "://";
    $link .= $_SERVER['HTTP_HOST'];
    $pinta_facebook_feed['root_link'] = fn_url("pinta_facebook_feed",'C');
    $pinta_facebook_feed['current_location'] = Registry::get('config.current_location');

    list($categories_tree) = fn_get_categories($params, DESCR_SL);
    $category = fn_get_list_category($categories_tree);
    $company_catigories = fn_facebook_feed_get_company_categories($company_id);
    Tygh::$app['view']->assign('company_cat', $company_catigories);

    Tygh::$app['view']->assign('categories', $category);
    Tygh::$app['view']->assign('categories_tree', $categories_tree);
    Tygh::$app['view']->assign('memcached', $memcached);

    Tygh::$app['view']->assign('pinta_facebook_feed', $pinta_facebook_feed);

}


