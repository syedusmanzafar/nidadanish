{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="manage_placeholders_form">

{include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
{assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

{assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
{if $audiences}
    <div class="table-responsive-wrapper" id="content_placeholders">
        <table width="100%" class="table table-middle table-objects table-striped table-responsive">
        <thead>
        <tr>
            <th class="left"  width="1%">
                {include file="common/check_items.tpl" check_statuses=''|fn_get_default_status_filters:true}
            </th>
            <th width="20%"><a class="cm-ajax" href="{"`$c_url`&sort_by=name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("name")}{if $search.sort_by == "name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
            <th class="right mobile-hide">&nbsp;</th>
            <th width="10%" class="right">
                <a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by == "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
            </th>
        </tr>
        </thead>

        {foreach from=$audiences item="audi"}
            <tr class="cm-row-item cm-row-status-{$audi.status|lower}">
                <td class="row-status left">
                    <input type="checkbox" name="audience_ids[]" value="{$audi.audience_id}" class="cm-item" />
                </td>
                <td class="left" data-th="{__("name")}">
                    <a href="{"cp_em_audience.update?audience_id=`$audi.audience_id`"|fn_url}" class="row-status cm-external-click link">{$audi.name}</a>
                    {if "ULTIMATE"|fn_allowed_for}
                        {include file="views/companies/components/company_name.tpl" object=$audi}
                    {/if}
                </td>
                <td class="center">
                    {capture name="tools_list"}
                        <li>{btn type="list" text=__("edit") href="cp_em_audience.update?audience_id=`$audi.audience_id`"}</li>
                        <li>{btn type="list" class="cm-confirm cm-post" text=__("delete") href="cp_em_audience.delete?audience_id=`$audi.audience_id`"}</li>
                    {/capture}
                    <div class="hidden-tools">
                        {dropdown content=$smarty.capture.tools_list}
                    </div>
                </td>
                <td width="10%" data-th="{__("status")}">
                    <div class="pull-right nowrap">
                        {include file="common/select_popup.tpl" popup_additional_class="dropleft" id=$audi.audience_id status=$audi.status hidden=false object_id_name="audience_id" table="cp_em_audiences"}
                    </div>
                </td>
            </tr>
        {/foreach}
        </table>  
    <!--content_placeholders--></div>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}

<div class="clearfix">
    {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
</div>
</form>
{/capture}

{capture name="adv_buttons"}
    {capture name="tools_list"}
        <li>{btn type="list" text=__("cp_em_aud_for_orders") href="cp_em_audience.add?type=O"}</li>
        <li>{btn type="list" text=__("cp_em_aud_for_viewed") href="cp_em_audience.add?type=V"}</li>
    {/capture}
    {dropdown content=$smarty.capture.tools_list icon="icon-plus" no_caret=true placement="right"}
    {*
    {include file="common/tools.tpl" tool_href="cp_em_audience.add" prefix="bottom" hide_tools="true" title=__("cp_em_add_audience") icon="icon-plus"}
    *}
{/capture}


{include file="common/mainbox.tpl" title=__("cp_em_audiences") sidebar=$smarty.capture.sidebar content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons content_id="manage_placeholders_form" select_languages=true}