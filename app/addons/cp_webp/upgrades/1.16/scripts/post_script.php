<?php

$addon_id = 'cp_webp';
$addon_scheme = Tygh\Addons\SchemesManager::clearInternalCache($addon_id);
$addon_scheme = Tygh\Addons\SchemesManager::getScheme($addon_id);

if (function_exists('fn_get_addon_settings_values')
    && function_exists('fn_get_addon_settings_vendor_values')
) {
    $setting_values = $settings_vendor_values = array();
    $settings_values = fn_get_addon_settings_values($addon_id);
    $settings_vendor_values = fn_get_addon_settings_vendor_values($addon_id);

    fn_update_addon_settings($addon_scheme, true, $settings_values, $settings_vendor_values);
} else {
    fn_update_addon_settings($addon_scheme, true);
}


$page = 0;
$img_per_page = 1000;
while ($all_imgs = db_get_array('SELECT * FROM ?:cp_web_image_list WHERE rel_image_crc = ?i ORDER BY image_crc ASC ?p', 0, db_paginate($page, $img_per_page))) {
    $page++;
    if (!empty($all_imgs)) {
        foreach($all_imgs as $img_data) {
            if (!empty($img_data['image_path'])) {
            
                $rel_image_crc = fn_cp_web_generate_crc32_field_upgrade_func($img_data['image_path']);
                
                if (!empty($rel_image_crc)) {
                    db_query("UPDATE ?:cp_web_image_list SET rel_image_crc = ?i WHERE image_crc = ?i", $rel_image_crc, $img_data['image_crc']);
                }
            }
        }
    }
}
unset($all_imgs);

function fn_cp_web_generate_crc32_field_upgrade_func($img_path)
{
    $img_rel_path = $rel_image_crc = '';
    if (!empty($img_path)) {
        if (strpos($img_path, 'thumbnails/') !== false) {
            $exploded = preg_split('/thumbnails\/[0-9]*\/[0-9]*\//i', $img_path, -1);
            if (!empty($exploded[1])) {
                $img_rel_path = $exploded[1];
            }
        } elseif (strpos($img_path, 'cp_ic_cropped/') !== false) {
            $exploded = explode('cp_ic_cropped/', $img_path);
            if (!empty($exploded[1])) {
                $img_rel_path = $exploded[1];
            }
        } elseif (strpos($img_path, 'cp_ic_white/') !== false) {
            $exploded = explode('cp_ic_white/', $img_path);
            if (!empty($exploded[1])) {
                $img_rel_path = $exploded[1];
            }
        } elseif (strpos($img_path, 'resampled/') !== false) {
            $exploded = explode('resampled/', $img_path);
            if (!empty($exploded[1])) {
                $img_rel_path = $exploded[1];
            }
        } elseif (strpos($img_path, 'watermarked/') !== false) {
            $exploded = preg_split('/watermarked\/[0-9]*\//i', $img_path, -1);
            if (!empty($exploded[1])) {
                $img_rel_path = $exploded[1];
            } else {
                $exploded = explode('watermarked/', $img_path);
                if (!empty($exploded[1])) {
                    $img_rel_path = $exploded[1];
                }
            }
        } else {
            $img_rel_path = $img_path;
        }
        if (!empty($img_rel_path)) {
            $rel_image_crc = crc32($img_rel_path);
        }
    }
    return $rel_image_crc;
}
