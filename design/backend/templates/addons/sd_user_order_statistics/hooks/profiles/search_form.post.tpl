<div class="control-group">
    <label class="control-label" for="paid_orders_from">{__("addons.sd_user_order_statistics.paid_orders")} {include file="common/tooltip.tpl" tooltip=__("addons.sd_user_order_statistics.paid_orders_tooltip")}</label>
    <div class="controls">
        <input type="text" name="paid_orders_from" id="paid_orders_from" value="{$search.paid_orders_from}" onfocus="this.select();" class="input-mini" /> - <input type="text" name="paid_orders_to" value="{$search.paid_orders_to}" onfocus="this.select();" class="input-mini" />
    </div>
</div>