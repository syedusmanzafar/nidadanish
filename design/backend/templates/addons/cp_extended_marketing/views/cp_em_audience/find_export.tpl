{capture name="mainbox"}
    {if $audience.audience_id}
        {$id=$audience.audience_id}
    {else}
        {$id=0}
    {/if}
    
    <form action="{""|fn_url}" method="post" name="userlist_form" id="userlist_form" class="{if $runtime.company_id && !"ULTIMATE"|fn_allowed_for}cm-hide-inputs{/if}">
        <input type="hidden" name="fake" value="1" />
        <input type="hidden" name="audience_id" value="{$id}" />
        {if $audience && $audience.files}
            <div class="control-group">
                <label class="control-label">{__("cp_em_download_last_csv")}:</label>
                <div class="controls">
                    {foreach from=$audience.files item="a_file"}
                        <div>
                            <a id="cp_filename" href="{"cp_em_audience.get_file?file=`$a_file`"|fn_url}">{$a_file}</a>
                        </div>
                    {/foreach}
                    <div>
                        <a class="btn" href="{"cp_em_audience.delete_files?audience_id=`$audience.audience_id`"|fn_url}">{__("cp_em_delete_files")}</a>
                    </div>
                </div>
            </div>
        {/if}
        {if $users}
            {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

            {$c_url=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

            {$rev=$smarty.request.content_id|default:"pagination_contents"}

            {$_params=$search|serialize}
            <input type="hidden" name="params" value={$_params}) />
            <table width="100%" class="table table-middle">
                <thead>
                    <tr>
                        <th width="1%" class="center {$no_hide_input}">{include file="common/check_items.tpl"}</th>
                        <th width="3%" class="nowrap">{__("id")}</th>
                        <th width="45%"><a class="cm-ajax" href="{"`$c_url`&sort_by=name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("person_name")}{if $search.sort_by == "name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th width="45%"><a class="cm-ajax" href="{"`$c_url`&sort_by=email&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("email")}{if $search.sort_by == "email"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                    </tr>
                </thead>
                {foreach from=$users item=user}
                    {assign var="allow_save" value=$user|fn_allow_save_object:"users"}

                    {if "ULTIMATE"|fn_allowed_for}
                        <tr class="cm-row-status-{$user.status|lower}{if !$allow_save || ($user.user_id == $smarty.session.auth.user_id)} cm-hide-inputs{/if}">
                    {/if}
                    <td class="center {$no_hide_input}">
                        <input type="checkbox" name="user_emails[]" value="{$user.email}" class="checkbox cm-item" /></td>
                        <td><a class="row-status" href="{"profiles.update?user_id=`$user.user_id`&user_type=`$user.user_type`"|fn_url}">{$user.user_id}</a></td>
                        <td class="row-status">{if $user.b_firstname || $user.b_lastname}<a href="{"profiles.update?user_id=`$user.user_id`&user_type=`$user.user_type`"|fn_url}">{$user.b_lastname} {$user.b_firstname}</a>{else}-{/if}{if $user.company_id}{include file="views/companies/components/company_name.tpl" object=$user}{/if}</td>
                        <td><a class="row-status" href="mailto:{$user.email|escape:url}">{$user.email}</a></td>
                    </tr>
                {/foreach}
            </table>
            {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
        
            <div class="control-group">
                <label class="control-label">{__("cp_em_export_to_csv")}:</label>
                <div class="controls checkbox-list">
                    {html_checkboxes name=export_fields options=$export_fields selected=array_keys($export_fields) assign=_html_checkboxes labels=false}
                    <ul class="cp-em__audience_fields">
                    {foreach $_html_checkboxes as $item}
                        <li><label>{$item nofilter}</label></li>
                    {/foreach}
                    </ul>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">{__("cp_em_csv_delimiter")}:</label>
                <div class="controls">
                    {include file="views/exim/components/csv_delimiters.tpl" name="export_delimiter" value="C"}
                </div>
            </div>
            {if $addons.newsletters.status == "A"}
                <div class="control-group">
                    <label for="cp_em_mail_list" class="control-label">{__("cp_em_mail_list")}:</label>
                    <div class="controls">
                        <select class="cp_em_mail_list_select" name="list_id" id="cp_em_mail_list">
                            {foreach from=$mailing_lists item="m_list"}
                                <option value="{$m_list.list_id}" {if $notice_data.list_id == $m_list.list_id}selected="selected"{/if}>{$m_list.object}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            {/if}
            <div><strong>{__("cp_em_if_not_selected_user_hint")}</strong></div>
            <div class="pull-right cp_buttons">
                {include file="buttons/button.tpl"  but_text="{__("cp_em_export_to_csv")}" but_role="submit-button" but_name="dispatch[cp_em_audience.export_to_csv]" but_target_form="userlist_form" but_id="cp_export_to_csv"}
                
                {if $addons.newsletters.status == "A"}
                    {include file="buttons/button.tpl"  but_text="{__("cp_em_export_to_newsletters")}" but_role="submit-button" but_name="dispatch[cp_em_audience.export_to_newsletters]" but_target_form="userlist_form" but_id="cp_export_to_newsletters"}
                {/if}
                {if $addons.email_marketing.status == "A"}
                    {include file="buttons/button.tpl"  but_text="{__("cp_em_export_to_mailchimp")}" but_role="submit-button" but_name="dispatch[cp_em_audience.export_to_mailchimp]" but_target_form="userlist_form" but_id="cp_export_to_mailchimp"}
                {/if}
                {if $addons.rus_unisender.status == "A"}
                    {include file="buttons/button.tpl"  but_text="{__("cp_em_export_to_unisender")}" but_role="submit-button" but_name="dispatch[cp_em_audience.export_to_unisender]" but_target_form="userlist_form" but_id="cp_export_to_unisender"}
                {/if}
            </div> 
    
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
    </form>
{/capture}
{if $audience.name}
    {$title="{__("cp_em_export_audience")}: `$audience.name`"}
{else}
    {$title=__("cp_em_export_audience")}
{/if}
{include file="common/mainbox.tpl" title=$title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons select_languages=false}
