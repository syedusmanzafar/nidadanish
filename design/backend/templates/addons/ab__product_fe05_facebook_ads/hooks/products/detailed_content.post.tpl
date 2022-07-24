{include file="common/subheader.tpl" title=__("ab__product_fe05_facebook_ads") target="#ab__product_fe05_facebook_ads"}
<div id="ab__product_fe05_facebook_ads" class="collapse in">
<div class="control-group">
<label class="control-label" for="ab__pfe05_condition">{__("ab__pfe05_condition")}:</label>
<div class="controls">
<select class="span3" name="product_data[ab__pfe05_condition]" id="ab__pfe05_condition">
{foreach ''|fn_ab__pfe05_conditions_list as $condition}
<option value="{$condition}" {if $product_data.ab__pfe05_condition == $condition}selected="selected"{/if}>{__("ab__pfe05_condition.`$condition`")}</option>
{/foreach}
</select>
</div>
</div>
</div>