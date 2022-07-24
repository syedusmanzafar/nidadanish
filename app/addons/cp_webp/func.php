<?php
/*****************************************************************************
*                                                        © 2013 Cart-Power   *
*           __   ______           __        ____                             *
*          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
*      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
*     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
*    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
*                                                                            *
*                                                                            *
* -------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license *
* and  accept to the terms of the License Agreement can install and use this *
* program.                                                                   *
* -------------------------------------------------------------------------- *
* website: https://store.cart-power.com                                      *
* email:   sales@cart-power.com                                              *
******************************************************************************/

use Tygh\Registry;
use Tygh\Bootstrap;
use Tygh\Storage;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$image_opt = Registry::get('addons.cp_image_optimization');
if (Registry::get('addons.cp_webp.status') == 'A' && (!isset($image_opt) || (!empty($image_opt) && $image_opt['status'] != 'A'))) {
    require_once 'lib/shortpixel-php-req.php';
}

if (extension_loaded('imagick')) {
    $t_abs_path = Storage::instance('images')->getAbsolutePath('');
    $t_image_path = 'cp_webp_not_rename_or_delete_this.png';
    $t_twebp_path = 'cp_webp_not_rename_or_delete_this.webp';
    if (Storage::instance('images')->isExist($t_image_path) && !isset(Tygh::$app['session']['cp_webp_imagick_failed'])) {
        try {
            $image = new Imagick();
            $image->readImage($t_abs_path . $t_image_path);
            $image->setOption('webp:method', '6'); 
            $image->writeImage($t_abs_path . $t_twebp_path);
            Tygh::$app['session']['cp_webp_imagick_failed'] = false;
        } catch (\Exception $e) {
            Tygh::$app['session']['cp_webp_imagick_failed'] = true;
        }
    }
}
if (function_exists('imagewebp') || (extension_loaded('imagick') && empty(Tygh::$app['session']['cp_webp_imagick_failed']))) {
    Tygh::$app['session']['cp_webp_has_resources'] = true;
} else {
    Tygh::$app['session']['cp_webp_has_resources'] = false;
}

/*
HOOKS
*/

function fn_cp_webp_update_company_pre($company_data, $company_id, $lang_code, $can_update)
{
    if (AREA == 'C' && empty($company_id) && fn_allowed_for('MULTIVENDOR')) {
        Registry::set('cp_wb_skip_webp', true);
    }
}

function fn_cp_webp_delete_image($image_id, $pair_id, $object_type, $image_file)
{
    if (fn_get_count_image_link($image_id) != 0) {
        return false;
    }
    if (empty($image_file)) {
        return false;
    }
    
    $image_subdir = fn_get_image_subdir($image_id);
    $original_path = $object_type . '/' . $image_subdir . '/' . $image_file;
    $webp_paths = db_get_array('SELECT webp_path, webp_crc FROM ?:cp_web_image_list WHERE MATCH(image_path) AGAINST (\'"?p"\')', $original_path);
    if (!empty($webp_paths)) {
        foreach($webp_paths as $w_path) {
            if (strpos($w_path['webp_path'], 'thumbnails') !== false) {
                fn_delete_image_thumbnails($w_path['webp_path']);
            } else {
                Storage::instance('images')->delete($w_path['webp_path']);
            }
            db_query("DELETE FROM ?:cp_web_image_list WHERE webp_crc = ?i", $w_path['webp_crc']);
        }
    }
}

function fn_cp_webp_mailer_send_pre($mailer, $transport, &$message, $area, $lang_code) 
{
    //replace webp images from emails
    $msg = $message->getBody();
    $images = $message->getEmbeddedImages();
    if (!empty($msg) && !empty($images)) {
        $webp_iamges = array();
        $total_embebed = count($images);
        foreach($images as $imb_img) {
            if (!empty($imb_img['mime_type']) && $imb_img['mime_type'] == 'image/webp' && !empty($imb_img['file'])) {
                $rel_path = explode('/images/', $imb_img['file']);
                if (!empty($rel_path) && !empty($rel_path[1])) {
                    $crc_webp = crc32($rel_path[1]);
                    $check_exist = db_get_field("SELECT image_path FROM ?:cp_web_image_list WHERE webp_crc = ?i", $crc_webp);
                    if (!empty($check_exist)) {
                        $file_mime_type = fn_get_mime_content_type($check_exist);
                        $file_ext = fn_get_image_extension($file_mime_type);
                        
                        $imb_img['file'] = $rel_path[0] . '/images/' . $check_exist;
                        $imb_img['mime_type'] = $file_mime_type;
                        $new_cid = 'csimg' . $total_embebed  . '.' . $file_ext;
                        $total_embebed +=1;
                        $webp_iamges[$imb_img['cid']] = $new_cid;
                        
                        $message->addEmbeddedImages($imb_img['file'], $new_cid, $imb_img['mime_type']);
                    }
                }
            }
        }
        if (!empty($webp_iamges)) {
            foreach($webp_iamges as $old => $new) {
                $msg = str_replace($old, $new, $msg);
            }
            $message->setBody($msg);
        }
    }
}

