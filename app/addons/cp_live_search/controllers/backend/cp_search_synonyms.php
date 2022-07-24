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
use Tygh\Navigation\LastView;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update') {
        if (!empty($_REQUEST['data'])) {
            $synonym_id = !empty($_REQUEST['synonym_id']) ? $_REQUEST['synonym_id'] : 0;
            fn_cp_update_search_synonym($_REQUEST['synonym_id'], $_REQUEST['data']);
        }
    }

    if ($mode == 'm_update') {
        if (!empty($_REQUEST['search_synonyms'])) {
            foreach ($_REQUEST['search_synonyms'] as $synonym) {
                $synonym_id = !empty($synonym['synonym_id']) ? $synonym['synonym_id'] : 0;
                fn_cp_update_search_synonym($synonym['synonym_id'], $synonym);
            }
        }
    }

    if ($mode == 'delete') {
        if (!empty($_REQUEST['synonym_id'])) {
            fn_cp_delete_search_synonym($_REQUEST['synonym_id']);
        }
    }

    if ($mode == 'm_delete') {
        if (!empty($_REQUEST['synonym_ids'])) {
            foreach ($_REQUEST['synonym_ids'] as $synonym_id) {
                fn_cp_delete_search_synonym($synonym_id);
            }
        }
    }
    
    return array(CONTROLLER_STATUS_OK, 'cp_search_synonyms.manage');
}

if ($mode == 'manage') {
    list($search_synonyms, $search) = fn_cp_get_search_synonyms($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'));

    Registry::get('view')->assign('search_synonyms', $search_synonyms);
    Registry::get('view')->assign('search', $search);

}

if ($mode == 'synonyms_list') {
    if (!defined('AJAX_REQUEST')) {
        exit;
    }
    $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
    $company_id = fn_cp_live_search_get_company_id(); 
    $objects = array();

    $synonyms = fn_cp_get_history_suggestions($q, $company_id, DESCR_SL);
    if (!in_array($q, $synonyms)) {
        array_unshift($synonyms, $q);
    }

    if (!empty($_REQUEST['synonym_id'])) {
        $q_cond = !empty($q) ? db_quote(' AND vars.variant = ?s', $q) : '';
        $exists_phrases = db_get_fields(
            'SELECT variant FROM ?:cp_search_synonym_variants as vars'
            . ' LEFT JOIN ?:cp_search_synonyms as synonyms ON synonyms.synonym_id = vars.synonym_id AND synonyms.lang_code = ?s'
            . ' WHERE vars.synonym_id != ?i AND synonyms.company_id = ?i ?p',
            DESCR_SL, $_REQUEST['synonym_id'], $company_id, $q_cond
        );
        $synonyms = !empty($exists_phrases) ? array_diff($synonyms, $exists_phrases) : $synonyms;
    }
    foreach ($synonyms as $synonym) {
        $objects[] = array(
            'id' => $synonym,
            'text' => $synonym
        );
    }
    
    Tygh::$app['ajax']->assign('objects', $objects);
    exit;
}