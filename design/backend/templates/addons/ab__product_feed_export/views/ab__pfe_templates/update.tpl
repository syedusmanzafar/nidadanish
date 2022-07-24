{assign var="id" value=$t.template_id|default:0}
{capture name="mainbox"}
<form action="{""|fn_url}" method="post" class="form-horizontal form-edit {if ""|fn_check_form_permissions} cm-hide-inputs{/if}" name="ab__pfe_template_form" enctype="multipart/form-data">
<input type="hidden" class="cm-no-hide-input" name="template_id" value="{$id}" />
<div id="content_general">
{** name **}
{assign var="elm" value="name"}{assign var="elm_id" value="elm_`$elm`"}{assign var="elm_name" value="ab__pfe_template_data[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-required cm-trim">{__("ab__pfe.template.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.template.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$t.$elm}" size="25" class="input-large" />
</div>
</div>
{** template **}
{assign var="elm" value="template"}{assign var="elm_id" value="elm_`$elm`"}{assign var="elm_name" value="ab__pfe_template_data[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-required cm-trim">{__("ab__pfe.template.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.template.field.`$elm`.tooltip")}</label>
<div class="controls">
<textarea name="{$elm_name}" id="{$elm_id}" class="input-large mon" rows="30" wrap="off" style="font-family: monospace, sans-serif; overflow: auto;">{$t.$elm}</textarea>
</div>
</div>
{** position **}
{assign var="elm" value="position"}{assign var="elm_id" value="elm_`$elm`"}{assign var="elm_name" value="ab__pfe_template_data[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label">{__("ab__pfe.template.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.template.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$t.$elm|default:1}" size="25" class="input-mini" />
</div>
</div>
{include file="common/select_status.tpl" input_name="ab__pfe_template_data[status]" id="elm_status" obj_id=$id obj=$t hidden=false}
</div>
</form>
{/capture}
{capture name="buttons"}
{if !$id}
{include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="ab__pfe_template_form" but_name="dispatch[ab__pfe_templates.update]"}
{else}
{include file="buttons/save_cancel.tpl" but_name="dispatch[ab__pfe_templates.update]" but_role="submit-link" but_target_form="ab__pfe_template_form" hide_first_button=false hide_second_button=false save=$id}
{if "ab__pfe_templates"|fn_check_view_permissions:"POST"}
{capture name="tools_list"}
<li>{btn type="list" text=__("delete") class="cm-confirm cm-post" href="ab__pfe_templates.delete?template_id=`$id`"}</li>
{/capture}
{dropdown content=$smarty.capture.tools_list}
{/if}
{/if}
{/capture}
{capture name="sidebar"}
{include file="addons/ab__product_feed_export/views/ab__pfe_templates/components/default_templates.tpl"}
{if $all_params}
<div class="sidebar-row">
{foreach from=$all_params key="k" item="group"}
{include file="common/subheader.tpl" title=__("{$k}_params") target="#{$k}_params"}
<div id="{$k}_params" class="in collapse">
{foreach from=$group key="k_param" item="param"}
<div class="control-group">
<code>{$ldelim}${$k_param}{$rdelim}</code>
<span> - {$param.name} {include file="common/tooltip.tpl" tooltip=$param.tooltip}</span>
</div>
{/foreach}
</div>
{/foreach}
</div>
{/if}
{hook name="ab__pfe:addons"}{/hook}
{/capture}
{if !$id}
{$title_end = __("ab__pfe.template.add")}
{else}
{$title_end = $t.name}
{/if}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__product_feed_export"}
{include
file="common/mainbox.tpl"
title_start=__("ab__product_feed_export")|truncate:40
title_end=$t.name
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
sidebar=$smarty.capture.sidebar
}
