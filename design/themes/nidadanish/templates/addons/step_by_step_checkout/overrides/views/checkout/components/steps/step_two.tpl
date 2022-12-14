<div class="ty-step__container{if $edit}-active{/if} ty-step-two" data-ct-checkout="billing_shipping_address" id="step_two">
    <h3 class="ty-step__title{if $edit}-active{/if}{if $complete && !$edit}-complete{/if} clearfix">
        {if !$complete || $edit}
            {$show_number_of_step = true}
        {/if}
        {if ($show_number_of_step && $number_of_step) || !$show_number_of_step}
            <span class="ty-step__title-left">{if $show_number_of_step}{$number_of_step}{else}<i class="ty-step__title-icon ty-icon-ok"></i>{/if}</span>
        {/if}
        <i class="ty-step__title-arrow ty-icon-down-micro"></i>


        {if $complete && !$edit}
            {hook name="checkout:step_two_edit_link"}
            <span class="ty-step__title-right">
                {include file="buttons/button.tpl" but_meta="ty-btn__secondary cm-ajax" but_href="checkout.checkout?edit_step=step_two&from_step={$cart.edit_step}" but_target_id="checkout_*" but_text=__("change") but_role="tool"}
            </span>
            {/hook}
        {/if}

        {hook name="checkout:step_two_edit_link_title"}
        {if $complete && !$edit}
            <a class="ty-step__title-txt cm-ajax" href="{"checkout.checkout?edit_step=step_two&from_step={$cart.edit_step}"|fn_url}" data-ca-target-id="checkout_*">{__("billing_shipping_address")}</a>
        {else}
            <span class="ty-step__title-txt">{__("billing_shipping_address")}</span>
        {/if}
        {/hook}
    </h3>

    <div id="step_two_body" class="ty-step__body{if $edit}-active{/if}{if !$edit} hidden{/if} cm-skip-save-fields">
        <form name="step_two_billing_address" class="{$ajax_form} cm-ajax-full-render {if $final_step == "step_two"}cm-checkout-recalculate-form{/if}" action="{""|fn_url}" method="{if !$edit}get{else}post{/if}">
            <input type="hidden" name="update_step" value="step_two" />
            <input type="hidden" name="next_step" value="{$next_step}" />
            <input type="hidden" name="result_ids" value="checkout*,account*" />
            <input type="hidden" name="dispatch" value="checkout.checkout" />

            {if $smarty.request.profile == "new"}
                {assign var="hide_profile_name" value=false}
            {else}
                {assign var="hide_profile_name" value=true}
            {/if}

            {if $edit}
                <div class="clearfix">
                    <div class="checkout__block">
                        {include file="views/profiles/components/multiple_profiles.tpl" show_text=true hide_profile_name=$hide_profile_name hide_profile_delete=true profile_id=$cart.profile_id create_href="checkout.checkout?edit_step=step_two&from_step={$cart.edit_step}&profile=new"}
                    </div>
                </div>
            {/if}

            {if $settings.Checkout.address_position == "billing_first"}
                {assign var="first_section" value="B"}
                {assign var="first_section_text" value=__("billing_address")}
                {assign var="sec_section" value="S"}
                {assign var="sec_section_text" value=__("shipping_address")}
                {assign var="ship_to_another_text" value=__("text_ship_to_billing")}
                {assign var="body_id" value="sa"}
            {else}
                {assign var="first_section" value="S"}
                {assign var="first_section_text" value=__("shipping_address")}
                {assign var="sec_section" value="B"}
                {assign var="sec_section_text" value=__("billing_address")}
                {assign var="ship_to_another_text" value=__("text_billing_same_with_shipping")}
                {assign var="body_id" value="ba"}
            {/if}

            {if $edit}
                {hook name="checkout:edit_profile_fields"}
                {if $profile_fields[$first_section]}
                    <div class="clearfix" data-ct-address="billing-address">
                        <div class="checkout__block">
                            {include file="views/profiles/components/profile_fields.tpl" section=$first_section body_id="" ship_to_another=true title=$first_section_text}
                        </div>
                    </div>
                {/if}

                {if $profile_fields[$sec_section]}
                    <div class="clearfix shipping-address__switch" data-ct-address="shipping-address">
                        {include file="views/profiles/components/profile_fields.tpl" section=$sec_section body_id=$body_id address_flag=$profile_fields|fn_compare_shipping_billing ship_to_another=$cart.ship_to_another title=$sec_section_text grid_wrap="checkout__block"}
                    </div>
                {/if}
                {/hook}

                {if $final_step == "step_two"}
                    {include file="views/checkout/components/final_section.tpl" recalculate=true}
                {else}
                    <div class="ty-checkout-buttons">
                        {if !$complete}
                            {hook name="checkout:step_two_button_components"}{/hook}
                        {/if}

                        {include file="buttons/button.tpl" but_meta="ty-btn__secondary" but_name="dispatch[checkout.update_steps]" but_text=__("continue")}
                    </div>
                {/if}
            {/if}

        </form>

    </div>

<!--step_two--></div>