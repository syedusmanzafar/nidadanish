{if !$runtime.company_id && $special_order_data}
<tr>
    <td class="statistic-label"><h4>{__("sc_united_shipping_separate_order_shipping")}:</h4></td>
    <td class="price right" data-ct-totals="total"><a target="_blank" href="{"orders.details?order_id=`$special_order_data.order_id`"|fn_url}"> {include file="common/price.tpl" value=$special_order_data.total}</a></td>
</tr>
{/if}