<?php

use Tygh\Registry;

define('DEVELOPMENT', true);
define('DEBUG_MODE', true);

error_reporting(E_ERROR);

//ini_set('display_errors', 1);
if (!defined('BOOTSTRAP')) {
    die('Access denied');
}
$start = microtime(true);
if (class_exists('Memcached')) {
    $mc = new Memcached();
    $mc->addServer("localhost", 11211);
    $status = $mc->getStats();
    if ($status === false) {
        $mc = false;
    }
} else {
    $mc = false;
}
$company_id = (isset($_REQUEST['company_id']) && !empty($_REQUEST['company_id'])) ? $_REQUEST['company_id'] : 0;

$pinta_facebook_feed = array();
if (!$pinta_facebook_feed['setting'] = json_decode(fn_facebook_feed_get_settings_for_feed($company_id))) {

    fn_set_notification('E', __('error'), __('not_settings'));

    return array(CONTROLLER_STATUS_OK, '/');
}
$currencies = Registry::get('currencies');

$pinta_facebook_feed['languages_setting'] = (isset($_GET['lang']) && !empty($_GET['lang'])) ? $_GET['lang'] : $pinta_facebook_feed['setting']->feed_language;
$pinta_facebook_feed['currency_setting'] = (isset($_GET['cur']) && !empty($_GET['cur'])) ? $_GET['cur'] : $pinta_facebook_feed['setting']->feed_currency;
$pinta_facebook_feed['feed_maping'] = $pinta_facebook_feed['setting']->feed_maping;
$pinta_facebook_feed['color_setting'] = fn_facebook_feed_get_options_id_from_setting($pinta_facebook_feed['setting']->feed_color, $pinta_facebook_feed['feed_maping']);
$pinta_facebook_feed['size_setting'] = fn_facebook_feed_get_options_id_from_setting($pinta_facebook_feed['setting']->feed_size, $pinta_facebook_feed['feed_maping']);
$pinta_facebook_feed['pattern_setting'] = fn_facebook_feed_get_options_id_from_setting($pinta_facebook_feed['setting']->feed_pattern, $pinta_facebook_feed['feed_maping']);
$pinta_facebook_feed['material_setting'] = fn_facebook_feed_get_options_id_from_setting($pinta_facebook_feed['setting']->feed_material, $pinta_facebook_feed['feed_maping']);
$pinta_facebook_feed['upload_without_img_setting'] = $pinta_facebook_feed['setting']->feed_upload_without_img;
$pinta_facebook_feed['turn_off_categories_setting'] = $pinta_facebook_feed['setting']->feed_turn_off_categories;
$pinta_facebook_feed['mapping_title'] = $pinta_facebook_feed['setting']->feed_mapping_title;
$pinta_facebook_feed['mapping_description'] = $pinta_facebook_feed['setting']->feed_mapping_description;
$pinta_facebook_feed['mapping_image_link'] = $pinta_facebook_feed['setting']->feed_mapping_image_link;
$pinta_facebook_feed['mapping_brand'] = $pinta_facebook_feed['setting']->feed_mapping_brand;
$pinta_facebook_feed['mapping_availability'] = $pinta_facebook_feed['setting']->feed_mapping_availability;
$pinta_facebook_feed['companies_setting'] = $pinta_facebook_feed['setting']->feed_companies;
$pinta_facebook_feed['companies_description'] = fn_get_company_data($pinta_facebook_feed['companies_setting'], $pinta_facebook_feed['languages_setting'], $extra = array());
$pinta_facebook_feed['companies_name'] = fn_get_companies($params = array('company_id' => $pinta_facebook_feed['companies_setting']), $auth, 0);
$pinta_facebook_feed['options_combination'] = array_merge($pinta_facebook_feed['color_setting'], $pinta_facebook_feed['size_setting'], $pinta_facebook_feed['pattern_setting'], $pinta_facebook_feed['material_setting']);
$coefficient = $currencies[$pinta_facebook_feed['currency_setting']]['coefficient'];
$xml = new DOMDocument("1.0", "UTF-8");

$rss = $xml->createElement("rss");
$rss_node = $xml->appendChild($rss);
$rss_node->setAttribute("version", "2.0");

$rss_node->setAttribute("xmlns:g", "http://base.google.com/ns/1.0");

$channel = $xml->createElement("channel");
$channel_node = $rss_node->appendChild($channel);

