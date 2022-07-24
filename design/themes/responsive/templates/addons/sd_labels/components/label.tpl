{$capture_styles_label    = "capture_styles_sd_labels_label_{$label.label_id}"}
{$capture_tooltip_content = "capture_label_tooltip_{$label.label_id}"}

{$image_width    = $image_width|default:40}
{$image_height   = $image_height|default:40}
{$tooltip_exists = $label.tooltip_content|strip_tags|trim}

{if $smarty.capture.$capture_styles_label !== "" && $label.display_type === "Tygh\Addons\SdLabels\Labels\Label::TEXT"|constant}
    {capture "{$capture_styles_label}"}
        {* Styles *}
        {if $label.display_type === "Tygh\Addons\SdLabels\Labels\Label::TEXT"|constant}
            .{$label.class} {
                background: {$label.background_color};
                color: {$label.text_color};
            }
        {/if}
    {/capture}
    <style class="js-move-to-head">{$smarty.capture.$capture_styles_label|strip nofilter}</style>
{/if}

{if $tooltip_exists && $smarty.capture.$capture_tooltip_content !== ""}
    {capture "{$capture_tooltip_content}"}
        <div class="sd-label-tooltip js-label-tooltip-content tooltip arrow-down"
            id="sd_label_tooltip_{$label.label_id}"
            data-sd-label-target-element="sd_label_{$label.label_id}"
        >
            <span class="tooltip-arrow"></span>
            {$label.tooltip_content nofilter}
        </div>
    {/capture}
    {$smarty.capture.$capture_tooltip_content|trim|strip nofilter}
{/if}

{strip}
    <div class="sd-label sd-label--type-{$label.display_type} {$label.class}{if $tooltip_exists} js-label-tooltip-toggle{/if}"
        data-sd-label-id="{$label.label_id}"
    >
        {if $label.display_type === "Tygh\Addons\SdLabels\Labels\Label::GRAPHIC"|constant}
            <span class="sd-label__image-wrapper" title="{$label.name}">
                {include "common/image.tpl"
                    images       = $label.main_pair
                    images       = $label.main_pair
                    image_width  = $image_width
                    image_height = $image_height
                    class        = "sd-label__image"
                    lazy_load    = false
                    no_lazy_load = true
                }
            </span>
        {elseif $label.display_type === "Tygh\Addons\SdLabels\Labels\Label::TEXT"|constant}
            <span class="sd-label__name">
                {if
                    !$product.company_id
                    && $show_master_product_discount_label
                    && ($product.discount_prc
                    || $product.list_discount_prc)
                    && $show_price_values
                    && $label.label_type == "discount"
                }
                    {"{__("sd_labels.save_up_to")} {$product.list_discount_prc}%"}
                {else}
                    {$label.name}
                {/if}
            </span>
        {/if}
    </div>
{/strip}
