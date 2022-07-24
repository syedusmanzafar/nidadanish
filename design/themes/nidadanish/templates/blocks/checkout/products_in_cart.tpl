{assign var="result_ids" value="cart*,checkout*,checkout_totals*"}
<style>
.ty-step__title-active,.ty-checkout-buttons .ty-btn,.ty-btn__login {
    background-color: #f62d32 !important;
}
.litecheckout__shipping-method__radio:checked+.litecheckout__shipping-method__wrapper {
    border: solid 1px;
    border-color: #f62d32;
    box-shadow: 0 0 0 4px rgb(190 49 49 / 20%), 0 0 0 1px #f62d32;
    background-color: #fff;
}
.checkout-grid {
	padding: 20px !important;
}
.ty-newsletters:first-child {
	display: none;
}
.ty-cart-statistic__item {
    color: #000000 !important;
}
.ty-step__container,input[type=text], input[type=password], textarea, select {
    background: white;
}
.order-products i.ty-delete-big__icon.ty-icon-cancel-circle,.litecheckout__shipping-method__title {
    color: #f62d32 !important;
}
.ty-customer-notes__text,.ty-customer-notes__text:hover,.ty-checkout__terms {
    border-color: red !important;
}
input[type=checkbox]:checked:before {
    content: '✓';
    position: absolute;
    background: red;
    height: 15px;
    width: 15px;
    border: 1px solid #4f4f4f;
    border-radius: 2px;
    color: white;
    font-size: 14px;
    text-align: center;
    font-weight: bold;
    line-height: 15px;
}
input[type=checkbox]:before {
    content: '✓';
    position: absolute;
    height: 15px;
    width: 15px;
    border: 1px solid #4f4f4f;
    background: white;
    border-radius: 2px;
    color: white;
    font-size: 14px;
    text-align: center;
    font-weight: bold;
    line-height: 15px;
}
.ty-newsletters__item label {
    vertical-align: middle;
    line-height: 18px;
}
.ty-shipping-country+.ty-shipping-state {
	margin-left:0 !important;
}
</style>
<form name="checkout_form" class="cm-check-changes cm-ajax cm-ajax-full-render" action="{""|fn_url}" method="post" enctype="multipart/form-data" id="checkout_form">
<input type="hidden" name="redirect_mode" value="checkout" />
<input type="hidden" name="result_ids" value="{$result_ids}" />
{*<div id="checkout_info_products_{$block.snapping_id}">
    <ul class="ty-order-products__list order-product-list">
    {hook name="block_checkout:cart_items"}
        {foreach from=$cart_products key="key" item="product" name="cart_products"}
            {hook name="block_checkout:cart_products"}
                {if !$cart.products.$key.extra.parent}
                    <li class="ty-order-products__item">
                        <bdi><a class="litecheckout__order-products-p" href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$product.product nofilter}</a></bdi>
                        {if !$product.exclude_from_calculate}
                            {include file="buttons/button.tpl" but_href="checkout.delete?cart_id=`$key`&redirect_mode=`$runtime.mode`" but_meta="ty-order-products__item-delete delete" but_target_id="cart_status*" but_role="delete" but_name="delete_cart_item"}
                        {/if}
                        {hook name="products:product_additional_info"}
                        {/hook}
                        <div class="ty-order-products__price">
                            {$product.amount}&nbsp;x&nbsp;{include file="common/price.tpl" value=$product.display_price}
                        </div>
                        {include file="common/options_info.tpl" product_options=$product.product_options no_block=true}
                        {hook name="block_checkout:product_extra"}{/hook}
                    </li>
                {/if}
            {/hook}
        {/foreach}
    {/hook}
    </ul>
<!--checkout_info_products_{$block.snapping_id}--></div>
OLD *}

