{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="manage_ab__pfe_templates_form" id="manage_ab__pfe_templates_form">
{include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}
{if $ab__pfe_templates}
<div class="table-responsive-wrapper longtap-selection">
<table width="100%" class="table table-middle table--relative table-responsive ab--table-templates">
<thead data-ca-bulkedit-default-object="true" data-target=".ab--table-templates" data-ca-bulkedit-component="defaultObject">
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
<th width="20%"><span>{__("ab__pfe.template.field.position")}</span></th>
<th width="15%"><span>{__("ab__pfe.template.field.name")}</span></th>
<th width="5%">&nbsp;</th>
<th width="10%" class="right">{__("ab__pfe.template.field.status")}</th>
</tr>
</thead>
{foreach $ab__pfe_templates as $t}
<tr class="cm-row-status-{$t.status|lower} cm-longtap-target"
data-ca-longtap-action="setCheckBox"
data-ca-longtap-target="input.cm-item"
data-ca-id="{$df.datafeed_id}"
>
<td class="left mobile-hide">
<input type="checkbox" name="template_id[]" value="{$t.template_id}" class="checkbox cm-item cm-item-status-{$t.status|lower}" />
</td>
<td data-th="{__("ab__pfe.template.field.position")}">{$t.position}</td>
<td data-th="{__("ab__pfe.template.field.name")}" class="row-status">{btn type="list" text="{$t.name}" href="ab__pfe_templates.update?template_id=`$t.template_id`"}</td>
<td class="nowrap">
<div class="hidden-tools">
{capture name="tools_list"}
<li>{btn type="list" text=__("edit") href="ab__pfe_templates.update?template_id=`$t.template_id`"}</li>
{if "ab__pfe_templates"|fn_check_view_permissions:"POST"}
<li>{btn type="list" text=__("delete") class="cm-confirm cm-post" href="ab__pfe_templates.delete?template_id=`$t.template_id`"}</li>
{/if}
{/capture}
{dropdown content=$smarty.capture.tools_list}
</div>
</td>
<td data-th="{__("ab__pfe.template.field.status")}" class="right nowrap">
{$has_permission = fn_check_permissions("tools", "update_status", "admin", "POST", ["table" => "ab__pfe_templates"])}
{include file="common/select_popup.tpl" popup_additional_class="dropleft" id=$t.template_id status=$t.status hidden=false object_id_name="template_id" table="ab__pfe_templates" non_editable=!$has_permission}
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
{include file="common/tools.tpl" tool_href="ab__pfe_templates.add" prefix="top" title=__("ab__pfe.template.add") hide_tools=true icon="icon-plus"}
{/capture}
{capture name="buttons"}
{if $ab__pfe_templates}
{capture name="tools_list"}
<li>{btn type="delete_selected" dispatch="dispatch[ab__pfe_templates.m_delete]" form="manage_ab__pfe_templates_form"}</li>
{/capture}
{dropdown content=$smarty.capture.tools_list}
{/if}
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__product_feed_export"}
{include
file="common/mainbox.tpl"
title_start=__("ab__product_feed_export")|truncate:40
title_end=__("ab__pfe.templates")
content=$smarty.capture.mainbox
adv_buttons=$smarty.capture.adv_buttons
buttons=$smarty.capture.buttons
sidebar=$smarty.capture.sidebar
content_id="manage_ab__pfe_templates"
}