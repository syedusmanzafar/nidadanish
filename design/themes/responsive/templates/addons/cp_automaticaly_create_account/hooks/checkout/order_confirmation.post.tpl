{$new_acc_email=""|fn_cp_get_new_account_email}
{if !empty($new_acc_email)}
    {$new_acc_pasword=""|fn_cp_get_new_account_password}
    {$reset_password_href=""|fn_cp_get_reset_password_href}
    
    <div class="cp_new_user_information">
        <h1 class="">{__("you_account_was_create")}</h1>
        {__("we_sent_all_information_on_your_email")}
            
        <div class="cp_account_create_new_password">
            <b>{__("your_current_email")} :</b> {$new_acc_email}</br>
            {if $addons.cp_automaticaly_create_account.show_pass_on_front == "Y"}
                <b>{__("your_current_password")} :</b> {$new_acc_pasword}
            {/if}
        </div>
        {if $settings.General.approve_user_profiles != "Y" && $addons.cp_automaticaly_create_account.email_activation != "Y"}
            {include file="buttons/button.tpl" but_meta="ty-btn__secondary" but_text=__("reset_password") but_href=$reset_password_href}
        {/if}
    </div>
{/if}