<div id="checkout_info_products_{$block.snapping_id}">
    {*<ul class="ty-order-products__list order-product-list">
    {hook name="block_checkout:cart_items"}
        {foreach from=$cart_products key="key" item="product" name="cart_products"}
            {hook name="block_checkout:cart_products"}
                {if !$cart.products.$key.extra.parent}
                    <li class="ty-order-products__item">
                        <bdi><a class="litecheckout__order-products-p" href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$product.product nofilter}</a></bdi>
                        {if !$product.exclude_from_calculate}
                            {include file="buttons/button.tpl" but_href="checkout.delete?cart_id=`$key`&redirect_mode=`$runtime.mode`" but_meta="ty-order-products__item-delete delete" but_target_id="cart_status*" but_role="delete" but_name="delete_cart_item"}
                        {/if}
                        {hook name="products:product_additional_info"}
                        {/hook}
                        <div class="ty-order-products__price">
                            {$product.amount}&nbsp;x&nbsp;{include file="common/price.tpl" value=$product.display_price}
                        </div>
                        {include file="common/options_info.tpl" product_options=$product.product_options no_block=true}
                        {hook name="block_checkout:product_extra"}{/hook}
                    </li>
                {/if}
            {/hook}
        {/foreach}
    {/hook}
    </ul>*}

{capture name="cartbox"}

