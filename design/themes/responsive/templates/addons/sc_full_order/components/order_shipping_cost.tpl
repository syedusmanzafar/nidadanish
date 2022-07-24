{if $order_info.shipping_cost}
    <tr class="ty-orders-summary__row-vendor">
        <td>{if $cp_show_company}- {$order_info.company_id|fn_get_company_name}{/if}</td>
        <td data-ct-orders-summary="summary-shipcost">{include file="common/price.tpl" value=$order_info.shipping_cost}</td>
    </tr>
{/if}