{if $addons.cp_automaticaly_create_account.work_mode == "ask_to_create" && !$auth.user_id}
    <div class="ty-control-group ty-checkout__terms">
        <div class="cm-field-container">
            <label for="cp_acc_ask_about_account" class="checkbox">
            <input type="hidden" name="cp_acc_create_account" value="N" />
            <input type="checkbox" id="cp_acc_ask_about_account" name="cp_acc_create_account" value="Y" class="cm-agreement checkbox" checked="checked"><span>{__("cp_ac_create_me_acc_txt")}</span></label>
        </div>
    </div>
{/if}