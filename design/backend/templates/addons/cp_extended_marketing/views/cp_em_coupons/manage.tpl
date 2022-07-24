{capture name="mainbox"}
    {assign var="c_icon" value="<i class=\"icon-`$search.sort_order_rev`\"></i>"}
    {assign var="c_dummy" value="<i class=\"icon-dummy\"></i>"}
    
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}
    {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
    {assign var="r_url" value=$c_url|escape:"url"}
    {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
    {if $coupons}
        <div class="table-responsive-wrapper">
            <table class="table table-middle table-objects table-striped table-responsive">
                <thead>
                    <tr>
                        <th width="30%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=coupon_code&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("coupon_code")}{if $search.sort_by == "coupon_code"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a> /&nbsp;&nbsp;&nbsp; <a class="cm-ajax" href="{"`$c_url`&sort_by=notice_name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_em_notice_txt")}{if $search.sort_by == "notice_name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="25%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=email&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("email")}{if $search.sort_by == "email"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="15%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=used&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_em_used_txt")}{if $search.sort_by == "used"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a> /
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=order_id&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("order_id")}{if $search.sort_by == "order_id"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="15%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=generate_time&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("date")}{if $search.sort_by == "generate_time"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="15%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=expire_time&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_em_expire_txt")}{if $search.sort_by == "expire_time"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$coupons item="coupon" key="key"}
                    <tr class="cm-row-item cp-em-log__status-{$coupon.status|lower}">
                        <td width="30%" data-th="{__("coupon_code")}">
                            {$coupon.coupon_code}
                            <div>
                                <a href="{"cp_em_notices.update?notice_id=`$coupon.notice_id`"|fn_url}">{$coupon.name}</a>
                            </div>
                            <div>
                                <a href="{"promotions.update?promotion_id=`$coupon.promotion_id`"|fn_url}">{$coupon.promo_name}</a>
                            </div>
                        </td>
                        <td width="25%" data-th="{__("email")}">
                            {$coupon.email}
                        </td>
                        <td width="15%" data-th="{__("cp_em_used_txt")}">
                            {if $coupon.used > 0}{__("yes")}{else}{__("no")}{/if}
                            {if $coupon.order_id}<a href="{"orders.details?order_id=`$coupon.order_id`"|fn_url}">#{$coupon.order_id}</a>{/if}
                        </td>
                        <td width="15%" data-th="{__("date")}">
                            {$coupon.generate_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
                        </td>
                        <td width="15%" data-th="{__("cp_em_expire_txt")}">
                            {$coupon.expire_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
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

{capture name="buttons"}
    {capture name="tools_list"}
        <li>{btn type="list" text=__("clean_logs") href="cp_em_logs.clear_logs" class="cm-confirm cm-post"}</li>
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}

{capture name="sidebar"}
    {include file="addons/cp_extended_marketing/views/cp_em_coupons/components/coupons_search_form.tpl"}
{/capture}

{include file="common/mainbox.tpl" title=__("cp_em_coupons_page") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar}