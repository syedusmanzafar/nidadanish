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

$menu = array(
    'position' => 600,
    'attrs' => array(
        'class'=>'is-addon'
     ),
    'subitems' => array(
        'cp_live_search_cache' => array(
            'href' => 'cp_search_cache.rebuild',
            'position' => 110
        ),
        'fields_weight' => array(
            'href' => 'cp_search_fields_weight.manage',
            'position' => 120
        ),
        'search_history' => array(
            'href' => 'cp_search_history.manage&section=all',
            'position' => 130
        ),
        'cp_search_phrases' => array(
            'href' => 'cp_search_phrases.manage',
            'position' => 140
        ),
        'cp_search_synonyms' => array(
            'href' => 'cp_search_synonyms.manage',
            'position' => 145
        ),
        'cp_search_motivation' => array(
            'href' => 'cp_live_search.motivation_update',
            'position' => 150
        ),
        'cp_ls_stylization' => array(
            'href' => 'cp_live_search.styles_update',
            'position' => 160
        ),
    ),
    'href' => 'cp_search_cache.rebuild',
);

if (isset($schema['central']['cart_power_addons'])) {
    $schema['central']['cart_power_addons']['items']['cp_live_search'] = $menu;
} else {
    $schema['central']['website']['items']['cp_live_search'] = $menu;
}

$schema['top']['administration']['items']['export_data']['subitems']['cp_ls_search_history'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'exim.export?section=cp_ls_search_history',
    'position' => 901
);

$schema['top']['administration']['items']['export_data']['subitems']['cp_ls_search_phrases'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'exim.export?section=cp_ls_search_phrases',
    'position' => 902
);

$schema['top']['administration']['items']['export_data']['subitems']['cp_ls_search_synonyms'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'exim.export?section=cp_ls_search_synonyms',
    'position' => 903
);

$schema['top']['administration']['items']['import_data']['subitems']['cp_ls_search_phrases'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'exim.import?section=cp_ls_search_phrases',
    'position' => 901
);

$schema['top']['administration']['items']['import_data']['subitems']['cp_ls_search_synonyms'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'exim.import?section=cp_ls_search_synonyms',
    'position' => 902
);

return $schema;