<div id="cart_items">
    <table class="ty-cart-content ty-table">

    {assign var="prods" value=false}

    <thead>
        <tr>
            <th class="ty-cart-content__title ty-left">{__("product")}</th>
            <th class="ty-cart-content__title ty-left">&nbsp;</th>
            <th class="ty-cart-content__title ty-right">{__("unit_price")}</th>
            <th class="ty-cart-content__title quantity-cell">{__("quantity")}</th>
            <th class="ty-cart-content__title ty-right">{__("total_price")}</th>
        </tr>
    </thead>

    <tbody>
	{$show_images = true}
    {if $cart_products}
	{$taxes = 0}
        {foreach from=$cart_products item="product" key="key" name="cart_products"}
            {assign var="obj_id" value=$product.object_id|default:$key}
            {hook name="checkout:items_list"}

                {if !$cart.products.$key.extra.parent}
                    <tr>
                        <td class="ty-cart-content__product-elem ty-cart-content__image-block">
                            {if $runtime.mode == "cart" || $show_images}
                                <div class="ty-cart-content__image cm-reload-{$obj_id}" id="product_image_update_{$obj_id}">
                                    {hook name="checkout:product_icon"}
                                        <a href="{"products.view?product_id=`$product.product_id`"|fn_url}">
                                        {include file="common/image.tpl" obj_id=$key images=$product.main_pair image_width=$settings.Thumbnails.product_cart_thumbnail_width image_height=$settings.Thumbnails.product_cart_thumbnail_height}</a>
                                    {/hook}
                                <!--product_image_update_{$obj_id}--></div>
                            {/if}
                        </td>

                        <td class="ty-cart-content__product-elem ty-cart-content__description" style="width: 50%;">
                            {strip}
                                <a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="ty-cart-content__product-title">
                                    {$product.product nofilter}
                                </a>
                                {if !$product.exclude_from_calculate}
                                    <a class="{$ajax_classs} ty-cart-content__product-delete ty-delete-big" href="{"checkout.delete?cart_id=`$key`&redirect_mode=`$runtime.mode`"|fn_url}" data-ca-target-id="cart_items,checkout_totals,cart_status*,checkout_steps,checkout_cart" title="{__("remove")}">&nbsp;<i class="ty-delete-big__icon ty-icon-cancel-circle"></i>
                                    </a>
                                {/if}
                            {/strip}
                            {hook name="products:product_additional_info"}
                                <div class="ty-cart-content__sku ty-sku cm-hidden-wrapper{if !$product.product_code} hidden{/if}" id="sku_{$key}">
                                    {__("sku")}: <span class="cm-reload-{$obj_id}" id="product_code_update_{$obj_id}">{$product.product_code}<!--product_code_update_{$obj_id}--></span>
                                </div>
                                {hook name="checkout:product_options"}
                                    {if $product.product_options}
                                        <div class="cm-reload-{$obj_id} ty-cart-content__options" id="options_update_{$obj_id}">
                                            <input type="hidden" name="no_cache" value="no_cache" />
                                            {include file="views/products/components/product_options.tpl" product_options=$product.product_options product=$product name="cart_products" id=$key location="cart" disable_ids=$disable_ids form_name="checkout_form"}
                                        <!--options_update_{$obj_id}--></div>
                                    {/if}
                                {/hook}
                            {/hook}

                            {assign var="name" value="product_options_$key"}
                            {capture name=$name}

                            {capture name="product_info_update"}
                                {hook name="checkout:product_info"}
                                    {if $product.exclude_from_calculate}
                                        <strong><span class="price">{__("free")}</span></strong>
                                    {elseif $product.discount|floatval || ($product.taxes && $settings.Checkout.tax_calculation != "subtotal")}
                                        {if $product.discount|floatval}
                                            {assign var="price_info_title" value=__("discount")}
                                        {else}
                                            {assign var="price_info_title" value=__("taxes")}
                                        {/if}
                                        <p><a data-ca-target-id="discount_{$key}" class="cm-dialog-opener cm-dialog-auto-size" rel="nofollow">{$price_info_title}</a></p>

                                        <div class="ty-group-block hidden" id="discount_{$key}" title="{$price_info_title}">
                                            <table class="ty-cart-content__more-info ty-table">
                                                <thead>
                                                    <tr>
                                                        <th class="ty-cart-content__more-info-title">{__("price")}</th>
                                                        <th class="ty-cart-content__more-info-title">{__("quantity")}</th>
                                                        {if $product.discount|floatval}<th class="ty-cart-content__more-info-title">{__("discount")}</th>{/if}
                                                        {if $product.taxes && $settings.Checkout.tax_calculation != "subtotal"}<th>{__("tax")}</th>{/if}
                                                        <th class="ty-cart-content__more-info-title">{__("subtotal")}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{include file="common/price.tpl" value=$product.original_price span_id="original_price_`$key`" class="none"}</td>
                                                        <td class="ty-center">{$product.amount}</td>
                                                        {if $product.discount|floatval}<td>{include file="common/price.tpl" value=$product.discount span_id="discount_subtotal_`$key`" class="none"}</td>{/if}
                                                        {if $product.taxes && $settings.Checkout.tax_calculation != "subtotal"}<td>{include file="common/price.tpl" value=$product.tax_summary.total span_id="tax_subtotal_`$key`" class="none"}</td>{/if}
                                                        <td>{include file="common/price.tpl" span_id="product_subtotal_2_`$key`" value=$product.display_subtotal class="none"}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    {/if}
                                    {include file="views/companies/components/product_company_data.tpl" company_name=$product.company_name company_id=$product.company_id}
                                {/hook}
                            {/capture}
                            {if $smarty.capture.product_info_update|trim}
                                <div class="cm-reload-{$obj_id}" id="product_info_update_{$obj_id}">
                                    {$smarty.capture.product_info_update nofilter}
                                <!--product_info_update_{$obj_id}--></div>
                            {/if}
                            {/capture}

                            {if $smarty.capture.$name|trim}
                            <div id="options_{$key}" class="ty-product-options ty-group-block">
                                <div class="ty-group-block__arrow">
                                    <span class="ty-caret-info"><span class="ty-caret-outer"></span><span class="ty-caret-inner"></span></span>
                                </div>
                                <bdi>{$smarty.capture.$name nofilter}</bdi>
                            </div>
                            {/if}
                        </td>

                        <td class="ty-cart-content__product-elem ty-cart-content__price cm-reload-{$obj_id}" id="price_display_update_{$obj_id}">
                        {include file="common/price.tpl" value=$product.display_price span_id="product_price_`$key`" class="ty-sub-price"}
                        <!--price_display_update_{$obj_id}--></td>

                        <td class="ty-cart-content__product-elem ty-cart-content__qty {if $product.is_edp == "Y" || $product.exclude_from_calculate} quantity-disabled{/if}">
                            {if $use_ajax == true && $cart.amount != 1}
                                {assign var="ajax_class" value="cm-ajax"}
                            {/if}

                            <div class="quantity cm-reload-{$obj_id}{if $settings.Appearance.quantity_changer == "Y"} changer{/if}" id="quantity_update_{$obj_id}">
                                <input type="hidden" name="cart_products[{$key}][product_id]" value="{$product.product_id}" />
                                {if $product.exclude_from_calculate}<input type="hidden" name="cart_products[{$key}][extra][exclude_from_calculate]" value="{$product.exclude_from_calculate}" />{/if}

                                <label for="amount_{$key}"></label>
                                {if $product.is_edp == "Y" || $product.exclude_from_calculate}
                                    {$product.amount}
                                {else}
                                    {if $settings.Appearance.quantity_changer == "Y"}
                                        <div class="ty-center ty-value-changer cm-value-changer">
                                        <a class="cm-increase ty-value-changer__increase">&#43;</a>
                                    {/if}
                                    <input type="text" size="3" id="amount_{$key}" name="cart_products[{$key}][amount]" value="{$product.amount}" class="ty-value-changer__input cm-amount"{if $product.qty_step > 1} data-ca-step="{$product.qty_step}"{/if} data-ca-min-qty="{if !$product.min_qty}{$default_minimal_qty}{else}{$product.min_qty}{/if}" />
                                    {if $settings.Appearance.quantity_changer == "Y"}
                                        <a class="cm-decrease ty-value-changer__decrease">&minus;</a>
                                        </div>
                                    {/if}
                                {/if}
                                {if $product.is_edp == "Y" || $product.exclude_from_calculate}
                                    <input type="hidden" name="cart_products[{$key}][amount]" value="{$product.amount}" />
                                {/if}
                                {if $product.is_edp == "Y"}
                                    <input type="hidden" name="cart_products[{$key}][is_edp]" value="Y" />
                                {/if}
                            <!--quantity_update_{$obj_id}--></div>
                        </td>

                        <td class="ty-cart-content__product-elem ty-cart-content__price cm-reload-{$obj_id}" id="price_subtotal_update_{$obj_id}">
                            {include file="common/price.tpl" value=$product.display_subtotal span_id="product_subtotal_`$key`" class="price"}
                            {if $product.zero_price_action == "A"}
                                <input type="hidden" name="cart_products[{$key}][price]" value="{$product.base_price}" />
                            {/if}
                        <!--price_subtotal_update_{$obj_id}--></td>
                    </tr>
                {/if}
            {/hook}
			{$taxes = $taxes + $product.clean_price}
        {/foreach}
        {/if}


    </tbody>
    </table>
	<div class="ty-cart-total" style="background: white">
		<div class="ty-cart-total__wrapper clearfix" id="checkout_totals">
			{if $cart_products && $show_coupon}
				<div class="ty-coupons__container">
					{include file="views/checkout/components/promotion_coupon.tpl"}
					{hook name="checkout:payment_extra"}
					{/hook}
					</div>
			{/if}


			<ul class="ty-cart-statistic ty-statistic-list">
				<li class="ty-cart-statistic__item ty-statistic-list-subtotal">
					<span class="ty-cart-statistic__title">{__("subtotal")}</span>
					<span class="ty-cart-statistic__value">{include file="common/price.tpl" value=$cart.display_subtotal}</span>
				</li>
				{if ($cart.discount|floatval)}
				<li class="ty-cart-statistic__item ty-statistic-list-discount">
					<span class="ty-cart-statistic__title">{__("including_discount")}</span>
					<span class="ty-cart-statistic__value discount-price">-{include file="common/price.tpl" value=$cart.discount}</span>
				</li>

				{/if}

				{if ($cart.subtotal_discount|floatval)}
				<li class="ty-cart-statistic__item ty-statistic-list-subtotal-discount">
					<span class="ty-cart-statistic__title">{__("order_discount")}</span>
					<span class="ty-cart-statistic__value discount-price">-{include file="common/price.tpl" value=$cart.subtotal_discount}</span>
				</li>
				{/if}
{*				<li class="ty-cart-statistic__item ty-statistic-list-taxes ty-cart-statistic__group">*}
{*					<span class="ty-cart-statistic__title ty-cart-statistic_title_main">{__("taxes")}</span>*}
{*				</li>*}

