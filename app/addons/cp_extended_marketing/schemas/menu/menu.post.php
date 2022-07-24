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

$schema['central']['marketing']['items']['cp_em_menu_txt'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'cp_em_notices.manage?type=all',
    'position' => 100,
    'subitems' => array(
        'cp_em_menu_aband' => array(
            'href' => 'cp_em_notices.manage?type=A',
            'position' => 203
        ),
        'cp_em_menu_wislist' => array(
            'href' => 'cp_em_notices.manage?type=W',
            'position' => 204
        ),
        'cp_em_menu_feedb' => array(
            'href' => 'cp_em_notices.manage?type=O',
            'position' => 205
        ),
        'cp_em_menu_viewed' => array(
            'href' => 'cp_em_notices.manage?type=V',
            'position' => 210
        ),
        'cp_em_menu_target' => array(
            'href' => 'cp_em_notices.manage?type=T',
            'position' => 215
        ),
        'cp_em_menu_audience' => array(
            'href' => 'cp_em_notices.manage?type=P',
            'position' => 220
        ),
        'cp_em_audiences' => array(
            'href' => 'cp_em_audience.manage',
            'position' => 225
        ),
        'cp_em_statistics' => array(
            'href' => 'cp_em_stats.manage',
            'position' => 230
        ),
        'cp_em_coupons_page' => array(
            'href' => 'cp_em_coupons.manage',
            'position' => 235
        ),
        'cp_em_email_logs' => array(
            'href' => 'cp_em_logs.manage',
            'position' => 240
        ),
        'cp_em_product_placeholders' => array(
            'href' => 'cp_em_placeholders.manage',
            'position' => 245
        ),
    )
);

return $schema;
