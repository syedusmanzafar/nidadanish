{include "common/widget_copy.tpl"
    widget_copy_title       = __("tip")
    widget_copy_text        = __("sd_labels.addon_settings.cron_configuration_info")
    widget_copy_code_text   = "php {$smarty.const.DIR_ROOT}"|fn_get_console_command:$config.admin_index:[
        "dispatch"      => "sd_labels_cron.assign",
        "cron_password" => $settings.Security.cron_password,
        "p"
    ]
}
