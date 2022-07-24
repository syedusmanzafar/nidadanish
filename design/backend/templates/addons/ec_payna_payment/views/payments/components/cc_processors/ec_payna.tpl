
{assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}

<div class="control-group">
    <label class="control-label cm-required" for="api_key">{__("ec_payna_payment.api_key")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][api_key]" id="api_key" value="{$processor_params.api_key}" >
    </div>
</div>

<div class="control-group">
    <label class="control-label cm-required" for="api_username">{__("ec_payna_payment.api_username")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][api_username]" id="api_username" value="{$processor_params.api_username}" >
    </div>
</div>
<div class="control-group">
    <label class="control-label cm-required" for="api_password">{__("ec_payna_payment.api_password")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][api_password]" id="api_password" value="{$processor_params.api_password}" >
    </div>
</div>
<div class="control-group">
    <label class="control-label cm-required" for="payment_timeout">{__("ec_payna_payment.payment_timeout")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][payment_timeout]" id="payment_timeout" value="{$processor_params.payment_timeout|default:30}" class="cm-value-interger">
    </div>
</div>
<div class="control-group">
    <label class="control-label cm-required" for="channel">{__("ec_payna_payment.channel")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][channel]" id="channel" >
            <option value="AIRTEL" {if $processor_params.channel == 'AIRTEL'}selected{/if}>AIRTEL</option>
            <option value="TIGO" {if $processor_params.channel == 'TIGO'}selected{/if}>TIGO</option>
            <option value="VODACOM" {if $processor_params.channel == 'VODACOM'}selected{/if}>VODACOM</option>
        </select>
    </div>
</div>
    
<div class="control-group">
    <label class="control-label" for="s_order_status">{__("ec_payna_payment.success_order_status")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][s_order_status]" id="s_order_status">
            {foreach from=$statuses item="s" key="k"}
            <option value="{$k}" {if $processor_params.s_order_status == $k || !$processor_params.s_order_status && $k == 'O'}selected="selected"{/if}>{$s}</option>
            {/foreach}
        </select>
    </div>
</div>


<div class="control-group">
    <label class="control-label" for="f_order_status">{__("ec_payna_payment.failure_order_status")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][f_order_status]" id="f_order_status">
            {foreach from=$statuses item="s" key="k"}
            <option value="{$k}" {if $processor_params.f_order_status == $k || !$processor_params.f_order_status && $k == 'O'}selected="selected"{/if}>{$s}</option>
            {/foreach}
        </select>
    </div>
</div>