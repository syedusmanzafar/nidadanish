<div class="litecheckout__field litecheckout__field--fill cm-field-container"
    data-ca-error-message-target-method="append">
    {$required = false}
    {if $profile_fields['S']}
        {foreach from=$profile_fields['S'] item=item}
            {if $item.field_name == 's_city' && $item.profile_required == 'Y'}
                {$required = true}
            {/if}
        {/foreach}
    {/if}

    {$cities = fn_get_cities_tree()}
    {$section = "S"}
    {$field.field_id = 'litecheckout_city'}
    {$_city = $city}
    {$_country = $user_data.s_country|default:$settings.Checkout.default_country}
    {$_state = $user_data.s_state|default:$settings.Checkout.default_state}
    <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_country_litecheckout_city" value="{$_country}"/>
    <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_state_litecheckout_city" value="{$_state}"/>
    <select class="cm-city cm-location-{if $section == "S"}shipping{else}billing{/if} litecheckout__input litecheckout__input--selectable litecheckout__input--selectable--select" 
        data-ca-field-id="litecheckout_city" 
        name="user_data[{if $section == "S"}s_city{else}b_city{/if}]" 
        data-ca-city="{$_city}" 
        data-ca-lite-checkout-field="user_data.{if $section == "S"}s_city{else}b_city{/if}"
        id="litecheckout_city"
        data-ca-lite-checkout-element="city"
        data-ca-lite-checkout-last-value="{$_city}">
        <option value="">- {__("select_city")} -</option>
        {if $cities && $cities.$_country.$_state}
            {foreach from=$cities.$_country.$_state item=city}
                <option {if $_city == $city.city_id}selected="selected"{/if} value="{$city.city_id}">{$city.code}</option>
            {/foreach}
        {/if}
    </select>      
    {* <label class="litecheckout__label {if $required}cm-required {/if}" for="litecheckout_city">{__("city")} </label> *}
</div>