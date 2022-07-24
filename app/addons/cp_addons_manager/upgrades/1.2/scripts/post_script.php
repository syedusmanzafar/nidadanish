<?php

$addon_id = 'cp_addons_manager';
$addon_scheme = Tygh\Addons\SchemesManager::clearInternalCache($addon_id);
$addon_scheme = Tygh\Addons\SchemesManager::getScheme($addon_id);

if (function_exists('fn_update_addon_language_variables')) {
    fn_update_addon_language_variables($addon_scheme);
}

if (version_compare(PRODUCT_VERSION, '4.10', '>=')){
    db_query('UPDATE ?:privileges SET group_id = ?s WHERE privilege = ?s', 'cp_addons_manager', 'manage_cp_addons_manager');
}