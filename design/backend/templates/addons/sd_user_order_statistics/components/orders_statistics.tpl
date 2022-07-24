<div class="sidebar-row">
    <h6>{__("orders_statistics")}</h6>
    <div class="sd-user-order-statistics__orders-info">
        <i class="icon-shopping-cart"></i>
        {if $refer_url}
            {if $refer_url == 'direct_link'}
                <p>{__("source")}: {__("direct_link")}</p>
            {else}
                <p>{__("source")}: <a target="_blank" href="{$refer_url}">{$refer_url}</a></p>
            {/if}
        {/if}
        <p>{__("total_orders")}: {if $orders_data.total_orders_search_string}<a target="_blank" href="{$orders_data.total_orders_search_string}">{/if}{$orders_data.total_orders}{if $orders_data.total_orders_search_string}</a>{/if} ({include file="common/price.tpl" value=$orders_data.orders_total})</p>
        <p>{__("paid_orders")}: {if $orders_data.paid_orders_search_string}<a target="_blank" href="{$orders_data.paid_orders_search_string}">{/if}{$orders_data.paid_orders_quantity}{if $orders_data.paid_orders_search_string}</a>{/if} ({include file="common/price.tpl" value=$orders_data.paid_orders_total})</p>
        <p>{__("reviews_quantity")}: {$orders_data.reviews_quantity}</p>
        {strip}
        <p>{__("addons.sd_user_order_statistics.current_orders")}:&nbsp;
            {if $orders_data.current_orders_search_string}
                <a target="_blank" href="{$orders_data.current_orders_search_string}">
            {/if}
            {$orders_data.current_orders_quantity}
            {if $orders_data.current_orders_search_string}
                </a>
            {/if}
            &nbsp;({include file="common/price.tpl" value=$orders_data.current_orders_total})
        </p>
        {/strip}
        {if $orders_data.user_carts_link}
            <p><a target="_blank" href="{$orders_data.user_carts_link}">{__("users_carts")}</a></p>
        {/if}

        {if $_REQUEST['dispatch'] == 'orders.details' && $usergroup_names}
            <p class = "sd-user-order-statistics__usergroup">{__("sd_user_order_statistics_usergroups")}:</p>
            {foreach from=$usergroup_names key=key_id item=usergroup_name}
                <ul type="square" class = "sd-user-order-statistics__groups-list">
                    <li>{$usergroup_name}</li>
                </ul>
            {/foreach}
        {/if}
    </div>
</div>
