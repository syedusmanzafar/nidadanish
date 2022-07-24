<?php



use Tygh\Enum\SiteArea;
use Tygh\Enum\UserTypes;
use Tygh\Enum\YesNo;
use Tygh\Tygh;
use Tygh\Registry;
use Tygh\Enum\OrderDataTypes;
use Tygh\Notifications\EventIdProviders\OrderProvider;


if (!defined('BOOTSTRAP')) { die('Access denied'); }


function fn_sc_single_notice_checkout_place_orders_pre_route($cart, $auth, $params){
	//Registry::set('sc_processor_place_order',true);
}

function fn_sc_single_notice_finish_payment($order_id, $pp_response, $force_notification){
    if($pp_response['order_status'] == 'O'){
        Registry::set('sc_processor_place_order',true);
    }

}        
function fn_sc_single_notice_cp_change_order_status_pre_notice(&$allow, $status_to, $status_from, $force_notification, $place_order, $order_info, $edp_data){
    fn_sc_send_default_notice($status_to, $status_from, $force_notification, $place_order, $order_info, $edp_data);
    $place_order_processor = Registry::ifGet('sc_processor_place_order', false);


//fn_print_r($order_info['parent_order_id']);

//fn_print_r('$place_order_processor',$place_order_processor);


//fn_print_r('$place_order',$place_order);

    if ($order_info['parent_order_id'] != 0){
        $allow = false;
        if ($status_to !== STATUS_INCOMPLETED_ORDER) {

            if ($place_order_processor) {
                $count = db_get_field("SELECT COUNT(*) FROM ?:orders WHERE parent_order_id = ?i AND status = ?s ", $order_info['parent_order_id'], STATUS_INCOMPLETED_ORDER);

                if ($count == 1){
                    fn_sc_send_single_notice($order_info['parent_order_id'], false, false, $place_order_processor);
                }
            } elseif ($place_order) {
                $cart = Tygh::$app['session']['cart'];
                $count = count($cart['product_groups']);
                $check_count = db_get_field("SELECT COUNT(*) FROM ?:orders WHERE parent_order_id = ?i",$order_info['parent_order_id']);


//united order fix shipping
                if ($check_count == $count + 1){
                    fn_sc_send_single_notice($order_info['parent_order_id'],false,false,$place_order);
                }
            } else {
                fn_sc_send_single_notice($order_info['parent_order_id'], $status_to,$order_info['order_id'], false);
            }
        }
    } else {
        $allow = true;
    }
}
function fn_sc_send_single_notice($parent_order_id, $status_to, $order_id, $place_order){
    $orders = db_get_array("SELECT order_id FROM ?:orders WHERE parent_order_id = ?i",$parent_order_id);
	
	
	$ordersss = db_get_array("SELECT order_id ,parent_order_id,status FROM ?:orders ORDER BY order_id DESC  LIMIT 5");
	
	
	//fn_print_r('$parent_order_id',$parent_order_id);
	 //fn_print_r('fn_sc_send_single_notice',$orders);
	 
	 //fn_print_r($ordersss);
	 
	 
	// exit('dfdfd');
	
	
	//fn_print_r($orders);
	
    foreach ($orders as $key => &$value) {
        $value['info'] = fn_sc_get_order_info($value['order_id']);
        $value['status_desr'] = reset(fn_get_statuses(STATUSES_ORDER,$value['info']['status']));
        $value['company'] = db_get_field("SELECT company FROM ?:companies WHERE company_id = ?i",$value['info']['company_id']);
		
		$sc_united_use_vendor = db_get_field("SELECT sc_united_use_vendor FROM ?:companies WHERE company_id = ?i",$value['info']['company_id']);
		
		//fn_print_r('$sc_united_use_vendor',$sc_united_use_vendor);
		//
		if($sc_united_use_vendor =="Y"){
			$value['company'] = __('an_united_vendor_shipping');
			
			
			$value['info']['subtotal'] = $value['info']['total'];
			
		}
		
        if ($order_id == $value['order_id']) {
            $value['status_to'] = $status_to;
            $value['sc_order_id'] = $order_id;
        }
    }
    $orders['firstname']    = isset($orders[0]['info']['firstname'])?$orders[0]['info']['firstname']:'';
    $orders['lastname']    =  isset($orders[0]['info']['lastname'])?$orders[0]['info']['lastname']:'';   
    $orders['email']    =     isset($orders[0]['info']['email'])?$orders[0]['info']['email']:''; 

    if ($place_order) {
        $orders['place_order'] = $place_order;
    }
    $event_dispatcher = Tygh::$app['event.dispatcher'];
    $res = $event_dispatcher->dispatch('sc_single_notice.mail', [
        'sc_single_notice_data' => $orders
    ]);
	
	if($parent_order_id == 5928 ){
		
		fn_print_r($orders);
		//fn_print_die($res);
	}
	
	//fn_print_r('event dispatch',$res);
}

