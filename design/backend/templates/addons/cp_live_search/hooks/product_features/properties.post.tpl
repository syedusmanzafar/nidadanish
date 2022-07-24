<div class="control-group {if !$feature.feature_type || $selectable_group|strpos:$feature.feature_type === false && $feature.purpose != "describe_product"}hidden{/if}">
    <label class="control-label" for="elm_cp_use_for_suggestions_{$id}">{__("cp_in_live_search")}</label>
    <div class="controls">
        <input type="hidden" name="feature_data[cp_ls_use]" value="N" />
        <input id="elm_cp_use_for_suggestions_{$id}" type="checkbox" name="feature_data[cp_ls_use]" value="Y" {if $feature.cp_ls_use == "Y"}checked="checked"{/if} />
    </div>
</div>