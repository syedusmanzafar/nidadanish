{$id = $datafeed.datafeed_id|default:0}
{$company_id = fn_get_runtime_company_id()}
{$storefront_id = $app["storefront"]->storefront_id}
{* scripts *}
{script src="js/tygh/node_cloning.js"}
{script src="js/tygh/backend/promotion_update.js"}
<script>
(function (_,$) {
var template_select = $('select[name="datafeed_data[template_id]"]');
var link = $('.cm-ab-template-link');
if (template_select.length && link.length) {
$(template_select).change(function () {
var val = template_select.val();
if (val) {
link.attr('href', fn_url('ab__pfe_templates.update?template_id=' + val)).removeClass('hidden');
} else {
link.attr('href', '').addClass('hidden');
}
}).trigger('change');
}
})(Tygh, Tygh.$);
function fn_ab__pfe_condition_add(name)
{
var $ = Tygh.$,
container = $('#ab__pfe_features_conditions'),
prefix = 'container_condition_',
conditions = $('div[id^=' + prefix + ']'),
new_key = 0;
conditions.each(function() {
var key = parseInt($(this).attr('id').str_replace(prefix, ''));
if (new_key <= key) {
new_key = key + 1;
}
});
$('.no-node.no-items', container).hide();
container.append('<div id="' + prefix + new_key + '" style="margin:10px;" class="cm-row-item"></div>');
$.ceAjax('request', '{"ab__pfe_datafeeds.new_feature_condition"|fn_url nofilter}&prefix=' + encodeURIComponent(name + '[' + new_key + ']') + '&elm_id=' + prefix + new_key, {$ldelim}result_ids: prefix + new_key{$rdelim});
}
</script>
{capture name="mainbox"}
{capture name="tabsbox"}
{$form_data = "datafeed_data"}
<form action="{""|fn_url}" method="post" name="datafeed_update_form" class="form-horizontal form-edit {if ""|fn_check_form_permissions} cm-hide-inputs{/if}" enctype="multipart/form-data">
<input type="hidden" name="selected_section" id="selected_section" value="{$smarty.request.selected_section}" />
<input type="hidden" name="datafeed_id" value="{$id}" />
<input type="hidden" name="{$form_data}[company_id]" value="{$datafeed.company_id|default:$company_id}" />
<input type="hidden" name="{$form_data}[storefront_id]" value="{$datafeed.storefront_id|default:$storefront_id}" />
{if $id}
<p style="max-width: 100%;overflow-x: auto;">
<code>
{__('ab__pfe.datafeed.link')}
<a href="{$filename}" target="_blank">{$filename}</a>
</code>
</p>
<hr>
{/if}
<div id="content_general">
{** name **}
{$elm = "name"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-required cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm}" class="input-large" />
</div>
</div>
{** filename **}
{$elm = "filename"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-required cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm}" size="25" class="input-xlarge" />
<select name="{"`$form_data`[ext]"}" id="elm_ext">
<option value="">---</option>
{foreach $extensions as $k => $data}
<option {if $datafeed.ext == $k}selected="selected"{/if} value="{$k}">.{$k}</option>
{/foreach}
</select>
</div>
</div>
{** template_id **}
{$elm = "template_id"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-required cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<select name="{$elm_name}" id="{$elm_id}">
<option value="">---</option>
{foreach $template_list as $k => $t}
<option {if $datafeed.$elm == $k}selected="selected"{/if} value="{$k}">{$t.name}</option>
{/foreach}
</select>
<a target="_blank" class="cm-ab-template-link btn hidden" title="{__("ab__pfe.template.edit")}">
<i class="icon-cog"></i>
</a>
</div>
</div>
{** lang_code **}
{$elm = "lang_code"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-required cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<select name="{$elm_name}" id="{$elm_id}">
{foreach $language_list as $k => $l}
<option {if $datafeed.$elm == $k}selected="selected"{/if} value="{$k}">{$l.name}</option>
{/foreach}
</select>
</div>
</div>
{** brand_id **}
{$elm = "brand_id"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-required cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm|default:0}" size="25" class="cm-select-text input-mini" />
</div>
</div>
{** currency_code **}
{$elm = "currency_code"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-required cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<select name="{$elm_name}" id="{$elm_id}">
{foreach $currency_list as $c}
<option {if $datafeed.$elm == $c.currency_code}selected="selected"{/if} value="{$c.currency_code}">{$c.description}</option>
{/foreach}
</select>
</div>
</div>
{** max_images **}
{$elm = "max_images"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-required cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm|default:1}" size="25" class="cm-select-text input-mini" />
</div>
</div>
{** images_full_size **}
{$elm = "images_full_size"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y' || !$datafeed.$elm}checked="checked"{/if} />
</div>
</div>
{** use_watermark **}
{if $addons.watermarks.status == 'A'}
{$elm = "use_watermark"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y' || !$datafeed.$elm}checked="checked"{/if} />
</div>
</div>
{/if}
{** export_variations **}
{if $addons.product_variations.status == 'A'}
{$elm = "export_variations"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y'}checked="checked"{/if} />
</div>
</div>
{/if}
{** promotions_apply **}
{$elm = "promotions_apply"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y'}checked="checked"{/if} />
</div>
</div>
{** auto_generate **}
{$elm = "auto_generate"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y'}checked="checked"{/if} />
</div>
</div>
{** generate_before_download **}
{$elm = "generate_before_download"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y'}checked="checked"{/if} />
</div>
</div>
{** output_to_display **}
{$elm = "output_to_display"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y'}checked="checked"{/if} />
</div>
</div>
{** login **}
{$elm = "login"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm}" size="25" class="cm-select-text input-large" />
</div>
</div>
{** password **}
{$elm = "password"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm}" size="25" class="cm-select-text input-large" />
</div>
</div>
{** position **}
{$elm = "position"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-required cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm|default:1}" size="25" class="cm-select-text input-mini" />
</div>
</div>
{include file="common/select_status.tpl" input_name="`$form_data`[status]" id="elm_datafeed_status" obj=$datafeed hidden=false}
{if $id > 0}
{$cron_key = fn_ab__pfe_get_cron_key()}
{if $cron_key}
{$cron_cmd = "0 1 * * * php `$config.dir.root`/`$config.admin_index` --dispatch=ab__pfe_datafeeds.generate --cron_key=`$cron_key` --datafeed_id=`$id` --s_storefront=`$datafeed.storefront_id`"}
{$cron_url = "ab__pfe_datafeeds.generate?cron_key=`$cron_key`&datafeed_id=`$id`&s_storefront=`$datafeed.storefront_id`"|fn_url}
<div style="max-width: 100%;overflow-x: auto;">{__('ab__pfe.generate_link', ['[cron_cmd]' => $cron_cmd, '[cron_url]' => $cron_url])}</div>
{else}
<div style="color:red;">{__('ab__pfe.errors.cron_key')}</div>
{/if}
{/if}
</div>
<div id="content_conditions" class="hidden">
{** price_from **}
{$elm = "price_from"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm}" size="25" class="input-mini" />
</div>
</div>
{** price_to **}
{$elm = "price_to"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm}" size="25" class="input-mini" />
</div>
</div>
{** amount_from **}
{$elm = "amount_from"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm}" size="25" class="input-mini" />
</div>
</div>
{** amount_to **}
{$elm = "amount_to"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm}" size="25" class="input-mini" />
</div>
</div>
{** only_in_stock **}
{$elm = "only_in_stock"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y'}checked="checked"{/if} />
</div>
</div>
{** only_with_description **}
{$elm = "only_with_description"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y'}checked="checked"{/if} />
</div>
</div>
{** only_with_images **}
{$elm = "only_with_images"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y'}checked="checked"{/if} />
</div>
</div>
{** stop_words **}
{$elm = "stop_words"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label {*cm-required *}cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{$datafeed.$elm}" size="25" class="input-large" />
<table width="100%">
<tr class="nowrap">
<td><label for="pname" class="checkbox inline"><input type="checkbox" value="Y" {if $datafeed.stop_words_search_fields.pname}checked="checked"{/if} name="{$form_data}[stop_words_search_fields][pname]" id="pname" />{__("product_name")}</label></td>
<td><label for="pshort" class="checkbox inline"><input type="checkbox" value="Y" {if $datafeed.stop_words_search_fields.pshort}checked="checked"{/if} name="{$form_data}[stop_words_search_fields][pshort]" id="pshort" />{__("short_description")}</label></td>
<td><label for="pfull" class="checkbox inline"><input type="checkbox" value="Y" {if $datafeed.stop_words_search_fields.pfull}checked="checked"{/if} name="{$form_data}[stop_words_search_fields][pfull]" id="pfull" />{__("full_description")}</label></td>
<td><label for="pkeywords" class="checkbox inline"><input type="checkbox" value="Y" {if $datafeed.stop_words_search_fields.pkeywords}checked="checked"{/if} name="{$form_data}[stop_words_search_fields][pkeywords]" id="pkeywords" />{__("keywords")}</label></td>
</tr>
</table>
</div>
</div>
</div>
<div id="content_included_products" class="hidden">
{** included_categories **}
{include file="common/subheader.tpl" title=__("ab__pfe.datafeed.field.included_categories") target="#ab__pfe_included_categories"}
<div id="ab__pfe_included_categories" class="in collapse">
{include file="pickers/categories/picker.tpl" input_name="`$form_data`[included_categories]" item_ids=$datafeed.included_categories multiple=true single_line=true use_keys="N" placement="right"}
{** included_subcategories **}
{$elm = "included_subcategories"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y'}checked="checked"{/if} />
</div>
</div>
</div>
{** features_conditions **}
{include file="common/subheader.tpl" title=__("ab__pfe.datafeed.field.features_conditions") target="#ab__pfe_features_conditions"}
<div id="ab__pfe_features_conditions" class="conditions-tree-group in collapse">
{$prefix = "`$form_data`[features_conditions]"}
<div class="no-node-root clearfix">
<div id="add_condition" class="btn-toolbar pull-right">
{include file="common/tools.tpl" hide_tools=true tool_onclick="fn_ab__pfe_condition_add('`$prefix`')" prefix="simple" link_text=__("add_condition")}
</div>
</div>
<div class="no-node no-items {if $datafeed.features_conditions}hidden{/if}">
<p class="no-items">{__("no_items")}</p>
</div>
{if $datafeed.features_conditions}
{foreach $datafeed.features_conditions as $k => $condition_data}
<div id="container_condition_{$k}" class="cm-row-item">
{include file="addons/ab__product_feed_export/views/ab__pfe_datafeeds/components/condition.tpl" condition_data=$condition_data prefix="`$prefix`[`$k`]"}
</div>
{/foreach}
{/if}
</div>
{** included_products **}
{include file="common/subheader.tpl" title=__("ab__pfe.datafeed.field.included_products") target="#ab__pfe_included_products"}
<div id="ab__pfe_included_products" class="in collapse">
{include file="pickers/products/picker.tpl" input_name="`$form_data`[included_products]" data_id="added_products" item_ids=$datafeed.included_products type="links" placement="right"}
</div>
{** included_vendors **}
{if fn_allowed_for("MULTIVENDOR")}
{include file="common/subheader.tpl" title=__("vendors") target="#ab__pfe_included_vendors"}
<div id="ab__pfe_included_vendors" class="in collapse">
{$_extra_url = ""}
{if $app["storefront"]->getCompanyIds()}
{foreach $app["storefront"]->getCompanyIds() as $_company_id}
{$_extra_url = "`$_extra_url`&company_id[]=`$_company_id`"}
{/foreach}
{/if}
{include file="pickers/companies/picker.tpl"
show_add_button=true
multiple=true
item_ids=$datafeed.included_companies
view_mode="list"
input_name="`$form_data`[included_companies]"
no_item_text=__("all_vendors")
extra_url=$_extra_url
}
</div>
{/if}
</div>
<div id="content_excluded_products" class="hidden">
{** excluded_categories **}
{include file="common/subheader.tpl" title=__("ab__pfe.datafeed.field.excluded_categories")}
{include file="pickers/categories/picker.tpl" input_name="`$form_data`[excluded_categories]" item_ids=$datafeed.excluded_categories multiple=true single_line=true use_keys="N" placement="right"}
{** excluded_subcategories **}
{$elm = "excluded_subcategories"}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{__("ab__pfe.datafeed.field.`$elm`")} {include file="common/tooltip.tpl" tooltip=__("ab__pfe.datafeed.field.`$elm`.tooltip")}</label>
<div class="controls">
<input type="hidden" name="{$elm_name}" value="N" />
<input type="checkbox" name="{$elm_name}" id="{$elm_id}" value="Y" {if $datafeed.$elm == 'Y'}checked="checked"{/if} />
</div>
</div>
{** excluded_products **}
{include file="common/subheader.tpl" title=__("ab__pfe.datafeed.field.excluded_products")}
{include file="pickers/products/picker.tpl" input_name="`$form_data`[excluded_products]" data_id="added_products" item_ids=$datafeed.excluded_products type="links" placement="right"}
</div>
{if !empty($all_params)}
<div id="content_params" class="hidden">
{foreach $all_params as $k => $group}
{include file="common/subheader.tpl" title=__("{$k}_params") target="#{$k}_params"}
<div id="{$k}_params" class="in collapse">
{foreach $group as $k_param => $param}
{$elm = $k_param}{$elm_id = "elm_`$elm`"}{$elm_name = "`$form_data`[params][`$elm`]"}
<div class="control-group">
<label for="{$elm_id}" class="control-label cm-trim">{$param.name} {include file="common/tooltip.tpl" tooltip=$param.tooltip}</label>
<div class="controls">
<input type="text" name="{$elm_name}" id="{$elm_id}" value="{($datafeed.params.$k_param)|default:$param.default}" size="25" class="input-large" />
<br><code>{$k_param}</code>
</div>
</div>
{/foreach}
</div>
{/foreach}
</div>
{/if}
{hook name="ab__pfe_datafeeds:tabs_content"}{/hook}
</form>
{/capture}
{include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox group_name=$runtime.controller active_tab=$smarty.request.selected_section track=true}
{/capture}
{capture name="buttons"}
{if !$id}
{include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="datafeed_update_form" but_name="dispatch[ab__pfe_datafeeds.update]"}
{else}
{include file="buttons/save_cancel.tpl" but_name="dispatch[ab__pfe_datafeeds.update]" but_role="submit-link" but_target_form="datafeed_update_form" hide_first_button=false hide_second_button=false save=$id}
{capture name="tools_list"}
<li>{btn type="list" text=__("ab__pfe.datafeed.generate") class="cm-post cm-ajax cm-comet" href="ab__pfe_datafeeds.manual_generate?datafeed_id=`$id`"}</li>
<li>{btn type="list" text=__("ab__pfe.datafeed.reset_status") class="cm-confirm cm-post" href="ab__pfe_datafeeds.reset_status?datafeed_id=`$id`"}</li>
{if "ab__pfe_datafeeds"|fn_check_view_permissions:"POST"}
<li class="divider"></li>
<li>{btn type="list" text=__("delete") class="cm-confirm cm-post" href="ab__pfe_datafeeds.delete?datafeed_id=`$id`"}</li>
{/if}
{/capture}
{dropdown content=$smarty.capture.tools_list}
{/if}
{/capture}
{if !$id}
{$title_end=__("ab__pfe.datafeed.add")}
{else}
{$title_end=$datafeed.name}
{/if}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__product_feed_export"}
{include
file="common/mainbox.tpl"
title_start=__("ab__product_feed_export")|truncate:40
title_end=$title_end
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
show_all_storefront=false
}
