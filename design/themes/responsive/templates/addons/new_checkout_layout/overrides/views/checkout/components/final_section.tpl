{$show_place_order = false}

{if $cart|fn_allow_place_order:$auth}
    {$show_place_order = true}
{/if}

{if $recalculate && !$cart.amount_failed}
    {$show_place_order = true}
{/if}

{if $show_place_order}

    <div class="clearfix {if !$is_payment_step} checkout__block ty-checkout-block-terms{/if}">
            {include file="views/checkout/components/customer_notes.tpl"}
			{capture name="mailing_lists"}
				{assign var="show_newsletters_content" value=false}

				{hook name="newsletters:checkout_email_subscription"}
				<div class="subscription-container" id="subsciption_{$tab_id}">
					{foreach from=$page_mailing_lists item=list}
						{if $list.show_on_checkout}
							{assign var="show_newsletters_content" value=true}
						{/if}
						<input type="hidden" name="all_mailing_lists[]" value="{$list.list_id}" />

						<div class="ty-newsletters__item{if !$list.show_on_checkout} hidden{/if}">
							<label><input type="checkbox" checked name="mailing_lists[]" value="{$list.list_id}" {if $user_mailing_lists[$list.list_id]}checked="checked"{/if} class="checkbox cm-news-subscribe" />{$list.object}</label>
						</div>
					{/foreach}
				<!--subsciption_{$tab_id}--></div>
				{/hook}
			{/capture}

			{if $show_newsletters_content}
			<div class="ty-newsletters">
				{include file="common/subheader.tpl" title=__("text_signup_for_subscriptions")}

				{$smarty.capture.mailing_lists nofilter}
			</div>
			{/if}

        
        {if !$suffix}
            {assign var="suffix" value=""|uniqid}
        {/if}
        {include file="addons/new_checkout_layout/overrides/views/checkout/components/terms_and_conditions.tpl" suffix=$suffix}
    </div>

    <input type="hidden" name="update_steps" value="1" />
    
    {if !$is_payment_step}
        <div class="clearfix">
            <div class="ty-checkout-buttons cm-checkout-place-order-buttons">
                {include file="buttons/place_order.tpl" but_text=__("submit_my_order") but_name="dispatch[checkout.place_order]" but_id="place_order"}
            </div>

            {if $recalculate && $cart.shipping_required}
                <input type="hidden" name="next_step" value="step_two" />
                <div class="ty-checkout-buttons cm-checkout-recalculate-buttons hidden">
                    {include file="buttons/button.tpl" but_meta="ty-btn__secondary cm-checkout-recalculate" but_name="dispatch[checkout.update_steps]" but_text=__("recalculate_shipping_cost")}
                </div>
            {/if}
        </div>
    {/if}

{else}

    {if $cart.shipping_failed}
        <p class="ty-error-text ty-center">{__("text_no_shipping_methods")}</p>
    {/if}

    {if $cart.amount_failed}
        <div class="checkout__block">
            <p class="ty-error-text">{__("text_min_order_amount_required")}&nbsp;<strong>{include file="common/price.tpl" value=$settings.Checkout.min_order_amount}</strong></p>
        </div>
    {/if}

    <div class="ty-checkout-buttons">
        {include file="buttons/continue_shopping.tpl" but_href=$continue_url|fn_url but_role="action"}
    </div>
    
{/if}