{capture name="section"}
    {include file="views/orders/components/orders_search_form.tpl"}
{/capture}
{include file="common/section.tpl" section_title=__("search_options") section_content=$smarty.capture.section class="ty-search-form" collapse=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{if $search.sort_order == "asc"}
    {assign var="sort_sign" value="<i class=\"ty-icon-down-dir\"></i>"}
{else}
    {assign var="sort_sign" value="<i class=\"ty-icon-up-dir\"></i>"}
{/if}
{if !$config.tweaks.disable_dhtml}
    {assign var="ajax_class" value="cm-ajax"}

{/if}

{include file="common/pagination.tpl"}

<table class="ty-table ty-orders-search">
    <thead>
    <tr>
        <th class="ty-orders-search-th__id">
            <a class="{$ajax_class}" href="{"`$c_url`&sort_by=order_id&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                {__("id")}
            </a>
            {if $search.sort_by == "order_id"}{$sort_sign nofilter}{/if}
        </th>
    
        <th class="ty-orders-search-th__status">
            <a class="{$ajax_class}" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                {__("status")}
            </a>
            {if $search.sort_by == "status"}{$sort_sign nofilter}{/if}
        </th>
        <th class="ty-orders-search-th__customer">
            <a class="{$ajax_class}" href="{"`$c_url`&sort_by=customer&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                {__("customer")}
            </a>
            {if $search.sort_by == "customer"}{$sort_sign nofilter}{/if}
        </th>
        <th class="ty-orders-search-th__date">
            <a class="{$ajax_class}" href="{"`$c_url`&sort_by=date&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                {__("date")}
            </a>
            {if $search.sort_by == "date"}{$sort_sign nofilter}{/if}
        </th>

        {hook name="orders:manage_header"}{/hook}

        <th class="ty-orders-search-th__total">
            <a class="{$ajax_class}" href="{"`$c_url`&sort_by=total&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                {__("total")}
            </a>
            {if $search.sort_by == "total"}{$sort_sign nofilter}{/if}
        </th>
        <th class="ty-orders-search__header ty-orders-search__header--actions ty-orders-search-th__actions">{__("actions")}</th>
    </tr>
    </thead>
    {foreach from=$orders item="o"}
        <tr>
        <td class="ty-orders-search__item{if $o.childs} ty-orders-search__item-childs{/if}">
                <a class="ty-orders-search__item-number" href="{"orders.details?order_id=`$o.order_id`"|fn_url}">
                    <strong>#{$o.order_id}</strong>
                </a>
                {if $o.childs}
                    <span class="cp-single-order-btn cm-combination" id="sw_single_order_{$o.order_id}">
                    {__("cp_show_suborders")}
                    <i class="ut2-icon-outline-expand_more"></i>
                    </span>
                {/if}
            </td>
            

            <td class="ty-orders-search__item">
                {if !$o.childs}
                    {include file="common/status.tpl" status=$o.status display="view"}
                {/if}
            </td>
            <td class="ty-orders-search__item">
                <ul class="ty-orders-search__user-info">
                    <li class="ty-orders-search__user-name">{$o.firstname} {$o.lastname}</li>
                    <li  class="ty-orders-search__user-mail"><a href="mailto:{$o.email|escape:url}">{$o.email}</a></li>
                </ul>
            </td>
            <td class="ty-orders-search__item"><a href="{"orders.details?order_id=`$o.order_id`"|fn_url}">{$o.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</a></td>

            {hook name="orders:manage_data"}{/hook}

            <td class="ty-orders-search__item">{include file="common/price.tpl" value=$o.total}</td>
            <td class="ty-orders-search__item ty-orders-search__item--actions">
                {include file="buttons/button.tpl"
                but_meta="cm-new-window ty-btn-icon"
                but_role="text"
                but_title=__("print_invoice")
                but_href="orders.print_invoice?order_id=`$o.order_id`"
                but_icon="ty-orders__actions-icon ty-icon-print"}

                {include file="buttons/button.tpl"
                but_meta="ty-btn-icon"
                but_role="text"
                but_title=__("re_order")
                but_href="orders.reorder?order_id=`$o.order_id`"
                but_icon="ty-orders__actions-icon ty-icon-cw"}

                {include file="buttons/button.tpl"
                but_meta="ty-btn-icon"
                but_role="text"
                but_title=__("search_products")
                but_href="products.search?search_performed=Y&order_ids=`$o.order_id`"
                but_icon="ty-orders__actions-icon ty-icon-search"}
            </td>
        </tr>


        {if $o.childs}
            <tbody id="single_order_{$o.order_id}" class="hidden">
            {foreach from=$o.childs item="suborder"}
                <tr class="suborders_{$o.order_id} cp_suborders">
                    <td class="ty-orders-search__item ty-orders-search__item-suborders"> 
                        {$company_data = $suborder.company_id|fn_get_company_data}
                        <ul>
                        <li><span class="hidden-phone">{__("cp_parent_order_id")}:</span> <a href="{"orders.details?order_id=`$suborder.order_id`"|fn_url}"><strong>#{$suborder.order_id}</strong></a></li>
                      {*  <li>{__("vendor")}: <a href="{"companies.products?company_id=`$company_data.company_id`"|fn_url}"><strong>{$company_data.company}</strong></a></li>*}
                        </ul>
                    </td>
                    <td class="ty-orders-search__item">{include file="common/status.tpl" status=$suborder.status display="view"}</td>
                    <td class="ty-orders-search__item">
                        <ul class="ty-orders-search__user-info">
                            <li class="ty-orders-search__user-name">{$suborder.firstname} {$suborder.lastname}</li>
                            <li  class="ty-orders-search__user-mail"><a href="mailto:{$suborder.email|escape:url}">{$suborder.email}</a></li>
                        </ul>
                    </td>
                    <td class="ty-orders-search__item"><a href="{"orders.details?order_id=`$suborder.order_id`"|fn_url}">{$suborder.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</a></td>


                    <td class="ty-orders-search__item">{include file="common/price.tpl" value=$suborder.total}</td>

                    <td class="ty-orders-search__item ty-orders-search__item--actions">
                        {include file="buttons/button.tpl"
                        but_meta="cm-new-window ty-btn-icon"
                        but_role="text"
                        but_title=__("print_invoice")
                        but_href="orders.print_invoice?order_id=`$suborder.order_id`"
                        but_icon="ty-orders__actions-icon ty-icon-print"}

                        {include file="buttons/button.tpl"
                        but_meta="ty-btn-icon"
                        but_role="text"
                        but_title=__("re_order")
                        but_href="orders.reorder?order_id=`$suborder.order_id`"
                        but_icon="ty-orders__actions-icon ty-icon-cw"}

                        {include file="buttons/button.tpl"
                        but_meta="ty-btn-icon"
                        but_role="text"
                        but_title=__("search_products")
                        but_href="products.search?search_performed=Y&order_ids=`$suborder.order_id`"
                        but_icon="ty-orders__actions-icon ty-icon-search"}
                    </td>


                </tr>

            {/foreach}
            </tbody>
        {/if}


        {foreachelse}
        <tr class="ty-table__no-items">
            <td colspan="7">
                <p class="ty-no-items">{__("text_no_orders")}</p>
            </td>
        </tr>
    {/foreach}
</table>

{include file="common/pagination.tpl"}

{capture name="mainbox_title"}{__("orders")}{/capture}