{if $feature_type == "ProductFeatures::EXTENDED"|enum && ($runtime.company_id && "ULTIMATE"|fn_allowed_for || "MULTIVENDOR"|fn_allowed_for)}
{if !$runtime.company_id}
<div class="control-group">
    <label class="control-label" for="elm_cost_ec_commission_wholesale{$num}">{__("ec_vendor_cost.ec_commission")}:</label>
    <div class="controls">
        <input type="text" name="feature_data[variants][{$num}][ec_commission]" id="elm_cost_ec_commission{$num}" size="55" value="{$var.ec_commission}" class="input-small cm-value-decimal" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="elm_cost_ec_commission_wholesale{$num}">{__("ec_vendor_cost.ec_commission_wholesale")}:</label>
    <div class="controls">
        <input type="text" name="feature_data[variants][{$num}][ec_commission_wholesale]" id="elm_cost_ec_commission_wholesale{$num}" size="55" value="{$var.ec_commission_wholesale}" class="input-small cm-value-decimal" />
    </div>
</div>
{/if}
{/if}