{*				<li class="ty-cart-statistic__item ty-statistic-list-tax">*}
{*					<span class="ty-cart-statistic__title">VAT (18% included)</span>*}
{*					<span class="ty-cart-statistic__value">{include file="common/price.tpl" value=($taxes*18/100)}</span>*}
{*				</li>*}
					{$show_shipping_estimation = true}
					{if $cart.shipping_required == true}
						<li class="ty-cart-statistic__item ty-statistic-list-shipping-method">
						{if $cart.shipping}
							<span class="ty-cart-statistic__title">
								{__("shipping_cost")}
							</span>
							<span class="ty-cart-statistic__value">{include file="common/price.tpl" value=$cart.display_shipping_cost}</span>
						{elseif $show_shipping_estimation}
							<span class="ty-cart-statistic__title">{__("shipping_cost")}</span>
							<span class="ty-cart-statistic__value">{$smarty.capture.shipping_estimation nofilter}</span>
						{/if}
						</li>
					{/if}



				<li class="ty-cart-statistic__item ty-statistic-list-subtotal">
					<span class="ty-cart-statistic__title">{__("total")}</span>
					<span class="ty-cart-statistic__value">{include file="common/price.tpl" value=$cart.display_subtotal+$cart.display_shipping_cost}</span>
				</li>
				{if $cart.payment_surcharge}
				<li class="ty-cart-statistic__item ty-statistic-list-payment-surcharge" id="payment_surcharge_line">
					{assign var="payment_data" value=$cart.payment_id|fn_get_payment_method_data}
					<span class="ty-cart-statistic__title">{$cart.payment_surcharge_title|default:__("payment_surcharge")}{if $payment_data.payment}&nbsp;({$payment_data.payment}){/if}:</span>
					<span class="ty-cart-statistic__value">{include file="common/price.tpl" value=$cart.payment_surcharge span_id="payment_surcharge_value"}</span>
				</li>
				{math equation="x+y" x=$cart.total y=$cart.payment_surcharge assign="_total"}
				{capture name="_total"}{$_total}{/capture}
				{/if}
			</ul>
			<div class="clearfix"></div>
			<ul class="ty-cart-statistic__total-list">
				<li class="ty-cart-statistic__item ty-cart-statistic__total">
					<span class="ty-cart-statistic__total-title">{__("total_cost")}</span>
					<span class="ty-cart-statistic__total-value">
						{include file="common/price.tpl" value=$_total|default:$smarty.capture._total|default:$cart.total span_id="cart_total" class="ty-price"}
					</span>
				</li>
			</ul>
		</div>
	</div>
	<!--checkout_form--></form>
<!--cart_items--></div>

{/capture}
{include file="buttons/update_cart.tpl"
		 but_id="button_cart"
		 but_meta="ty-btn--recalculate-cart hidden hidden-phone hidden-tablet"
		 but_name="dispatch[checkout.update]"
}
{include file="common/mainbox_cart.tpl" title=__("cart_items") content=$smarty.capture.cartbox}
{script src="js/tygh/cart_content.js"}

<!--checkout_info_products_{$block.snapping_id}--></div>
