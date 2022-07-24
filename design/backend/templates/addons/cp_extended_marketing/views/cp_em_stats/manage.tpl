{capture name="mainbox"}
    {assign var="c_icon" value="<i class=\"icon-`$search.sort_order_rev`\"></i>"}
    {assign var="c_dummy" value="<i class=\"icon-dummy\"></i>"}
    
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}
    {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
    {assign var="r_url" value=$c_url|escape:"url"}
    {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
    {if $em_stats}
        <div class="table-responsive-wrapper">
            <table class="table table-middle table-objects table-striped table-responsive">
                <thead>
                    <tr>
                        <th width="30%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=notice_name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_em_notice_txt")}{if $search.sort_by == "notice_name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a> /&nbsp;&nbsp;&nbsp; <a class="{$ajax_class}" href="{"`$c_url`&sort_by=type&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("type")}{if $search.sort_by == "type"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="15%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=notices_sent&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_em_sent_amount")}{if $search.sort_by == "notices_sent"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="15%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=returns_form_email&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_em_returns_form_email")}{if $search.sort_by == "returns_form_email"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="15%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=reviews_placed&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_em_reviews_placed")}{if $search.sort_by == "reviews_placed"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="15%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=orders_placed&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_em_orders_placed")}{if $search.sort_by == "orders_placed"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        {*
                        <th width="15%">
                            {__("cp_em_conversion_percent")}
                        </th>
                        *}
                        <th width="15%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=orders_placed_total&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_em_orders_placed_total")}{if $search.sort_by == "orders_placed_total"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$em_stats item="stat" key="key"}
                    <tr class="cm-row-item cp-em-log__type-{$stat.type|lower}">
                        <td width="30%" data-th="{__("cp_em_notice_txt")}">
                            <a href="{"cp_em_notices.update?notice_id=`$stat.notice_id`"|fn_url}">{$stat.name}</a>
                            {if "ULTIMATE"|fn_allowed_for}
                                {include file="views/companies/components/company_name.tpl" object=$stat}
                            {/if}
                            <div>
                                {if $notice_types[$stat.type]}
                                    {$notice_types[$stat.type].title}
                                {else}
                                    {$stat.type}
                                {/if}
                            </div>
                            <div>
                                <strong>{__("cp_em_coupon_codes")}:</strong> {$stat.coupons_generated} ({$stat.coupons_used})
                            </div>
                            <div>
                                <strong>{__("cp_em_email_opening_txt")}:</strong> {$stat.email_openings}
                            </div>
                        </td>
                        <td width="15%" data-th="{__("cp_em_sent_amount")}">
                            {$stat.notices_sent} {*({include file="common/price.tpl" value=$stat.orders_total_sent})*}
                        </td>
                        <td width="15%" data-th="{__("cp_em_returns_form_email")}">
                            {$stat.returns_form_email} ({round($stat.returns_form_email/$stat.notices_sent*100, 1)}%)
                        </td>
                        <td width="15%" data-th="{__("cp_em_reviews_placed")}">
                            {$stat.reviews_placed} ({round($stat.reviews_placed/$stat.notices_sent*100, 1)}%)
                        </td>
                        <td width="15%" data-th="{__("cp_em_orders_placed")}">
                            {$stat.orders_placed} ({round($stat.orders_placed/$stat.notices_sent*100, 1)}%){* ({include file="common/price.tpl" value=$stat.orders_placed_total})*}
                        </td>
                        {*
                        <td width="15%" data-th="{__("cp_em_conversion_percent")}">
                            {if $stat.type != "O" && $stat.orders_total_sent > 0 && $stat.orders_placed_total > 0}
                                <strong>{round($stat.orders_placed/$stat.notices_sent*100, 2)}&nbsp;%</strong>
                            {elseif $stat.type == "O"}
                                <strong>{round($stat.reviews_placed/$stat.notices_sent*100, 2)}&nbsp;%</strong>
                            {/if}
                        </td>
                        *}
                        <td>
                            <strong>{include file="common/price.tpl" value=$stat.orders_placed_total}</strong>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
{/capture}

{*
{capture name="buttons"}
    {capture name="tools_list"}
        <li>{btn type="list" text=__("clean_logs") href="cp_em_logs.clear_logs" class="cm-confirm cm-post"}</li>
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}
*}
{capture name="sidebar"}
    {include file="addons/cp_extended_marketing/views/cp_em_stats/components/stats_search_form.tpl"}
{/capture}
{include file="common/mainbox.tpl" title=__("cp_em_statistics_page") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar}