<?php


use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'delete') {
        $children_ids = db_get_fields('SELECT order_id FROM ?:orders WHERE parent_order_id = ?i ORDER BY order_id ASC', $_REQUEST['order_id']);
        
        if (!empty($children_ids)) {
            foreach ($children_ids as $id) {
                db_query("DELETE FROM ?:order_data WHERE order_id = ?i", $id);
                db_query("DELETE FROM ?:order_details WHERE order_id = ?i", $id);
                db_query("DELETE FROM ?:orders WHERE order_id = ?i", $id);
                db_query("DELETE FROM ?:product_file_ekeys WHERE order_id = ?i", $id);
                db_query("DELETE FROM ?:profile_fields_data WHERE object_id = ?i AND object_type='O'", $id);
                db_query("DELETE FROM ?:order_docs WHERE order_id = ?i", $id);

                $shipment_ids = db_get_fields('SELECT shipment_id FROM ?:shipment_items WHERE order_id = ?i GROUP BY shipment_id', $id);
                fn_delete_shipments($shipment_ids);
            }
            
        }      
    }
}