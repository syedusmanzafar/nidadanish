{if !$runtime.company_id}
<p>{__("ec_vendor_cost.calculated_selling_price")}: {include file="common/price.tpl" value=$product.ec_calculated_price}</p>
<p>{__("ec_vendor_cost.calculated_wholesale_selling_price")}: {include file="common/price.tpl" value=$product.ec_calculated_wholesale_price}</p>

{/if}