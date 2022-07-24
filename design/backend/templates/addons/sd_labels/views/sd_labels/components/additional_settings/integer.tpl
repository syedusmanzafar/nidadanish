<div class="control-group">
    <label for="elm_label_additional_setting_{$additional_setting.code}_{$lable_id}"
        class="control-label cm-integer {if $additional_setting.reqired} cm-required{/if}"
    >
        {__($additional_setting.name)}
    </label>
    <div class="controls">
        <input id="elm_label_additional_setting_{$additional_setting.code}_{$lable_id}"
            class=""
            type="text"
            name="label_data[additional_settings][{$additional_setting.code}]"
            value="{$additional_setting.value}"
        />
    </div>
</div>
