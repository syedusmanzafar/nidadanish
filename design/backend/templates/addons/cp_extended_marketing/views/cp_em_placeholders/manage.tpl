{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="manage_placeholders_form">

{include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
{assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

{assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
{if $placeholders}
    <div class="table-responsive-wrapper" id="content_placeholders">
        <table width="100%" class="table table-middle table-responsive">
        <thead>
        <tr>
            <th class="left"  width="1%">
                {include file="common/check_items.tpl" check_statuses=''|fn_get_default_status_filters:true}
            </th>
            <th width="20%"><a class="cm-ajax" href="{"`$c_url`&sort_by=name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("name")}{if $search.sort_by == "name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
            <th width="20%"><a class="cm-ajax" href="{"`$c_url`&sort_by=placeholder&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_em_placeholder_txt")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_viewed_period_send")} params="ty-subheader__tooltip"}{/if}{if $search.sort_by == "type"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
            <th width="6%">&nbsp;</th>
            </tr>
        </thead>

        {foreach from=$placeholders item="placeholder"}
            <tr>
                <td class="row-status left">
                    <input type="checkbox" name="placeholder_ids[]" value="{$placeholder.placeholder_id}" class="cm-item" />
                </td>
                <td class="left" data-th="{__("name")}">
                    <a href="{"cp_em_placeholders.update?placeholder_id=`$placeholder.placeholder_id`"|fn_url}" class="underlined">{$placeholder.name}</a>
                    {if "ULTIMATE"|fn_allowed_for}
                        {include file="views/companies/components/company_name.tpl" object=$placeholder}
                    {/if}
                </td>
                <td class="left" data-th="{__("cp_em_placeholder_txt")}">
                    {$placeholder.placeholder}
                </td>
                <td class="center">
                    {capture name="tools_list"}
                        <li>{btn type="list" text=__("edit") href="cp_em_placeholders.update?placeholder_id=`$placeholder.placeholder_id`"}</li>
                        <li>{btn type="list" class="cm-confirm cm-post" text=__("delete") href="cp_em_placeholders.delete?placeholder_id=`$placeholder.placeholder_id`"}</li>
                    {/capture}
                    <div class="hidden-tools">
                        {dropdown content=$smarty.capture.tools_list}
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
    {include file="common/tools.tpl" tool_href="cp_em_placeholders.add" prefix="bottom" hide_tools="true" title=__("cp_em_add_placeholder") icon="icon-plus"}
{/capture}

{capture name="buttons"}
    {capture name="tools_list"}
        {if $placeholders}
            <li>{btn type="delete_selected" dispatch="dispatch[cp_em_placeholders.m_delete]" form="manage_placeholders_form"}</li>
        {/if}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}

{include file="common/mainbox.tpl" title=__("cp_em_product_placeholders") sidebar=$smarty.capture.sidebar content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons content_id="manage_placeholders_form" select_languages=true}