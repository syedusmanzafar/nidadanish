<?php
/*****************************************************************************
*                                                                            *
*                   All rights reserved! eComLabs LLC                        *
* http://www.ecom-labs.com/about-us/ecom-labs-modules-license-agreement.html *
*                                                                            *
*****************************************************************************/

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_ecl_detailed_images_update_image(&$image_data, $image_id, $image_type, $images_path, &$_data, $mime_type, $is_clone)
{
    if ($image_type == 'detailed' && !empty($image_data['size'])) {
        $width_limit = Registry::get('addons.ecl_detailed_images.image_width');
        $height_limit = Registry::get('addons.ecl_detailed_images.image_height');

        if ((!empty($width_limit) && $_data['image_x'] > $width_limit) || (!empty($height_limit) && $_data['image_y'] > $height_limit)) {
            list($cont, $format) = fn_resize_image($image_data['path'], $width_limit, $height_limit, Registry::get('settings.Thumbnails.thumbnail_background_color'));

            if (!empty($cont)) {
                fn_put_contents($image_data['path'], $cont);

                $image_data['size'] = filesize($image_data['path']);

                list($image_data['image_x'], $image_data['image_y'], $mime_type) = fn_get_image_size($image_data['path']);
                $_data['image_x'] = $image_data['image_x'];
                $_data['image_y'] = $image_data['image_y'];
            }
        }
    }
}