{if $field == "qty_discounts"}

{if !"ULTIMATE:FREE"|fn_allowed_for}
    {assign var="usergroups" value="C"|fn_get_usergroups}
{/if}
{assign var="product_price" value="`$product.product_id`_price"}

<div id="content_bulk_qty_discounts_{$product.product_id}">
    {if empty($filled_groups.B.price)}
        <input type="hidden" name="additional_products_data[{$product.product_id}][price]" value="{$product.price}" />
    {/if}
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
    {foreach from=$product.prices item="price" key="_key" name="prod_prices"}
    <tr class="cm-row-item" >
        {if $price.lower_limit == "1" && $price.usergroup_id == "0"}
            <td class="{$no_hide_input_if_shared_product}">&nbsp;{$price.lower_limit}</td>
            <td class="{$no_hide_input_if_shared_product}">
                &nbsp;{if $price.percentage_discount == 0}{$price.price|default:"0.00"}{else}{$price.percentage_discount}{/if}
            </td>
            <td class="{$no_hide_input_if_shared_product}">
                &nbsp;{if $price.percentage_discount == 0}{__("absolute")}{else}{__("percent")}{/if}
            </td>
            {if !"ULTIMATE:FREE"|fn_allowed_for}
            <td class="{$no_hide_input_if_shared_product}">&nbsp;{__("all")}</td>
            {/if}
            <td class="nowrap {$no_hide_input_if_shared_product} right">&nbsp;</td>
        {else}
            <td class="{$no_hide_input_if_shared_product}">
                <input type="text" name="additional_products_data[{$product_price}][prices][{$_key}][lower_limit]" value="{$price.lower_limit}" class="input-micro" />
            </td>
            <td class="{$no_hide_input_if_shared_product}">
                <input type="text" name="additional_products_data[{$product_price}][prices][{$_key}][price]" value="{if $price.percentage_discount == 0}{$price.price|default:"0.00"}{else}{$price.percentage_discount}{/if}" size="10" class="input-small" />
            </td>
            <td class="{$no_hide_input_if_shared_product}">
                <select class="span2" name="additional_products_data[{$product_price}][prices][{$_key}][type]">
                    <option value="A" {if $price.percentage_discount == 0}selected="selected"{/if}>{__("absolute")} ({$currencies.$primary_currency.symbol nofilter})</option>
                    <option value="P" {if $price.percentage_discount != 0}selected="selected"{/if}>{__("percent")} (%)</option>
                </select>
            </td>
            {if !"ULTIMATE:FREE"|fn_allowed_for}
            <td class="{$no_hide_input_if_shared_product}">
                <select id="usergroup_id" name="additional_products_data[{$product_price}][prices][{$_key}][usergroup_id]" class="span2">
                    {foreach from=fn_get_default_usergroups() item="usergroup"}
                        {if $price.usergroup_id != $usergroup.usergroup_id}
                            <option value="{$usergroup.usergroup_id}">{$usergroup.usergroup}</option>
                        {else}
                            {*we should do this because there are no descriptions for default usergroups in database*}
                            {assign var="default_usergroup_name" value=$usergroup.usergroup}
                        {/if}
                    {/foreach}
                    {foreach from=$usergroups item="usergroup"}
                        {if $price.usergroup_id != $usergroup.usergroup_id}
                            <option value="{$usergroup.usergroup_id}">{$usergroup.usergroup}</option>
                        {/if}
                    {/foreach}
                        <option value="{$price.usergroup_id}" selected="selected">{if $default_usergroup_name}{$default_usergroup_name}{else}{$price.usergroup_id|fn_get_usergroup_name}{/if}</option>
                </select>
                {assign var="default_usergroup_name" value=""}
            </td>
            {/if}
            <td class="nowrap {$no_hide_input_if_shared_product} right">
                {include file="buttons/clone_delete.tpl" microformats="cm-delete-row" no_confirm=true}
            </td>
        {/if}
    </tr>
    {/foreach}
    {math equation="x+1" x=$_key|default:0 assign="new_key"}
    <tr class="{cycle values="table-row , " reset=1}{$no_hide_input_if_shared_product}" id="box_add_bulk_qty_discount_{$product.product_id}">
        <td>
            <input type="text" name="additional_products_data[{$product_price}][prices][{$new_key}][lower_limit]" value="" class="input-micro" /></td>
        <td>
            <input type="text" name="additional_products_data[{$product_price}][prices][{$new_key}][price]" value="0.00" size="10" class="input-small" /></td>
        <td>
        <select class="span2" name="additional_products_data[{$product_price}][prices][{$new_key}][type]">
            <option value="A" selected="selected">{__("absolute")} ({$currencies.$primary_currency.symbol nofilter})</option>
            <option value="P">{__("percent")} (%)</option>
        </select></td>
        {if !"ULTIMATE:FREE"|fn_allowed_for}
        <td>
            <select id="usergroup_id" name="additional_products_data[{$product_price}][prices][{$new_key}][usergroup_id]" class="span2">
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
            {include file="buttons/multiple_buttons.tpl" item_id="add_bulk_qty_discount_`$product.product_id`" hide_clone=true}
        </td>
    </tr>
    </tbody>
    </table>

</div>
{/if}