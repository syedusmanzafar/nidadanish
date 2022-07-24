{script src="js/tygh/template_editor.js"}
{$allow_save=true}


{capture name="mainbox"}
    {if !$id}
        {$id=$notice_data.notice_id|default:0}
    {/if}
    {$c_url=$config.current_url|fn_query_remove}
    {$r_url=$c_url|escape:"url"}

    <form action="{""|fn_url}" method="post" name="em_notice_form_{$id}" enctype="multipart/form-data" class="conditions-tree form-horizontal form-edit {if !$allow_save}cm-hide-inputs{/if}">
        <input type="hidden" class="cm-no-hide-input" name="notice_id" value="{$id}" />
        <input type="hidden" name="result_ids" value="preview_dialog" />
        <input type="hidden" class="cm-no-hide-input" name="notice_data[company_id]" value="{$runtime.company_id}"/>
        <input type="hidden" class="cm-no-hide-input" name="notice_data[type]" value="{$notice_data.type}"/>
        <input type="hidden" class="cm-no-hide-input" name="selected_section" value="{$smarty.request.selected_section}" />
        <input type="hidden" class="cm-no-hide-input" name="return_url" value="{$r_url}" />
        
        {capture name="tabsbox"}
        <div id="content_general">
            {include file="common/subheader.tpl" title=__("information") target="#acc_information"}
            <div id="acc_information" class="document-editor__wrapper collapse in collapse-visible">
                <div class="control-group">
                    <label for="cp_em_notice_name_{$id}" class="cm-required control-label">{__("name")}:</label>
                    <div class="controls">
                        <input id="cp_em_notice_name_{$id}" type="text" class="input-large"  name="notice_data[name]" value="{$notice_data.name}">
                    </div>
                </div>
                {if "ULTIMATE"|fn_allowed_for}
                    {include file="views/companies/components/company_field.tpl"
                        name="notice_data[company_id]"
                        id="elm_notice_data_`$id`"
                        selected=$notice_data.company_id
                    }
                {/if}
                {if $notice_data.type == "P"}
                    <div class="control-group">
                        <label for="cp_em_audience_id_{$id}" class="control-label">{__("cp_em_type_p")}:</label>
                        <div class="controls">
                            <select class="cp_em_action_select" name="notice_data[audience_id]" id="cp_em_audience_id_{$id}">
                                {foreach from=$audiences item="audi"}
                                    <option value="{$audi.audience_id}" {if $notice_data.audience_id == $audi.audience_id}selected="selected"{/if}>{$audi.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {/if}
                {if $notice_data.type == "T"}
                    {$cur_type = $notice_data.type}
                    <div class="control-group">
                        <label for="cp_em_action_type_{$id}" class="control-label">{__("cp_em_action_type")}:</label>
                        <div class="controls">
                            <select class="cp_em_action_select" name="notice_data[action_type]" id="cp_em_action_type_{$id}">
                                {foreach from=$notice_types.$cur_type.actions key="act_key" item="t_action"}
                                    <option value="{$act_key}" {if $notice_data.action_type == $act_key}selected="selected"{/if}>{$t_action}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    {if $addons.newsletters.status == "A"}
                        <div class="control-group{if $notice_data.action_type && $notice_data.action_type != "S"} hidden{/if}" id="cp_em_mailing_list_select">
                            <label for="cp_em_mail_list" class="control-label">{__("cp_em_mail_list")}:</label>
                            <div class="controls">
                                <select class="cp_em_mail_list_select" name="notice_data[list_id]" id="cp_em_mail_list">
                                    {foreach from=$mailing_lists item="m_list"}
                                        <option value="{$m_list.list_id}" {if $notice_data.list_id == $m_list.list_id}selected="selected"{/if}>{$m_list.object}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    {/if}
                    {if $addons.form_builder.status == "A"}
                        <div class="control-group{if $notice_data.action_type && $notice_data.action_type != "K"} hidden{/if}" id="cp_em_form_select">
                            <label for="cp_em_page_id" class="control-label">{__("cp_em_form_page")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_form_page")} params="ty-subheader__tooltip"}{/if}:</label>
                            <div class="controls">
                                <select class="cp_em_mail_list_select" name="notice_data[page_id]" id="cp_em_page_id">
                                    {foreach from=$form_pages item="f_page"}
                                        <option value="{$f_page.page_id}" {if $notice_data.page_id == $f_page.page_id}selected="selected"{/if}>{$f_page.page}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    {/if}
                {/if}
                <div class="control-group{if $notice_data.action_type && in_array($notice_data.action_type, array("F","A")) || $notice_data.type == "P"} hidden{/if}" id="cp_em_send_after_bl">
                    <label for="cp_em_notice_send_after_{$id}" class="control-label">{__("cp_em_send_after_label")}:</label>
                    <div class="controls">
                        <input id="cp_em_notice_send_after_{$id}" type="text" class="input-small"  name="notice_data[send_after]" value="{$notice_data.send_after}">
                        {if $notice_data.type == "T"}
                            <div class="{if !$notice_data.action_type || ($notice_data.action_type && $notice_data.action_type != "B")}hidden{/if}" id="cp_em_action_ba_block">
                                <span class="cp-em__action-after">{__("cp_em_after_action")}:</span>
                                <input type="hidden" name="notice_data[before_after]" value="B" />
                                <input type="checkbox" class="cp-em__action-box" name="notice_data[before_after]" value="A" {if $notice_data.before_after == "A"}checked="checked"{/if} />
                            </div>
                        {/if}
                    </div>
                </div>
                {if $notice_data.type == "V"}
                    <div class="control-group">
                        <label for="cp_em_products_limit_{$id}" class="control-label">{__("cp_em_products_limit")}:</label>
                        <div class="controls">
                            <input id="cp_em_products_limit_{$id}" type="text" class="input-small"  name="notice_data[products_limit]" value="{$notice_data.products_limit|default:"5"}">
                        </div>
                    </div>
                {/if}
                {if $notice_data.type == "T"}
                    <div class="control-group{if !$notice_data.action_type || ($notice_data.action_type && !in_array($notice_data.action_type, array("F","A")))} hidden{/if}" id="cp_em_purchase_period_bl">
                            
                            <label class="{if $notice_data.action_type != "F"}hidden {/if}cp-em__more_value_f control-label">{__("cp_em_purchase_period")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_purchase_period")} params="ty-subheader__tooltip"}{/if}:</label>
                            <label class="{if $notice_data.action_type != "A"}hidden {/if}cp-em__more_value_a control-label">{__("cp_em_not_active_period")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_not_active_period")} params="ty-subheader__tooltip"}{/if}:</label>
                            
                        <div class="controls">
                            <input type="text" name="notice_data[purchase_period]" id="cp_em_purchase_period_{$id}" value="{$notice_data.purchase_period|default:"30"}" class="input-small">
                        </div>
                    </div>
                    {if $date_fields || $addons.age_verification.status == "A"}
                        <div class="{if !$notice_data.action_type || ($notice_data.action_type && $notice_data.action_type != "B")}hidden {/if}control-group" id="cp_em_birthday_field">
                            <label for="cp_em_date_field_id_{$id}" class="control-label">{__("cp_em_birthday_field")}:</label>
                            <div class="controls">
                                <select name="notice_data[date_field_id]" id="cp_em_date_field_id_{$id}">
                                    {if $addons.age_verification.status == "A"}
                                        <option value="0" {if $notice_data.date_field_id == 0}selected="selected"{/if}>{__("cp_em_age_verif_addon_field")}</option>
                                    {/if}
                                    {if $date_fields}
                                        {foreach from=$date_fields item="d_field"}
                                            <option value="{$d_field.field_id}" {if $notice_data.date_field_id == $d_field.field_id}selected="selected"{/if}>{$d_field.description}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>
                    {/if}
                {elseif $notice_data.type == "V"}
                    <div class="control-group">
                            <label class="control-label">{__("cp_em_viewed_period_send")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_viewed_period_send")} params="ty-subheader__tooltip"}{/if}:</label>
                        <div class="controls">
                            <input type="text" name="notice_data[purchase_period]" id="cp_em_purchase_period_{$id}" value="{$notice_data.purchase_period|default:"30"}" class="input-small">
                        </div>
                    </div>
                {/if}
                {if $notice_data.type == "O"}
                    <div class="control-group">
                        <label class="control-label">{__("cp_em_orders_statuses")}:</label>
                        <div class="controls">
                            {foreach from=$statuses item="status"}
                                {assign var="_status" value=$status.status}
                                <label class="checkbox inline" for="elm_status{$status.status}">{$status.description}<input type="checkbox" id="elm_status{$status.status}" name="notice_data[order_statuses][{$status.status}]" value="{$status.status}" {if $notice_data.order_statuses.$_status}checked="checked"{/if}></label>
                            {/foreach}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">{__("cp_em_review_type")}:</label>
                        <div class="controls">
                            <select name="notice_data[review_type]">
                                <option value="N" {if $notice_data.review_type == "N"}selected="selected"{/if}>{__("cp_em_no_object")}</option>
                                <option value="P" {if $notice_data.review_type == "P"}selected="selected"{/if}>{__("products")}</option>
                                <option value="T" {if $notice_data.review_type == "T"}selected="selected"{/if}>{__("testimonials")}</option>
                                {if "MULTIVENDOR"|fn_allowed_for}
                                    <option value="V" {if $notice_data.review_type == "V"}selected="selected"{/if}>{__("cp_em_vendor_store_review")}</option>
                                {/if}
                            </select>
                        </div>
                    </div>
                {/if}
                {hook name="cp_em_notice:notice_settings"}{/hook}
                {if $notice_data.type != "O"}
                    <div class="control-group">
                        <label for="cp_em_notice_generate_promo" class="control-label">{__("cp_em_generate_promo")}:</label>
                        <div class="controls">
                            <input type="hidden" name="notice_data[generate_promo]" value="N" />
                            <input id="cp_em_notice_generate_promo" type="checkbox" name="notice_data[generate_promo]" value="Y" {if $notice_data.generate_promo == "Y"}checked="checked"{/if}>
                        </div>
                    </div>
                    <div class="{if $notice_data.generate_promo != "Y"}hidden{/if}" id="cp_em_notice_promo_block">
                        <div class="control-group">
                            <label for="cp_em_notice_promotion_id_{$id}" class="control-label">{__("cp_em_promotion_for_code")}:</label>
                            <div class="controls">
                                <select id="cp_em_notice_promotion_id_{$id}" name="notice_data[promotion_id]" >
                                    <option value="0">{__("none")}</option>
                                    {if $avail_promotions}
                                        {foreach from=$avail_promotions item="promo"}
                                            <option value="{$promo.promotion_id}" {if $promo.promotion_id == $notice_data.promotion_id}selected="selected"{/if}>{$promo.name}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="cp_em_notice_spromocode_duration_{$id}" class="control-label">{__("cp_em_code_duration")}:</label>
                            <div class="controls">
                                <input id="cp_em_notice_spromocode_duration_{$id}" type="text" class="input-small"  name="notice_data[promocode_duration]" value="{$notice_data.promocode_duration}">
                            </div>
                        </div>
                    </div>
                {/if}
                <div class="control-group">
                    <label for="cp_em_notice_add_pixel_{$id}" class="control-label">{__("cp_em_add_pixel")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_add_pixel")} params="ty-subheader__tooltip"}{/if}:</label>
                    <div class="controls">
                        <input type="hidden" name="notice_data[add_pixel]" value="N" />
                        <input id="cp_em_notice_add_pixel_{$id}" type="checkbox" name="notice_data[add_pixel]" value="Y" {if $notice_data.add_pixel == "Y"}checked="checked"{/if}>
                    </div>
                </div>
                {if "MULTIVENDOR"|fn_allowed_for && $notice_data.type == "A"}
                    <div class="control-group">
                        <label for="cp_em_notice_for_vendors_{$id}" class="control-label">{__("cp_em_notice_for_vendors")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_notice_for_vendors")} params="ty-subheader__tooltip"}{/if}:</label>
                        <div class="controls">
                            <input type="hidden" name="notice_data[for_vendors]" value="N" />
                            <input id="cp_em_notice_for_vendors_{$id}" type="checkbox" name="notice_data[for_vendors]" value="Y" {if $notice_data.for_vendors == "Y"}checked="checked"{/if}>
                        </div>
                    </div>
                {/if}
                <div class="control-group">
                    <label for="cp_em_nsend_from_{$id}" class="control-label">{__("cp_em_send_from_email")}:</label>
                    <div class="controls">
                        <input id="cp_em_nsend_from_{$id}" type="text" class="input-long"  name="notice_data[send_from]" value="{$notice_data.send_from}">
                    </div>
                </div>
                <div class="control-group">
                    <label for="cp_em_reply_to_{$id}" class="control-label">{__("cp_em_send_reply_email")}:</label>
                    <div class="controls">
                        <input id="cp_em_reply_to_{$id}" type="text" class="input-long"  name="notice_data[reply_to]" value="{$notice_data.reply_to}">
                    </div>
                </div>
                {include file="common/select_status.tpl" input_name="notice_data[status]" id="elm_notice_status" obj_id=$id obj=$notice_data hidden=false}
            </fieldset>
                
            </div>
            {include file="common/subheader.tpl" title=__("availability") target="#acc_availability"}
            <div id="acc_availability" class="collapse in">
                {if !"ULTIMATE:FREE"|fn_allowed_for}
                    <div class="control-group">
                        <label class="control-label">{__("usergroups")}:</label>
                        <div class="controls">
                            {include file="common/select_usergroups.tpl" id="ug_id" name="notice_data[usergroup_ids]" usergroups=["type"=>"C", "status"=>["A", "H"]]|fn_get_usergroups:$smarty.const.DESCR_SL usergroup_ids=$notice_data.usergroup_ids input_extra="" list_mode=false}
                        </div>
                    </div>
                {/if}
                <div class="control-group">
                    <label class="control-label" for="elm_use_avail_period">{__("use_avail_period")}:</label>
                    <div class="controls">
                        <input type="checkbox" name="avail_period" id="elm_use_avail_period" {if $notice_data.from_date || $notice_data.to_date}checked="checked"{/if} value="Y" onclick="fn_activate_calendar(this);"/>
                    </div>
                </div>

                {capture name="calendar_disable"}{if !$notice_data.from_date && !$notice_data.to_date}disabled="disabled"{/if}{/capture}

                <div class="control-group">
                    <label class="control-label" for="elm_date_holder_from">{__("avail_from")}:</label>
                    <div class="controls">
                        <input type="hidden" name="notice_data[from_date]" value="0" />
                        {include file="common/calendar.tpl" date_id="elm_date_holder_from" date_name="notice_data[from_date]" date_val=$notice_data.from_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="elm_date_holder_to">{__("avail_till")}:</label>
                    <div class="controls">
                        <input type="hidden" name="notice_data[to_date]" value="0" />
                        {include file="common/calendar.tpl" date_id="elm_date_holder_to" date_name="notice_data[to_date]" date_val=$notice_data.to_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
                    </div>
                </div>

                <script language="javascript">
                    function fn_activate_calendar(el)
                    {
                        var $ = Tygh.$;
                        var jelm = $(el);
                        var checked = jelm.prop('checked');

                        $('#elm_date_holder_from,#elm_date_holder_to').prop('disabled', !checked);
                    }

                    fn_activate_calendar(Tygh.$('#elm_use_avail_period'));
                </script>
            </div>
            {include file="common/subheader.tpl" title=__("cp_em_is_test_mode") target="#acc_test_mode"}
            <div id="acc_test_mode" class="collapse in">
                <div class="control-group">
                    <label for="cp_em_notice_is_test_{$id}" class="control-label">{__("cp_em_is_test_mode")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_is_test_mode")} params="ty-subheader__tooltip"}{/if}:</label>
                    <div class="controls">
                        <input type="hidden" name="notice_data[is_test]" value="N" />
                        <input id="cp_em_notice_is_test_{$id}" type="checkbox" name="notice_data[is_test]" value="Y" {if $notice_data.is_test == "Y"}checked="checked"{/if}>
                    </div>
                </div>
                <div class="control-group">
                    <label for="cp_em_test_email_{$id}" class="control-label">{__("cp_em_test_email")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_test_email")} params="ty-subheader__tooltip"}{/if}:</label>
                    <div class="controls">
                        <input id="cp_em_test_email_{$id}" type="text" class="input-short"  name="notice_data[test_email]" value="{$notice_data.test_email}">
                    </div>
                </div>
                <div class="control-group">
                    <label for="cp_em_hidden_email_{$id}" class="control-label">{__("cp_em_hidden_email")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_hidden_email")} params="ty-subheader__tooltip"}{/if}:</label>
                    <div class="controls">
                        <input id="cp_em_hidden_email_{$id}" type="text" class="input-short"  name="notice_data[hidden_email]" value="{$notice_data.hidden_email}">
                    </div>
                </div>
            </div>
        <!--content_general--></div>
        <div id="content_message">
            <div class="control-group">
                <label for="elm_notice_subject_{$id}" class="cm-required control-label">{__("cp_em_subject_label")}:</label>
                <div class="controls">
                    <input id="elm_notice_subject_{$id}" type="text" name="notice_data[subject]" value="{$notice_data.subject}" class="span9 cm-emltpl-set-active">
                </div>
            </div>
            <div class="control-group ie-redactor">
                <label class="control-label cm-required" for="elm_notice_messasge_{$id}">{__("cp_em_message_label")}:</label>
                <div class="controls">
                    <textarea id="elm_notice_messasge_{$id}" name="notice_data[message]" cols="55" rows="14" class="cm-wysiwyg input-textarea-long cm-emltpl-set-active cm-active">{$notice_data.message}</textarea>
                </div>
            </div>
        <!--content_message--></div>
            {if $id}
                <div id="content_stats">
                {if $notice_data.statistics}
                    <div class="table-responsive-wrapper">
                        <table class="table table-middle table-objects table-striped table-responsive">
                            <thead>
                                <tr> 
                                    <th width="15%">{__("cp_em_sent_amount")}</th>
                                    <th width="15%">{__("cp_em_returns_form_email")}</th>
                                    <th width="15%">{__("cp_em_reviews_placed")}</th>
                                    <th width="15%">{__("cp_em_orders_placed")}</th>
                                    {*
                                    <th width="15%">{__("cp_em_conversion_percent")}</th>
                                    *}
                                    <th width="15%">{__("cp_em_orders_placed_total")}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="15%" data-th="{__("cp_em_sent_amount")}">
                                        {$notice_data.statistics.notices_sent}
                                    </td>
                                    <td width="15%" data-th="{__("cp_em_returns_form_email")}">
                                        {$notice_data.statistics.returns_form_email} ({round($notice_data.statistics.returns_form_email/$notice_data.statistics.notices_sent*100, 1)}%)
                                    </td>
                                    <td width="15%" data-th="{__("cp_em_reviews_placed")}">
                                        {$notice_data.statistics.reviews_placed} ({round($notice_data.statistics.reviews_placed/$notice_data.statistics.notices_sent*100, 1)}%)
                                    </td>
                                    <td width="15%" data-th="{__("cp_em_orders_placed")}">
                                        {$notice_data.statistics.orders_placed} ({round($notice_data.statistics.orders_placed/$notice_data.statistics.notices_sent*100, 1)}%)
                                    </td>
                                    {*
                                    <td width="15%" data-th="{__("cp_em_conversion_percent")}">
                                        {if $notice_data.statistics.type != "O" && $notice_data.statistics.orders_total_sent > 0 && $notice_data.statistics.orders_placed_total > 0}
                                            <strong>{round($notice_data.statistics.orders_placed/$notice_data.statistics.notices_sent*100, 2)}&nbsp;%</strong>
                                        {elseif $notice_data.statistics.type == "O"}
                                            <strong>{round($notice_data.statistics.reviews_placed/$notice_data.statistics.notices_sent*100, 2)}&nbsp;%</strong>
                                        {/if}
                                    </td>
                                    *}
                                    <td>
                                        <strong>{include file="common/price.tpl" value=$notice_data.statistics.orders_placed_total}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                {else}
                    <p class="no-items">{__("no_data")}</p>
                {/if}
                <!--content_stats--></div>
            {/if}
        {/capture}
        {include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}
    </form>
    {hook name="cp_em_notice:tabs_extra"}
    {/hook}
{/capture}

{capture name="sidebar"}
    <div class="sidebar-row">
        <h6>{__("cp_em_placeholders_title")}</h6>
        <ul class="nav nav-list variables-list variables-list--variables cm-cp-sidebar-variables" id="sidebar_variables">
            {foreach from=$placeholders key="var_key" item="var"}
                {if $var.is_group}
                    {if !$var.variables}
                        {continue}
                    {/if}
                    <span class="strong micro-note">{$var.title}</span>
                    <span class="icon-plus hand nav-opener"></span>
                    <ul class="hidden nav nav-list">
                        {foreach $var.variables as $subvar_key => $subvar}
                            <li class="variables-list__item">
                                {*
                                <span class="cm-emltpl-insert-variable label hand" data-ca-template-value="{$subvar_key}">{$subvar_key}</span>
                                *}
                                <span onclick="fn_cp_em_copy_clip(this);" class="cm-tooltip label hand" title="{__("cp_em_click_to_copy")}" data-ca-template-value="{$subvar_key}">{$subvar_key}</span>
                                <span class="micro-note">- {$subvar.title}</span>
                            </li>
                        {/foreach}
                    </ul>
                {else}
                    <li class="variables-list__item">
                        {*
                        <span class="cm-emltpl-insert-variable label hand" data-ca-template-value="{$var_key}">{$var_key}</span>
                        *}
                        <span onclick="fn_cp_em_copy_clip(this);" class="cm-tooltip label hand" title="{__("cp_em_click_to_copy")}" data-ca-template-value="{$var_key}">{$var_key}</span>
                        <span class="micro-note">- {$var.title}</span>
                    </li>
                {/if}
            {/foreach}
        </ul>
    </div>
    {include file="addons/cp_extended_marketing/views/cp_em_notices/preview.tpl" preview=[]}
    
    <script language="javascript">
        (function(_,$){
            $(document).on("change", ".cp_em_action_select", function(){
                var sel_val = $(this).val();
                if (sel_val && sel_val == 'B') {
                    $('#cp_em_action_ba_block').show();
                    $('#cp_em_birthday_field').show();
                    $('#cp_em_purchase_period_bl').hide();
                } else {
                    $('#cp_em_action_ba_block').hide();
                    $('#cp_em_birthday_field').hide();
                }
                if (sel_val && sel_val == 'F') {
                    $('#cp_em_purchase_period_bl').show();
                    $('#cp_em_send_after_bl').hide();
                    $('.cp-em__more_value_a').hide();
                    $('.cp-em__more_value_f').show();
                } else if (sel_val && sel_val == 'A') {
                    $('#cp_em_send_after_bl').hide();
                    $('#cp_em_purchase_period_bl').show();
                    $('.cp-em__more_value_a').show();
                    $('.cp-em__more_value_f').hide();
                } else {
                    $('#cp_em_purchase_period_bl').hide();
                    $('#cp_em_send_after_bl').show();
                }
                if (sel_val && sel_val == 'S') {
                    $('#cp_em_mailing_list_select').show();
                } else {
                    $('#cp_em_mailing_list_select').hide();
                }
                if (sel_val && sel_val == 'K') {
                    $('#cp_em_form_select').show();
                } else {
                    $('#cp_em_form_select').hide();
                }
            });
            $(document).on("click", "#cp_em_notice_generate_promo", function(){
                var check = $(this).prop('checked');
                if (check) {
                    $('#cp_em_notice_promo_block').show();
                } else {
                    $('#cp_em_notice_promo_block').hide();
                }
            });
        })(Tygh,Tygh.$);
    </script>
    {hook name="cp_em_notice:notice_scripts"}{/hook}
{/capture}
<script language="javascript">
    function fn_cp_em_copy_clip(element) {
        var txt = "{$ldelim}{$ldelim} " + $(element).text() + " {$rdelim}{$rdelim}";
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(txt).select();
        document.execCommand("copy");
        $temp.remove();
    }
</script>

{capture name="buttons"}
    {capture name="tools_list"}
        {if $id}
            <li>{btn type="list" text=__("cp_em_send_test_mail") href="cp_em_notices.send_test?notice_id=`$id`&return_url=`$r_url`" class="cm-post"}</li>
            <li>{btn type="list" text=__("preview") class="cm-ajax cm-form-dialog-opener" dispatch="dispatch[cp_em_notices.preview]" form="em_notice_form_`$id`"}</li>
            <li class="divider"></li>
            <li>{btn type="delete" href="cp_em_notices.delete?notice_id=`$id`&return_url=`$r_url`" class="cm-post"}</li>
        {/if}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}

    {include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="em_notice_form_`$id`" but_name="dispatch[cp_em_notices.update]" save=$id}
{/capture}

{if !$id}
    {$up_title=__("cp_em_new_notice")}
{else}
    {$up_title="{__("cp_em_edit_notice")}:&nbsp;`$notice_data.name`"}
{/if}

{include file="common/mainbox.tpl" title=$up_title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar sidebar_position="right" select_languages=true}


