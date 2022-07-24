{include file="common/subheader.tpl" title=__("esp") target="#esp_category_setting"}
<fieldset>
	<div id="esp_category_setting" class="in collapse">
        <div class="control-group">
            <label class="control-label" for="elm_category_use_esp">{__("use_esp")}:</label>
            <div class="controls">
            <input type="hidden" value="N" name="category_data[use_esp]"/>
            <input type="checkbox" class="cm-toggle-checkbox" value="Y" name="category_data[use_esp]" id="elm_category_use_esp"{if $category_data.use_esp == 'Y'} checked="checked"{/if} />
            </div>
        </div>
	</div>
</fieldset>