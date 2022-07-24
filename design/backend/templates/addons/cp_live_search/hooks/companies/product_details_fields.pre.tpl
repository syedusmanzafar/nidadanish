<div class="control-group">
    <label for="stop_words" class="control-label">{__("cp_stop_words")}</label>
    <div class="controls">
	<input class="input-large" form="form" type="text" name="product_data[stop_words]" id="stop_words" size="55" value="{$product_data.stop_words}" />
	{include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id='product' name="update_all_vendors[stop_words]"}
    </div>
</div>