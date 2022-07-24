{$enum_NO = "YesNo::NO"|enum}
{$enum_YES = "YesNo::YES"|enum}

{if $layouts}
    {$sd_labels_display_data = $layouts.$selected_layout.sd_labels}
    {$sd_labels_area = "product-list"}
    {$sd_labels_image_width  = $addons.sd_labels.products_viewer_graphic_label_image_width}
    {$sd_labels_image_height = $addons.sd_labels.products_viewer_graphic_label_image_height}
{elseif $block.type === "products"
    && isset($block.properties.show_sd_labels) && $block.properties.show_sd_labels === $enum_YES
}
    {$sd_labels_display_data = $block.properties}
    {$sd_labels_area = "product-list"}
    {$sd_labels_image_width  = $addons.sd_labels.products_viewer_graphic_label_image_width}
    {$sd_labels_image_height = $addons.sd_labels.products_viewer_graphic_label_image_height}
{elseif $block.type === "main" || $quick_view || $is_variation}
    {$sd_labels_display_data = $sd_labels_display_settings}
    {$sd_labels_area = "detail-page"}
    {$sd_labels_image_width  = $addons.sd_labels.detail_product_graphic_label_image_width}
    {$sd_labels_image_height = $addons.sd_labels.detail_product_graphic_label_image_height}
{/if}

{if $sd_labels_display_data}
    {$show_sd_labels  = $sd_labels_display_data.show_sd_labels}
    {$is_overlay      = $sd_labels_display_data.sd_labels_overlay}
    {$labels_position = $sd_labels_display_data.sd_labels_position|lower}
    {$sd_labels_area  = $sd_labels_area}
    {$sd_labels_image_width  = $sd_labels_image_width}
    {$sd_labels_image_height = $sd_labels_image_height}
    {$sd_labels_hide_labels_on_hover = ($sd_labels_display_data.sd_labels_hide_labels_on_hover_over_product_card === $enum_YES || $sd_labels_display_data.sd_labels_hide_labels_on_hover_over_image === $enum_YES)}

    {if $is_overlay !== $enum_YES}
        {$sd_labels_hide_labels_on_hover = false}
    {/if}
{/if}

{$show_sd_labels  = $show_sd_labels|default:    $enum_NO scope="parent"}
{$is_overlay      = $is_overlay|default:        $enum_NO scope="parent"}
{$labels_position = $labels_position|default:   false    scope="parent"}
{$sd_labels_area  = $sd_labels_area|default:    false    scope="parent"}
{$sd_labels_image_width   = $sd_labels_image_width|default:  40 scope="parent"}
{$sd_labels_image_height  = $sd_labels_image_height|default: 40 scope="parent"}
{$sd_labels_hide_labels_on_hover = $sd_labels_hide_labels_on_hover scope="parent"}