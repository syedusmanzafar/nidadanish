{if $ab__pfe_feature_name}
{assign var="id" value=$ab__pfe_feature_name.item_id}
{assign var="return_url" value=$smarty.request.return_url}
{assign var="category_id" value=$smarty.request.category_id}
{else}
{assign var="id" value=0}
{/if}
<form action="{""|fn_url}" method="post" id="update_ab__pfe_feature_name_form_{$id}" name="update_ab__pfe_feature_name_form_{$id}" class="form-horizontal form-edit cm-disable-empty-files" enctype="multipart/form-data">
<input type="hidden" name="redirect_url" value="{$smarty.request.return_url}" />
<input type="hidden" name="item_id" value="{$id}" />
<input type="hidden" name="category_id" value="{$smarty.request.category_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" >
<input type="hidden" name="item_data[category_id]" value="{$category_id}" />
<fieldset>
<div class="control-group">
<label class="control-label cm-required" for="ab__pfe_feature_name_feature_{$id}">{__("feature")}</label>
<div class="controls">
<select id="ab__pfe_feature_name_feature_{$id}" name="item_data[feature_id]" class="span8">
<option value="">---</option>
{foreach $ab__pfe_features AS $feature}
<option value="{$feature.feature_id}" {if $ab__pfe_feature_name.feature_id == $feature.feature_id}selected="selected"{/if}>{$feature.description}</option>
{/foreach}
</select>
</div>
</div>
<div class="control-group">
<label class="control-label cm-required" for="ab__pfe_feature_name_datafeed_{$id}">{__("ab__pfe.datafeed")}</label>
<div class="controls">
<select id="ab__pfe_feature_name_datafeed_{$id}" name="item_data[datafeed_id]" class="span8">
<option value="">---</option>
{foreach $ab__pfe_datafeeds AS $datafeed}
<option value="{$datafeed.datafeed_id}" {if $ab__pfe_feature_name.datafeed_id == $datafeed.datafeed_id}selected="selected"{/if}>{$datafeed.name}</option>
{/foreach}
</select>
</div>
</div>
<div class="control-group">
<label class="control-label cm-required" for="ab__pfe_feature_name_name_{$id}">{__("name")}</label>
<div class="controls">
<input class="span8" type="text" name="item_data[name]" value="{$ab__pfe_feature_name.name}" id="ab__pfe_feature_name_{$id}" />
</div>
</div>
</fieldset>
<div class="buttons-container">
{include file="buttons/save_cancel.tpl" but_name="dispatch[ab__pfe_feature_names.update]" cancel_action="close" extra="" hide_first_button=false save=$id}
</div>
</form>