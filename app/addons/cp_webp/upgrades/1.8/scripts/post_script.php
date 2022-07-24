<?php

$addon_id = 'cp_webp';
$addon_scheme = Tygh\Addons\SchemesManager::clearInternalCache($addon_id);
$addon_scheme = Tygh\Addons\SchemesManager::getScheme($addon_id);

if (function_exists('fn_get_addon_settings_values')
    && function_exists('fn_get_addon_settings_vendor_values')
) {
    $setting_values = $settings_vendor_values = array();
    $settings_values = fn_get_addon_settings_values($addon_id);
    $settings_vendor_values = fn_get_addon_settings_vendor_values($addon_id);

    fn_update_addon_settings($addon_scheme, true, $settings_values, $settings_vendor_values);
} else {
    fn_update_addon_settings($addon_scheme, true);
}

db_query("REPLACE INTO ?:privileges (privilege, is_default, section_id) VALUES ('view_cp_webp', 'Y', 'addons')");
db_query("REPLACE INTO ?:privileges (privilege, is_default, section_id) VALUES ('manage_cp_webp', 'Y', 'addons')");

if (version_compare(PRODUCT_VERSION, '4.10', '>=')) {
    db_query("UPDATE ?:privileges SET is_view = ?s, group_id = ?s WHERE privilege = ?s", 'Y', 'cp_webp_priv_group', 'view_cp_webp');
    db_query("UPDATE ?:privileges SET group_id = ?s WHERE privilege = ?s", 'cp_webp_priv_group', 'manage_cp_webp');
}