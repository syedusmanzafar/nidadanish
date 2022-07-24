<form action="{""|fn_url}" method="post" name="cp_addon_action_form_{$key}">
    <input type="hidden" name="product_id" value="{$addon.product_id}">
    <input type="hidden" name="license_key" value="{$addon.license_key}">
    <input type="hidden" name="result_ids" value="addon_upload_container">
    {assign var="has_error" value=false}
    {if $addon.domain_without_licenses || $addon.domain_without_validation || $addon.error_messages}
        {$has_error = true}
    {/if}
    <div class="cp-addon-action-wrap clearfix">
        {if version_compare($addon.installed_version, $addon.version) < 0}
            <div class="cp-addon-action-title">{__("cp_am_can_install_avail_version")}</div>
            <div class="cp-version-link">
                <a class="cp-am-link cm-dialog-opener cm-dialog-auto-size cm-ajax" href="{"cp_addons_manager.version_info&package_id=`$addon.package_id`"|fn_url}" data-ca-dialog-title="{__("cp_versions_history")}" data-ca-target-id="content_versions_{$addon.package_id}"><span class="cp-am-version">v{$addon.version}</span></a>
            </div>
            {if !$has_error}
            <div class="cp-addon-action-buttons">
                {if $addon.status}
                    {if !$addon.versions.current}
                        {assign var="inst_link" value=$extra.install_instruction|default:"#"}
                        <div class="cp-addon-reinstall-text">
                            {__("cp_am_need_reinstall", ["[link]" => $inst_link])}
                        </div>
                    {else}
                        {if !$is_am}
                            <a class="cp-am-btn cp-am-blue cm-submit cm-ajax cp-am-tooltip" title="{__("cp_upgrade_tooltip_text")}" data-ca-dispatch="dispatch[cp_addons_manager.upgrade]" data-ca-target-form="cp_addon_action_form_{$key}">{__("cp_upgrade_to")}&nbsp;v{$addon.version}</a>
                        {/if}
                        <div class="cp-uc-link-wrap">
                            {if !$is_am}<span>{__("or")}</span>{/if}
                            <a class="cp-uc-link" target="_blank" href="{"upgrade_center.refresh"|fn_url}">{__("cp_update_via_uc")}</a>
                        </div>
                    {/if}
                {else}
                    <a class="cp-am-btn cp-am-blue cm-submit cm-ajax" data-ca-dispatch="dispatch[cp_addons_manager.install]" data-ca-target-form="cp_addon_action_form_{$key}">{__("cp_install")}&nbsp;v{$addon.version}</a>
                {/if}
            </div>
            {/if}
        {else}
            <div class="cp-addon-action-title">{__("cp_am_installed_avail_version")}</div>
            <div class="cp-version-link">
                <a class="cp-am-link cm-dialog-opener cm-dialog-auto-size cm-ajax" href="{"cp_addons_manager.version_info&package_id=`$addon.package_id`"|fn_url}" data-ca-dialog-title="{__("cp_versions_history")}" data-ca-target-id="content_versions_{$addon.package_id}"><span class="cp-am-version">v{$addon.installed_version}</span></a>
            </div>
        {/if}
    </div>
    {if $addon.extra_info}
        <div class="cp-addon-extra-info clearfix">
            {if $addon.extra_info|is_array}
                {foreach from=$addon.extra_info item="extra_info_item"}
                    <div class="cp-extra-info-item">{$extra_info_item nofilter}</div>
                {/foreach}
            {else}
                {$addon.extra_info nofilter}
            {/if}
        </div>
    {/if}
    {if $has_error}
        <div class="cp-addon-error-wrap clearfix">
            {$error_domains=[]}
            {if $addon.domain_without_licenses}
                {$error_domains = $addon.domain_without_licenses}
            {/if}
            {if $addon.domain_without_validation}
                {$error_domains = $error_domains|fn_array_merge:$addon.domain_without_validation}
            {/if}
            {if $error_domains}
            <div class="cp-addon-error">
                <div class="cp-addon-error-title">
                    {__("cp_no_license_for_domains")}&nbsp;<span class="cp-am-tooltip" title="{__("cp_domains_error_reasons")}"><i class="cp-am-icon cp-am-icon-question"></i></span>
                </div>
                <div class="cp-addon-error-message-item">
                    <ul>
                        {foreach from=$error_domains item=domain}
                            <li>{$domain}</li>
                        {/foreach}
                    </ul>
                </div>
            </div>
            {/if}
            {if $addon.error_messages}
            <div class="cp-addon-error">
                <div class="cp-addon-error-title">{__("error")}</div>
                <div class="cp-addon-error-message-item">
                    <ul>
                        {foreach from=$addon.error_messages item=message}
                            <li>{$message nofilter}</li>
                        {/foreach}
                    </ul>
                </div>
            </div>
            {/if}
            <a class="cp-am-btn cp-am-red" target="_blank" href="{"cp_addons_manager.support"|fn_url}">{__("cp_am_contact_us")}</a>
        </div>
    {/if}
    
    {if !$without_subscr}
        <div class="cp-addon-subscr-wrap clearfix">
            {capture name="valid_date"}
                <span class="strong">{$addon.valid_till|date_format:"`$settings.Appearance.date_format`"}</span>
            {/capture}
            
            {if $addon.license_active == "Y"}
                <div class="cp-addon-subscr-title">
                    <span class="cp-addon-subscr-exists">{__("cp_am_subscribe_exists", ["[date]" => $smarty.capture.valid_date])}</span>
                </div>
                {*<div class="cp-addon-subscr-text">{__("cp_am_subscribe_exists_text")}</div>*}
                <div class="cp-addon-subscr-buttons">
                    <a class="cp-am-btn cp-am-green" target="_blank" href="{"cp_addons_manager.prolongate&product_id=`$addon.product_id`"|fn_url}">{__("cp_am_prolong_subscr")}</a>
                </div>
            {else}
                <div class="cp-addon-subscr-title">
                    <span class="cp-addon-subscr-expired">{__("cp_am_subscribe_expired", ["[date]" => $smarty.capture.valid_date])}</span>
                </div>
                {*<div class="cp-addon-subscr-text">{__("cp_am_subscribe_expired_text")}</div>*}
                <div class="cp-addon-subscr-buttons">
                    <a class="cp-am-btn cp-am-red" target="_blank" href="{"cp_addons_manager.prolongate&product_id=`$addon.product_id`"|fn_url}">{__("cp_am_buy_subscr")}</a>
                </div>
            {/if}
        </div>
    {/if}
</form>
