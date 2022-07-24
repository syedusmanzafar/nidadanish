{if $smarty.request.user_type == 'C'}
    <td class="row-status" data-th="{__("addons.sd_user_order_statistics.paid_orders")}">{include file="common/price.tpl" value=$user.paid_orders}</td>
{/if}
