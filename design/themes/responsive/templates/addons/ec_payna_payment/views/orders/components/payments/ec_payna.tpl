<div class="ty-control-group" >
    <label class="cm-required" for="payna_channel_div_{$payment.payment_id}">{__("ec_payna_payment.channel")}:</label>
    <select name="payment_info[channel]" id="payna_channel_div_{$payment.payment_id}">
        <option value="TIGO">TIGO</option>
        <option value="VODACOM">VODACOM</option>
        <option value="AIRTEL">AIRTEL</option>
    </select>
</div>
<div class="ty-control-group" >
    <label class="cm-required" for="payna_phone_{$payment.payment_id}">{__("ec_payna_payment.phone")}:</label>
    <input type="text" name="payment_info[msisdn]" id="payna_phone_{$payment.payment_id}" value="{$cart.payment_info.msisdn|default:$cart.user_data.phone}"/>
</div>

<script>
(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function(context) {
        var mask_elements = context.find('#payna_phone_{$payment.payment_id}'); //.s_phone, .b_phone, .phone, input[name="company_data[phone]"],
        _.otp_no_phone_mask = "+255999999999";
        if (mask_elements.length) {
            {literal}
            mask_elements.each(function() {
                $(this).inputmask({
                    mask: _.otp_no_phone_mask,
                    showMaskOnHover: true,
                    showMaskOnFocus: true,
                    autoUnmask: true,
                });
            });  
            {/literal}  
        }
    });
})(Tygh, Tygh.$);
</script>    