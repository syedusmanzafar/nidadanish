<div class="row-fluid">
    <label class="control-label"><b>{__("sd_labels")}</b></label>
    <div class="object-selector object-selector--mobile-full-width object-selector--half-width">
        <select class="cm-object-selector"
            id="sd_labels_variants"
            multiple="multiple"
            name="search_labels[]"
            data-ca-placeholder="{__("search")}"
            data-ca-enable-images="true"
            data-ca-image-width="30"
            data-ca-image-height="30"
            data-ca-enable-search="true"
            data-ca-page-size="10"
            data-ca-close-on-select="false"
        >
            {foreach $labels as $label}
                {if $label.attachable === "YesNo::YES"|enum}
                    {$label_is_selected = $label.label_id|in_array:$search_labels}

                    <option value="{$label.label_id}"{if $label_is_selected} selected="selected"{/if}>
                        {$label.name}
                    </option>
                {/if}
            {/foreach}
        </select>
    </div>
</div>
