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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$company_id = Registry::get('runtime.company_id');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if ($mode == 'rebuild') {

        $lang_codes = array_keys(fn_get_translation_languages());
        $company_ids = empty($company_id) ? fn_get_all_companies_ids() : array($company_id);
        fn_cp_rebuild_search_cache($company_ids, $lang_codes, true);

    } elseif ($mode == 'repair') {

        $lang_codes = array_keys(fn_get_translation_languages());
        $company_ids = empty($company_id) ? fn_get_all_companies_ids() : array($company_id);
        foreach ($company_ids as $company_id) {
            foreach ($lang_codes as $lang_code) {
                fn_cp_modify_search_cache_table('REPAIR', '', $company_id, $lang_code);
            }
        }
        fn_set_notification('N', __('notice'), __('done'));

    } elseif ($mode == 'optimize') {

        $lang_codes = array_keys(fn_get_translation_languages());
        $company_ids = empty($company_id) ? fn_get_all_companies_ids() : array($company_id);
        foreach ($company_ids as $company_id) {
            foreach ($lang_codes as $lang_code) {
                fn_cp_modify_search_cache_table('OPTIMIZE', '', $company_id, $lang_code);
            }
        }
        fn_set_notification('N', __('notice'), __('done'));

    } elseif ($mode == 'drop') {

        $lang_codes = array_keys(fn_get_translation_languages());
        $company_ids = empty($company_id) ? fn_get_all_companies_ids() : array($company_id);
        foreach ($company_ids as $company_id) {
            foreach ($lang_codes as $lang_code) {
                fn_cp_modify_search_cache_table('DROP', '', $company_id, $lang_code);
            }
        }
        fn_set_notification('N', __('notice'), __('done'));

    }

    return array(CONTROLLER_STATUS_OK, 'cp_search_cache.rebuild');
}

if ($mode == 'rebuild') {
    $company_ids = fn_get_all_companies_ids();
    $is_ult = (fn_allowed_for('MULTIVENDOR') != true && count($company_ids) > 1) ? true : false;
    Registry::get('view')->assign('cache_info', fn_cp_live_search_get_cache_info($company_id, $is_ult));

    if ($is_ult && empty($company_id)) {
        Registry::get('view')->assign('all_cron_command', fn_cp_live_search_get_cron_command(array_shift($company_ids), true));
    }
}
