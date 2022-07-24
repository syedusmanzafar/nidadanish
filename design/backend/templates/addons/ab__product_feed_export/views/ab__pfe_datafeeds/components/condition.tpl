<div class="conditions-tree-node clearfix">
<div class="pull-right">
<a class="icon-trash cm-tooltip cm-delete-row" name="remove" id="{$item_id}" title="{__("remove")}"></a>
</div>
{assign var="key" value=$prefix|md5}
{* select feature *}
<select name="{$prefix}[condition_element]" id="ab__pfe_condition_parent_{$key}">
{if $condition_data.condition_element}
<option value="{$condition_data.condition_element}" selected></option>
{/if}
</select>
<select name="{$prefix}[operator]" id="ab__pfe_condition_operator_{$key}">
{foreach from=['eq', 'neq', 'lte', 'gte', 'lt', 'gt', 'in', 'nin'] item="op"}
<option value="{$op}" {if $op == $condition_data.operator}selected="selected"{/if}>{__("promotion_op_`$op`")}</option>
{/foreach}
</select>
<input type="hidden" name="{$prefix}[condition]" value="{$condition_data.condition}"/>
<select name="{$prefix}[value]" {if $condition_data.operator == 'in' || $condition_data.operator == 'nin'}multiple{/if} class="hidden" id="ab__pfe_condition_child_{$key}">
{foreach from=","|explode:$condition_data.value item="preselected_child"}
<option value="{$preselected_child}" selected></option>
{/foreach}
</select>
<input id="ab__pfe_condition_child_input_{$key}" type="text" name="{$prefix}[value]" value="{$condition_data.value}" class="hidden input-long"/>
<input id="ab__pfe_condition_child_input_name_{$key}" type="text" name="{$prefix}[value_name]" value="{$condition_data.value_name}" class="hidden input-medium"/>
<script>
(function (_, $) {
$(document).ready(function() {
var chainedCondition = new _.ChainedPromotionConditionForm({
operatorSelect: $('#ab__pfe_condition_operator_{$key}'),
parentSelect: $('#ab__pfe_condition_parent_{$key}'),
childSelect: $('#ab__pfe_condition_child_{$key}'),
childInput: $('#ab__pfe_condition_child_input_{$key}'),
settings: {
parent: {
dataUrl: "{'product_features.get_features_list'|fn_url}"
}
}
});
chainedCondition.render();
chainedCondition.render();
chainedCondition.render();
});
})(Tygh, Tygh.$);
</script>
</div>
