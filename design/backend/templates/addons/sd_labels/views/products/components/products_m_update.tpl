<div class="row-fluid">
    <input type="hidden" name="{if $override_box}override_products_data[sd_labels_override][]{else}products_data[{$product.product_id}][sd_labels][]{/if}" id="{$product.product_id}" />
    <div class="object-selector object-selector--mobile-full-width">
        <select
            {if $override_box}id="field_{$field}__{$product.product_id}_" disabled="disabled"{/if}
            class="cm-object-selector{if $override_box} elm-disabled{/if}"
            name="{if $override_box}override_products_data[sd_labels_override][]{else}products_data[{$product.product_id}][sd_labels][]{/if}"
            multiple="multiple"
            data-ca-enable-images="true"
            data-ca-image-width="30"
            data-ca-image-height="30"
            data-ca-enable-search="true"
            data-ca-page-size="10"
            data-ca-placeholder="-{__("none")}-"
            data-ca-close-on-select="false"
        >
            {foreach $sd_labels as $label_id => $label}
                {$label_is_selected = $label_id|in_array:$product.sd_labels}

                <option value="{$label_id}"{if !$override_box && $label_is_selected} selected="selected"{/if}>
                    {$label.name}
                </option>
            {/foreach}
        </select>
    </div>
</div>
