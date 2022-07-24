{capture name="mainbox"}
    {if $audience.audience_id}
        {$id=$audience.audience_id}
    {else}
        {$id=0}
    {/if}
    <form action="{""|fn_url}" method="get" class="form-horizontal form-edit {$form_meta}" name="cp_custom_audience_form">
        <input type="hidden" name="fake" value="1" />
        <input type="hidden" name="audience_id" value="{$id}" />
        <input type="hidden" name="type" value="{$audience.type|default:"$aud_type"}" />
        <div id="cp_custom_audience">
            <div class="control-group">
            {if $aud_type == "O"}
                <label class="control-label">{__("cp_em_audience_type")}:</label>
                <div class="controls">
                    {__("cp_em_aud_for_orders")}
                </div>
            {else}
                <label class="control-label">{__("cp_em_audience_type")}:</label>
                <div class="controls">
                    {__("cp_em_aud_for_viewed")}
                </div>
            {/if}
            </div>
            <div class="control-group">
                <label class="control-label">{__("cp_em_audience_name")}:</label>
                <div class="controls">
                    <input type="text" name="name" value="{$audience.name|default:"{__("cp_em_type_p")}"}" class="cm-trim input-long" />
                </div>
            </div>
            {if "ULTIMATE"|fn_allowed_for}
                {include file="views/companies/components/company_field.tpl"
                    name="company_id"
                    id="elm_notice_data_`$id`"
                    selected=$audience.company_id
                }
            {/if}
            <div class="control-group">
                <label class="control-label">{if $aud_type == "O"}{__("cp_em_order_products")}{else}{__("cp_em_recently_viewed")}{/if}:</label>
                <div class="controls">
                    <label class="control-label">{__("cp_em_prod_operator")}:</label>
                    <select name="prod_oper">
                        <option value="OR" {if $search.prod_oper == "OR"}selected="selected"{/if}>{__("cp_em_or_operator")}</option>
                        <option value="AND" {if $search.prod_oper == "AND"}selected="selected"{/if}>{__("cp_em_and_operator")}</option>
                        <option value="NOT" {if $search.prod_oper == "NOT"}selected="selected"{/if}>{__("cp_em_not_operator")}</option>
                    </select>
                    {include file="addons/cp_extended_marketing/pickers/products/picker.tpl" positions="" input_name="p_ids" data_id="added_products" item_ids=$search.p_ids type="table" placement="right"}
                </div>
            </div>
            <div class="row-fluid group span12">
                <div class="group span6">
                    {if $aud_type == "O"}
                        <div class="control-group">
                            <label for="order_exists" class="control-label">{__("cp_em_orders_total")}&nbsp;({$currencies.$primary_currency.symbol nofilter})</label>
                            <div class="controls">
                                <input type="text" class="input-small" name="orders_total_from" id="orders_total" value="{$search.orders_total_from}" size="3" /> - <input type="text" class="input-small" name="orders_total_to" value="{$search.orders_total_to}" size="3" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">{__("cp_em_purchased_period")}:</label>
                            <div class="controls">
                                {include file="common/period_selector.tpl" period=$search.period  display="form"}
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">{__("cp_em_last_purchase")}:</label>
                            <div class="controls">
                                {include file="common/period_selector.tpl" prefix="last_" period=$search.last_period  display="form"}
                            </div>
                        </div>
                    {/if}
                    <div class="control-group">
                        <label class="control-label">{__("cp_em_products_from_categories")}:</label>
                        <div class="controls">
                            {include file="addons/cp_extended_marketing/pickers/categories/picker.tpl" data_id="categories" input_name="cid" item_ids=$search.cid hide_link=true hide_delete_button=true display_input_id="cid" disable_no_item_text=true view_mode="list" but_meta="btn" show_active_path=true}
                         </div>
                    </div>
                </div>
                {if $aud_type == "O"}
                    <div class="group span6 form-horizontal">
                        <div class="control-group">
                            <label for="elm_user_type" class="control-label">{__("usergroup")}:</label>
                            <div class="controls">
                                {assign var="user_type" value=$search.usergroup_id}
                                <select class="" id="elm_user_type" name="usergroup_id">
                                    <option {if $search.usergroup_id == 0}selected="selected"{/if} value="0">- {__("all")} -</option>
                                    <option {if $search.usergroup_id == 1}selected="selected"{/if} value="1">{__("guest")}</option>
                                    <option {if $search.usergroup_id == 2}selected="selected"{/if} value="2">{__("usergroup_registered")}</option>
                                    {if $usergroups}
                                        {foreach from=$usergroups item="u_group"}
                                            <option {if $u_group.usergroup_id == $search.usergroup_id}selected="selected"{/if} value="{$u_group.usergroup_id}">{$u_group.usergroup}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="elm_company_country" class="control-label">{__("country")}:</label>
                            <div class="controls">
                                {assign var="_country" value=$search.country}
                                <select class="" id="elm_company_country" name="country">
                                    <option value="">- {__("select_country")} -</option>
                                    {foreach from=$countries item="country" key="code"}
                                        <option {if $_country == $code}selected="selected"{/if} value="{$code}">{$country}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
            
                        {include file="addons/cp_extended_marketing/views/cp_em_audience/get_states.tpl"}
                        <div class="control-group">
                            <label class="control-label">{__("city")}:</label>
                            <div class="controls">
                                <input type="text" name="city" size="32" value="{$search.city}" class="input-long" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">{__("order_status")}:</label>
                            <div class="controls checkbox-list">
                                {if !$status_type}{assign var="status_type" value=$smarty.const.STATUSES_ORDER}{/if}
                                {assign var="order_status_descr" value=$status_type|fn_get_simple_statuses}
                                {if !$search.status}
                                    {assign var="order_statuses" value=$order_status_descr|array_keys}
                                {else}
                                    {assign var="order_statuses" value=$search.status}
                                {/if}
                                {include file="common/status.tpl" status=$order_statuses display="checkboxes" name="status" columns=5}
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
            <div class="cp-em__aud-btns">
                {include file="buttons/button.tpl"  but_text="{__("cp_em_save_audience")}" but_meta="cm-post" but_role="submit-button" but_name="dispatch[cp_em_audience.update_aud]" but_target_form="cp_custom_audience_form" but_id="cp_save_aud"}
                {include file="buttons/button.tpl"  but_text="{__("cp_em_search_and_export")}" but_role="submit-button" but_name="dispatch[cp_em_audience.find_export]" but_target_form="cp_custom_audience_form" but_id="cp_search"}
            </div>
            
        <!--cp_custom_audience--></div>
    </form>
{/capture}
{if $audience.name}
    {$title="{__("cp_em_editing_audience")}: `$audience.name`"}
{else}
    {$title=__("cp_em_new_audience")}
{/if}
{include file="common/mainbox.tpl" title=$title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons select_languages=false}
