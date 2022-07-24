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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

$access_key = !empty($_REQUEST['access_key']) ? $_REQUEST['access_key'] : '';
$company_id = !empty($_REQUEST['company_id']) ? $_REQUEST['company_id'] : Registry::get('runtime.company_id');

if ($access_key != Registry::get('addons.cp_live_search.cron_password')) {
    fn_echo(__('error'));
}

$lang_codes = array_keys(fn_get_translation_languages());

if ($mode == 'generate') {
    $company_ids = empty($company_id) ? array(0) : array($company_id);
    if (!empty($_REQUEST['all'])) {
       $company_ids = fn_get_all_companies_ids();
    }
    
    fn_cp_rebuild_search_cache($company_ids, $lang_codes, false);

    exit;

} elseif ($mode == 'generate_indexes') {
    fn_cp_ls_create_product_indexes($company_id, $lang_codes, true);
    exit;
}
