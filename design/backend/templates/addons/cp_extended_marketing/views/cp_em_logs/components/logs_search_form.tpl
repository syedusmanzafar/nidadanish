<div class="sidebar-row">
    <h6>{__("search")}</h6>
    <form action="{""|fn_url}" name="logs_form" method="get">
        <input type="hidden" name="object" value="{$smarty.request.object}">
        <div class="sidebar-field">
            <label for="email">{__("email")}</label>
            <input type="text" name="email" id="email" value="{$search.email}" size="30"/>
        </div>
        {include file="common/period_selector.tpl" period=$search.period extra="" display="form" button="false"}
        <div class="sidebar-field">
            <label for="s_type">{__("type")}</label>
            <select name="type" id="s_type">
                <option value="">{__("all")}</option>
                {foreach from=$notice_types item="n_type"}
                    <option value="{$n_type.type}" {if $search.type && in_array($n_type.type, $search.type)}selected="selected"{/if}>{$n_type.title}</option>
                {/foreach}
            </select>
        </div>
        {*
        <div class="sidebar-field">
            <label class="products">{__("products")}{include file="common/tooltip.tpl" tooltip=__("cp_em_for_search_by_products_notice")}</label>
            {if $search.p_ids}
                {assign var="product_ids" value=","|explode:$search.p_ids}
            {/if}
            {include file="pickers/products/picker.tpl" data_id="added_products" but_text=__("add") item_ids=$product_ids input_name="p_ids" type="links" no_container=true picker_view=true}
            {assign var="views" value="products"|fn_get_views}
        </div>
        *}
        {include file="buttons/search.tpl" but_name="dispatch[cp_em_logs.manage]"}
    </form>
</div>