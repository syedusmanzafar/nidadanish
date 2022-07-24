{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="manage_ab__pfe_datafeeds_form" id="manage_ab__pfe_datafeeds_form" xmlns="http://www.w3.org/1999/html">
{include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}
{if $ab__pfe_datafeeds}
<div class="table-responsive-wrapper longtap-selection">
<table width="100%" class="table table-middle table--relative table-responsive ab--table-datafeeds">
<thead data-ca-bulkedit-default-object="true" data-target=".ab--table-datafeeds" data-ca-bulkedit-component="defaultObject">
<tr>
<th width="1%" class="left mobile-hide">
{include file="common/check_items.tpl"}
<input type="checkbox"
class="bulkedit-toggler hide"
data-ca-bulkedit-toggler="true"
data-ca-bulkedit-disable="[data-ca-bulkedit-default-object=true]"
data-ca-bulkedit-enable="[data-ca-bulkedit-expanded-object=true]"
/>
</th>
<th width="5%"><span>{__("ab__pfe.datafeed.field.position")}</span></th>
<th width="40%"><span>{__("ab__pfe.datafeed.field.name")}</span></th>
<th width="25%"><span>{__("ab__pfe.datafeed.field.last_timestamp")}</span></th>
<th width="5%">&nbsp;</th>
<th width="5%" class="right">{__("ab__pfe.datafeed.field.status")}</th>
</tr>
</thead>
{foreach $ab__pfe_datafeeds as $df}
<tr class="cm-row-status-{$df.status|lower} cm-longtap-target"
data-ca-longtap-action="setCheckBox"
data-ca-longtap-target="input.cm-item"
data-ca-id="{$df.datafeed_id}"
>
<td class="left mobile-hide">
<input type="checkbox" name="datafeeds_ids[]" value="{$df.datafeed_id}" class="checkbox cm-item cm-item-status-{$df.status|lower}" />
</td>
<td data-th="{__("ab__pfe.datafeed.field.position")}">{$df.position|default:0}</td>
<td data-th="{__("ab__pfe.datafeed.field.name")}" class="row-status">
{btn type="list" text="{$df.name}" href="ab__pfe_datafeeds.update?datafeed_id=`$df.datafeed_id`"}
{include file="views/companies/components/company_name.tpl" object=$df}
</td>
<td data-th="{__("ab__pfe.datafeed.field.last_timestamp")}" class="row-status">
<div id="ab__pfe_{$df.datafeed_id}">{$df.last_update_date}<!--ab__pfe_{$df.datafeed_id}--></div>
</td>
<td class="nowrap">
<div class="hidden-tools">
{capture name="tools_list"}
<li>{btn type="list" text=__("edit") href="ab__pfe_datafeeds.update?datafeed_id=`$df.datafeed_id`"}</li>
{if "ab__pfe_datafeeds"|fn_check_view_permissions:"POST"}
<li>{btn type="list" text=__("delete") class="cm-confirm cm-post cm-comet" href="ab__pfe_datafeeds.delete?datafeed_id=`$df.datafeed_id`"}</li>
{/if}
<li class="divider"></li>
{$c_url = fn_url($config.current_url)}
<li>
{btn
type="list"
text=__("ab__pfe.datafeed.generate")
class="cm-ajax cm-post"
href="ab__pfe_datafeeds.manual_generate?datafeed_id=`$df.datafeed_id`&return_url=`$c_url|escape:url`"
data=[
"data-ca-target-id" => "ab__pfe_`$df.datafeed_id`"
]
}
</li>
<li>{btn type="list" text=__("ab__pfe.datafeed.reset_status") class="cm-confirm cm-post" href="ab__pfe_datafeeds.reset_status?datafeed_id=`$df.datafeed_id`"}</li>
{/capture}
{dropdown content=$smarty.capture.tools_list}
</div>
</td>
<td data-th="{__("ab__pfe.datafeed.field.status")}" class="right nowrap">
{$has_permission = fn_check_permissions("tools", "update_status", "admin", "POST", ["table" => "ab__pfe_datafeeds"])}
{include file="common/select_popup.tpl" popup_additional_class="dropleft" id=$df.datafeed_id status=$df.status hidden=false object_id_name="datafeed_id" table="ab__pfe_datafeeds" non_editable=!$has_permission}
</td>
</tr>
{/foreach}
</table>
</div>
{else}
<p class="no-items">{__("no_data")}</p>
{/if}
<div class="clearfix">
{include file="common/pagination.tpl" div_id=$smarty.request.content_id}
</div>
</form>
{/capture}
{capture name="adv_buttons"}
{include file="common/tools.tpl" tool_href="ab__pfe_datafeeds.add" prefix="top" title=__("ab__pfe.datafeed.add") hide_tools=true icon="icon-plus"}
{/capture}
{capture name="buttons"}
{if $ab__pfe_datafeeds}
{capture name="tools_list"}
<li>{btn type="delete_selected" dispatch="dispatch[ab__pfe_datafeeds.m_delete]" form="manage_ab__pfe_datafeeds_form"}</li>
{/capture}
{dropdown content=$smarty.capture.tools_list}
{/if}
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__product_feed_export"}
{include
file="common/mainbox.tpl"
title_start=__("ab__product_feed_export")|truncate:40
title_end=__("ab__pfe.datafeeds")
content=$smarty.capture.mainbox
adv_buttons=$smarty.capture.adv_buttons
buttons=$smarty.capture.buttons
sidebar=$smarty.capture.sidebar
content_id="manage_ab__pfe_datafeeds"
select_storefront=true
show_all_storefront=false
}