<?php
/*****************************************************************************
*                                                                            *
*                   All rights reserved! eComLabs LLC                        *
* http://www.ecom-labs.com/about-us/ecom-labs-modules-license-agreement.html *
*                                                                            *
*****************************************************************************/

use Tygh\Registry;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'resize_images') {
        $product_ids = db_get_fields("SELECT DISTINCT(object_id) FROM ?:images_links WHERE object_type = ?s AND detailed_id != ?i", 'product', 0);
        if (!empty($product_ids)) {
            foreach ($product_ids as $product_id) {
                $main_pair = fn_get_image_pairs($product_id, 'product', 'M', false, true, DESCR_SL);
                $additional_pairs = fn_get_image_pairs($product_id, 'product', 'A', false, true, DESCR_SL);

                $pairs = array_merge(array($main_pair), $additional_pairs);

                foreach ($pairs as $pair) {
                    if (!empty($pair['detailed'])) {
                        $width_limit = Registry::get('addons.ecl_detailed_images.image_width');
                        $height_limit = Registry::get('addons.ecl_detailed_images.image_height');

                        if ((!empty($width_limit) && $pair['detailed']['image_x'] > $width_limit) || (!empty($height_limit) && $pair['detailed']['image_y'] > $height_limit)) {
                            list($cont, $format) = fn_resize_image($pair['detailed']['absolute_path'], $width_limit, $height_limit, Registry::get('settings.Thumbnails.thumbnail_background_color'));

                            if (!empty($cont)) {
                                fn_put_contents($pair['detailed']['absolute_path'], $cont);

                                list($pair['detailed']['image_x'], $pair['detailed']['image_y'], $mime_type) = fn_get_image_size($pair['detailed']['absolute_path']);

                                db_query("UPDATE ?:images SET image_x = ?i, image_y = ?i WHERE image_id = ?i", $pair['detailed']['image_x'], $pair['detailed']['image_y'], $pair['detailed_id']);
                            }

                            fn_echo(' . ');
                        }
                    }
                }
            }
        }

        fn_set_notification('N', __('notice'), __('done'));

        return array(CONTROLLER_STATUS_REDIRECT, 'products.manage');
    }
    return;
}