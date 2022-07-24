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

    if ($mode == 'm_update') {
        if (!empty($_REQUEST['search_phrases'])) {
            foreach ($_REQUEST['search_phrases'] as $phrase) {
                if (empty($phrase['phrase_id'])) {
                    continue;
                }
                fn_cp_update_search_phrases($phrase['phrase_id'], $phrase);
            }
        }
    }

    if ($mode == 'delete') {
        if (!empty($_REQUEST['phrase_id'])) {
            fn_cp_delete_search_phrase($_REQUEST['phrase_id']);
        }
    }

    if ($mode == 'm_delete') {
        if (!empty($_REQUEST['phrase_ids'])) {
            foreach ($_REQUEST['phrase_ids'] as $phrase_id) {
                fn_cp_delete_search_phrase($phrase_id);
            }
        }
    }

    if ($mode == 'update') {
        if (!empty($_REQUEST['data'])) {
            $phrase_id = !empty($_REQUEST['phrase_id']) ? $_REQUEST['phrase_id'] : 0;
            fn_cp_update_search_phrases($phrase_id, $_REQUEST['data']);
        }
    }
    
    return array(CONTROLLER_STATUS_OK, 'cp_search_phrases.manage');
}

if ($mode == 'manage') {
    list($search_phrases, $search) = fn_cp_get_search_phrases($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'));

    Registry::get('view')->assign('search_phrases', $search_phrases);
    Registry::get('view')->assign('search', $search);

} elseif ($mode == 'update') {
    if (!empty($_REQUEST['phrase_id'])) {
        $phrase = fn_cp_get_search_phrase($_REQUEST['phrase_id']);
        Registry::get('view')->assign('phrase', $phrase);
    }
}

if ($mode == 'phrases_list') {
    if (!defined('AJAX_REQUEST')) {
        exit;
    }
    $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
    $company_id = fn_cp_live_search_get_company_id(); 
    $objects = array();

    $phrases = fn_cp_get_history_suggestions($q, $company_id, DESCR_SL);
    if (!in_array($q, $phrases)) {
        array_unshift($phrases, $q);
    }
    foreach ($phrases as $phrase) {
        $objects[] = array(
            'id' => $phrase,
            'text' => $phrase
        );
    }
    
    Tygh::$app['ajax']->assign('objects', $objects);
    exit;
}