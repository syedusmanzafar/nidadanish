{include file="common/subheader.tpl" title=__("an_is_main_settings") target="#an_is_main_settings"}

<fieldset id="an_is_main_settings" class="collapse-visible collapse in">

<div class="control-group">
    <label class="control-label" for="free_shipping">{__("an_is_main")}:</label>
    <div class="controls">
        <input type="hidden" name="shipping_data[an_is_main]" value="N" />
        <input type="checkbox" name="shipping_data[an_is_main]" id="free_shipping" {if $shipping.an_is_main == 'Y'}checked="checked"{/if} value="Y" />
    </div>
</div>

</fieldset>