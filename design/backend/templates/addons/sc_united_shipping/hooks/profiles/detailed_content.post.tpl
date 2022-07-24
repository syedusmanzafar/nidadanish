{if !$runtime.company_id}
    {include file="common/subheader.tpl" title=__("sc_united_shipping_title_vendor")}

    <div id="sc_united_shipping">
        <div class="control-group">
            <label class="control-label" for="elm_sc_united_use_vendor">{__("sc_united_shipping_special_customer")}:</label>
            <div class="controls">
                <label class="checkbox">
                    <input type="hidden" name="user_data[sc_united_use_vendor]" value="N" />
                    <input type="checkbox" name="user_data[sc_united_use_vendor]" id="elm_sc_united_use_vendor" value="Y" {if $user_data.sc_united_use_vendor == "Y"}checked="checked"{/if}/>
                </label>
            </div>
        </div>
        <!--sc_united_shipping--></div>
{/if}