function fn_sc_send_default_notice($status_to, $status_from, $force_notification, $place_order, $order_info, $edp_data){
    if ($status_to !== STATUS_PARENT_ORDER && $status_to !== STATUS_INCOMPLETED_ORDER) {
        $status_id = strtolower($status_to);

        /** @var \Tygh\Notifications\EventDispatcher $event_dispatcher */
        $event_dispatcher = Tygh::$app['event.dispatcher'];

        /** @var \Tygh\Notifications\Settings\Factory $notification_settings_factory */
        $notification_settings_factory = Tygh::$app['event.notification_settings.factory'];

        $notification_rules = Tygh::$app['event.notification_settings.factory']->create([
            UserTypes::CUSTOMER => false,
            UserTypes::ADMIN    => true,
            UserTypes::VENDOR   => true,
        ]);

        $event_dispatcher->dispatch(
            "order.status_changed.{$status_id}",
            ['order_info' => $order_info],
            $notification_rules,
            new OrderProvider($order_info)
        );
        if ($edp_data) {
            $notification_rules = fn_get_edp_notification_rules($force_notification ?: [], $edp_data);
            $event_dispatcher->dispatch(
                'order.edp',
                ['order_info' => $order_info, 'edp_data' => $edp_data],
                $notification_rules,
                new OrderProvider($order_info, $edp_data)
            );
        }
    }
}
//get_order_info without company condition
function fn_sc_get_order_info($order_id, $native_language = false, $format_info = true, $get_edp_files = false, $skip_static_values = false, $lang_code = CART_LANGUAGE)
{
    if (!empty($order_id)) {

        //$condition = fn_get_company_condition('?:orders.company_id');
        $condition ='';

        $order = db_get_row("SELECT * FROM ?:orders WHERE ?:orders.order_id = ?i $condition", $order_id);

        if (!empty($order)) {
            $lang_code = ($native_language == true) ? $order['lang_code'] : $lang_code;

            if (isset($order['ip_address'])) {
                $order['ip_address'] = fn_ip_from_db($order['ip_address']);
            }

            $order['discount'] = floatval($order['discount']);
            $order['subtotal_discount'] = floatval($order['subtotal_discount']);
            $order['payment_surcharge'] = floatval($order['payment_surcharge']);
            $order['payment_method'] = fn_get_payment_method_data($order['payment_id'], $lang_code);

            // Get additional profile fields
            $additional_fields = db_get_hash_single_array(
                "SELECT field_id, value FROM ?:profile_fields_data "
                . "WHERE object_id = ?i AND object_type = 'O'",
                array('field_id', 'value'), $order_id
            );
            $order['fields'] = $additional_fields;

            $order['products'] = db_get_hash_array(
                "SELECT ?:order_details.*, ?:product_descriptions.product, ?:products.status as product_status FROM ?:order_details "
                . "LEFT JOIN ?:product_descriptions ON ?:order_details.product_id = ?:product_descriptions.product_id AND ?:product_descriptions.lang_code = ?s "
                . "LEFT JOIN ?:products ON ?:order_details.product_id = ?:products.product_id "
                . "WHERE ?:order_details.order_id = ?i ORDER BY ?:product_descriptions.product",
                'item_id', $lang_code, $order_id
            );

            $order['promotions'] = unserialize($order['promotions']);
            if (!empty($order['promotions'])) { // collect additional data
                $params = array (
                    'promotion_id' => array_keys($order['promotions']),
                );
                list($promotions) = fn_get_promotions($params);
                foreach ($promotions as $pr_id => $p) {
                    $order['promotions'][$pr_id]['name'] = $p['name'];
                    $order['promotions'][$pr_id]['short_description'] = $p['short_description'];
                }
            }

            // Get additional data
            $additional_data = db_get_hash_single_array("SELECT type, data FROM ?:order_data WHERE order_id = ?i", array('type', 'data'), $order_id);

            $order['taxes'] = array();
            $order['tax_subtotal'] = 0;
            $order['display_shipping_cost'] = $order['shipping_cost'];

            // Replace country, state and title values with their descriptions
            $order_company_id = isset($order['company_id']) ? $order['company_id'] : ''; // company_id will be rewritten by user field, so need to save it.
            fn_add_user_data_descriptions($order, $lang_code);
            $order['company_id'] = $order_company_id;

            $order['need_shipping'] = false;
            $deps = array();

            // Get shipping information
            if (!empty($additional_data[OrderDataTypes::SHIPPING])) {
                $order['shipping'] = unserialize($additional_data[OrderDataTypes::SHIPPING]);

                foreach ($order['shipping'] as $key => $v) {
                    $shipping_id = isset($v['shipping_id']) ? $v['shipping_id'] : 0;
                    $shipping_name = fn_get_shipping_name($shipping_id, $lang_code);
                    if ($shipping_name) {
                        $order['shipping'][$key]['shipping'] = $shipping_name;
                    }
                }
            }

            if (!fn_allowed_for('ULTIMATE:FREE')) {
                // Get shipments common information
                $order['shipment_ids'] = db_get_fields(
                    "SELECT sh.shipment_id FROM ?:shipments AS sh LEFT JOIN ?:shipment_items AS s_items ON (sh.shipment_id = s_items.shipment_id) "
                    . "WHERE s_items.order_id = ?i GROUP BY s_items.shipment_id",
                    $order_id
                );

                $_products = db_get_array("SELECT item_id, SUM(amount) AS amount FROM ?:shipment_items WHERE order_id = ?i GROUP BY item_id", $order_id);
                $shipped_products = array();

                if (!empty($_products)) {
                    foreach ($_products as $_product) {
                        $shipped_products[$_product['item_id']] = $_product['amount'];
                    }
                }
                unset($_products);

            }
            foreach ($order['products'] as $k => $v) {
                //Check for product existance
                if (empty($v['product'])) {
                    $order['products'][$k]['deleted_product'] = true;
                } else {
                    $order['products'][$k]['deleted_product'] = false;
                }

                $order['products'][$k]['discount'] = 0;

                $v['extra'] = @unserialize($v['extra']);
                if ($order['products'][$k]['deleted_product'] == true && !empty($v['extra']['product'])) {
                    $order['products'][$k]['product'] = $v['extra']['product'];
                } else {
                    $order['products'][$k]['product'] = fn_get_product_name($v['product_id'], $lang_code);
                }

                $order['products'][$k]['company_id'] = empty($v['extra']['company_id']) ? 0 : $v['extra']['company_id'];

                if (!empty($v['extra']['discount']) && floatval($v['extra']['discount'])) {
                    $order['products'][$k]['discount'] = $v['extra']['discount'];
                    $order['use_discount'] = true;
                }

                if (!empty($v['extra']['promotions'])) {
                    $order['products'][$k]['promotions'] = $v['extra']['promotions'];
                }

                if (isset($v['extra']['base_price'])) {
                    $order['products'][$k]['base_price'] = floatval($v['extra']['base_price']);
                } else {
                    $order['products'][$k]['base_price'] = $v['price'];
                }
                $order['products'][$k]['original_price'] = $order['products'][$k]['base_price'];

                // Form hash key for this product
                $order['products'][$k]['cart_id'] = $v['item_id'];
                $deps['P_'.$order['products'][$k]['cart_id']] = $k;

                // Unserialize and collect product options information
                if (!empty($v['extra']['product_options'])) {
                    if ($format_info == true) {

                        $stored_options = $v['extra']['product_options_value'];
                        $source_options = fn_get_selected_product_options_info($v['extra']['product_options'], $lang_code);
                        $option_id_key  = 'option_id';

                        $order['products'][$k]['product_options'] = fn_array_merge(
                            fn_array_combine(array_column($stored_options, $option_id_key), $stored_options),
                            fn_array_combine(array_column($source_options, $option_id_key), $source_options),
                            true
                        );

                    }

                    $product_options_value = ($skip_static_values == false && !empty($v['extra']['product_options_value'])) ? $v['extra']['product_options_value'] : array();

                    if (empty($v['extra']['stored_price']) || (!empty($v['extra']['stored_price']) && $v['extra']['stored_price'] != 'Y')) { // apply modifiers if this is not the custom price
                        $order['products'][$k]['original_price'] = fn_apply_options_modifiers($v['extra']['product_options'], $order['products'][$k]['base_price'], 'P', $product_options_value, array('product_data' => $v));
                    }
                }

                $order['products'][$k]['extra'] = $v['extra'];
                $order['products'][$k]['tax_value'] = 0;
                $order['products'][$k]['display_subtotal'] = $order['products'][$k]['subtotal'] = ($v['price'] * $v['amount']);

                // Get information about edp
                if ($get_edp_files == true && $order['products'][$k]['extra']['is_edp'] == 'Y') {
                    $order['products'][$k]['files'] = db_get_array(
                        "SELECT ?:product_files.file_id, ?:product_files.activation_type, ?:product_files.max_downloads, "
                        . "?:product_file_descriptions.file_name, ?:product_file_ekeys.active, ?:product_file_ekeys.downloads, "
                        . "?:product_file_ekeys.ekey, ?:product_file_ekeys.ttl FROM ?:product_files "
                        . "LEFT JOIN ?:product_file_descriptions ON ?:product_file_descriptions.file_id = ?:product_files.file_id "
                        . "AND ?:product_file_descriptions.lang_code = ?s "
                        . "LEFT JOIN ?:product_file_ekeys ON ?:product_file_ekeys.file_id = ?:product_files.file_id "
                        . "AND ?:product_file_ekeys.order_id = ?i WHERE ?:product_files.product_id = ?i AND ?:product_files.status = ?s",
                        $lang_code, $order_id, $v['product_id'], 'A'
                    );
                }

                // Get shipments information
                // If current edition is FREE, we still need to check shipments accessibility (need to display promotion link)
                if (isset($shipped_products[$k])) {
                    $order['products'][$k]['shipped_amount'] = $shipped_products[$k];
                    $order['products'][$k]['shipment_amount'] = $v['amount'] - $shipped_products[$k];

                } else {
                    $order['products'][$k]['shipped_amount'] = 0;
                    $order['products'][$k]['shipment_amount'] = $v['amount'];
                }

                if ($order['products'][$k]['shipped_amount'] < $order['products'][$k]['amount']) {
                    if (!empty($order['shipping'])) {
                        $group_key = empty($v['extra']['group_key']) ? 0 : $v['extra']['group_key'];
                        $order['shipping'][$group_key]['need_shipment'] = true;
                    } else {
                        $order['need_shipment'] = true;
                    }
                }

                // Check if the order needs the shipping method
                if (!($v['extra']['is_edp'] == 'Y' && (!isset($v['extra']['edp_shipping']) || $v['extra']['edp_shipping'] != 'Y'))) {
                    $order['need_shipping'] = true;
                }

                // Adds flag that defines if product page is available
                $order['products'][$k]['is_accessible'] = fn_is_accessible_product($v);

                $order['products'][$k]['product_url'] = fn_url('products.view?product_id=' . $v['product_id'] . '&storefront_id=' . $order['storefront_id'], SiteArea::STOREFRONT, 'current', $lang_code);

                //fn_set_hook('get_order_items_info_post', $order, $v, $k);
            }

            // Unserialize and collect taxes information
            if (!empty($additional_data[OrderDataTypes::TAXES])) {
                $order['taxes'] = unserialize($additional_data[OrderDataTypes::TAXES]);
                if (is_array($order['taxes'])) {
                    foreach ($order['taxes'] as $tax_id => $tax_data) {

                        $actual_tax_name = fn_get_tax_name($tax_id, $lang_code);
                        $order['taxes'][$tax_id]['description'] = empty($actual_tax_name) ? $tax_data['description'] : $actual_tax_name;

                        if (Registry::get('settings.Checkout.tax_calculation') == 'unit_price') {
                            foreach ($tax_data['applies'] as $_id => $value) {
                                if (preg_match('/^P_/', $_id) && isset($deps[$_id])) {
                                    $order['products'][$deps[$_id]]['tax_value'] += $value;
                                    if ($tax_data['price_includes_tax'] != 'Y') {
                                        $order['products'][$deps[$_id]]['subtotal'] += $value;
                                        $order['products'][$deps[$_id]]['display_subtotal'] += (Registry::get('settings.Appearance.cart_prices_w_taxes') == 'Y') ? $value : 0;
                                    }
                                }
                                if (preg_match('/^S_/', $_id)) {
                                    if ($tax_data['price_includes_tax'] != 'Y') {
                                        $order['shipping_cost'] += $value;
                                        $order['display_shipping_cost'] += (Registry::get('settings.Appearance.cart_prices_w_taxes') == 'Y') ? $value : 0;
                                    }
                                }
                            }
                        }

                        if ($tax_data['price_includes_tax'] != 'Y') {
                            $order['tax_subtotal'] += $tax_data['tax_subtotal'];
                        }
                    }
                } else {
                    $order['taxes'] = array();
                }
            }

            if (!empty($additional_data[OrderDataTypes::COUPONS])) {
                $order['coupons'] = unserialize($additional_data[OrderDataTypes::COUPONS]);
            }

            if (!empty($additional_data[OrderDataTypes::CURRENCY])) {
                $order['secondary_currency'] = unserialize($additional_data[OrderDataTypes::CURRENCY]);
            }

            if (!empty($order['issuer_id'])) {
                $order['issuer_data'] = fn_get_user_short_info($order['issuer_id']);
            }

            // Recalculate subtotal
            $order['subtotal'] = $order['display_subtotal'] = 0;
            foreach ($order['products'] as $v) {
                $order['subtotal'] += $v['subtotal'];
                $order['display_subtotal'] += $v['display_subtotal'];
            }

            // Unserialize and collect payment information
            if (!empty($additional_data[OrderDataTypes::PAYMENT])) {
                $order['payment_info'] = unserialize(fn_decrypt_text($additional_data[OrderDataTypes::PAYMENT]));
            }

            if (empty($order['payment_info']) || !is_array($order['payment_info'])) {
                $order['payment_info'] = array();
            }

            // Get product groups
            if (!empty($additional_data[OrderDataTypes::GROUPS])) {
                $order['product_groups'] = unserialize($additional_data[OrderDataTypes::GROUPS]);
            }

            $order['doc_ids'] = db_get_hash_single_array("SELECT type, doc_id FROM ?:order_docs WHERE order_id = ?i", array('type', 'doc_id'), $order_id);
        }

        //fn_set_hook('get_order_info', $order, $additional_data);

        return $order;
    }

    return false;
}