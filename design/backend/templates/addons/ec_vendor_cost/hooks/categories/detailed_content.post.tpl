{if !$runtime.company_id}
<div class="control-group">
    <label class="control-label" for="elm_cost_ec_commission">{__("ec_vendor_cost.ec_commission")}:</label>
    <div class="controls">
        <input type="text" name="category_data[ec_commission]" id="elm_cost_ec_commission" size="55" value="{$category_data.ec_commission}" class="input-small cm-value-decimal" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="elm_cost_ec_commission_wholesale">{__("ec_vendor_cost.ec_commission_wholesale")}:</label>
    <div class="controls">
        <input type="text" name="category_data[ec_commission_wholesale]" id="elm_cost_ec_commission_wholesale" size="55" value="{$category_data.ec_commission_wholesale}" class="input-small cm-value-decimal" />
    </div>
</div>
{/if}