if (isset($pinta_facebook_feed['companies_name'][0][0]["company"])) {
    $channel_node->appendChild($xml->createElement("title", /*$pinta_facebook_feed['companies_name'][0][0]["company"]." ".*/date("Y-m-d H:i:s")));
}
$channel_node->appendChild($xml->createElement("link", fn_get_storefront_url()));
if (isset($pinta_facebook_feed['companies_description']["company_description"])) {
    $channel_node->appendChild($xml->createElement("description", $pinta_facebook_feed['companies_description']["company_description"]));
}
if ($pinta_facebook_feed['turn_off_categories_setting']) {
    $products = fn_get_products(array(), 0, $pinta_facebook_feed['languages_setting']);
} else {
    $products = fn_get_products_in_categoryes_facebook($pinta_facebook_feed['companies_setting']);
}
//$products = [
//    0 => [
//        [
//            'product_id' => 6284,
//            'main_category' => 124
//        ]
//    ]
//];
$array_load = [];
if(isset($_REQUEST['limit']) && ((int)$_REQUEST['limit'] > 0)){
    $products[0] = array_splice($products[0],100,(int)$_REQUEST['limit']);

}

foreach ($products[0] as $product) {
//    if($product['product_id'] != 11441){
//        continue;
//    }
    if (in_array($product['product_id'], $array_load)) {
        continue;
    } else {
        $array_load[] = $product['product_id'];
    }
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        $siteLink = "https";
    else
        $siteLink = "http";
    $siteLink .= "://";
    $siteLink .= $_SERVER['HTTP_HOST'];

    $a = '';
    $params = array('product_id' => $product['product_id']);
    $brands = "";

    $product_data = fn_facebook_feed_memcached(
        'product_data',
        $product['product_id'],
        $pinta_facebook_feed['languages_setting'],
        'fn_get_product_data',
        $mc,
        $auth,
        $company_id
    );


    $product_options = fn_facebook_feed_memcached(
        'product_options',
        $product['product_id'],
        $pinta_facebook_feed['languages_setting'],
        'fn_get_product_options',
        $mc,
        $auth,
        $company_id
    );
    $category_data = fn_facebook_feed_memcached(
        'category_data',
        $product['main_category'],
        $pinta_facebook_feed['languages_setting'],
        'fn_get_category_data',
        $mc,
        $auth,
        $company_id
    );
    $google_category = fn_facebook_feed_memcached(
        'google_category',
        $product['main_category'],
        $pinta_facebook_feed['languages_setting'],
        'fn_get_google_category_',
        $mc,
        $auth,
        $company_id
    );
    if ($pinta_facebook_feed['feed_maping'] == 'options') {
        $product_inventories = fn_get_product_options($product['product_id'], $pinta_facebook_feed['languages_setting']);
        if (isset($product_inventories) && !empty($product_inventories)) {
            foreach ($product_inventories as $product_inventory) {
                if (in_array($product_inventory['option_id'], $pinta_facebook_feed['options_combination'])) {
                    $a = !empty($product_inventory['variants']) ? $product_inventory['variants'] : array();
                }
            }
        }
    } else {
        $product_inventories = fn_facebook_feed_get_product_feature($product['product_id'], $pinta_facebook_feed['languages_setting']);
        if (isset($product_inventories) && !empty($product_inventories)) {
            foreach ($product_inventories as $product_inventory) {
                if (in_array($product_inventory['feature_id'], $pinta_facebook_feed['options_combination'])) {
                    $a = $product_inventory;
                }
            }
        }
    }

    if (isset($product_data["product_features"]) && !empty($product_data["product_features"])) {
        foreach ($product_data["product_features"] as $product_features) {
            if ($product_features['description'] == "Brand") {
                $brands = isset($product_features["variant"])? $product_features["variant"] : '';
            }
            if ($product_features['feature_id'] == 59) {
                $variant_id = isset($product_features['variant_id']) ? $product_features['variant_id'] : false;
                if ($variant_id) {
                    $variant = (isset($product_features['variants']) && isset($product_features['variants'][$variant_id])) ? $product_features['variants'][$variant_id] : false;
                    if ($variant) {
                        $brands = $variant["variant"];

                    }
                }
            }
        }
    }
    if ($pinta_facebook_feed['turn_off_categories_setting'] ||
        (!$pinta_facebook_feed['turn_off_categories_setting'] && $google_category)) {
        $product = $product_data;
        $prod_title = mb_substr($product['product'], 0, 149);
        $desc_text = mb_substr(trim(strip_tags(html_entity_decode(htmlspecialchars_decode($product_data["full_description"])))), 0, 998);
        if (empty($desc_text)) {
            $desc_text = $prod_title;
        }

//        if(isset($product_data['discounts'])){
//            $product_data['ec_retail_price'] = $product_data['taxed_price'];
//        } elseif(isset($product_data['list_discount'])) {
//
//        }
//         fn_print_die($product_data['ec_retail_price']);die;

        if (isset($a) && ($a != null) && count($a) >= 1) {
            $product_id = '';

            $price = $product['ec_retail_price'];
            $price_fixed = round($product_data['price'] / $coefficient, 2);
            $list_price_fixed = round($product_data['ec_retail_price'] / $coefficient, 2);


            $item = $channel_node->appendChild($xml->createElement("item"));

            $id = $item->appendChild($xml->createElement("g:id", $product['product_id']));
            $description = $item->appendChild($xml->createElement("g:description"));
            $contents = $xml->createCDATASection( escapeXml( $desc_text));
            $description->appendChild($contents);
            $link = $item->appendChild($xml->createElement("g:link", fn_url('products.view?product_id=' . $product['product_id'], 'C')));
            $brand = $item->appendChild($xml->createElement("g:brand", ($brands) ? (string)$brands : ""));
            $image = (isset($product_data["main_pair"]["detailed"]["image_path"])) ? $product_data["main_pair"]["detailed"]["image_path"] : ($siteLink . '/images/cscart_logo_600x600.png');
            $image_value = mappingField($image, 'mapping_image_link', $product, $product_data, $pinta_facebook_feed);
            $image_link = $item->appendChild($xml->createElement("g:image_link", $image_value));            if (isset($product_data['image_pairs']) && !empty($product_data['image_pairs'])) {
                $images_link = [];
                foreach ($product_data['image_pairs'] as $image) {
                    if (isset($image['detailed']) && !empty($image['detailed'])) {
                        if (isset($image['detailed']['https_image_path']) && !empty($image['detailed']['https_image_path'])) {
                            $images_link[] = $image['detailed']['https_image_path'];
                        }
                    }

                }
                if(!empty($images_link)){
                    $item->appendChild($xml->createElement("g:additional_image_link", implode(',',$images_link)));
                }
            }

//            $product_type = $item->appendChild($xml->createElement("g:product_type"));
//            $contents = $xml->createCDATASection(htmlentities($category_data['category']));
//            $product_type->appendChild($contents);
            $condition = $item->appendChild($xml->createElement("g:condition", 'new'));
            $availability = $item->appendChild($xml->createElement("g:availability", ($product['amount'] > 0) ? 'in stock' : 'out of stock'));

            if (!$pinta_facebook_feed['turn_off_categories_setting']) {
              //  $category = $item->appendChild($xml->createElement("g:google_product_category", fn_get_google_category_name(($google_category["google_category_id"] != 0) ? $google_category["google_category_id"] : $google_category["google_main_category_id"])));
            }

            $custom_label_0 = $item->appendChild($xml->createElement("g:mpn", (isset($product_inventory["product_code"])) ? $product_inventory["product_code"] : $product['product_code']));

            if (($pinta_facebook_feed['feed_maping'] == 'options')) {
                foreach ($product_inventories as $product_inventory) {

                    foreach ($product_inventory["variants"] as $combination) {
                        if (in_array($combination['option_id'], $pinta_facebook_feed['color_setting'])) {
                            $color[] = $combination['variant_name'];
                        }
                        if (in_array($combination['option_id'], $pinta_facebook_feed['size_setting'])) {
                            $size[] = $combination['variant_name'];
                        }
                        if (in_array($combination['option_id'], $pinta_facebook_feed['material_setting'])) {
                            $material[] = $combination['variant_name'];
                        }
                        if (in_array($combination['option_id'], $pinta_facebook_feed['pattern_setting'])) {
                            $pattern[] = $combination['variant_name'];
                        }

                    }
                }
            } else {
                foreach ($product_inventories as $product_inventory) {
                    if (in_array($product_inventory['feature_id'], $pinta_facebook_feed['color_setting'])) {
                        $color[] = $product_inventory['variant'];
                    }
                    if (in_array($product_inventory['feature_id'], $pinta_facebook_feed['size_setting'])) {
                        $size[] = $product_inventory['variant'];
                    }
                    if (in_array($product_inventory['feature_id'], $pinta_facebook_feed['material_setting'])) {
                        $material[] = $product_inventory['variant'];
                    }
                    if (in_array($product_inventory['feature_id'], $pinta_facebook_feed['pattern_setting'])) {
                        $pattern[] = $product_inventory['variant'];
                    }
                }

            }
            $title = $item->appendChild($xml->createElement("g:title"));
//                if (ctype_upper(str_replace(' ', '', $prod))) {
//                    $prod = ucfirst(strtolower($prod));
//                }
//                $prod = mb_strimwidth($prod, 0, 100, '...');
            $title_text = $xml->createCDATASection( escapeXml( $prod_title));
            $title->appendChild($title_text);

            if (floatval($list_price_fixed) < floatval($price_fixed) || $list_price_fixed === $price_fixed ) {
                $price = $item->appendChild($xml->createElement("g:price", round($product['ec_retail_price'] / $coefficient, 2) . ' ' . $pinta_facebook_feed['currency_setting']));
//            } elseif(floatval($list_price_fixed) > floatval($price_fixed) && (floatval($product['price']) === floatval($product['list_price']))){
//                $price = $item->appendChild($xml->createElement("g:price", round($product['list_price'] / $coefficient, 2) . ' ' . $pinta_facebook_feed['currency_setting']));
            } else {
                $item->appendChild($xml->createElement("g:sale_price",$price_fixed  . ' ' . $pinta_facebook_feed['currency_setting']));
                $item->appendChild($xml->createElement("g:price",   $list_price_fixed. ' ' . $pinta_facebook_feed['currency_setting']));
            }

            if (!empty($color)) {
                $item->appendChild($xml->createElement("g:color", implode(',', $color)));
            }
            if (!empty($size)) {
                $item->appendChild($xml->createElement("g:size", implode(',', $size)));
            }
            if (!empty($material)) {
                $item->appendChild($xml->createElement("g:material", implode(',', $material)));
            }
            if (!empty($pattern)) {
                $item->appendChild($xml->createElement("g:pattern", implode(',', $pattern)));
            }
            generationCategoriesStr($item,$xml,$category_data,$pinta_facebook_feed['languages_setting']);

                generationCategoriesFields($item,$xml,$category_data,$pinta_facebook_feed['languages_setting']);

        } else {
            if (isset($product_data["main_pair"]["detailed"]["image_path"]) || $pinta_facebook_feed['upload_without_img_setting']) {
                $item = $channel_node->appendChild($xml->createElement("item"));

                $id = $item->appendChild($xml->createElement("g:id", $product['product_id']));
                $product = $product_data;

                $price_fixed = round($product_data['price'] / $coefficient, 2);
                $list_price_fixed = round($product_data['ec_retail_price'] / $coefficient, 2);

                $title = $item->appendChild($xml->createElement("g:title"));
//                $prod = $product['product'];
//                if (ctype_upper(str_replace(' ', '', $prod))) {
//                    $prod = ucfirst(strtolower($prod));
//                }
//                $prod = mb_strimwidth($prod, 0, 100, '...');
                $desc_text = escapeXml($desc_text);

                $title_text = $xml->createCDATASection((escapeXml(mappingField($prod_title, 'mapping_title', $product, $product_data, $pinta_facebook_feed))));
                $title->appendChild($title_text);
                $description = $item->appendChild($xml->createElement("g:description"));

                $contents = $xml->createCDATASection(
                    (  mappingField($desc_text, 'mapping_description', $product, $product_data, $pinta_facebook_feed))
                );
                $description->appendChild($contents);

                $link_text = fn_url('products.view?product_id=' . $product['product_id'], 'C');
                $link = $item->appendChild($xml->createElement("g:link"));
                $link_text_cdata = $xml->createTextNode($link_text);
                $link->appendChild($link_text_cdata);

                if (isset($product['company_name'])) {
                    $brand_value = mappingField((!empty($brands) ? $brands : $product['company_name']), 'mapping_brand', $product, $product_data, $pinta_facebook_feed);
                    $brand = $item->appendChild($xml->createElement("g:brand"));
                    $brand_text = $xml->createTextNode($brand_value);
                    $brand->appendChild($brand_text);
                    // $brand = $item->appendChild($xml->createElement("g:brand", $brand_value));
                }
                $image = (isset($product_data["main_pair"]["detailed"]["image_path"])) ? $product_data["main_pair"]["detailed"]["image_path"] : ($siteLink . '/images/cscart_logo_600x600.png');
                $image_value = mappingField($image, 'mapping_image_link', $product, $product_data, $pinta_facebook_feed);
                $image_link = $item->appendChild($xml->createElement("g:image_link", $image_value));
                if (isset($product_data['image_pairs']) && !empty($product_data['image_pairs'])) {
                    $images_link = [];
                    foreach ($product_data['image_pairs'] as $image) {
                        if (isset($image['detailed']) && !empty($image['detailed'])) {
                            if (isset($image['detailed']['https_image_path']) && !empty($image['detailed']['https_image_path'])) {
                                $images_link[] = $image['detailed']['https_image_path'];
                            }
                        }

                    }
                    if(!empty($images_link)){
                        $item->appendChild($xml->createElement("g:additional_image_link", implode(',',$images_link)));
                    }
                }
//                $product_type = $item->appendChild($xml->createElement("g:product_type"));
//                $contents = $xml->createCDATASection(htmlentities($category_data['category']));
//                $product_type->appendChild($contents);

                $condition = $item->appendChild($xml->createElement("g:condition", 'new'));
                $availability_value = mappingField(($product['amount'] > 0) ? 'in stock' : 'out of stock', 'mapping_availability', $product, $product_data, $pinta_facebook_feed);
                $availability = $item->appendChild($xml->createElement("g:availability", $availability_value));
//fn_print_die($product_data);die;
                if (floatval($list_price_fixed) < floatval($price_fixed) || $list_price_fixed === $price_fixed ) {
                    $price = $item->appendChild($xml->createElement("g:price", round($product['ec_retail_price'] / $coefficient, 2) . ' ' . $pinta_facebook_feed['currency_setting']));

//                } elseif(floatval($list_price_fixed) > floatval($price_fixed) && (floatval($product['price']) === floatval($product['list_price']))){
//                    $price = $item->appendChild($xml->createElement("g:price", round($product['list_price'] / $coefficient, 2) . ' ' . $pinta_facebook_feed['currency_setting']));

                } else {
                    $item->appendChild($xml->createElement("g:sale_price",$price_fixed  . ' ' . $pinta_facebook_feed['currency_setting']));
                    $item->appendChild($xml->createElement("g:price",   $list_price_fixed. ' ' . $pinta_facebook_feed['currency_setting']));
                }

                if (!$pinta_facebook_feed['turn_off_categories_setting']) {
                  //  $category = $item->appendChild($xml->createElement("g:google_product_category", fn_get_google_category_name(($google_category["google_category_id"] != 0) ? $google_category["google_category_id"] : $google_category["google_main_category_id"])));
                }
                generationCategoriesStr($item,$xml,$category_data,$pinta_facebook_feed['languages_setting']);
                generationCategoriesFields($item,$xml,$category_data,$pinta_facebook_feed['languages_setting']);

            }
        }
       // fn_print_die($product_data);
    }
} 
function escapeXml(string $text)
{
    $newStr1 = removeBs($text);
    $newStr2 = str_replace(["\0", "\1", "\2", "\3", "\4", "\5", "\6", "\7", "\10", "\13", "\14", "\16", "\17", "\20",
        "\21", "\22", "\23", "\24", "\25", "\26", "\27", "\30", "\31", "\32", "\33", "\34", "\35", "\36", "\37",],
        '', $newStr1);
    $newStr3 = strip_tags($newStr2);
    return $newStr3;
}
 function removeBs($Str)
{
    mb_regex_encoding('UTF-8');
    mb_internal_encoding("UTF-8");
    $StrArr = preg_split('/(?<!^)(?!$)/u', $Str);
        //$StrArr = explode('',$Str);
        $NewStr = '';
        foreach ($StrArr as $Char) {
            $CharNo = ord($Char);
            if ($CharNo == 163) {
                $NewStr .= $Char;
                continue;
            }
            if ($CharNo > 31 && $CharNo < 127) {
                $NewStr .= $Char;
            }

        }
    return $NewStr;
}
function generationCategoriesStr(&$item,&$xml,$category_data,$lang_id)
{

    if(empty($category_data)){
        return;
    }
    if(isset($category_data['id_path']) && !empty($category_data['id_path'])){
        $explode = explode('/',$category_data['id_path']);
        $str = [];
        foreach ($explode as $key => $element){
            $data = fn_get_category_data($element, $lang_id, $field_list = '', false);
            if(!empty($data)){
                if(isset($data['category']) && !empty($data['category'])) {
                    $str[] = $data['category'];
                }
            }
        }

        if(!empty($str)){
            $item->appendChild($xml->createElement("g:google_product_category", htmlspecialchars(implode(" > ",$str))));
        }
    }
    return;

}
function generationCategoriesFields(&$item,&$xml,$category_data,$lang_id)
{
    if(empty($category_data)){
        return;
    }
   if(isset($category_data['id_path']) && !empty($category_data['id_path'])){
       $explode = explode('/',$category_data['id_path']);
       foreach ($explode as $key => $element){
           $data = fn_get_category_data($element, $lang_id, $field_list = '', false);

           if(!empty($data)){
               if(isset($data['category']) && !empty($data['category'])) {
                   if($key < 5) {
                       $item->appendChild($xml->createElement("g:custom_label_".$key, htmlspecialchars($data['category'])));
                   }
               }
           }
       }
   }
   return;

}

