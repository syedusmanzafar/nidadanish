{script src="js/addons/sd_pusher/pusher.min.js"}

{if $product_presence_event}
    {assign var="presence_channel" value="`$smarty.const.SD_PUSHER_CHANNELS_PRODUCT_PRESENCE`_`$smarty.get.product_id`"}
{elseif $order_presence_event}
    {assign var="presence_channel" value="`$smarty.const.SD_PUSHER_CHANNELS_ORDER_PRESENCE`_`$smarty.get.order_id`"}
{elseif $order_edit_presence_event}
    {assign var="presence_channel" value="`$smarty.const.SD_PUSHER_CHANNELS_ORDER_EDIT_PRESENCE`_`$cart.order_id`"}
{elseif $customer_presence_event}
    {assign var="presence_channel" value="`$smarty.const.SD_PUSHER_CHANNELS_CUSTOMER_PRESENCE`_`$smarty.get.user_id`"}
{elseif $cetegory_presence_event}
    {assign var="presence_channel" value="`$smarty.const.SD_PUSHER_CHANNELS_CATEGORY_PRESENCE`_`$smarty.get.category_id`"}
{/if}

<script type="text/javascript">
    (function(_, $) {
        $.extend(_, {
            sd_pusher_channel: "{$smarty.const.SD_PUSHER_CHANNELS_PRIVATE}_{$auth.user_id|escape:'javascript'}",
            sd_pusher_presence_channel: "{$presence_channel|escape:'javascript'}",
            sd_pusher_auth_token: "{$auth.user_id|fn_sd_pusher_encrypt|escape:'javascript'}",

            sd_pusher_key: "{$addons.sd_pusher.key|escape:'javascript'}",
            sd_pusher_endpoint: "{fn_sd_pusher_url('pusher.auth', 'C')|escape:'javascript'}",
            sd_pusher_notify_url: "{fn_sd_pusher_url('pusher.notify')|escape:'javascript'}",
            sd_pusher_user_id: "{$auth.user_id|escape:'javascript'}",
            sd_pusher_cluster: "{$addons.sd_pusher.cluster|escape:'javascript'}",
            sd_pusher_encrypted: "{$smarty.const.SD_PUSHER_ENCRYPTED|escape:'javascript'}",
            sd_pusher_event_notification: "{$smarty.const.SD_PUSHER_EVENT_NOTIFICATION|escape:'javascript'}",
            sd_pusher_administrator: "{__('administrator')|escape:'javascript'}",
        });
    }(Tygh, Tygh.$));
</script>

{script src="js/addons/sd_pusher/pusher.js"}
