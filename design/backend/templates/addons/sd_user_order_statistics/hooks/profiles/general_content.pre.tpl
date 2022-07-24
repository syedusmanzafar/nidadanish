{if $orders_data}
    {capture name="sidebar"}
        {include file="addons/sd_user_order_statistics/components/orders_statistics.tpl"}
    {/capture}
{/if}
