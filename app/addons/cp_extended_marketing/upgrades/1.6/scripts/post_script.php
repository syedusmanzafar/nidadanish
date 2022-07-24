<?php

$addon_id = 'cp_extended_marketing';
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

$tables = array('cp_em_aband_cart_sent','cp_em_feedback_sent','cp_em_targeted_sent','cp_em_viewed_sent');
foreach($tables as $table) {
    db_query("UPDATE ?:$table SET hash = CRC32(hash)");
    db_query("ALTER TABLE ?:$table CHANGE hash hash int(11) unsigned NOT NULL default '0'");
    db_query("CREATE INDEX hash ON ?:$table(hash)");
    db_query("CREATE INDEX in_queue ON ?:$table(in_queue)");
}