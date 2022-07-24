{capture "sd_product_labels_less_settings"}
    @_sd-pl-label-text-on-detail-page-font-size: {$addons.sd_labels.detail_product_text_label_font_size}px;
    @_sd-pl-label-text-on-product-list-font-size: {$addons.sd_labels.products_viewer_text_label_font_size}px;
    {if $addons.sd_labels.text_label_round_corners == "YesNo::YES"|enum}
        @_sd-pl-label-text-border-radius: {$addons.sd_labels.text_label_corner_radius}px;
    {else}
        @_sd-pl-label-text-border-radius: 0;
    {/if}
{/capture}
{style content=$smarty.capture.sd_product_labels_less_settings type="less"}

{style src="addons/sd_labels/styles.less"}
{style src="addons/sd_labels/theme_styles.less"}
