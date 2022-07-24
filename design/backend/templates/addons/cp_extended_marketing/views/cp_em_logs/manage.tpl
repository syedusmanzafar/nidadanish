{capture name="mainbox"}
    {assign var="c_icon" value="<i class=\"icon-`$search.sort_order_rev`\"></i>"}
    {assign var="c_dummy" value="<i class=\"icon-dummy\"></i>"}
    
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}
    {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
    {assign var="r_url" value=$c_url|escape:"url"}
    {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
    {if $em_logs}
        <div class="table-responsive-wrapper">
            <table class="table table-middle table-objects table-striped table-responsive">
                <thead>
                    <tr>
                        <th width="25%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=notice_name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("name")}{if $search.sort_by == "notice_name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="15%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=notice_type&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("type")}{if $search.sort_by == "notice_type"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="15%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=date&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("date")}{if $search.sort_by == "date"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="30%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=email&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("email")}{if $search.sort_by == "email"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="5%">{__("cp_em_message_txt")}</th>
                        <th width="10%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=is_test&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("test")}{if $search.sort_by == "is_test"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                        <th width="10%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by == "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$em_logs item="log" key="key"}
                    <tr class="cm-row-item cp-em-log__status-{$log.status|lower}">
                        <td width="25%" data-th="{__("name")}">
                            <a href="{"cp_em_notices.update?notice_id=`$log.notice_id`"|fn_url}">{$log.name}</a>
                            {if "ULTIMATE"|fn_allowed_for}
                                {include file="views/companies/components/company_name.tpl" object=$log}
                            {/if}
                        </td>
                        <td width="15%" data-th="{__("type")}">
                            {if $notice_types[$log.type]}
                                {if in_array($log.type, array("A","W")) && $log.session_id}
                                    <a href="{"cart.cart_list?session_id=`$log.session_id`"|fn_url}">{$notice_types[$log.type].title}</a>
                                {else}
                                    {$notice_types[$log.type].title}
                                {/if}
                            {/if}
                        </td>
                        <td width="15%" data-th="{__("date")}">
                            {$log.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
                        </td>
                        <td width="30%" data-th="{__("email")}">{$log.email}</td>
                        <td width="5%" data-th="{__("cp_em_message_txt")}"><a href="{"cp_em_logs.see_msg?log_id=`$log.log_id`"|fn_url}" title="{__("cp_em_message_txt")}" class="cm-dialog-opener cm-dialog-auto-size" data-ca-target-id="preview_log_{$log.log_id}">{__("cp_em_see_txt")}</a></td>
                        <td width="10%" data-th="{__("test")}">
                            {$log.is_test}
                        </td>
                        <td width="10%" data-th="{__("status")}">
                            {if $log.status == "S"}
                                {__("cp_em_sent_txt")}
                            {elseif $log.status == "E"}
                                {__("cp_em_error_txt")}
                            {/if}
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
    {include file="addons/cp_extended_marketing/views/cp_em_logs/components/logs_search_form.tpl"}
{/capture}

{include file="common/mainbox.tpl" title=__("cp_em_email_logs") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar}