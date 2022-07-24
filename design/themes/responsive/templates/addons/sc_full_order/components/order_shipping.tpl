{if $order_info.shipping}
    
    <tr class="ty-orders-summary__row-vendor">
        <td>{if $cp_show_company}- {$order_info.company_id|fn_get_company_name}{/if}</td>
        <td data-ct-orders-summary="summary-ship">
            {hook name="orders:totals_shipping"}
            {if $use_shipments}
                <ul>
                    {foreach from=$order_info.shipping item="shipping_method"}
                        <li>{if $shipping_method.shipping} {$shipping_method.shipping} {else} – {/if}</li>
                    {/foreach}
                </ul>
            {else}
                {foreach from=$order_info.shipping item="shipping" name="f_shipp"}

                    {if $shipments[$shipping.group_key].carrier_info.tracking_url && $shipments[$shipping.group_key].tracking_number}
                        {$shipping.shipping}&nbsp;({__("tracking_number")}: <a target="_blank" href="{$shipments[$shipping.group_key].carrier_info.tracking_url nofilter}">{$shipments[$shipping.group_key].tracking_number}</a>)
                        {$shipment.carrier_info.info nofilter}
                    {elseif $shipments[$shipping.group_key].tracking_number}
                        {$shipping.shipping}&nbsp;({__("tracking_number")}: {$shipments[$shipping.group_key].tracking_number})
                        {$shipment.carrier_info.info nofilter}
                    {elseif $shipments[$shipping.group_key].carrier}
                        {$shipping.shipping}&nbsp;({__("carrier")}: {$shipments[$shipping.group_key].carrier_info.name nofilter})
                        {$shipment.carrier_info.info nofilter}
                    {else}
                        {$shipping.shipping}
                    {/if}
                    {if !$smarty.foreach.f_shipp.last}<br>{/if}
                {/foreach}
            {/if}
            {/hook}
        </td>
    </tr>
{/if}