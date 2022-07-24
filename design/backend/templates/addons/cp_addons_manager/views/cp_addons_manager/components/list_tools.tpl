<div class="cp-addon-tools clearfix" id="cp_addon_tools_{$addon.addon_name}">
    {if $is_am}
        <span class="cp-am-tool cp-am-tooltip" title="{__("cp_addon_status_on")}">
            <i class="cp-am-icon cp-am-icon-is-on"></i>
        </span>
    {elseif $addon.status}
        {if $addon.status == "A"}
            <a href="{"addons.update_status?id=`$addon.addon_name`&status=D&redirect_url=`$c_url`"|fn_url}" class="cp-am-tool cp-am-tooltip cm-ajax cm-post" title="{__("cp_addon_status_on")}" data-ca-target-id="cp_addon_tools_{$addon.addon_name}">
                <i class="cp-am-icon cp-am-icon-is-on"></i>
            </a>
        {else}
            <a href="{"addons.update_status?id=`$addon.addon_name`&status=A&redirect_url=`$c_url`"|fn_url}" class="cp-am-tool cp-am-tooltip cm-ajax cm-post" title="{__("cp_addon_status_off")}" data-ca-target-id="cp_addon_tools_{$addon.addon_name}">
                <i class="cp-am-icon cp-am-icon-is-off"></i>
            </a>
        {/if}
    {else}
        <span class="cp-am-tool cp-am-tooltip" title="{__("cp_addon_status_not_installed")}">
            <i class="cp-am-icon cp-am-icon-is-off cp-am-icon-disabled"></i>
        </span>
    {/if}
    {if $addon.has_settings}
        {if $addon.separate || $smarty.const.PRODUCT_VERSION|version_compare:"4.13":">"}
            <a href="{"addons.update?addon=`$addon.addon_name`"|fn_url}" class="cp-am-tool cp-am-tooltip" title="{__("cp_am_settings")}">
                <i class="cp-am-icon cp-am-icon-settings"></i>
            </a>
        {else}
            <a href="{"addons.update?addon=`$addon.addon_name`&return_url=`$c_url`"|fn_url}" class="cm-dialog-opener cm-ajax cp-am-tool cp-am-tooltip" data-ca-target-id="content_group{$addon.addon_name}" title="{__("cp_am_settings")}"  data-ca-dialog-title="{__("settings")}: {$addon.current_name}">
                <i class="cp-am-icon cp-am-icon-settings"></i>
            </a>
        {/if}
    {else}
        <span href="" class="cp-am-tool cp-am-tooltip" title="{__("cp_am_settings")}">
            <i class="cp-am-icon cp-am-icon-settings cp-am-icon-disabled"></i>
        </span>
    {/if}
    
    {if $addon.documentation_link}
        <a href="{$addon.documentation_link}" target="_blank" class="cp-am-tool cp-am-tooltip" title="{__("cp_am_documentation")}">
            <i class="cp-am-icon cp-am-icon-question"></i>
        </a>
    {else}
        <span class="cp-am-tool cp-am-tooltip" title="{__("cp_am_documentation_not_exists")}">
            <i class="cp-am-icon cp-am-icon-question cp-am-icon-disabled"></i>
        </a>
    {/if}
<!--cp_addon_tools_{$addon.addon_name}--></div>
