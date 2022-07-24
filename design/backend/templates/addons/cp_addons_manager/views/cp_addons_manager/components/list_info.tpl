<div class="cp-addon-info-wrap">
    <div class="cp-addons-list-title">
        {*<a class="cm-dialog-opener cm-dialog-auto-size cm-ajax cp-addon-title" href="{"cp_addons_manager.addon_info&product_id=`$addon.product_id`"|fn_url}" data-ca-dialog-title="{__("cp_addon_info")}" data-ca-target-id="content_addon_{$addon.product_id}">{$addon.product}</a>*}
        {$addon.product}
    </div>
    <div class="cp-addons-list-description">
        {$addon.short_description|default:$addon.short_description nofilter}
    </div>
</div>
<div class="cp-versions-info-wrap">
    <div class="cp-versions-title">{__("cp_am_versions")}:</div>
    <div class="cp-versions">
        {if $addon.installed_version}
            <div class="cp-version cp-installed-version">
                {__("cp_installed_version")}:&nbsp;<span class="strong">v{$addon.installed_version}</span>
                <span class="cp-am-tooltip" title="{include file="addons/cp_addons_manager/views/cp_addons_manager/components/release_info.tpl" release=$addon.versions.current}">
                    <i class="cp-am-icon cp-am-icon-question"></i>
                </span>
            </div>
        {/if}
        {if $addon.version}
            <div class="cp-version cp-available-version">
                {__("cp_available_version")}: <span class="strong">v{$addon.version}</span>
                <span class="cp-am-tooltip" title="{include file="addons/cp_addons_manager/views/cp_addons_manager/components/release_info.tpl" release=$addon}">
                    <i class="cp-am-icon cp-am-icon-question"></i>
                </span>
            </div>
        {/if}
        {if $addon.versions.latest}
            <div class="cp-version cp-actual-version">
                {__("cp_actual_version")}: <span class="strong">v{$addon.versions.latest.version}</span>
                <span class="cp-am-tooltip" title="{include file="addons/cp_addons_manager/views/cp_addons_manager/components/release_info.tpl" release=$addon.versions.latest}">
                    <i class="cp-am-icon cp-am-icon-question"></i>
                </span>
            </div>
        {/if}
    </div>
</div>
