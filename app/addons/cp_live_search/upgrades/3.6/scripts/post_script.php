<?php

$addon_id = 'cp_live_search';
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

if (fn_allowed_for('MULTIVENDOR:ULTIMATE')) {
    $storefront_ids = db_get_fields('SELECT storefront_id FROM ?:storefronts');
    // Add storefronts styles
    $settings = Tygh\Settings::instance()->getValues('cp_live_search', 'ADDON');
    if (!empty($settings['display_options']['style_settings'])) {
        $styles = $settings['display_options']['style_settings'];
        foreach ($storefront_ids as $storefront_id) {
            Tygh\Settings::instance()->updateValue('style_settings', $styles, 'cp_live_search', false, null, true, $storefront_id);
        }
    }
    // Add storefronts motivation
    $lang_codes = array_keys(fn_get_translation_languages());
    foreach ($lang_codes as $lang_code) {
        $data = array(
            'object_type' => 'D',
            'object_id' => 0,
            'company_id' => 0,
            'lang_code' => $lang_code,
        );
        $content = db_get_field('SELECT content FROM ?:cp_search_motivation WHERE ?w', $data);
        if (!empty($content)) {
            $data['content'] = $content;
            foreach ($storefront_ids as $data['object_id']) {
                db_query('REPLACE INTO ?:cp_search_motivation ?e', $data);
            }
        }
    }
}