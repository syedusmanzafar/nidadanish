<div class="ty-step__container{if $edit}-active{/if} ty-step-one" data-ct-checkout="user_info" id="step_one">
    <h3 class="ty-step__title{if $edit}-active{/if}{if $complete && !$edit}-complete{/if} clearfix">
        <span class="ty-step__title-left">{if !$complete || $edit}{$number_of_step}{else}<i class="ty-step__title-icon ty-icon-ok"></i>{/if}</span>
        <i class="ty-step__title-arrow ty-icon-down-micro"></i>

        {if $complete && !$edit}
            {hook name="checkout:step_one_edit_link"}
            <span class="ty-step__title-right">
                {include file="buttons/button.tpl" but_meta="ty-btn__secondary cm-ajax" but_href="checkout.checkout?edit_step=step_one&from_step={$cart.edit_step}" but_target_id="checkout_*" but_text=__("change") but_role="tool"}
            </span>
            {/hook}
        {/if}

        {if ($settings.Checkout.disable_anonymous_checkout == "Y" && !$auth.user_id) || ($settings.Checkout.disable_anonymous_checkout != "Y" && !$auth.user_id && !$contact_info_population) || $smarty.session.failed_registration == true}
            {assign var="title" value=__("please_sign_in")}
        {else}
            {if $auth.user_id != 0}
                {if $user_data.firstname || $user_data.lastname}
                    {assign var="login_info" value="`$user_data.firstname`&nbsp;`$user_data.lastname`"}
                {else}
                    {assign var="login_info" value="`$user_data.email`"}
                {/if}
            {else}
                {assign var="login_info" value=__("guest")}
            {/if}

            {assign var="title" value="{__("signed_in_as")} `$login_info`"}
        {/if}

        {hook name="checkout:step_one_edit_link_title"}
        {if $contact_info_population && !$edit}
            <a class="ty-step__title-txt" href="{"checkout.checkout?edit_step=step_one&from_step={$cart.edit_step}"|fn_url}" data-ca-target-id="checkout_*">{$title|strip_tags nofilter}</a>
        {else}
            <span class="ty-step__title-txt">{$title|strip_tags nofilter}</span>
        {/if}
        {/hook}
    </h3>

    <div id="step_one_body" class="ty-step__body{if $edit}-active{/if}{if !$edit} hidden{/if}">
        {if ($settings.Checkout.disable_anonymous_checkout == "Y" && !$auth.user_id) || ($settings.Checkout.disable_anonymous_checkout != "Y" && !$auth.user_id && !$contact_info_population) || $smarty.session.failed_registration == true}
            <div id="step_one_login" {if $smarty.request.login_type == "register"}class="hidden"{/if}>
                <div class="clearfix">
                    {include file="views/checkout/components/checkout_login.tpl" checkout_type="one_page"}
                </div>
            </div>
            <div id="step_one_register" class="clearfix{if $smarty.request.login_type != "register"} hidden{/if}">
                <form name="step_one_register_form" class="" action="{""|fn_url}" method="post">
                    {hook name="checkout:user_register_form"}
                    <input type="hidden" name="result_ids" value="checkout*,account*" />
                    <input type="hidden" name="return_to" value="checkout" />
                    <input type="hidden" name="user_data[register_at_checkout]" value="Y" />
                    <div class="checkout__block">
                        {include file="common/subheader.tpl" title=__("register_new_account")}
                        {include file="views/profiles/components/profiles_account.tpl" nothing_extra="Y" location="checkout"}
                        {include file="views/profiles/components/profile_fields.tpl" section="C" nothing_extra="Y" exclude=["email"]}

                        {hook name="checkout:checkout_steps"}{/hook}

                        {include file="common/image_verification.tpl" option="register"}

                        <div class="clearfix"></div>
                    </div>
                    {/hook}
                    <div class="ty-checkout-buttons clearfix">
                        {include file="buttons/button.tpl" but_meta="ty-btn__secondary" but_name="dispatch[checkout.add_profile]" but_text=__("register")}
                        {include file="buttons/button.tpl" but_onclick="Tygh.$('#step_one_register').hide(); Tygh.$('#step_one_login').show();" but_text=__("cancel") but_role="text" but_meta="ty-checkout__register-cancel"}
                    </div>
                </form>
            </div>
        {else}
            <form name="step_one_contact_information_form" {if $ajaxcheckout} class="{$ajax_form}" {/if} action="{""|fn_url}" method="{if !$edit}get{else}post{/if}">
                <input type="hidden" name="update_step" value="step_one" />
                <input type="hidden" name="next_step" value="{$next_step}" />
                <input type="hidden" name="result_ids" value="checkout*" />
                {if $edit}
                    <div class="clearfix">
                        <div class="checkout__block">
                            {include file="views/profiles/components/profile_fields.tpl" req="Y" section="C" nothing_extra="Y" email_extra=$smarty.capture.email_extra}
                            <a href="{"auth.change_login"|fn_url}" class="ty-checkout__relogin">{__("sign_in_as_different")}</a>
                        </div>
                    </div>
                    {hook name="checkout:checkout_steps"}
                        <div class="ty-checkout-buttons">
                            {include file="buttons/button.tpl" but_meta="ty-btn__secondary" but_name="dispatch[checkout.update_steps]" but_text=$but_text}
                        </div>
                    {/hook}
                {/if}
            </form>
        {/if}

    </div>
<!--step_one--></div>