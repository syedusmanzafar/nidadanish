<div id="container_addon_option_sd_labels_text_label_round_corners">
    <div id="addon_option_sd_labels_text_label_round_corners"
        {if !$runtime.company_id && !fn_allowed_for("MULTIVENDOR") && !$runtime.simple_ultimate}
            class="disable-overlay-wrap"
        {/if}
    >
        {if !$runtime.company_id && !fn_allowed_for("MULTIVENDOR") && !$runtime.simple_ultimate}
            <div class="disable-overlay" id="addon_option_sd_labels_text_label_round_corners_overlay"></div>
        {/if}
        <div class="control-group setting-wide">
            <div class="controls">
                {if fn_allowed_for("ULTIMATE") && !$runtime.company_id}
                    <div class="right update-for-all">
                        {include "buttons/update_for_all.tpl"
                            display=true
                            object_id="text_label_round_corners"
                            name="update_all_text_label_round_corners"
                            hide_element="addon_option_sd_labels_text_label_round_corners"
                        }
                    </div>
                {/if}
            </div>
            <label class="control-label">
                {__("sd_labels.addon_settings.round_corners")}:
            </label>
            <div class="controls">
                <input type="hidden" name="text_label_round_corners" value="N" />
                <input
                    id="text_label_round_corners"
                    type="checkbox"
                    name="text_label_round_corners"
                    value="Y"
                    {if $addons.sd_labels.text_label_round_corners == "YesNo::YES"|enum}checked="checked"{/if}
                    onchange="
                        $(this).prop('checked')
                        ? $('#addon_option_sd_labels_text_label_corner_radius').show()
                        : $('#addon_option_sd_labels_text_label_corner_radius').hide()
                    "
                />
            </div>
        </div>
    </div>

    <div id="addon_option_sd_labels_text_label_corner_radius"
        class="
            {if $addons.sd_labels.text_label_round_corners == "YesNo::NO"|enum}hidden{/if}
            {if !$runtime.company_id && !fn_allowed_for("MULTIVENDOR") && !$runtime.simple_ultimate}
                disable-overlay-wrap
            {/if}
        "
    >
        {if !$runtime.company_id && !fn_allowed_for("MULTIVENDOR") && !$runtime.simple_ultimate}
            <div class="disable-overlay" id="addon_option_sd_labels_text_label_corner_radius_overlay"></div>
        {/if}
        <div class="control-group setting-wide">
            <div class="controls">
                {if fn_allowed_for("ULTIMATE") && !$runtime.company_id}
                    <div class="right update-for-all">
                        {include "buttons/update_for_all.tpl"
                            display=true
                            object_id="text_label_corner_radius"
                            name="update_all_text_label_corner_radius"
                            hide_element="addon_option_sd_labels_text_label_corner_radius"
                        }
                    </div>
                {/if}
            </div>
            <label class="control-label">
                {__("sd_labels.addon_settings.corner_radius")}:
            </label>
            <div class="controls">
                <input
                    id="text_label_corner_radius"
                    type="number"
                    name="text_label_corner_radius"
                    value="{$addons.sd_labels.text_label_corner_radius}"
                    min="1"
                />
            </div>
        </div>
    </div>
</div>

{script src="js/addons/sd_labels/settings.js"}
