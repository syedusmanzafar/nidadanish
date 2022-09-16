<?php


/**
 * Initialize snippet product table variable for gift certificate.
 *
 * @param \Tygh\Template\Snippet\Snippet                                    $snippet
 * @param \Tygh\Addons\GiftCertificates\Documents\GiftCertificate\Context   $context
 * @param \Tygh\Template\Collection                                         $variable_collection
 */
function fn_sc_full_order_init_product_tables($snippet, $context, $variable_collection)
{
    $object_factory = Tygh::$app['template.object_factory'];
    $order = $context->getOrder();



    $products = $order->data['products'];


    $config = array(
        'class' => '\Tygh\Template\Snippet\Table\TableVariable',
        'arguments' => array(
            '#context', '#snippet', '@template.renderer',
            '@template.snippet.table.column_repository',
            '@template.variable_collection_factory',
            '#items'
        ),
        'name' => 'products_table'
    );



    foreach ($products as &$item) {
        $item['display_subtotal'] = $item['subtotal'];

        if (isset($item['extra']['base_price'])) {
            $item['base_price'] = $item['extra']['base_price'];
            $item['original_price'] = $item['extra']['base_price'];
        }
    }
    unset($item);

    /*
        $variable = new \Tygh\Template\VariableProxy(
            $config,
            $context,
            $object_factory,
            array('snippet' => $snippet, 'items' => $products)
        );
    */


    $section_vendor_products = array();


    if(!empty($order->data['child_order_details_info'])){
       foreach ($order->data['child_order_details_info'] as $order_id_child => $item_order_data) {

           if(!empty($item_order_data['is_sc_united_ship_order']) && $item_order_data['is_sc_united_ship_order'] =="Y"){
               continue;
           }

           $company_name = fn_get_company_name($item_order_data['company_id']);
           $section_vendor_products[$order_id_child]['company'] = $company_name;


           $st = fn_get_status_data($item_order_data['status'],"O");

           $section_vendor_products[$order_id_child]['status_name'] = $st['description'];

           $section_vendor_products[$order_id_child]['order_id'] = $item_order_data['order_id'];


           //fn_print_die($item_order_data['products']);

           foreach ($item_order_data['products'] as &$item_d) {
               $item_d['display_subtotal'] = $item_d['subtotal'];

               if (isset($item_d['extra']['base_price'])) {
                   $item_d['base_price'] = $item_d['extra']['base_price'];
                   $item_d['original_price'] = $item_d['extra']['base_price'];
               }


               //get additional data

               $product_data = array();
               $product_data['product_options'] = array();

               if(!empty($item_d['extra']['product_options'])){
                   $product_data['product_options'] = $item_d['extra']['product_options'];
               }

               $image = fn_get_cart_product_icon($item_d['product_id'], $product_data);

               //fn_print_die($image);
           }

           $section_vendor_products[$order_id_child]['products'] = $item_order_data['products'];


           $variable = new \Tygh\Template\VariableProxy(
               $config,
               $context,
               $object_factory,
               array('snippet' => $snippet, 'items' => $item_order_data['products'])
           );


           $section_vendor_products[$order_id_child]['items'] =$variable;

           unset($item);

       }
    }



    //fn_print_die($section_vendor_products);

    $variable_collection->add('section_vendor_products', $section_vendor_products);

    //$variable_collection->add('qqq_ttt', "sdfsdfsdfsdf");
    //$variable_collection->add('products_table', $variable);

    /*
        $variable = new \Tygh\Template\VariableProxy(
            $config,
            $context,
            $object_factory,
            array('snippet' => $snippet, 'items' => $section_vendor_products)
        );
    */
}
