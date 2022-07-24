{if $field == "qty_discounts"}
<script type="text/javascript">
    (function($) {
        function fn_disable_multiple_buttons()
        {
            var checked = $('[id*=elements-switcher-qty_discounts]').prop('checked');
            $('.bulk_qty_multiple_buttons [id*=add_bulk_qty_discount_apply_values]').each(function(index, element){
                if (checked) {
                    $(element).show();
                } else {
                    $(element).hide();
                }
            });
        }

        $(document).ready(function(){
            var checked = $('#elements-switcher-qty_discounts__').prop('checked');
            $('[id*=field_qty_discounts__]').each(function(index, element){
                $(element).prop('disabled', !checked);
                if (!checked) {
                    $(element).prop('checked', false);
                }
            });
            fn_disable_multiple_buttons();
            $('[id*=elements-switcher-qty_discounts]').click(function(){
                fn_disable_multiple_buttons();
            });
        });
    }(Tygh.$));
</script>
{if !"ULTIMATE:FREE"|fn_allowed_for}
    {assign var="usergroups" value="C"|fn_get_usergroups}
{/if}
{assign var="product_price" value="`$product.product_id`_price"}

<div id="content_bulk_qty_discounts_apply_values">
    <table class="table table-middle">
    <thead class="cm-first-sibling">
    <tr>
        <th>{__("quantity")}</th>
        <th>{__("value")}</th>
        <th>{__("type")}</th>
        {if !"ULTIMATE:FREE"|fn_allowed_for}
            <th>{__("usergroup")}</th>
        {/if}
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    {math equation="x+1" x=$_key|default:0 assign="new_key"}
    <tr class="{cycle values="table-row , " reset=1}{$no_hide_input_if_shared_product}" id="box_add_bulk_qty_discount_apply_values">
        <td>
            <input type="text" id="field_{$field}__" name="override_products_data[prices][{$new_key}][lower_limit]" value="" class="input-micro" /></td>
        <td>
            <input type="text" id="field_{$field}__" name="override_products_data[prices][{$new_key}][price]" value="0.00" size="10" class="input-small" /></td>
        <td>
        <select class="span2" id="field_{$field}__" name="override_products_data[prices][{$new_key}][type]">
            <option value="A" selected="selected">{__("absolute")} ({$currencies.$primary_currency.symbol nofilter})</option>
            <option value="P">{__("percent")} (%)</option>
        </select></td>
        {if !"ULTIMATE:FREE"|fn_allowed_for}
        <td>
            <select id="field_{$field}__" name="override_products_data[prices][{$new_key}][usergroup_id]" class="span2">
                {foreach from=fn_get_default_usergroups() item="usergroup"}
                    <option value="{$usergroup.usergroup_id}">{$usergroup.usergroup}</option>
                {/foreach}
                {foreach from=$usergroups item="usergroup"}
                    <option value="{$usergroup.usergroup_id}">{$usergroup.usergroup}</option>
                {/foreach}
            </select>
        </td>
        {/if}
        <td class="right bulk_qty_multiple_buttons">
            {include file="buttons/multiple_buttons.tpl" item_id="add_bulk_qty_discount_apply_values" hide_clone=true}
        </td>
    </tr>
    </tbody>
    </table>

</div>
{/if}