{if $additinal_orders}
    {include file="common/subheader.tpl" title=__("cp_one_order_childs") target="#cp_one_order_childs"}
    <div id="cp_one_order_childs" class="collapse in">
        <table width="100%" class="table table-middle">
            <thead>
            <tr>
                <th width="15%">{__("id")}</th>
                <th width="15%">{__("status")}</th>
                <th width="15%">{__("date")}</th>
                <th width="15%">{__("total")}</th>
            </tr>
            </thead>
            {foreach from=$additinal_orders item="o"}
                <tr>
                    <td>        <a href="{"orders.details&order_id=`$o.order_id`"|fn_url}" class="underlined">{__("order")} #{$o.order_id}</a></td>
                    <td>  {include file="common/select_popup.tpl" suffix="o" order_info=$o id=$o.order_id status=$o.status items_status=$order_status_descr update_controller="orders" notify=true notify_department=true notify_vendor=$notify_vendor status_target_id="orders_total,`$rev`" extra="&return_url=`$extra_status`" statuses=$order_statuses btn_meta="btn btn-info o-status-`$o.status` btn-small"|lower}
                    </td>
                    <td>{$o.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
                    <td>  {include file="common/price.tpl" value=$o.total}</td>
                </tr>
            {/foreach}
        </table>
    </div>

{/if}
