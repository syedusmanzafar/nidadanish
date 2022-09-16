<tr class="cm-longtap-target"
    data-ca-longtap-action="setCheckBox"
    data-ca-longtap-target="input.cm-item"
    data-ca-id="{$o.order_id}"
>
    <td width="6%" class="left mobile-hide">

        <input type="checkbox" name="order_ids[]" value="{$o.order_id}" class="cm-item cm-item-status-{$o.status|lower} hide" /></td>
    <td width="15%" data-th="{__("id")}">
        <div>
            {if $o.is_parent_order  == "Y"}
            <a href="#" alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" id="on_cp_suborders_{$o.order_id}" class="cm-combination">
                <span class="icon-caret-right"> </span>
            </a>
            <a href="#" alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" id="off_cp_suborders_{$o.order_id}" class="cm-combination" style="display: none;">
                <span class="icon-caret-down"> </span>
            </a>
            {/if}
            <a href="{"orders.details?order_id=`$o.order_id`"|fn_url}" class="underlined">{__("order")} <bdi>#{$o.order_id}</bdi></a>
            {if $order_statuses[$o.status].params.appearance_type == "I" && $o.invoice_id}
                <p class="muted">{__("invoice")} #{$o.invoice_id}</p>
            {elseif $order_statuses[$o.status].params.appearance_type == "C" && $o.credit_memo_id}
                <p class="muted">{__("credit_memo")} #{$o.credit_memo_id}</p>
            {/if}
            {include file="views/companies/components/company_name.tpl" object=$o}
            {if $o.childs}
                <p class="muted"><small>{__("cp_show_suborders")}</small></p>
            {/if}
        </div>
    </td>
    <td width="15%" data-th="{__("status")}">
    {if !$o.childs}
        {include file="common/select_popup.tpl"
        suffix="o"
        order_info=$o
        id=$o.order_id
        status=$o.status
        items_status=$order_status_descr
        update_controller="orders"
        notify=$notify
        notify_department=$notify_department
        notify_vendor=$notify_vendor
        status_target_id="orders_total,`$rev`"
        extra="&return_url=`$extra_status`"
        statuses=$order_statuses
        btn_meta="btn btn-info o-status-`$o.status` order-status btn-small"|lower
        text_wrap=true
        }
        {if $o.issuer_id}
            {if $o.issuer_name|trim}
                <p class="muted shift-left manager-order">{$o.issuer_name}</p>
            {else}
                <p class="muted shift-left manager-order">{$o.issuer_email}</p>
            {/if}
        {/if}
    {/if}
    </td>
    <td width="15%" class="nowrap" data-th="{__("date")}">{$o.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
    <td width="17%" data-th="{__("customer")}">
        {if $o.email}<a href="mailto:{$o.email|escape:url}">@</a> {/if}
        {if $o.user_id}<a href="{"profiles.update?user_id=`$o.user_id`"|fn_url}">{/if}{$o.lastname} {$o.firstname}{if $o.user_id}</a>{/if}
        {if $o.company}<p class="muted">{$o.company}</p>{/if}
    </td>
    <td width="14%" {if $o.phone}data-th="{__("phone")}"{/if}><bdi><a href="tel:{$o.phone}">{$o.phone}</a></bdi></td>

    {hook name="orders:manage_data"}{/hook}

    <td class="center" data-th="{__("tools")}">
        {capture name="tools_items"}
            <li>{btn type="list" href="orders.details?order_id=`$o.order_id`" text={__("view")}}</li>
            {hook name="orders:list_extra_links"}
                <li>{btn type="list" href="order_management.edit?order_id=`$o.order_id`" text={__("edit")}}</li>
                <li>{btn type="list" href="order_management.edit?order_id=`$o.order_id`&copy=1" text={__("copy")}}</li>
            {$current_redirect_url=$config.current_url|escape:url}
                <li>{btn type="list" href="orders.delete?order_id=`$o.order_id`&redirect_url=`$current_redirect_url`" class="cm-confirm" text={__("delete")} method="POST"}</li>
            {/hook}
        {/capture}
        <div class="hidden-tools">
            {dropdown content=$smarty.capture.tools_items}
        </div>
    </td>
    <td width="10%" class="right" data-th="{__("total")}">
        {include file="common/price.tpl" value=$o.total}
    </td>
</tr>


{if $o.childs}
    <tbody class="hidden" id="cp_suborders_{$o.order_id}">
    {foreach from=$o.childs item="suborder"}
        <tr class="suborders_{$suborder.order_id} cp_suborders  cp_suborders_{$o.order_id}">
            <td width="6%" class="left mobile-hide">

                <input type="checkbox" name="order_ids[]" value="{$suborder.order_id}" class="cm-item cm-item-status-{$suborder.status|lower} hide" /></td>
            <td  class="sg_order_sub_row_padding"  width="15%" data-th="{__("id")}">
                <a href="{"orders.details?order_id=`$suborder.order_id`"|fn_url}" class="underlined">{__("order")} <bdi>#{$suborder.order_id}</bdi></a>
                {if $suborderrder_statuses[$suborder.status].params.appearance_type == "I" && $suborder.invoice_id}
                    <p class="muted">{__("invoice")} #{$suborder.invoice_id}</p>
                {elseif $suborderrder_statuses[$suborder.status].params.appearance_type == "C" && $suborder.credit_memo_id}
                    <p class="muted">{__("credit_memo")} #{$suborder.credit_memo_id}</p>
                {/if}
                {include file="views/companies/components/company_name.tpl" object=$suborder}
            </td>
            <td width="15%" data-th="{__("status")}">
                {include file="common/select_popup.tpl"
                suffix="o"
                order_info=$suborder
                id=$suborder.order_id
                status=$suborder.status
                items_status=$order_status_descr
                update_controller="orders"
                notify=$notify
                notify_department=$notify_department
                notify_vendor=$notify_vendor
                status_target_id="orders_total,`$rev`"
                extra="&return_url=`$extra_status`"
                statuses=$order_statuses
                btn_meta="btn btn-info o-status-`$suborder.status` order-status btn-small"|lower
                text_wrap=true
                }
                {if $suborder.issuer_id}
                    {if $suborder.issuer_name|trim}
                        <p class="muted shift-left manager-order">{$suborder.issuer_name}</p>
                    {else}
                        <p class="muted shift-left manager-order">{$suborder.issuer_email}</p>
                    {/if}
                {/if}
            </td>
            <td width="15%" class="nowrap" data-th="{__("date")}">{$suborder.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
            <td width="17%" data-th="{__("customer")}">
                {if $suborder.email}<a href="mailto:{$suborder.email|escape:url}">@</a> {/if}
                {if $suborder.user_id}<a href="{"profiles.update?user_id=`$suborder.user_id`"|fn_url}">{/if}{$suborder.lastname} {$suborder.firstname}{if $suborder.user_id}</a>{/if}
                {if $suborder.company}<p class="muted">{$suborder.company}</p>{/if}
            </td>
            <td width="14%" {if $suborder.phone}data-th="{__("phone")}"{/if}><bdi><a href="tel:{$suborder.phone}">{$suborder.phone}</a></bdi></td>

            {hook name="orders:manage_data"}{/hook}
            <td class="center" data-th="{__("tools")}">
                {capture name="tools_items"}
                    <li>{btn type="list" href="orders.details?order_id=`$suborder.order_id`" text={__("view")}}</li>

                        <li>{btn type="list" href="order_management.edit?order_id=`$suborder.order_id`" text={__("edit")}}</li>
                        <li>{btn type="list" href="order_management.edit?order_id=`$suborder.order_id`&copy=1" text={__("copy")}}</li>
                    {$current_redirect_url=$config.current_url|escape:url}
                        <li>{btn type="list" href="orders.delete?order_id=`$suborder.order_id`&redirect_url=`$current_redirect_url`" class="cm-confirm" text={__("delete")} method="POST"}</li>

                {/capture}
                <div class="hidden-tools">
                    {dropdown content=$smarty.capture.tools_items}
                </div>
            </td>
            <td width="10%" class="right" data-th="{__("total")}">
                {include file="common/price.tpl" value=$suborder.total}
            </td>
        </tr>

    {/foreach}
    </tbody>
{/if}