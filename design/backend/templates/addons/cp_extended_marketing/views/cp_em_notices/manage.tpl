{capture name="mainbox"}
    {assign var="c_icon" value="<i class=\"icon-`$search.sort_order_rev`\"></i>"}
    {assign var="c_dummy" value="<i class=\"icon-dummy\"></i>"}

    <form action="{""|fn_url}" method="post" name="cp_em_notices_list_form">
        <input type="hidden" name="fake" value="1" />

        {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

        {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
        {assign var="r_url" value=$c_url|escape:"url"}
        {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}

        <div class="items-container" id="cp_em_notices_list">
        {if $em_notices}
            <div class="table-responsive-wrapper">
                <table class="table table-middle table-objects table-striped table-responsive">
                    <thead>
                        <tr>
                            <th width="1%" class="center mobile-hide">{include file="common/check_items.tpl"}</th>
                            <th width="50%">
                                <a class="cm-ajax" href="{"`$c_url`&sort_by=name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("name")}{if $search.sort_by == "name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                            </th>
                            <th width="15%">
                                <a class="cm-ajax" href="{"`$c_url`&sort_by=type&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("type")}{if $search.sort_by == "type"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                            </th>
                            <th width="15%">{__("cp_em_emails_in_queue")}</th>
                            <th class="right mobile-hide">&nbsp;</th>
                            <th width="10%" class="right">
                                <a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by == "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$em_notices item="notice"}
                            {assign var="edit_href" value="cp_em_notices.update?notice_id=`$notice.notice_id`"|fn_url}
                            {assign var="id" value=$notice.notice_id}

                            <tr class="cm-row-item cm-row-status-{$notice.status|lower}">
                                <td width="1%" class="center mobile-hide"><input type="checkbox" name="notice_ids[]" value="{$notice.notice_id}" class="cm-item" /></td>
                                <td width="50%" data-th="{__("name")}">
                                    <a href="{$edit_href}" class="row-status cm-external-click link">{$notice.name}</a>
                                    {if "ULTIMATE"|fn_allowed_for}
                                        {include file="views/companies/components/company_name.tpl" object=$notice}
                                    {/if}
                                </td>
                                <td width="15%" data-th="{__("type")}">
                                    {if $notice_types[$notice.type]}{$notice_types[$notice.type].title}{/if}
                                </td>
                                <td width="15%" data-th="{__("cp_em_emails_in_queue")}">
                                    {if $notice.emails_in_queue && $notice.emails_in_queue.total}
                                        <div><strong>{__("cp_em_total_txt")}</strong>: {$notice.emails_in_queue.total}</div>
                                        <div><strong>{__("cp_em_first_txt")}</strong>: {$notice.emails_in_queue.min_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</div>
                                        {if $notice.emails_in_queue.total > 1}
                                            <div><strong>{__("cp_em_last_txt")}</strong>: {$notice.emails_in_queue.max_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</div>
                                        {/if}
                                    {else}
                                        0
                                    {/if}
                                </td>
                                <td width="10%" class="right nowrap mobile-hide">
                                    <div class="pull-right hidden-tools">
                                        {capture name="items_tools"}
                                            <li>{btn type="list" text=__("edit") href="cp_em_notices.update?notice_id=`$notice.notice_id`"}</li>
                                            <li class="divider"></li>
                                            <li>{btn type="text" text=__("delete") href="cp_em_notices.delete?notice_id=`$notice.notice_id`" class="cm-confirm cm-ajax cm-ajax-full-render" data=["data-ca-target-id" => cp_em_notices_list] method="POST"}</li>
                                        {/capture}
                                        {dropdown content=$smarty.capture.items_tools class="dropleft"}
                                    </div>
                                </td>
                                <td width="10%" data-th="{__("status")}">
                                    <div class="pull-right nowrap">
                                        {include file="common/select_popup.tpl" popup_additional_class="dropleft" id=$id status=$notice.status hidden=false object_id_name="notice_id" table="cp_em_notices"}
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
        <!--cp_em_notices_list--></div>
        {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
        {if $cur_type && $cur_type.type}
            {capture name="adv_buttons"}
                {include file="common/tools.tpl" tool_href="cp_em_notices.add?type=`$cur_type.type`" prefix="top" hide_tools="true" title=__("cp_em_add_new_notice") icon="icon-plus"}
            {/capture}
        {/if}
    </form>
{/capture}

{if $cur_type && $cur_type.type}
    {$pg_title = $cur_type.manage_title}
{else}
    {$pg_title = __("cp_em_all_notices")}
{/if}
{include file="common/mainbox.tpl" title=$pg_title content=$smarty.capture.mainbox select_languages=true adv_buttons=$smarty.capture.adv_buttons}