function mappingField($value, $key, $product, $product_data, $pinta_facebook_feed)
{
    if (isset($value) && !empty($value) && !is_null($value) && ($value != '')) {
        return $value;
    } else {
        if (isset($pinta_facebook_feed[$key]) && !empty($pinta_facebook_feed[$key]) &&
            !is_null($pinta_facebook_feed[$key]) && ($pinta_facebook_feed[$key] != '')) {
            switch ($pinta_facebook_feed[$key]) {
                case 'Title':
                    $prod = $product['product'];
                    if (ctype_upper(str_replace(' ', '', $prod))) {
                        $prod = ucfirst(strtolower($prod));
                    }
                    $prod = mb_strimwidth($prod, 0, 100, '...');
                    return $prod;
                    break;
                case 'Description':
                    return strip_tags(
                        html_entity_decode(
                            htmlspecialchars_decode(
                                $product_data["full_description"]
                            )
                        )
                    );
                    break;
                case 'Image link':
                    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                        $siteLink = "https";
                    else
                        $siteLink = "http";
                    $siteLink .= "://";
                    $siteLink .= $_SERVER['HTTP_HOST'];
                    return (isset($product_data["main_pair"]["detailed"]["image_path"])) ? $product_data["main_pair"]["detailed"]["image_path"] : ($siteLink . '/design/backend/media/images/cart_logo.png');
                    break;
                case 'Brand':
                    if (isset($product['company_name'])) {
                        return $product['company_name'];
                    } else {
                        return '';
                    }
                    break;
                case 'Availability':
                    return ($product['amount'] > 0) ? 'in stock' : 'out of stock';
                    break;
            }
        } else {
            return $value;
        }
    }
}

$xml->formatOutput = true;
if(isset($_REQUEST['action'])){
    $action = $_GET["action"];
} elseif(isset($mode) && !empty($mode)){
    $action = $mode;
} else {
    $action = 'save';
}
switch ($action) {
    case 'generate':
        header('Content-Type: application/xml');
        echo $xml->saveXML();
//        file_put_contents(
//            'timeout.log',
//           date("Y-m-d H:i:s"). ' Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.'.PHP_EOL,
//            FILE_APPEND |LOCK_EX
//        );
        exit;
        break;
    case 'save':
        $company_id = (isset($_GET["company_id"]) && !is_null($_GET["company_id"])) ? ($_GET["company_id"]) : null;

        if (!is_null($company_id)) {
            $saved_file_name = 'pinta_feed_company_' . $company_id . '.xml';
        } else {
            $saved_file_name = 'pinta_feed.xml';
        }

        $xml->save(DIR_ROOT . '/' . $saved_file_name);
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            $link = "https";
        else
            $link = "http";
        $link .= "://";
        $link .= $_SERVER['HTTP_HOST'];
        $link .= '/' . $saved_file_name;
        header('Location: ' . $link);
        exit;
        break;
}
