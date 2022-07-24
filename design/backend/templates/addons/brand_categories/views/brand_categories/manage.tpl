{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="search_list_form" class="form-horizontal form-edit  cm-disable-empty-files  cm-processed-form cm-check-changes" enctype="multipart/form-data">

{include file="common/pagination.tpl" save_current_page=true save_current_url=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{assign var="c_icon" value="<i class=\"icon-`$search.sort_order_rev`\"></i>"}
{assign var="c_dummy" value="<i class=\"icon-dummy\"></i>"}

{if $list}
<table class="table table-middle">
<thead>
    <tr>
        <th class="left" width="1%">
            {include file="common/check_items.tpl" check_statuses=''|fn_get_default_status_filters:true}
        </th>
        <th class="left" width="3%">#</th>
        <th class="nowrap left" width="30%"><a class="cm-ajax" href="{"`$c_url`&sort_by=name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("name")}{if $search.sort_by == "name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
        <th class="nowrap center"></th>
    </tr>
</thead>

{foreach from=$list item=item}

<tr class="cm-row-status-{$item.status|lower}">
    <td class="left" width="1%"><input type="checkbox" name="item_ids[]" value="{$item.id}" class="checkbox cm-item cm-item-status-{$item.status|lower}" /></td>
    <td class="left" width="3%">{$item.id}</td>
    <td class="left"><a href="{"brand_categories.update&id=`$item.id`"|fn_url}">{$item.name}</a></td>
    <td class="center">
        {capture name="tools_items"}
            <li>{btn type="list" href="brand_categories.update?id=`$item.id`" text={__("edit")}}</li>
            <li>{btn type="list" href="brand_categories.delete?id=`$item.id`" text={__("delete")}}</li>
        {/capture}
        <div class="hidden-tools">
            {dropdown content=$smarty.capture.tools_items}
        </div>
    </td>
</tr>
{/foreach}
</table>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}

{include file="common/pagination.tpl"}
</form>
{/capture}

{capture name="buttons"}
{include file="common/tools.tpl" tool_href="brand_categories.add" prefix="top" title=__("create") hide_tools=true icon="icon-plus"}
    {capture name="tools_list"}
        <li>{btn type="delete_selected" dispatch="dispatch[brand_categories.m_delete]" form="search_list_form"}</li>
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}

{include file="common/mainbox.tpl" title=__("brand_categories") content=$smarty.capture.mainbox  sidebar=$smarty.capture.sidebar tools=$smarty.capture.tools buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons}