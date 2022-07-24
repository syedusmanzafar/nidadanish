{if !$runtime.company_id}
<div class="control-group">
<div class="controls">{__("ec_vendor_cost.calculated_selling_price")}: {include file="common/price.tpl" value=$product_data.ec_calculated_price}</div>
<div class="controls">{__("ec_vendor_cost.calculated_wholesale_selling_price")}: {include file="common/price.tpl" value=$product_data.ec_calculated_wholesale_price}</div>
</div>
{/if}