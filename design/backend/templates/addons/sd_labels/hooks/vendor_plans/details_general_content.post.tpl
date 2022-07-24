<div class="control-group">
    <label class="control-label" for="elm_is_default_{$id}">
        {__("sd_labels.available_for_vendor_plan")}:
    </label>
    <div class="controls">
        <input type="hidden" name="plan_data[sd_available_for_vendors]" value={"YesNo::NO"|enum} />
        <input
            type="checkbox"
            id="elm_is_default_{$id}"
            name="plan_data[sd_available_for_vendors]"
            size="10"
            value="{"YesNo::YES"|enum}"
            {if $plan.sd_available_for_vendors === "YesNo::YES"|enum}checked="checked"{/if}
        />
    </div>
</div>
