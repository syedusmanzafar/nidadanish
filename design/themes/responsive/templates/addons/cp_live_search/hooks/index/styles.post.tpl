{style src="addons/cp_live_search/styles.less"}
{style src="addons/cp_live_search/fontello.css"}

<style type="text/css">
    {if $cp_ls_styles.background}
        div.live-search-box,
        div.live-search-box .cp-ls-no-items {
            background: {$cp_ls_styles.background.color};
        }
        
        div.live-search-box .live-bottom-buttons,
        div.live-search-box .cp-ls-section-li {
            border-color: darken({$cp_ls_styles.background.color}, 15%);
        }

        div.live-search-box .live-item-li:hover,
        div.live-search-box .cp-ls-section-li:hover {
            background: {$cp_ls_styles.background.hover_color};
        }        
    {/if}

    {if $cp_ls_styles.header_background}
        div.live-search-box .cp-ls-header {
            background: {$cp_ls_styles.header_background.color};
        }
        div.live-search-box .cp-ls-header:hover {
            background: {$cp_ls_styles.header_background.hover_color};
        }
    {/if}
    
    {if $cp_ls_styles.popup_titles}
        div.live-search-box a,
        div.live-search-box .cp-ls-header,
        div.live-search-box .live-group-category {
            color: {$cp_ls_styles.popup_titles.color};
        }
        
        div.live-search-box a:hover,
        div.live-search-box .cp-ls-header:hover,
        div.live-search-box .live-group-category:hover {
            color: {$cp_ls_styles.popup_titles.hover_color};
        }
    {/if}

    {if $cp_ls_styles.load_more}
        div.live-search-box .live-bottom-buttons .cp-ls-load-more {
            border-color: {$cp_ls_styles.load_more.color};
            background: {$cp_ls_styles.load_more.color};
            color: {$cp_ls_styles.background.color};
        }
        
        div.live-search-box .live-bottom-buttons .cp-ls-load-more:hover {
            border-color: {$cp_ls_styles.load_more.hover_color};
            background: {$cp_ls_styles.load_more.hover_color};
        }
    {/if}
    
    {if $cp_ls_styles.view_all}
        div.live-search-box .live-bottom-buttons .cp-ls-view-all {
            background: {$cp_ls_styles.view_all.color};
        }
        
        div.live-search-box .live-bottom-buttons .cp-ls-view-all:hover {
            background: {$cp_ls_styles.view_all.hover_color};
        }
    {/if}

    {if $cp_ls_styles.view_all_text}
        div.live-search-box .live-bottom-buttons .cp-ls-view-all {
            border-color: {$cp_ls_styles.view_all_text.color};
            color: {$cp_ls_styles.view_all_text.color};
        }
        
        div.live-search-box .live-bottom-buttons .cp-ls-view-all:hover {
            border-color: {$cp_ls_styles.view_all_text.hover_color};
            color: {$cp_ls_styles.view_all_text.hover_color};
        }
    {/if}
    
    {if $cp_ls_styles.add_to_cart}
        div.live-search-box .cp-live-search-buttons .cp-ls-add-to-cart,
        div.live-search-box .cp-live-search-buttons .cp-ls-icon-option {
            color: {$cp_ls_styles.add_to_cart.color};
        }
        div.live-search-box .cp-live-search-buttons .cp-ls-add-to-cart:hover,
        div.live-search-box .cp-live-search-buttons .cp-ls-icon-option:hover {
            color: {$cp_ls_styles.add_to_cart.hover_color};
        }
    {/if}
    
    {if $cp_ls_styles.add_to_wishlist}
        div.live-search-box .cp-live-search-buttons .cp-ls-add-to-wishlist {
            color: {$cp_ls_styles.add_to_wishlist.color};
        }
        div.live-search-box .cp-live-search-buttons .cp-ls-add-to-wishlist:hover {
            color: {$cp_ls_styles.add_to_wishlist.hover_color};
        }
    {/if}
    
    {if $cp_ls_styles.add_to_compare}
        div.live-search-box .cp-live-search-buttons .cp-ls-add-to-compare {
            color: {$cp_ls_styles.add_to_compare.color};
        }
        div.live-search-box .cp-live-search-buttons .cp-ls-add-to-compare:hover {
            color: {$cp_ls_styles.add_to_compare.hover_color};
        }
    {/if}
    
    {if $cp_ls_styles.product_name}
        div.live-search-box .live-info-container .live-product-name {
            color: {$cp_ls_styles.product_name.color};
        }
        div.live-search-box .live-info-container .live-product-name:hover {
            color: {$cp_ls_styles.product_name.hover_color};
        }
        div.live-search-box .live-info-container .cp-ls-company-title {
            color: lighten({$cp_ls_styles.product_name.color}, 25%);
        }
    {/if}
    
    {if $cp_ls_styles.product_name}
        div.live-search-box .live-info-container .live-product-name {
            color: {$cp_ls_styles.product_name.color};
        }
        div.live-search-box .live-info-container .live-product-name:hover {
            color: {$cp_ls_styles.product_name.hover_color};
        }
    {/if}
    
    {if $cp_ls_styles.product_price}
        div.live-search-box .live-info-container .live-product-price {
            color: {$cp_ls_styles.product_price.color};
        }
        div.live-search-box .live-info-container .live-product-price:hover {
            color: {$cp_ls_styles.product_price.hover_color};
        }
    {/if}

    {if $cp_ls_styles.list_price}
        div.live-search-box .live-info-container .live-product-list-price {
            color: {$cp_ls_styles.list_price.color};
        }
        div.live-search-box .live-info-container .live-product-list-price:hover {
            color: {$cp_ls_styles.list_price.hover_color};
        }
    {/if}
    
    {if $cp_ls_styles.product_code}
        div.live-search-box .live-info-container .live-product-code {
            color: {$cp_ls_styles.product_code.color};
        }
        div.live-search-box .live-info-container .live-product-code:hover {
            color: {$cp_ls_styles.product_code.hover_color};
        }
    {/if}
    
    {if $addons.cp_live_search.show_product_category != "each"}
        div.live-search-box .live-product-name-wrap {
            margin-right: 10px;
        }
    {/if}
    {if $addons.cp_live_search.show_thumbnails != "Y"}
        div.live-search-box .live-info-container {
            margin-left: 10px;
        }
    {else $addons.cp_live_search.thumbnails_width}
        div.live-search-box .live-info-container {
            margin-left: {$addons.cp_live_search.thumbnails_width+10}px;
        }
    {/if}
</style>
