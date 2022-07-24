<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

$schema['pinta_facebook_feed'] = array(
    'permissions' => array('GET' => 'view_pinta_facebook_feed', 'POST' => 'manage_pinta_facebook_feed'),
    'modes' => array(
        'delete' => array(
            'permissions' => 'manage_pinta_facebook_feed'
        )
    ),
);

$schema['tools']['modes']['update_status']['param_permissions']['table']['pinta_facebook_feed'] = 'manage_pinta_facebook_feed';

return $schema;