function fn_cp_webp_get_image_pairs_post($object_ids, $object_type, $pair_type, $get_icon, $get_detailed, $lang_code, &$pairs_data, $detailed_pairs, $icon_pairs)
{
    //excluding webp images for yml files
    if (fn_allowed_for('MULTIVENDOR')) {
        $check_new_vendor = Registry::get('cp_wb_skip_webp');
    }
    if (strpos(Registry::get('runtime.controller'), 'yml') !== false || !empty($check_new_vendor)) {
        return;
    }

    if (AREA == 'C' && !empty($pairs_data) && !empty(Tygh::$app['session']['cp_wb_user_system']) && Tygh::$app['session']['cp_wb_user_system'] != 'mac' && !empty(Tygh::$app['session']['cp_webp_has_resources'])) {
        static $x_settings;
        static $water_settings;
        static $hash_formats;
        
        if (!isset($x_settings)) {
            $x_settings = Registry::get('addons.cp_webp');
        }
        if (!isset($water_settings)) {
            $water_settings = Registry::get('addons.watermarks');
        }
        if (!isset(Tygh::$app['session']['cp_wb_img_to_webp']) && $x_settings['force_generate'] != 'Y') {
            Tygh::$app['session']['cp_wb_img_to_webp'] = array();
        }
        if (!isset($hash_formats)) {
            $hash_formats = fn_cp_webp_image_hash_formats();
        }
        foreach ($pairs_data as $obj_id => &$obj_data) {
            foreach($obj_data as $pair_id => &$pair_data) {
                $type = '';
                if (!empty($pair_data['icon']) && !empty($pair_data['icon']['image_path'])) {
                    $type = 'icon';
                } elseif (!empty($pair_data['detailed']) && !empty($pair_data['detailed']['image_path'])) {
                    $type = 'detailed';
                }
                if (empty($type)) {
                    continue;
                };
                $img_ext = '';
                $img_pathinfo = fn_pathinfo($pair_data[$type]['image_path']);
                if (!empty($img_pathinfo) && !empty($img_pathinfo['extension'])) {
                    $img_ext = $img_pathinfo['extension'];
                }
                if (strpos($pair_data[$type]['image_path'], '.' . $img_ext) === false) { // fix for up registr extensions
                    $img_ext = strtoupper($img_ext);
                }
                if (!empty($img_ext) && strpos($pair_data[$type]['image_path'], '/watermark/') === false) {
                    if (!in_array($img_ext, array('png', 'jpg', 'jpeg', 'PNG', 'JPG', 'JPEG'))) {
                        continue;
                    }
                    $basename = fn_basename($pair_data[$type]['image_path']);
                    
                    if (!empty($pair_data[$type]['relative_path']) && $water_settings['status'] != 'A') {
                        list($original_path) = explode('?', $pair_data[$type]['relative_path']);
                        if ($x_settings['use_hash'] == 'Y') {
                            $webp_crc_path = crc32($original_path) . '.webp';
                        } else {
                            $webp_crc_path = fn_cp_webp_replace_ext('.' . $img_ext, '-' . $hash_formats[$img_ext] . '.webp', $basename);
                        }
                        $webp_path = fn_cp_webp_replace_ext($basename, $webp_crc_path, $original_path);
                    } else {
                        list($url) = explode('?', $pair_data[$type]['image_path']);
                        list($original_path, $webp_path) = fn_cp_webp_resolve_original_paths($url);
                    }
                    if (Storage::instance('images')->isExist($original_path)) {
                        if (!empty($webp_path) && Storage::instance('images')->isExist($webp_path)) {
                            if (!empty($original_path) && !empty(Tygh::$app['session']['cp_webp_ignore_list']) && in_array($original_path, Tygh::$app['session']['cp_webp_ignore_list'])) {
                                continue;
                            }
                            $pair_data[$type] = fn_cp_webp_replace_img_data($pair_data[$type], $original_path, $webp_path, $img_ext);
                            if (isset(Tygh::$app['session']['cp_wb_img_to_webp']) && in_array($pair_data[$type]['image_path'], Tygh::$app['session']['cp_wb_img_to_webp'])) {
                                Tygh::$app['session']['cp_wb_img_to_webp'] = array_diff(Tygh::$app['session']['cp_wb_img_to_webp'], $pair_data[$type]['image_path']);
                            }
                            
                        } else {
                            if ($x_settings['force_generate'] != 'Y') {
                                if (!in_array($pair_data[$type]['image_path'], Tygh::$app['session']['cp_wb_img_to_webp'])) {
                                    Tygh::$app['session']['cp_wb_img_to_webp'][] = $pair_data[$type]['image_path'];
                                }
                            } else {
                                $webp_path = fn_cp_webp_generate_webp_source($pair_data[$type]['image_path'], true);
                                if (!empty($webp_path)) {
                                    $pair_data[$type] = fn_cp_webp_replace_img_data($pair_data[$type], $original_path, $webp_path, $img_ext);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function fn_cp_webp_image_to_display_post(&$image_data, $images, $image_width, $image_height)
{
    if (AREA == 'C' && !empty($image_data) && !empty($image_data['image_path']) && strpos($image_data['image_path'], '.webp') === false 
        && !empty($image_data['absolute_path']) && !empty(Tygh::$app['session']['cp_wb_user_system']) && Tygh::$app['session']['cp_wb_user_system'] != 'mac' && !empty(Tygh::$app['session']['cp_webp_has_resources'])) {
        
        $image_data['origin_image'] = $image_data['image_path'];
        if (defined('CP_WEBP_GENERATE_MODE') && CP_WEBP_GENERATE_MODE == 'Y') {
            $webp_path = fn_cp_webp_generate_webp_source($image_data['image_path'], true);
            if (!empty($webp_path)) {
                $image_data['image_path'] = $webp_path;
                $image_data['generate_image'] = strpos($webp_path, '&image_path=') !== false;
            }
        } else {
            list($thumb_path) = explode('?', $image_data['image_path']);
            list($original_path, $webp_path) = fn_cp_webp_resolve_original_paths($thumb_path);
            if (!empty($webp_path) && Storage::instance('images')->isExist($webp_path)) {
                if (!empty(Tygh::$app['session']['cp_webp_ignore_list']) && in_array($original_path, Tygh::$app['session']['cp_webp_ignore_list'])) {
                    return false;
                }
                $new_path = Storage::instance('images')->getUrl($webp_path);
                $image_data['image_path'] = $new_path;
                $image_data['generate_image'] = strpos($new_path, '&image_path=') !== false;
            } else {
                if (!isset(Tygh::$app['session']['cp_wb_img_to_webp'])) {
                    Tygh::$app['session']['cp_wb_img_to_webp'] = array();
                }
                if (!in_array($image_data['image_path'], Tygh::$app['session']['cp_wb_img_to_webp'])) {
                    Tygh::$app['session']['cp_wb_img_to_webp'][] = $image_data['image_path'];
                }
            }
        }
    }
}

function fn_cp_webp_replace_img_data($data, $basename, $webp_basename, $orig_ext)
{   
    if (!empty($data) && !empty($basename) && !empty($webp_basename)) {
        $values = array('image_path','http_image_path','https_image_path','absolute_path');
        foreach($values as $val) {
            if (!empty($data[$val])) {
                $data[$val] = str_replace($basename, $webp_basename, $data[$val]);
            }
        }
    }
    return $data;
}

function fn_cp_webp_generate_webp_source($url, $from_img = false) 
{
    list($url) = explode('?', $url);
    static $_settings;
    if (!isset($_settings)) {
        $_settings = Registry::get('addons.cp_webp');
    }
    if (!empty(Tygh::$app['session']['cp_webp_has_resources'])) {
        list($original_path, $webp_path) = fn_cp_webp_resolve_original_paths($url);
        if (empty($original_path) || empty($webp_path) || !Storage::instance('images')->isExist($original_path)) {
            return false;
        }
        if (!empty(Tygh::$app['session']['cp_webp_ignore_list']) && in_array($original_path, Tygh::$app['session']['cp_webp_ignore_list'])) {
            return false;
        }
        if (Storage::instance('images')->isExist($webp_path)) {
            if (!empty($from_img)) {
                return Storage::instance('images')->getUrl($webp_path);
            } else {
                return false;
            }
        }
        if (!empty($_settings['use_cron']) && $_settings['use_cron'] == 'Y') {
            $save_data = array(
                'image_path' => $original_path,
                'webp_path' => $webp_path
            );
            db_replace_into('cp_web_images_for_change', $save_data);
            return false;
        }
        $abs_path = Storage::instance('images')->getAbsolutePath('');
    } else {
        return false;
    }
    if (extension_loaded('imagick') && empty(Tygh::$app['session']['cp_webp_imagick_failed'])) {
        fn_cp_webp_imagick_convert_to_webp($abs_path, $original_path, $webp_path);
        
    } elseif(function_exists('imagewebp')) {
        $abs_path = Storage::instance('images')->getAbsolutePath('');
        fn_cp_webp_gd_convert_to_webp ($abs_path, $original_path, $webp_path, $_settings);
    } else {
        return false;
    }
    if (empty($webp_path)) {
        return false;
    }
    if (Storage::instance('images')->isExist($webp_path)) {
        $save_data = array(
            'webp_crc' => crc32($webp_path),
            'webp_path' => $webp_path,
            'image_path' => $original_path,
            'image_crc' => crc32($original_path),
        );
        $save_data['rel_image_crc'] = fn_cp_web_generate_crc32_field($save_data['image_path']);
        
        db_replace_into('cp_web_image_list', $save_data);
        
        if (!empty($_settings['use_logs']) && $_settings['use_logs'] == 'Y') {
            $save_log_data = $save_data;
            $save_log_data['timestamp'] = time();
            
            $original_abs_path = Storage::instance('images')->getAbsolutePath($original_path);
            $img_old_size = filesize($original_abs_path)/1048576;
            $save_log_data['image_size'] = number_format($img_old_size, 3, '.', '');
            
            $webp_abs_path = Storage::instance('images')->getAbsolutePath($webp_path);
            $webp_size = filesize($webp_abs_path)/1048576;
            $save_log_data['webp_size'] = number_format($webp_size, 3, '.', '');
            
            db_replace_into('cp_web_log_images', $save_log_data);
        }
    }
    
    return Storage::instance('images')->getUrl($webp_path);
}

function fn_cp_webp_resolve_original_paths($url) {
    $hash_formats = array(
        'jpg' => 1,
        'JPG' => 1,
        'png' => 2,
        'PNG' => 2,
        'jpeg' => 3,
        'JPEG' => 3
    );
    $original_path = fn_cp_webp_url_to_absolute($url);
    list(, $original_path) = explode('/images/', $original_path);
    $img_pathinfo = fn_pathinfo($original_path);
    if (!empty($img_pathinfo) && !empty($img_pathinfo['extension'])) {
        $img_ext = $img_pathinfo['extension'];
        if (!in_array($img_ext, array('png', 'jpg', 'jpeg', 'PNG', 'JPG', 'JPEG'))) {
            return array(false, false);
        }
    } else {
        return array(false, false);
    }
    if (defined('CP_WEBP_USE_HASH') && CP_WEBP_USE_HASH == 'Y') {
        $webp_crc_path = crc32($original_path) . '.webp';
    } else {
        $webp_crc_path = fn_cp_webp_replace_ext('.' . $img_ext, '-' . $hash_formats[$img_ext] . '.webp', $img_pathinfo['basename']);
    }
    $webp_file_path = fn_cp_webp_replace_ext($img_pathinfo['basename'], $webp_crc_path, $original_path);
    
    return array($original_path, $webp_file_path);
}

function fn_cp_webp_absolute_to_url($path) {
    static $host;
    static $root;
    if(!isset($host) || !isset($root)) {
        $config = Registry::get('config');
        $host = defined('HTTPS') ? $config['https_location'] : $config['http_location'];
        $root = $config['dir']['root'];
    }
    return str_replace($root, $host, $path);
}

function fn_cp_webp_url_to_absolute($url) {
    static $host;
    static $root;
    if(!isset($host) || !isset($root)) {
        $config = Registry::get('config');
        $host = defined('HTTPS') ? $config['https_location'] : $config['http_location'];
        $root = $config['dir']['root'];
    }
    return str_replace($host, $root, $url);
}

function fn_cp_webp_settings_notice_handler() {
    return __('notes_about_addon_delete');
}

function fn_cp_webp_render_tags($attributes) {
    if(function_exists('smarty_modifier_render_tag_attrs')) {
        return smarty_modifier_render_tag_attrs($attributes);
    }
    $attributes = (array) $attributes;
    $result = [];
    foreach ($attributes as $name => $value) {
        if (is_bool($value)) {
            if ($value) {
                $result[] = $name;
            }
            continue;
        } elseif (is_array($value)) {
            $value = json_encode($value);
        }

        $result[] = sprintf('%s="%s"', $name, htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE));
    }
    return implode(' ', $result);
}

function fn_cp_cp_webp_get_system()
{
    $u_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";
    
    //First get the platform
    if (preg_match('/ipad|iphone/i', $u_agent)) {
        if (preg_match('/safari/i', $u_agent) && preg_match('/version\/14./i', $u_agent)) {
            $platform = 'windows';
        } elseif (preg_match('/safari/i', $u_agent) && preg_match('/CPU OS 14_/i', $u_agent)) {
            $platform = 'windows';
        } elseif (preg_match('/safari/i', $u_agent) && preg_match('/CPU iPhone OS 14_/i', $u_agent)) {
            $platform = 'windows';
        } else {
            $platform = 'mac';
        }
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent) && preg_match('/safari/i', $u_agent) && !preg_match('/chrome/i', $u_agent)) {
        $platform = 'mac';
//     } elseif (preg_match('/YaBrowser/i', $u_agent)) {
//         $platform = 'mac';
    } elseif (preg_match('/windows nt/i', $u_agent) && (preg_match('/edge\//i', $u_agent) || preg_match('/edg\//i', $u_agent))) {
        preg_match_all('#edge\/(.+?)\.#is', $u_agent, $edge);
        preg_match_all('#edg\/(.+?)\.#is', $u_agent, $edg);
        if (!empty($edge) && !empty($edge[1]) && !empty($edge[1][0]) && $edge[1][0] < 18) {
            $platform = 'mac';
        } elseif (!empty($edg) && !empty($edg[1]) && !empty($edg[1][0]) && $edg[1][0] < 18) {
            $platform = 'mac';
        } else {
            $platform = 'windows';
        }
    } elseif (!preg_match('/opera mini/i', $u_agent) && (preg_match('/opera\//i', $u_agent) || preg_match('/opr\//i', $u_agent))) {
        preg_match_all('#version\/(.+?)\.#is', $u_agent, $opera);
        preg_match_all('#opr\/(.+?)\.#is', $u_agent, $opr);
        if (!empty($opera) && !empty($opera[1]) && !empty($opera[1][0]) && $opera[1][0] >= 12) {
            $platform = 'windows';
        } elseif (!empty($opr) && !empty($opr[1]) && !empty($opr[1][0]) && $opr[1][0] >= 12) {
            $platform = 'windows';
        } else {
            $platform = 'mac';
        }
    } elseif (preg_match('/firefox\//i', $u_agent)) {
        preg_match_all('#firefox\/(.+?)\.#is', $u_agent, $ff);
        if (!empty($ff) && !empty($ff[1]) && !empty($ff[1][0]) && $ff[1][0] < 65) {
            $platform = 'mac';
        } else {
            $platform = 'windows';
        }
    } elseif (preg_match('/opera /i', $u_agent) && preg_match('/windows nt/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows/i', $u_agent) && preg_match('/chrome/i', $u_agent)) {
        preg_match_all('#chrome\/(.+?)\.#is', $u_agent, $chrome);
        if (!empty($chrome) && !empty($chrome[1]) && !empty($chrome[1][0]) && $chrome[1][0] >= 32) {
            $platform = 'windows';
        } else {
            $platform = 'mac';
        }
    } elseif (preg_match('/windows/i', $u_agent) && preg_match('/safari/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows nt/i', $u_agent) && (preg_match('/msie/i', $u_agent) || preg_match('/like gecko/i', $u_agent))) {
        $platform = 'mac';
    } else {
        $platform = 'windows';
    }
    $ignore_list = db_get_fields("SELECT image_path FROM ?:cp_web_ignore_list");
    Tygh::$app['session']['cp_webp_ignore_list'] = $ignore_list;
    return $platform;
}

function fn_cp_web_cron_run_info()
{
    $admin_ind = Registry::get('config.admin_index');
    $__params = Registry::get('addons.cp_webp');
    if (!empty($__params) && !empty($__params['cron_pass'])) {
        $cron_pass = $__params['cron_pass'];
    } else {
        $cron_pass = '';
    }
    $hint = '<b>' . __("cp_web_use_this_for_crop_cron") . ':</b><br>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_webp.crop_cron --cron_pass=' . $cron_pass;
    
    return $hint;
}

function fn_cp_web_check_pixel_key_info()
{
    
    $hint = '<b>' . __("cp_web_use_this_to_check_key_status") . ' <a class="cm-ajax" href="' . fn_url('cp_webp.check_key') . '">' . __('cp_web_check_txt') . '</a>';
    
    return $hint;
}

function fn_cp_webp_cron_convert()
{
    if (extension_loaded('imagick') && empty(Tygh::$app['session']['cp_webp_imagick_failed'])) {
        $lib_use = 'imagick';
    } elseif (function_exists('imagewebp')) {
        $lib_use = 'gd';
    }
    $addon_settings = Registry::get('addons.cp_webp');
    if (!empty($addon_settings['use_pixel']) && $addon_settings['use_pixel'] == 'Y' && !empty($addon_settings['pixel_key'])) {
        $lib_use = 's_pixel';
        $sp_codes = fn_cp_webp_shortpixel_error_codes();
    }
    if (!empty($lib_use) && $addon_settings['use_cron'] == 'Y') {
        $img_limits  = !empty($addon_settings['cron_limit']) ? $addon_settings['cron_limit'] : 0;
        if (!empty($img_limits)) {
            $db_images = db_get_array("SELECT * FROM ?:cp_web_images_for_change LIMIT $img_limits");
        } else {
            $db_images = db_get_array("SELECT * FROM ?:cp_web_images_for_change");
        }
        $abs_path = Storage::instance('images')->getAbsolutePath('');
        $ignore_images = db_get_fields("SELECT image_path FROM ?:cp_web_ignore_list");
        if (!empty($db_images)) {
            $start_time = time();
            $stats = array(
                'save' => 0,
                'skip' => 0,
                'errors' => 0,
                'optimizated' => 0,
                'converted' => 0,
                'shortpixel' => 0,
            );
            $ids = $pending_images = array();
            $counter = $pending_imgs = 0;
            $back_key = true;
            foreach($db_images as $d_image) {
                $ids[] = $d_image['image_id'];
                if (!empty($ignore_images) && !empty($d_image['image_path']) && in_array($d_image['image_path'], $ignore_images)) {
                    continue;
                }
                if (!empty($d_image['image_path']) && file_exists('images/' . $d_image['image_path']) && !empty($d_image['webp_path'])) {
                    if ($lib_use == 'imagick') {
                    
                        fn_cp_webp_imagick_convert_to_webp($abs_path, $d_image['image_path'], $d_image['webp_path']);
                        $stats['converted'] += 1;
                    } elseif ($lib_use == 'gd') {
                        $res = fn_cp_webp_gd_convert_to_webp ($abs_path, $d_image['image_path'], $d_image['webp_path'], $addon_settings);
                        if (!empty($res)) {
                            $stats['converted'] += 1;
                        } else {
                            $stats['errors'] += 1;
                        }
                    } elseif ($lib_use == 's_pixel' && !empty($back_key)) {
                        fn_cp_webp_shortpixel_run($d_image, $addon_settings['pixel_key'], $abs_path, '', $sp_codes, $back_key, $stats, $pending_images, $pending_imgs, $from_pending);
                        if (empty($back_key)) {
                            if (extension_loaded('imagick') && empty(Tygh::$app['session']['cp_webp_imagick_failed'])) {
                                $lib_use = 'imagick';
                                continue;
                            } elseif (function_exists('imagewebp')) {
                                $lib_use = 'gd';
                                continue;
                            } else {
                                break;
                            }
                        }
                    }
                    if (Storage::instance('images')->isExist($d_image['webp_path'])) {
                        $save_crc = array(
                            'webp_crc' => crc32($d_image['webp_path']),
                            'webp_path' => $d_image['webp_path'],
                            'image_path' => $d_image['image_path'],
                            'image_crc' => crc32($d_image['image_path']),
                        );
                        $save_crc['rel_image_crc'] = fn_cp_web_generate_crc32_field($save_crc['image_path']);
                        
                        db_replace_into('cp_web_image_list', $save_crc);
                        
                        if (!empty($addon_settings['use_logs']) && $addon_settings['use_logs'] == 'Y') {
                            $save_log_data = $save_crc;
                            $save_log_data['timestamp'] = time();
                            
                            $original_abs_path = Storage::instance('images')->getAbsolutePath($d_image['image_path']);
                            $img_old_size = filesize($original_abs_path)/1048576;
                            $save_log_data['image_size'] = number_format($img_old_size, 3, '.', '');
                            
                            $webp_abs_path = Storage::instance('images')->getAbsolutePath($d_image['webp_path']);
                            $webp_size = filesize($webp_abs_path)/1048576;
                            $save_log_data['webp_size'] = number_format($webp_size, 3, '.', '');
                            
                            db_replace_into('cp_web_log_images', $save_log_data);
                        }
                    }
                    $counter += 1;
                    if ($counter >= 100) {
                        db_query("DELETE FROM ?:cp_web_images_for_change WHERE image_id IN (?n)", $ids);
                        $ids = array();
                        $counter = 0;
                    }
                }
            }
            if (!empty($pending_images)) {
                $from_pending = 1;
                foreach($pending_images as $p_key => $s_image) {
                    unset($pending_images[$p_key]);
                    fn_cp_webp_shortpixel_run($s_image, $addon_settings['pixel_key'], $abs_path, '', $sp_codes, $back_key, $stats, $pending_images, $pending_imgs, $from_pending);
                }
            }
            if (!empty($ids)) {
                db_query("DELETE FROM ?:cp_web_images_for_change WHERE image_id IN (?n)", $ids);
            }
            //fix for incredible case with out of limit for int(11) field
            $max_id = db_get_next_auto_increment_id('cp_web_images_for_change');
            if (!empty($max_id) && $max_id >= 4004967295) {
                db_query("TRUNCATE TABLE ?:cp_web_images_for_change");
            }
            
            $save_data = array(
                'start_type' => $start_time,
                'end_time' => time(),
                'info' => serialize($stats),
            );
            db_replace_into('cp_web_logs', $save_data);
        }
    }
    if (!empty($addon_settings['clear_logs']) && $addon_settings['clear_logs'] > 0) {
        db_query("DELETE FROM ?:cp_web_logs WHERE start_time < ?i", TIME - 60*60*24*$addon_settings['clear_logs']);
        db_query("DELETE FROM ?:cp_web_log_images WHERE timestamp < ?i", TIME - 60*60*24*$addon_settings['clear_logs']);
    }
    return true;
}

function fn_cp_webp_imagick_convert_to_webp($abs_path, $image_path, $webp_path)
{
    try {
        $image = new Imagick();
        $image->readImage($abs_path . $image_path);
        $image->setOption('webp:method', '6'); 
        $image->writeImage($abs_path . $webp_path); 
    } catch(\Exception $e) {
        return false;
    }

    return true;
}

function fn_cp_webp_gd_convert_to_webp($abs_path, $image_path, $webp_path, $addon_settings)
{
    if (!empty($image_path) && !empty($webp_path)) {
        list(, , $mime) = fn_get_image_size($abs_path . $image_path);
        if ($mime == 'image/jpeg') {
            $src = imagecreatefromjpeg($abs_path . $image_path);

        } elseif ($mime == 'image/png') {
            $src = imagecreatefrompng($abs_path . $image_path);

        } elseif ($mime == 'image/gif') {
            $src = imagecreatefromgif($abs_path . $image_path);

        } else {
            return false;
        }
        imagepalettetotruecolor($src);
        imagealphablending($src, true);
        imagesavealpha($src, true);
        $quality = 80;
        if (!empty($addon_settings['quality'])) {
            $addon_settings['quality'] = intval($addon_settings['quality']);
            if ($addon_settings['quality'] > 0) {
                $quality = $addon_settings['quality'];
            }
        }
        imagewebp($src, $abs_path . $webp_path, $quality);
        imagedestroy($src);
        $content = file_get_contents($abs_path . $webp_path);
        Storage::instance('images')->put($webp_path, array('contents' => $content, 'caching' => true, 'overwrite' => true));
    }
    return true;
}

function fn_cp_webp_check_key_status($key)
{
    try {
        ShortPixel\setKey($key);
        list($result,$fff,$gg) = \ShortPixel\ShortPixel::getClient()->apiStatus($key);
        $result = (array) $result;
    } catch(\ShortPixel\AccountException $ex) {
        $sp_error_code = $ex->getCode();
        $sp_error_msg = $ex->getMessage();
    } catch (\ShortPixel\ClientException $ex) {
        $sp_error_code = $ex->getCode();
        $sp_error_msg = $ex->getMessage();
    } 
    catch (\ShortPixel\PersistException $ex) {
        $sp_error_code = $ex->getCode();
        $sp_error_msg = $ex->getMessage();
    }
    $masg = '';
    if (!empty($result) && !empty($result['Status'])) {
        $result['Status'] = (array) $result['Status'];
        
        $masg .= '<b>' .  __('status') . ':</b> ' . $result['Status']['Message'] . '<br />';
    }
    if (isset($result['APICallsMade'])) {
        $masg .= '<b>' .  __('cp_webp_api_calls_made') . ':</b> ' . $result['APICallsMade'] . '<br />';
    }
    if (isset($result['APICallsFree'])) {
        $masg .= '<b>' .  __('cp_webp_api_calls_free') . ':</b> ' . $result['APICallsFree'] . '<br />';
    }
    if (isset($result['APICallsQuota'])) {
        $masg .= '<b>' .  __('cp_webp_api_calls_quota') . ':</b> ' . $result['APICallsQuota'] . '<br />';
    }
    if (isset($result['DomainCheck'])) {
        $masg .= '<b>' .  __('cp_webp_api_domain_check') . ':</b> ' . $result['DomainCheck'] . '<br />';
    }
    if (!empty($masg)) {
        $masg = '<br />' . $masg;
    }
    
    fn_set_notification('N', __('notice'), $masg);
    
    return true;
}
function fn_cp_webp_shortpixel_run($image, $api_key, $abs_folder, $s_compression, $sp_codes, &$back_key, &$stats, &$pending_images, &$pending_imgs, &$from_pending = 0)
{
    if (!empty($image) && !empty($api_key) && !empty($back_key)) {
        $short_size = $short_down_path = $compr_text = $error_msg = '';
        $error_code = $status_code = 0;
        
        $img_local_path = $abs_folder . $image['image_path'];
        $folder_path = $abs_folder;
            
        if (substr($folder_path, -1) == '/') {
            $folder_path = substr($folder_path, 0, -1);
        }
        if (!empty($from_pending)) {
            $need_wait = true;
        } else {
            $need_wait = false;
        }
        $need_wait = true;// fix to remove panding images. Avoid multiple API quota using by 1 image
        try {
            ShortPixel\setKey($api_key);
            if (!empty($need_wait)) {
                if (!empty($s_compression)) {
                    $result = ShortPixel\fromFile($img_local_path)->optimize($s_compression)->generateWebP()->toFiles($folder_path);
                } else {
                    $result = ShortPixel\fromFile($img_local_path)->generateWebP()->toFiles($folder_path);
                }
            } else {
                if (!empty($s_compression)) {
                    $result = ShortPixel\fromFile($img_local_path)->wait(0)->optimize($s_compression)->generateWebP()->toFiles($folder_path);
                } else {
                    $result = ShortPixel\fromFile($img_local_path)->wait(0)->generateWebP()->toFiles($folder_path);
                }
            }
            
        } catch(\ShortPixel\AccountException $ex) {
            $back_key = false;
            $error_msg = $ex->getMessage();
            
            return false;
        } catch (\ShortPixel\ClientException $ex) {
            $error_code = $ex->getCode();
            $error_msg = $ex->getMessage();
            
            $stats['errors'] += 1;
            return false;
        } 
        catch (\ShortPixel\PersistException $ex) {
            $error_code = $ex->getCode();
            $error_msg = $ex->getMessage();
            return false;
        }
        if (!empty($result)) {
            $res_status = $result->status;
            if(count($result->succeeded)) {
                $res_data = $result->succeeded[0];
                if (!empty($res_data)) {
                    $res_data = (array) $res_data;
                }
                $status_data = $result->succeeded[0]->Status;
                if (!empty($status_data)) {
                    $status_code = $status_data->Code;
                    $error_msg = $status_data->Message;
                }
            } elseif(count($result->same)) {
                $res_data = $result->same[0];
                if (!empty($res_data)) {
                    $res_data = (array) $res_data;
                }
                $stats['skip'] += 1;
                
                $status_data = $result->same[0]->Status;
                if (!empty($status_data)) {
                    $status_code = $status_data->Code;
                    $error_msg = $status_data->Message;
                }
            } elseif (count($result->pending)) {
                if ($from_pending < 4) {
                    $pending_images[] = $image;
                    $pending_imgs +=1;
                    if ($pending_imgs > CP_WEBP_PENDING_IMGS) {
                        $from_pending +=1;
                        foreach($pending_images as $p_key => $s_image) {
                            unset($pending_images[$p_key]);
                            fn_cp_webp_shortpixel_run($s_image, $api_key, $abs_folder, $s_compression, $sp_codes, $back_key, $session, $pending_images, $pending_imgs, $from_pending);
                        }
                    }
                }
                return true;
            } elseif(count($result->failed)) {
                $res_data = $result->failed[0];
                if (!empty($res_data)) {
                    $res_data = (array) $res_data;
                }
                $status_data = $result->failed[0]->Status;
                if (!empty($status_data)) {
                    $status_code = $status_data->Code;
                    $error_msg = $status_data->Message;
                }
            }
        }
        if (empty($status_code)) {
            return false;
        }
        // code > 2 - it is an error
        if ($status_code == -403) {
                
            $back_key = false;
            return false;
        } elseif ($status_code == -301) {
            $stats['big_img'] += 1;
            return false;
        }
        if (empty($error_msg) && !empty($sp_codes) && !empty($sp_codes[$status_code])) {
            $error_msg = $sp_codes[$status_code];
        } else {
            $error_msg = 'SP: ' . $error_msg;
        }
        if ($status_code > 2 && !empty($sp_codes[$status_code])) {
            $stats['errors'] += 1;
            
        }
        if (!empty($res_data['WebPLossyURL'])) {
            if (!empty($s_compression)) {
                $short_size = $res_data['WebPLossySize'];
                $short_down_path = $res_data['WebPLossyURL'];
                
            } else {
                $short_size = $res_data['WebPLosslessSize'];
                $short_down_path = $res_data['WebPLosslessURL'];
            }
        }
        if (!empty($short_down_path)) {
            $stats['converted'] += 1;
            $stats['shortpixel'] += 1;
            if (Bootstrap::getIniParam('allow_url_fopen') == true) {
                $img_result = @file_get_contents($short_down_path);
            } else {
                $img_result = Http::get($short_down_path);
            }
            if (!empty($img_result)) {
                Storage::instance('images')->put($image['webp_path'], array('contents' => $img_result, 'caching' => true, 'overwrite' => true));
                
                $fsp = fopen('./images/' . $image['webp_path'], 'wb');
                fwrite($fsp, $img_result);
                fclose($fsp);
            
                if ($res_data['OriginalSize'] > $short_size) {
                    $size_diff = $res_data['OriginalSize'] - $short_size;
                    
                    $stats['save'] += $size_diff;
                    $stats['optimizated'] += 1;
                    
                }
            }
        }
    }
    return true;
}

// return shortpixel error codes
function fn_cp_webp_shortpixel_error_codes()
{
    $error_codes = array(
        1 => 'SP: No errors, image scheduled for processing',
        2 => 'SP: No errors, image processed, download URL available',
        -102 => 'SP: Invalid URL. Please make sure the URL is properly urlencoded and points to a valid image file',
        -105 => 'SP: URL is missing for the call',
        -106 => 'SP: URL is inaccessible from our server(s) due to access restrictions',
        -107 => 'SP: Too many URLs in a POST, maximum allowed has been exceeded',
        -108 => 'SP: Invalid user used for optimizing images from a particular domain',
        -201 => 'SP: Invalid image format',
        -202 => 'SP: Invalid image or unsupported image format',
        -203 => 'SP: Could not download file',
        -301 => 'SP: The file is larger than the remaining quota',
        -302 => 'SP: The file is no longer available',
        -303 => 'SP: Internal API error: the file was not written on disk',
        -305 => 'SP: Internal API error: Unknown, details usually in message',
        -401 => 'SP: Invalid API key. Please check that the API key is the one provided to you',
        -403 => 'SP: Quota exceeded. You need to subscribe to a larger plan or to buy an additional one time package to increase your quota',
        -404 => 'SP: The maximum number of URLs in the optimization queue reached. Please try again in a minute',
        -500 => 'SP: API is in maintenance mode. Please come back later',
    );
    return $error_codes;
}

function fn_cp_webp_get_logs($params, $items_per_page) 
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );
    $params = array_merge($default_params, $params);
    $fields = array (
        "?:cp_web_logs.*",
    );
    $sortings = array (
        'start_time' => "?:cp_web_logs.start_time",
        'end_time' => "?:cp_web_logs.end_time",
        'type' => "?:cp_web_logs.type"
    );
    $condition = $join = $group = '';
    if (!empty($params['s_from_date'])) {
        $time_from = fn_parse_date($params['s_from_date']);
        if (!empty($time_from)) {
            $condition .= db_quote(" AND ?:cp_web_logs.start_time >= ?i", $time_from);
        }
    }
    if (!empty($params['s_to_date'])) {
        $time_to = fn_parse_date($params['s_to_date']);
        if (!empty($time_to)) {
            $condition .= db_quote(" AND ?:cp_web_logs.start_time <= ?i", $time_to);
        }
    }
    $sorting = db_sort($params, $sortings, 'end_time', 'desc');
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_web_logs $join WHERE 1 $condition $group");
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }

    $logs = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:cp_web_logs $join WHERE 1 $condition $group $sorting $limit", 'log_id');
    if (!empty($logs)) {
        foreach($logs as &$log_data) {
            if (!empty($log_data['info'])) {
                $log_data['info'] = unserialize($log_data['info']);
            }
        }
    }
    return array($logs, $params);
}

function fn_cp_webp_clear_cron_logs()
{
    db_query("TRUNCATE TABLE ?:cp_web_logs");
    return true;
}

function fn_cp_webp_clear_webp_logs()
{
    db_query("TRUNCATE TABLE ?:cp_web_log_images");
    return true;
}

function fn_cp_webp_get_ignore_list($params, $items_per_page) 
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );
    $params = array_merge($default_params, $params);
    $fields = array (
        "?:cp_web_ignore_list.*",
    );
    $sortings = array (
        'image' => "?:cp_web_ignore_list.image_path",
    );
    $condition = $join = $group = '';
    
    $sorting = db_sort($params, $sortings, 'image', 'asc');
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_web_ignore_list $join WHERE 1 $condition $group");
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }

    $logs = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:cp_web_ignore_list $join WHERE 1 $condition $group $sorting $limit", 'image_id');
    
    return array($logs, $params);
}

function fn_cp_webp_update_ignore_img($image_path)
{
    if (!empty($image_path)) {
        $save_data = array(
            'image_path' => $image_path
        );
        db_replace_into('cp_web_ignore_list', $save_data);
    }
    return true;
}

function fn_cp_webp_delete_ignore_img($image_ids)
{
    if (!empty($image_ids)) {
        if (!is_array($image_ids)) {
            $image_ids = (array) $image_ids;
        }
        db_query("DELETE FROM ?:cp_web_ignore_list WHERE image_id IN (?n)", $image_ids);
    }
    return true;
}

function fn_cp_webp_install() 
{
    if (version_compare(PRODUCT_VERSION, '4.9.3', '>')) {
        db_query("UPDATE ?:privileges SET is_view = ?s, group_id = ?s WHERE privilege = ?s", 'Y', 'cp_webp_priv_group', 'view_cp_webp');
        db_query("UPDATE ?:privileges SET group_id = ?s WHERE privilege = ?s", 'cp_webp_priv_group', 'manage_cp_webp');
    }
    
    return true;
}


function fn_cp_webp_replace_ext($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);
    if ($pos !== false){
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

function fn_cp_webp_image_hash_formats() {
    $data = array(
        'jpg' => 1,
        'JPG' => 1,
        'png' => 2,
        'PNG' => 2,
        'jpeg' => 3,
        'JPEG' => 3
    );
    return $data;
}

function fn_cp_webp_clear_tables()
{
    db_query("TRUNCATE TABLE ?:cp_web_images_for_change");
    db_query("TRUNCATE TABLE ?:cp_web_image_list");
    return true;
}

function fn_cp_web_delete_webp_images_btn()
{
    $hint = '<b>' . __("cp_web_delete_all_webp_images") . ' <a class="cm-ajax cm-confirm cm-post btn" href="' . fn_url('cp_webp.delete_webp_img') . '">' . __('cp_web_delete_webp') . '</a>';
    return $hint;
}

function fn_cp_web_delete_webp_images()
{
    Storage::instance('images')->deleteByPattern('*.webp');
    Storage::instance('images')->deleteByPattern('*/*/*.webp');
    Storage::instance('images')->deleteByPattern('thumbnails/*/*/*/*/*.webp');
    Storage::instance('images')->deleteByPattern('watermarked/*/*/*/*.webp');
    Storage::instance('images')->deleteByPattern('watermarked/*/*/*/*/*/*/*.webp');
    fn_cp_webp_clear_tables();
    
    return true;
}

function fn_cp_webp_get_webp_logs($params, $items_per_page) 
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );
    $params = array_merge($default_params, $params);
    $fields = array (
        "?:cp_web_log_images.*",
    );
    $sortings = array (
        'timestamp' => "?:cp_web_log_images.timestamp",
        'image_size' => "?:cp_web_log_images.image_size",
        'webp_size' => "?:cp_web_log_images.webp_size",
        'image_path' => "?:cp_web_log_images.image_path",
        'webp_path' => "?:cp_web_log_images.webp_path",
    );
    $condition = $join = $group = '';
    if (!empty($params['s_from_date'])) {
        $time_from = fn_parse_date($params['s_from_date']);
        if (!empty($time_from)) {
            $condition .= db_quote(" AND ?:cp_web_log_images.timestamp >= ?i", $time_from);
        }
    }
    if (!empty($params['s_to_date'])) {
        $time_to = fn_parse_date($params['s_to_date']);
        if (!empty($time_to)) {
            $condition .= db_quote(" AND ?:cp_web_log_images.timestamp <= ?i", $time_to);
        }
    }
    if (!empty($params['s_image_path'])) {
        $trimed_img_path = trim($params['s_image_path']);
        if (!empty($trimed_img_path)) {
            $condition .= db_quote(" AND ?:cp_web_log_images.image_path LIKE ?l", '%' . $trimed_img_path . '%');
        }
    }
    if (!empty($params['s_webp_path'])) {
        $trimed_webp_path = trim($params['s_webp_path']);
        if (!empty($trimed_webp_path)) {
            $condition .= db_quote(" AND ?:cp_web_log_images.webp_path LIKE ?l", '%' . $trimed_webp_path . '%');
        }
    }
    $sorting = db_sort($params, $sortings, 'timestamp', 'desc');
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_web_log_images $join WHERE 1 $condition $group");
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }

    $webp_logs = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:cp_web_log_images $join WHERE 1 $condition $group $sorting $limit", 'webp_crc');
    if (!empty($webp_logs)) {
        /** @var \Tygh\Storefront\Storefront $storefront */
        $storefront = Tygh::$app['storefront'];
        $url = $storefront->url;
        foreach($webp_logs as &$log_data) {
            $log_data['img_http_path'] = Storage::instance('images')->getUrl($log_data['image_path'], '', $url);
            $log_data['webp_http_path'] = Storage::instance('images')->getUrl($log_data['webp_path'], '', $url);
            $log_data['compress_percent'] = round(100-($log_data['webp_size']/$log_data['image_size'])*100);
        }
    }
    return array($webp_logs, $params);
}

