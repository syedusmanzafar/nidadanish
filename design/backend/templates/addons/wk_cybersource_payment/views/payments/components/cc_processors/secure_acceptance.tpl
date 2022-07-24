
{assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}

<div class="control-group">
    <label class="control-label cm-required" for="wk_cybersource_profile_Id">{__("wk_cybersource_profileid")}{include file="common/tooltip.tpl" tooltip=__('help_wk_cybersource_profileid')}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][wk_cybersource_profileid]" id="wk_cybersource_profileid" value="{$processor_params.wk_cybersource_profileid}" >
    </div>
</div>


<div class="control-group">
    <label class="control-label cm-required" for="wk_cybersource_access_key">{__("wk_cybersource_access_key")}{include file="common/tooltip.tpl" tooltip=__('help_wk_cybersource_access_key')}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][wk_cybersource_access_key]" id="wk_cybersource_access_key" value="{$processor_params.wk_cybersource_access_key}" >
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-required" for="wk_cybersource_secret_key">{__("wk_cybersource_secret_key")}{include file="common/tooltip.tpl" tooltip=__('help_wk_cybersource_secret_key')}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][wk_cybersource_secret_key]" id="wk_cybersource_secret_key" value="{$processor_params.wk_cybersource_secret_key}" >
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-required" for="wk_cybersource_mode">{__("wk_cybersource_mode")}{include file="common/tooltip.tpl" tooltip=__('help_wk_cybersource_mode')}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][wk_cybersource_mode]" id="wk_cybersource_mode">
            <option value="test" {if $processor_params.wk_cybersource_mode == "test"}selected="selected"{/if}>Test</option>
            <option value="live" {if $processor_params.wk_cybersource_mode == "live"}selected="selected"{/if}>Live</option>
        </select>
    </div>
</div>



<div class="control-group">
    <label class="control-label" for="s_order_status">{__("success_order_status")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][s_order_status]" id="s_order_status">
            {foreach from=$statuses item="s" key="k"}
            <option value="{$k}" {if $processor_params.s_order_status == $k || !$processor_params.s_order_status && $k == 'O'}selected="selected"{/if}>{$s}</option>
            {/foreach}
        </select>
    </div>
</div>


<div class="control-group">
    <label class="control-label" for="f_order_status">{__("failure_order_status")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][f_order_status]" id="f_order_status">
            {foreach from=$statuses item="s" key="k"}
            <option value="{$k}" {if $processor_params.f_order_status == $k || !$processor_params.f_order_status && $k == 'O'}selected="selected"{/if}>{$s}</option>
            {/foreach}
        </select>
    </div>
</div>

