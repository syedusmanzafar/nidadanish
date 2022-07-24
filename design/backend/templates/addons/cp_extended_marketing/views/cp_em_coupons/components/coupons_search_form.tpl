<div class="sidebar-row">
    <h6>{__("search")}</h6>
    <form action="{""|fn_url}" name="logs_form" method="get">
        <input type="hidden" name="object" value="{$smarty.request.object}">
        <div class="sidebar-field">
            <label for="coupon_code">{__("coupon_code")}</label>
            <input type="text" name="coupon_code" id="coupon_code" value="{$search.coupon_code}" size="30"/>
        </div>
        <div class="sidebar-field">
            <label for="email">{__("email")}</label>
            <input type="text" name="email" id="email" value="{$search.email}" size="30"/>
        </div>
        {include file="common/period_selector.tpl" period=$search.period extra="" display="form" button="false"}
        <div class="sidebar-field">
            <span>{__("cp_em_expired_txt")}</span>
            <span class="cp-em__search_expired">
                <input type="hidden" name="" value="N" />
                <input type="checkbox" name="expired" id="expired" value="Y" {if $search.expired == "Y"}checked="checked"{/if} />
            </span>
        </div>
        {include file="buttons/search.tpl" but_name="dispatch[cp_em_coupons.manage]"}
    </form>
</div>