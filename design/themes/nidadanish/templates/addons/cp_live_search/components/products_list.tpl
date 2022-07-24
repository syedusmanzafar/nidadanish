<ul class="live-products" id="live_search_products{$search_prefix}{$search_input_id}">
    {assign var="group_category_name" value=""}
    {foreach from=$live_result item="product" name="live_result_products"}

        {assign var="category_group_displayed" value=false}

        {if $category_names && $category_names[$product.main_category]
            && $category_names[$product.main_category] != $group_category_names
        }
            {$category_group_displayed = true}
            {$group_category_names = $category_names[$product.main_category]}
            <li class="live-group-category">
                <a href="{"categories.view?category_id=`$product.main_category`"|fn_url}">{$group_category_names}</a>
            </li>
        {/if}
        
        <li class="live-item-li clearfix">
            {if $category_labels && $category_labels[$product.main_category]}
                <div class="cp-category-label-wrap">
                    <a href="{"categories.view?category_id=`$category_labels[$product.main_category].category_id`"|fn_url}">
                        <div class="cp-category-label" title="{$category_labels[$product.main_category].category}" style="background-color: {$category_labels[$product.main_category].color};">
                            {$category_labels[$product.main_category].category}
                        </div>
                    </a>
                </div>
            {/if}
            
            <a class="cm-ls-product" href="{"products.view&product_id=`$product.product_id``$cp_query_params`"|fn_url}">
                <div class="live-item-container">
                    {if $addons.cp_live_search.show_thumbnails == 'Y'}
                        <div class="live-image-container">
                            {include file="common/image.tpl" images=$product.main_pair image_width=$addons.cp_live_search.thumbnails_width|default:50 image_height=$addons.cp_live_search.thumbnails_height|default:50}
                        </div>
                    {/if}
                    <div class="live-info-container">
                        <div class="live-product-name-wrap">
                            <span class="live-product-name product-title" title="{$product.product nofilter}">{$product.product nofilter}</span>
                        </div>
                        {if $product.master_product_id && $search.separate_common_products && $product.company_name}
                            <div class="cp-ls-company-title">{$product.company_name}</div>
                        {/if}
                        {if $addons.cp_live_search.show_product_price == 'Y'
                            && ($settings.General.allow_anonymous_shopping != 'hide_price_and_add_to_cart' || $auth.user_id != 0)
                        }
                            <div class="live-product-price-wrap">
                                {if $product.zero_price_action == "R" && (!$product.price || $product.price == 0.0)}
                                    <span class="ty-no-price">{__("contact_us_for_price")}</span>
                                {else}
                                    {if $addons.cp_live_search.show_list_price == 'Y'}
                                        {assign var="old_price" value=0}
                                        {if $product.discount}
                                            {$old_price = $product.original_price|default:$product.base_price}
                                        {elseif $product.list_discount}
                                            {$old_price = $product.list_price}
                                        {/if}
                                        {if $old_price && $old_price != 0.0 && $old_price > $product.price}
                                            <span class="live-product-list-price ty-list-price">{include file="common/price.tpl" value=$old_price}</span>
                                        {/if}
                                    {/if}
                                    <span class="live-product-price ty-price-num">
                                        {include file="common/price.tpl" value=$product.price}
                                    </span>
                                    {if $settings.Appearance.show_prices_taxed_clean == "Y" && $product.taxed_price}
                                        <div class="live-product-price cp-taxed-price">
                                            {if $product.clean_price != $product.taxed_price && $product.included_tax}
                                                ({include file="common/price.tpl" value=$product.taxed_price} {__("inc_tax")})
                                            {/if}
                                        </div>
                                    {/if}
                                {/if}
                            </div>
                        {/if}
                        {if $addons.cp_live_search.show_product_code == 'Y'}
                            <div class="live-product-code-wrap">
                                <span class="live-product-code">{$product.product_code nofilter}</span>
                            </div>
                        {/if}
                    </div>
                </div>
            </a>
                    
            <div class="cp-live-search-buttons">
                {$current_url = "`$config.current_url``$cp_query_params`"|escape:url}
                {if !$current_url}
                    {$current_url = ""|fn_url}
                {/if}
                
                {if $addons.cp_live_search.show_add_to_wishlist == 'Y' && $addons.wishlist.status == "A"}
                    <a class="ty-btn cp-ls-add-to-wishlist" onclick="$.ceAjax('request', fn_url('wishlist.add?product_data[{$product.product_id}][product_id]={$product.product_id}&product_data[{$product.product_id}][amount]={$amount}{$cp_query_params}'), { result_ids: 'cart_status*,wish_list*,abt__unitheme_wishlist_count,abt__youpitheme_wishlist_count', full_render: true, method: 'post' });" rel="nofollow" title="{__("add_to_wishlist")}"><i class="cp-ls-icon-wishlist"></i></a>
                {/if}
                
                {if $addons.cp_live_search.show_add_to_comparison_list == 'Y'}
                    {assign var="compare_redirect_url" value="product_features.compare"|fn_url|escape:url}
                    <a class="ty-btn cp-ls-add-to-compare cm-ajax" href="{"product_features.add_product?product_id=`$product.product_id``$cp_query_params`&redirect_url=`$compare_redirect_url`"|fn_url}" rel="nofollow" title="{__("add_to_comparison_list")}" data-ca-target-id="comparison_list,account_info*,cart_status*,abt__unitheme_compared_products"><i class="cp-ls-icon-compare"></i></a>
                {/if}
                
                {if ($addons.cp_live_search.show_add_to_cart == 'Y'
                    && ($settings.General.allow_anonymous_shopping == 'allow_shopping' || $auth.user_id != 0))
                    && !($product.zero_price_action == "R" && (!$product.price || $product.price == 0.0))}

                    {$quick_view_url = "products.quick_view?product_id=`$product.product_id`&prev_url=`$current_url`&result_ids='cart_status*'`$cp_query_params`"}
                    {if $quick_nav_ids}
                        {$quick_nav_ids = ","|implode:$quick_nav_ids}
                        {$quick_view_url = $quick_view_url|fn_link_attach:"n_items=`$quick_nav_ids`"}
                    {/if}

                    {if $product.min_qty}
                        {$amount = $product.min_qty}
                    {else}
                        {$amount = 1}
                    {/if}

                    {if $product.has_options
                        || $product.has_child_variations
                        || ($product.company_id == 0 && $product.master_product_offers_count)
                    }
                        <a class="cm-dialog-opener cm-dialog-auto-size ty-btn cp-ls-select-options" data-ca-view-id="{$product.product_id}" data-ca-target-id="product_quick_view" href="{$quick_view_url|fn_url}" data-ca-dialog-title="{__("quick_product_viewer")}" rel="nofollow" title="{__('select')}"><i class="cp-ls-icon-option"></i></a>
                    {elseif
                        $settings.General.inventory_tracking != "Y"
                        || $settings.General.allow_negative_amount == "Y"
                        || ($product.amount > 0 && $product.amount >= $product.min_qty)
                        || $product.tracking == "ProductTracking::DO_NOT_TRACK"|enum
                        || $product.is_edp == "Y"
                    }
                        <a class="ty-btn cp-ls-add-to-cart" onclick="$.ceAjax('request', fn_url('checkout.add?product_data[{$product.product_id}][product_id]={$product.product_id}&product_data[{$product.product_id}][amount]={$amount}{$cp_query_params}'), { result_ids: 'cart_status*', full_render: true, method: 'post' });" rel="nofollow" title="{__("add_to_cart")}"><i class="cp-ls-icon-cart"></i></a>
                    {/if}
                {/if}
            </div>
        </li>
    {/foreach}
<!--live_search_products{$search_prefix}{$search_input_id}--></ul>

