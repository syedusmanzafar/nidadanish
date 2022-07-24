<div class="litecheckout__field cm-field-container {$wrapper_class} {$field_type_class_postfix}"
    data-ca-error-message-target-method="append">
    {if $field.field_type == "ProfileFieldTypes::STATE"|enum}
        {$_country = $settings.Checkout.default_country}
        {$_state = $field_value|default:$settings.Checkout.default_state}

        <select {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if}
            id="{$field_id}"
            class="cm-state litecheckout__input litecheckout__input--selectable litecheckout__input--selectable--select {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if}{if $field.class} {$field.class}{/if}{$input_meta}"
            data-ca-lite-checkout-field="{$field_name_helper}"
            data-ca-lite-checkout-auto-save-on-change="true"
            aria-label="{$field.description}"
            title="{$field.description}"
            {$field.attributes|render_tag_attrs nofilter}
            name="{$field_name}"
        >
            {if $field.required !== "Y"}
                <option value="">- {__("select_state")} -</option>
            {/if}
            {if $states && $states.$_country}
                {foreach $states.$_country as $state}
                    <option {if $_state == $state.code}selected="selected"{/if} value="{$state.code}">{$state.state}</option>
                {/foreach}
            {/if}
        </select>

        <input
            {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if}
            type="text"
            id="{$field_id}_d"
            name="{$field_name}"
            size="32"
            maxlength="64"
            value="{$_state}"
            disabled="disabled"
            class="cm-state {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if} ty-input-text litecheckout__input hidden{$input_meta}"
            data-ca-lite-checkout-field="{$field_name_helper}"
            data-ca-lite-checkout-auto-save-on-change="true"
        />

    {elseif $field.field_type == "ProfileFieldTypes::COUNTRY"|enum}
        {$_country = $field_value|default:$settings.Checkout.default_country}

        <select
            {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if}
            id="{$field_id}"
            class="ty-profile-field__select-country cm-country litecheckout__input litecheckout__input--selectable litecheckout__input--selectable--select {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if} {if $field.class} {$field.class}{/if}{$input_meta}"
            data-ca-lite-checkout-field="{$field_name_helper}"
            data-ca-lite-checkout-auto-save-on-change="true"
            aria-label="{$field.description}"
            title="{$field.description}"
            {$field.attributes|render_tag_attrs nofilter}
            name="{$field_name}"
        >
            {hook name="profiles:country_selectbox_items"}
            {if $field.required !== "Y"}
                <option value="">- {__("select_country")} -</option>
            {/if}
            {foreach $countries as $code => $country}
                <option {if $_country == $code}selected="selected"{/if} value="{$code}">{$country}</option>
            {/foreach}
            {/hook}
        </select>
    {elseif $field.field_type == "ProfileFieldTypes::TEXT_AREA"|enum}
        <textarea class="litecheckout__input{if $field.class} {$field.class}{/if}{$input_meta}"
              id="{$field_id}"
              autocomplete="{$field.autocomplete}"
              name="{$field_name}"
              placeholder=" "
              data-ca-lite-checkout-field="{$field_name_helper}"
              data-ca-lite-checkout-auto-save="true"
              aria-label="{$field.description}"
              title="{$field.description}"
              {$field.attributes|render_tag_attrs nofilter}
        >{$field_value}</textarea>
    {elseif $field.field_type == "ProfileFieldTypes::CHECKBOX"|enum}
        <input type="hidden" name="{$field_name}" value="{"YesNo::NO"|enum}" data-ca-lite-checkout-field="{$field_name_helper}" {if $field.is_disabled}disabled="disabled"{/if} />
        <input class="litecheckout__input{if $field.class} {$field.class}{/if}{$input_meta}"
               id="{$field_id}"
               type="checkbox"
               name="{$field_name}"
               value="{"YesNo::YES"|enum}"
               data-ca-lite-checkout-field="{$field_name_helper}"
               data-ca-lite-checkout-auto-save="true"
               autocomplete="{$field.autocomplete}"
               aria-label="{$field.description}"
               title="{$field.description}"
               {if $field_value == "YesNo::YES"|enum}checked{/if}
               {$field.attributes|render_tag_attrs nofilter}
        />
    {elseif $field.field_type == "ProfileFieldTypes::DATE"|enum}
        {$extra = "data-ca-lite-checkout-field=`$field_name_helper` data-ca-lite-checkout-auto-save=true data-ca-lite-checkout-auto-save-on-change=true {$field.attributes|render_tag_attrs nofilter}"}
        {$date_meta = "litecheckout__input{if $field.class} {$field.class}{/if}{$input_meta}"}
        {include
            file="common/calendar.tpl"
            date_id="`$field_id`"
            date_name=$field_name
            date_val=$field_value
            date_meta=$date_meta
            extra=$extra
        }
    {elseif $field.field_type == "ProfileFieldTypes::RADIO"|enum}
        {foreach $field.values as $value => $name}
            <label for="{$field_id}_{$value}">
                <input class="radio litecheckout__input{if $field.class} {$field.class}{/if}{$input_meta}"
                   type="radio"
                   id="{$field_id}_{$value}"
                   name="{$field_name}"
                   value="{$value}"
                   data-ca-lite-checkout-field="{$field_name_helper}"
                   data-ca-lite-checkout-auto-save-on-change="true"
                   {if (!$field_value && $name@first) || $field_value == $value}checked{/if}
                   {$field.attributes|render_tag_attrs nofilter}
                />
                {$name}
            </label>
        {/foreach}
    {elseif $field.field_type == "ProfileFieldTypes::SELECT_BOX"|enum}
        <select class="litecheckout__input{if $field.class} {$field.class}{/if}{$input_meta}"
                autocomplete="{$field.autocomplete}"
                id="{$field_id}"
                data-ca-lite-checkout-field="{$field_name_helper}"
                data-ca-lite-checkout-auto-save-on-change="true"
                aria-label="{$field.description}"
                title="{$field.description}"
                name="{$field_name}"
                {$field.attributes|render_tag_attrs nofilter}
        >
            {if $field.required == "YesNo::NO"|enum}
                <option value="">--</option>
            {/if}
            {foreach $field.values as $value => $name}
                <option {if $field_value == $value}selected{/if} value="{$value}">{$name}</option>
            {/foreach}
        </select>
    {elseif $field.field_type == "ProfileFieldTypes::ADDRESS_TYPE"|enum}
        <label for="{$field_id}_residential">
            <input class="radio litecheckout__input{if $field.class} {$field.class}{/if}{$input_meta}"
               type="radio"
               id="{$field_id}_residential"
               name="{$field_name}"
               value="residential"
               data-ca-lite-checkout-field="{$field_name_helper}"
               data-ca-lite-checkout-auto-save-on-change="true"
               {if !$field_value || $field_value == "residential"}checked{/if}
               {$field.attributes|render_tag_attrs nofilter}
            />
            {__("address_residential")}
        </label>
        <label for="{$field_id}_commercial">
            <input class="radio litecheckout__input{if $field.class} {$field.class}{/if}{$input_meta}"
                type="radio"
                id="{$field_id}_commercial"
                name="{$field_name}"
                value="commercial"
                data-ca-lite-checkout-field="{$field_name_helper}"
                data-ca-lite-checkout-auto-save-on-change="true"
                {if $field_value == "commercial"}checked{/if}
                {$field.attributes|render_tag_attrs nofilter}
            />
            {__("address_commercial")}
        </label>
    {elseif $field.field_type == $smarty.const.EC_DISTRICT}
        {$cities = fn_get_cities_tree()}
        {$districts = fn_get_districts_tree()}
        {if $section == "S"}
            {$_country = $user_data.s_country|default:$settings.Checkout.default_country}
            {$_state   = $user_data.s_state|default:$settings.Checkout.default_state}
            {$_city    = $user_data.s_city|default:$cities.$_country.$_state.0.city_id}
            {$_district = $user_data.s_district|default:$field_value}
        {else}
            {$_country = $user_data.b_country|default:$settings.Checkout.default_country}
            {$_state   = $user_data.b_state|default:$settings.Checkout.default_state}
            {$_city    = $user_data.b_city|default:$cities.$_country.$_state.0.city_id}   
            {$_district = $user_data.b_district|default:$field_value}     
        {/if}
        <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_country_{$field.field_name}" value="{$_country}">
        <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_state_{$field.field_name}" value="{$_state}">
        <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_city_{$field.field_name}" value="{$_city}">        
        <select class="cm-district {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if} litecheckout__input{if $field.class} {$field.class}{/if}{$input_meta}" data-ca-district="{$_district}"
            autocomplete="{$field.autocomplete}"
            id="{$field_id}"
            data-ca-lite-checkout-field="{$field_name_helper}"
            data-ca-lite-checkout-auto-save-on-change="true"
            aria-label="{$field.description}"
            title="{$field.description}"
            name="{$field_name}"
            {$field.attributes|render_tag_attrs nofilter}
        >
            <option value="">-{__("select_district")} -</option>
            {if $districts && $districts.$_country.$_state.$_city}
                {foreach from=$districts.$_country.$_state.$_city item=district}
                    <option {if $_district == $district.district_id}selected="selected"{/if} value="{$district.district_id}">{$district.code}</option>
                {/foreach}
            {/if}
        </select>
    {elseif $field.field_type == $smarty.const.EC_CITY}
        <div class="litecheckout__field litecheckout__field--fill cm-field-container"
            data-ca-error-message-target-method="append">
            {$cities = fn_get_cities_tree()}
            {$field.field_id = 'litecheckout_b_city'}
            {if $section == "S"}
                {$_country = $user_data.s_country|default:$settings.Checkout.default_country}
                {$_state   = $user_data.s_state|default:$settings.Checkout.default_state}
                {$_city    = $user_data.s_city|default:$cities.$_country.$_state.0.city_id}
            {else}
                {$_country = $user_data.b_country|default:$settings.Checkout.default_country}
                {$_state   = $user_data.b_state|default:$settings.Checkout.default_state}
                {$_city    = $user_data.b_city|default:$cities.$_country.$_state.0.city_id}   
            {/if}
            {if !$_city}
                {$_city = $field_value}
            {/if}
            <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_country_litecheckout_city" value="{$_country}"/>
            <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_state_litecheckout_city" value="{$_state}"/>
            <select class="cm-city cm-location-{if $section == "S"}shipping{else}billing{/if} litecheckout__input litecheckout__input--selectable litecheckout__input--selectable--select" 
                data-ca-field-id="litecheckout_b_city" 
                name="user_data[{if $section == "S"}s_city{else}b_city{/if}]" 
                data-ca-city="{$_city}" 
                data-ca-lite-checkout-field="user_data.{if $section == "S"}s_city{else}b_city{/if}"
                id="litecheckout_b_city"
                data-ca-lite-checkout-element="city"
                data-ca-lite-checkout-last-value="{$_city}">
                <option value="">- {__("select_city")} -</option>
                {if $cities && $cities.$_country.$_state}
                    {foreach from=$cities.$_country.$_state item=city}
                        <option {if $_city == $city.city_id}selected="selected"{/if} value="{$city.city_id}">{$city.code}</option>
                    {/foreach}
                {/if}
            </select>      
        </div>
    {else}
        <input class="litecheckout__input{if $field.class} {$field.class}{/if}{$input_meta}"
            placeholder=" "
            id="{$field_id}"
            type="{$type}"
            name="{$field_name}"
            value="{$field_value}"
            data-ca-lite-checkout-field="{$field_name_helper}"
            data-ca-lite-checkout-auto-save="true"
            autocomplete="{$field.autocomplete}"
            aria-label="{$field.description}"
            title="{$field.description}"
            {$field.attributes|render_tag_attrs nofilter}
        />
    {/if}
    <label class="litecheckout__label {$label_meta}" for="{$field_id}">{$field.description}</label>
</div>