function fn_cp_web_convert_to_ignore($image_crc)
{
    if (!empty($image_crc)) {
        $img_path = db_get_row("SELECT image_path,webp_path FROM ?:cp_web_log_images WHERE image_crc = ?i", $image_crc);
        if (!empty($img_path)) {
            if (!empty($img_path['image_path'])) {
                fn_cp_webp_update_ignore_img($img_path['image_path']);
                db_query("DELETE FROM ?:cp_web_image_list WHERE image_crc = ?i", $image_crc);
                db_query("DELETE FROM ?:cp_web_log_images WHERE image_crc = ?i", $image_crc);
            }
            if (!empty($img_path['webp_path'])) {
                Storage::instance('images')->deleteByPattern($img_path['webp_path']);
                Storage::instance('images')->deleteByPattern('thumbnails/*/*/' . $img_path['webp_path']);
            }
        }
    }
    return true;
}

function fn_cp_web_generate_crc_img_names_for_exists()
{
    $page = 0;
    $img_per_page = 1000;
    while ($all_imgs = db_get_array('SELECT * FROM ?:cp_web_image_list WHERE rel_image_crc = ?i ORDER BY image_crc ASC ?p', 0, db_paginate($page, $img_per_page))) {
        $page++;
        if (!empty($all_imgs)) {
            foreach($all_imgs as $img_data) {
                if (!empty($img_data['image_path'])) {
                    $rel_image_crc = fn_cp_web_generate_crc32_field($img_data['image_path']);
                    if (!empty($rel_image_crc)) {
                        db_query("UPDATE ?:cp_web_image_list SET rel_image_crc = ?i WHERE image_crc = ?i", $rel_image_crc, $img_data['image_crc']);
                    }
                }
            }
        }
    }
    unset($all_imgs);
    return true;
}

function fn_cp_web_generate_crc32_field($img_path)
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

function fn_cp_web_remove_rows_by_crc_path($image_crc)
{
    if (!empty($image_crc)) {
        $rel_image_crc = db_get_fields("SELECT rel_image_crc FROM ?:cp_web_image_list WHERE image_crc = ?i AND rel_image_crc > ?i", $image_crc, 0);
        db_query("DELETE FROM ?:cp_web_image_list WHERE image_crc = ?i", $image_crc);
        
        if (!empty($rel_image_crc)) {
            db_query("DELETE FROM ?:cp_web_image_list WHERE rel_image_crc IN (?n)", $rel_image_crc);
        }
    }
    return true;
}
