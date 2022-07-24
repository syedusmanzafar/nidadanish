{if $sect.header}
    <div class="control-group">
        <h2 class="sw-block-title">{__("{$sect.header}")}</h2>
    </div>
{/if}

<div id="container_sw_allow_vendors_to_register_by_themselves" class="control-group sw_size_2 allow_vendors_to_register_by_themselves">
    <label for="sw_{$sect.setting_data.apply_for_vendor.name}" class="control-label ">
        {__("sw.allow_vendors_to_register_by_themselves")}:
    </label>
    <div class="controls">
        <input type="hidden" name="settings[apply_for_vendor]" value="{"YesNo::NO"|enum}" {if $disable_input}disabled="disabled"{/if} />
        <input type="hidden" name="settings[create_vendor_administrator_account]" value="{$sect.setting_data.create_vendor_administrator_account.value}" {if $disable_input}disabled="disabled"{/if} />
        {include file="common/switcher.tpl"
            checked=($sect.setting_data.apply_for_vendor.value === "YesNo::YES"|enum && $sect.setting_data.create_vendor_administrator_account.value === "YesNo::YES"|enum)
            input_name="settings[apply_for_vendor]"
            input_value="YesNo::YES"|enum
            input_id="sw_`$sect.setting_data.apply_for_vendor.name`"
            input_disabled=$disable_input
        }
    </div>
    <script>
        (function($, _) {
            $('#sw_{$sect.setting_data.apply_for_vendor.name}').on('change', function(e) {
                $('input[name="settings[create_vendor_administrator_account]"]').val(e.target.checked ? "{"YesNo::YES"|enum}" : "{"YesNo::NO"|enum}");
            });
        }(Tygh.$, Tygh));
    </script>
</div>

{if $sect.setting_data.vendors_prior_approval}
    <div id="container_sw_vendors_prior_approval" class="control-group sw_size_2 vendors_prior_approval">
        <label for="sw_{$sect.setting_data.vendors_prior_approval.name}" class="control-label ">
            {__("sw.check_newly_registered_vendors")}:
        </label>
        <div class="controls">
            <input type="hidden" name="settings[vendors_prior_approval]" value="none" {if $disable_input}disabled="disabled"{/if} />
            {include file="common/switcher.tpl"
                checked=($sect.setting_data.vendors_prior_approval.value === "all")
                input_name="settings[vendors_prior_approval]"
                input_value="all"
                input_id="sw_`$sect.setting_data.vendors_prior_approval.name`"
                id="sw_`$sect.setting_data.vendors_prior_approval.name`_id"
                input_disabled=($sect.setting_data.apply_for_vendor.value !== "YesNo::YES"|enum)
            }
        </div>
    </div>
    <script>
        (function($, _) {
            $('#sw_{$sect.setting_data.apply_for_vendor.name}').on('change', function(e) {
                $('#sw_{$sect.setting_data.vendors_prior_approval.name}_id').ceSwitchCheckbox('setActive', e.target.checked);

                $('input[type="hidden"][name="settings[vendors_prior_approval]"]').val(
                    $('#sw_{$sect.setting_data.vendors_prior_approval.name}_id').ceSwitchCheckbox('getStatus')
                        ? "all"
                        : "none"
                );
            });
        }(Tygh.$, Tygh));
    </script>
{/if}

<div id="container_sw_vendor_profile_fields" class="control-group sw_size_2 vendor_profile_fields">
    <label class="control-label">{__("sw.set_up_vendor_profile_fields")}</label>
    <div class="controls">
        {include file="buttons/button.tpl" but_href="profile_fields.manage?profile_type=S" but_text=__("sw.configure") but_role="action" but_target="_blank"}
    </div>
</div>

