<?php
/***************************************************************************
*                                                                          *
*   (c) 2016 ThemeHills - Premium themes and addons					       *
*                                                                          *
****************************************************************************/

use Tygh\Http;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_ath_animate_render_block_register_cache(&$block, $cache_key, $block_schema, $cache_this_block,
            $display_this_block)
{
	if (!empty($block['anim_effect'])) {
		$block['user_class_wrapper'] = $block['user_class'];
		$block['user_class'] = '';

		$smarty = \Tygh::$app['view'];
		$smarty->assign('block', $block);
	}
}

function fn_ath_animate_render_block_content_after($block_schema, $block, &$block_content)
{
	if (!empty($block['anim_effect'])) {
		$smarty = \Tygh::$app['view'];
		$smarty->assign('block_inner', $block_content);
		$block_content = $smarty->fetch('addons/ath_animate/views/block_manager/render/animate_wrapper.tpl');
	}
}

function fn_get_anim_effects_list() {
	$result = json_decode(fn_get_contents(__DIR__ . '/animate-config.json'), true);
	
	return $result;
}

function fn_get_anim_effects_list_settings() {
	$anim_effects = fn_get_anim_effects_list();
	
	$data = array(
        '' => ' -- '
    );

	foreach ($anim_effects as $anim_group_name => $anim_group) {
		foreach ($anim_group as $anim_effect) {                         
			$data[$anim_effect]=$anim_effect;
		}
	}
			
	return $data;
}

function fn_settings_variants_addons_ath_animate_grid() {
			
	return fn_get_anim_effects_list_settings();

}

function fn_settings_variants_addons_ath_animate_list() {
			
	return fn_get_anim_effects_list_settings();

}

function fn_settings_variants_addons_ath_animate_compact_list() {
			
	return fn_get_anim_effects_list_settings();

}

if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}
 
function fn_activate_license_ath_animate( $api_params, $license_server_url ) {
	$api_params['slm_action'] = 'slm_activate';
	return json_decode(Http::get($license_server_url, $api_params), true);
}

function fn_check_license_ath_animate($type) {
	$name = 'ath_animate';
		
	$api_params = array(
		'slm_action' => 'slm_check',
		'secret_key' => '588dcea47787b9.48200647',
		'license_key' => Registry::get('addons.'.$name.'.license'),
		'registered_domain' => Registry::get('config.http_host')
	);
	if (fn_allowed_for('ULTIMATE') && !Registry::get('runtime.simple_ultimate')) {
		$api_params['product_ref'] = 'lt';
	}
	if (fn_allowed_for('MULTIVENDOR')) {
		$api_params['product_ref'] = 'mv';
	}
	if (fn_allowed_for('MULTIVENDOR:ULTIMATE')) {
		$api_params['product_ref'] = 'mvplt';
	}

	$license_server_url = 'http://licenses.themehills.com';

	$response = json_decode(Http::get($license_server_url, $api_params), true);

	if ($response == '') {
		$ativation = false;
		$message = 'license_error_not_available';
	} elseif ($response['result'] == 'success') {
		if ($response['status'] == 'active') {
			$check_host = array_search(Registry::get('config.http_host'), array_column($response['registered_domains'], 'registered_domain'));
			if ($check_host !== false) {
				$ativation = true;
			} elseif ($response['max_allowed_domains'] > count($response['registered_domains'])) {	
				$activate_response = fn_activate_license_ath_animate($api_params, $license_server_url);
				if ($activate_response['result'] == 'success') 
					$ativation = true;
				else {			
					$ativation = false;
					$message = 'license_error_'.$name.'_already_activated';
				}
			} else {
				$ativation = false;
				$message = 'license_error_'.$name.'_domain_limit';
			}
			if ( (fn_allowed_for('ULTIMATE') && !Registry::get('runtime.simple_ultimate')) && ($response['product_ref'] != $api_params['product_ref']) ) {
				$ativation = false;
				$message = 'license_error_'.$name.'_wrong_prod';
			}
		} elseif ( $response['status'] == 'pending' ) {
			if ( (fn_allowed_for('ULTIMATE') && !Registry::get('runtime.simple_ultimate')) && ($response['product_ref'] != $api_params['product_ref']) ) {
					$ativation = false;
					$message = 'license_error_'.$name.'_wrong_prod';
			} else {
				$activate_response = fn_activate_license_ath_animate($api_params, $license_server_url);
				if ($activate_response['result'] == 'success') 
					$ativation = true;
				else {						
					$ativation = false;
					$message = 'license_error_'.$name.'_domain_limit';
				}
			}
		} elseif ( $response['status'] == 'expired' ) {
			$ativation = false;
			$message = 'license_error_'.$name.'_expired';
		} elseif ( $response['status'] == 'blocked' ) {
			$ativation = false;
			$message = 'license_error_'.$name.'_blocked';
		} else {		
			$ativation = false;
			$message = 'license_error_'.$name.'_contect_us';
		}
	} else {
		$ativation = false;
		$message = 'license_error_'.$name.'_contect_us';
	}
	
	if (!$ativation) {		
		db_query("UPDATE ?:addons SET status = ?s WHERE addon = ?s", 'D', $name);
		fn_set_notification('E', __('error'), __($message));
		
		if ($type == 'return') {
			return array(CONTROLLER_STATUS_REDIRECT, 'addons.manage');
		} else {
			exit;
		}
	}
};
