<div class="ty-orders-detail-vendor">
    <div class="ty-orders-detail-vendor__header">
        <div class="ty-orders-detail-vendor__header-lc">
            {__("vendor")}: <a href="{"companies.view&company_id=`$order_info.company_id`"|fn_url}"><strong>{$order_info.company_id|fn_get_company_name}</strong></a>
        </div>
        <div class="ty-orders-detail-vendor__header-rc cm-combination" id="sw_order_{$order_info.order_id}"> 
            <i class="ut2-icon-outline-expand_more"></i>
        </div>
    </div>
    <table class="ty-orders-detail__table ty-table ty-orders-detail-vendor__body" id="order_{$order_info.order_id}">
        {hook name="orders:items_list_header"}
            <thead>
            <tr>
                <th class="ty-orders-detail__table-product">{__("product")}</th>
                <th class="ty-orders-detail__table-price">{__("price")}</th>
                <th class="ty-orders-detail__table-quantity">{__("quantity")}</th>
                {if $order_info.use_discount}
                    <th class="ty-orders-detail__table-discount">{__("discount")}</th>
                {/if}
                {if $order_info.taxes && $settings.Checkout.tax_calculation != "subtotal"}
                    <th class="ty-orders-detail__table-tax">{__("tax")}</th>
                {/if}
                <th class="ty-orders-detail__table-subtotal">{__("subtotal")}</th>
            </tr>
            </thead>
        {/hook}
        {foreach from=$order_info.products item="product" key="key"}
            {hook name="orders:items_list_row"}
            {if !$product.extra.parent}
                <tr class="ty-valign-top">
                    <td>
                        <div class="clearfix">
                            <div class="ty-float-left ty-orders-detail__table-image">
                                {hook name="orders:product_icon"}
                                {if $product.is_accessible}<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{/if}
                                    {include file="common/image.tpl" obj_id=$key images=$product.main_pair image_width=$settings.Thumbnails.product_cart_thumbnail_width image_height=$settings.Thumbnails.product_cart_thumbnail_height}
                                {if $product.is_accessible}</a>{/if}
                                {/hook}
                            </div>

                            <div class="ty-overflow-hidden ty-orders-detail__table-description-wrapper">
                                <div class="ty-ml-s ty-orders-detail__table-description">
                                    {if $product.is_accessible}<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{/if}
                                        {$product.product nofilter}
                                        {if $product.is_accessible}</a>{/if}
                                    {if $product.extra.is_edp == "Y"}
                                        <div class="ty-right">
                                            <a href="{"orders.order_downloads?order_id=`$order_info.order_id`"|fn_url}">[{__("download")}]</a>
                                        </div>
                                    {/if}
                                    {if $product.product_code}
                                        <div class="ty-orders-detail__table-code">{__("sku")}:&nbsp;{$product.product_code}</div>
                                    {/if}
                                    {hook name="orders:product_info"}
                                    {if $product.product_options}{include file="common/options_info.tpl" product_options=$product.product_options inline_option=true}{/if}
                                    {/hook}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="ty-right">
                        {if $product.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$product.original_price}{/if}
                    </td>
                    <td class="ty-center">&nbsp;{$product.amount}</td>
                    {if $order_info.use_discount}
                        <td class="ty-right">
                            {if $product.extra.discount|floatval}{include file="common/price.tpl" value=$product.extra.discount}{else}-{/if}
                        </td>
                    {/if}
                    {if $order_info.taxes && $settings.Checkout.tax_calculation != "subtotal"}
                        <td class="ty-center">
                            {if $product.tax_value|floatval}{include file="common/price.tpl" value=$product.tax_value}{else}-{/if}
                        </td>
                    {/if}
                    <td class="ty-right">
                        &nbsp;{if $product.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$product.display_subtotal}{/if}
                    </td>
                </tr>
            {/if}
            {/hook}
        {/foreach}

        {hook name="orders:extra_list"}
        {assign var="colsp" value=5}
        {if $order_info.use_discount}{assign var="colsp" value=$colsp+1}{/if}
        {if $order_info.taxes && $settings.Checkout.tax_calculation != "subtotal"}{assign var="colsp" value=$colsp+1}{/if}
        {/hook}

    </table>
</div>