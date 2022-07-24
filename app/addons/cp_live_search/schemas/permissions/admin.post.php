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

$schema['cp_search_cache'] = array(
    'permissions' => array('GET' => 'view_cp_live_search', 'POST' => 'manage_cp_live_search')
);

$schema['cp_search_fields_weight'] = array(
    'modes' => array( 
        'manage' => array(
            'permissions' => 'view_cp_live_search'
        ),
        'update' => array(
            'permissions' => 'manage_cp_live_search'
        )
    ),
    'permissions' => 'manage_cp_live_search'
);

$schema['cp_search_history'] = array(
    'modes' => array(
        'manage' => array(
            'permissions' => 'view_cp_live_search'
        ),
        'clear' => array(
            'permissions' => 'manage_cp_live_search'
        ),
        'm_delete' => array(
            'permissions' => 'manage_cp_live_search'
        ),
        'delete' => array(
            'permissions' => 'manage_cp_live_search'
        )
    ),
    'permissions' => array ('GET' => 'view_cp_live_search', 'POST' => 'manage_cp_live_search')
);

$schema['cp_live_search'] = array(
    'modes' => array (
        'motivation_update' => array(
            'vendor_only' => true,
            'use_company' => true,
            'permissions' => array ('GET' => 'view_cp_live_search', 'POST' => 'manage_cp_live_search')
        ),
        'styles_update' => array(
            'vendor_only' => true,
            'use_company' => true,
            'permissions' => array ('GET' => 'view_cp_live_search', 'POST' => 'manage_cp_live_search')
        )
    )
);

$schema['cp_search_phrases'] = array(
    'modes' => array (
        'manage' => array(
            'vendor_only' => true,
            'use_company' => true,
            'permissions' => 'view_cp_live_search'
        ),
        'update' => array(
            'vendor_only' => true,
            'use_company' => true,
            'permissions' => 'manage_cp_live_search'
        ),
        'm_update' => array(
            'permissions' => 'manage_cp_live_search'
        ),
        'm_delete' => array(
            'permissions' => 'manage_cp_live_search'
        ),
        'delete' => array(
            'permissions' => 'manage_cp_live_search'
        )
    )
);

return $schema;
