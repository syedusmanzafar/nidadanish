{if $sd_show_labels}
    {$label_thumbnails_size = $label_thumbnails_size|default:50}

    {include "common/subheader.tpl"
        title=__("sd_labels_general_section")
        target="#acc_sd_labels"
    }

    <div class="row-fluid">
        <label class="control-label" for="sd_labels_variants">{__("sd_labels")}</label>
        <div class="controls">
            <input type="hidden" name="product_data[sd_labels]" />
            <div class="object-selector object-selector--mobile-full-width object-selector--half-width">
                <select class="cm-object-selector"
                    id="sd_labels_variants"
                    multiple="multiple"
                    name="product_data[sd_labels][{$label.label_id}]"
                    data-ca-placeholder="-{__("none")}-"
                    data-ca-enable-images="true"
                    data-ca-image-width="30"
                    data-ca-image-height="30"
                    data-ca-enable-search="true"
                    data-ca-page-size="10"
                    data-ca-close-on-select="false"
                >
                    {foreach $sd_labels as $label}
                        {if $label.attachable === "YesNo::YES"|enum}
                            {$label_is_selected = $label.label_id|in_array:$product_data.sd_labels}

                            <option value="{$label.label_id}"{if $label_is_selected} selected="selected"{/if}>
                                {$label.name}
                            </option>
                        {/if}
                    {/foreach}
                </select>
            </div>
        </div>
        {if $sd_show_apply_labels_for_variations}
            <label class="control-label" for="sd_labels_variations">{__("sd_labels.apply_to_child_variations")}</label>
            <div class="controls">
                <input type="hidden" name="product_data[sd_labels_for_variations]" value={"YesNo::NO"|enum} />
                <input
                    type="checkbox"
                    id="sd_labels_variations"
                    name="product_data[sd_labels_for_variations]"
                    value="{"YesNo::YES"|enum}"
                    {if $product_data.sd_labels_for_variations === "YesNo::YES"|enum}checked="checked"{/if}
                />
            </div>
        {/if}
    </div>
{/if}
