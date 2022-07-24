{if $sect.header}
    <div class="control-group">
        <h2 class="sw-block-title">{__("{$sect.header}")}</h2>
    </div>
{/if}

<div id="container_sw_products_prior_approval" class="control-group sw_size_2 products_prior_approval">
    <label for="sw_products_prior_approval" class="control-label ">
        {__("sw.products_prior_approval")}:
    </label>
    <div class="controls">
        <input type="hidden" name="settings[products_prior_approval]" value="none" {if $disable_input}disabled="disabled"{/if} />
        {include file="common/switcher.tpl"
            checked=($sect.setting_data.products_prior_approval === "all")
            input_name="settings[products_prior_approval]"
            input_value="all"
            input_id="sw_products_prior_approval"
        }
    </div>
</div>

<div id="container_sw_products_updates_approval" class="control-group sw_size_2 products_updates_approval">
    <label for="sw_products_updates_approval" class="control-label ">
        {__("sw.products_updates_approval")}:
    </label>
    <div class="controls">
        <input type="hidden" name="settings[products_updates_approval]" value="none" {if $disable_input}disabled="disabled"{/if} />
        {include file="common/switcher.tpl"
            checked=($sect.setting_data.products_updates_approval === "all")
            input_name="settings[products_updates_approval]"
            input_value="all"
            input_id="sw_products_updates_approval"
            input_class="cm-submit"
        }
        {if ($sect.setting_data.products_updates_approval === "all")}
            {include file="buttons/button.tpl" but_href="addons.update&addon=vendor_data_premoderation&selected_section=settings&selected_sub_section=vendor_data_premoderation_products" but_text=__("sw.configure") but_role="action" but_target="_blank" but_meta="shift-left"}
        {/if}
    </div>
</div>

<div id="container_sw_vendor_profile_updates_approval" class="control-group sw_size_2 vendor_profile_updates_approval">
    <label for="sw_vendor_profile_updates_approval" class="control-label ">
        {__("sw.vendor_profile_updates_approval")}:
    </label>
    <div class="controls">
        <input type="hidden" name="settings[vendor_profile_updates_approval]" value="none" {if $disable_input}disabled="disabled"{/if} />
        {include file="common/switcher.tpl"
            checked=($sect.setting_data.vendor_profile_updates_approval === "all")
            input_name="settings[vendor_profile_updates_approval]"
            input_value="all"
            input_id="sw_vendor_profile_updates_approval"
        }
    </div>
</div>
