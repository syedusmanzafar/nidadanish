{$capture_product_name      = "name_{$obj_id}"}
{$capture_discount_label    = "discount_label_{$obj_prefix}{$obj_id}"}
{$capture_product_labels    = "product_labels_{$obj_prefix}{$obj_id}"}
{$capture_sd_product_labels = "sd_product_labels_{$obj_prefix}{$obj_id}"}

{include "addons/sd_labels/common/labels_params.tpl"}

{capture "{$capture_discount_label}"}
    {if $show_sd_labels === "YesNo::NO"|enum}
        {$smarty.capture.$capture_discount_label nofilter}
    {/if}
{/capture}

{capture "{$capture_product_labels}"}
    {if $show_sd_labels === "YesNo::NO"|enum}
        {$smarty.capture.$capture_product_labels nofilter}
    {/if}
{/capture}

{capture "{$capture_sd_product_labels}"}
    {if $show_sd_labels === "YesNo::YES"|enum}
        {$label_list.hide_labels_on_hover = $sd_labels_hide_labels_on_hover}

        {if $is_overlay === "YesNo::YES"|enum}
            {$label_list.location = "overlay"}

            {if $labels_position}
                {$label_list.position = $labels_position}
            {/if}
        {else}
            {$label_list.location = "product-name"}
        {/if}

        {if $sd_labels_area}
            {$label_list.area = $sd_labels_area}
        {/if}

        <div class="sd-product-labels-update-container cm-reload-{$obj_prefix}{$obj_id}"
            id="product_labels_update_sd_labels_{$obj_prefix}{$obj_id}">

            {include "addons/sd_labels/components/labels_container.tpl"
                label_list    = $label_list
                items_text    = $product.sd_labels.text
                items_graphic = $product.sd_labels.graphic
                image_width  = $sd_labels_image_width
                image_height = $sd_labels_image_height
                items_text_show_in_column    = $addons.sd_labels.text_label_is_column
                items_graphic_show_in_column = $addons.sd_labels.graphic_label_is_column
            }
        <!--product_labels_update_sd_labels_{$obj_prefix}{$obj_id}--></div>
    {/if}
{/capture}

{capture "{$capture_product_name}"}
    {if $show_sd_labels === "YesNo::YES"|enum && $is_overlay === "YesNo::NO"|enum}
        {$smarty.capture.$capture_sd_product_labels nofilter}
    {/if}

    {$smarty.capture.$capture_product_name nofilter}
{/capture}
