{hook name="order_management:profile_billing_address"}
    {if !fn_is_empty($user_data)}
        {if $profile_fields.B}
            {if $user_data.b_firstname || $user_data.b_lastname}
                <p class="strong">{$user_data.b_firstname} {$user_data.b_lastname}</p>
            {/if}
            {if $user_data.b_address}
                <p>{$user_data.b_address}</p>
            {/if}
            {if $user_data.b_address_2}
                <p>{$user_data.b_address_2}</p>
            {/if}
            {if $user_data.b_city || $user_data.b_state_descr || $user_data.b_zipcode}
                <p>{$user_data.b_district_descr} {$user_data.b_city_descr}{if $user_data.b_city && ($user_data.b_state_descr || $user_data.b_zipcode)},{/if} {$user_data.b_state_descr} {$user_data.b_zipcode}</p>
            {/if}
            {if $user_data.b_country_descr}<p>{$user_data.b_country_descr}</p>{/if}
            {include file="views/profiles/components/profile_fields_info.tpl" fields=$profile_fields.B}
            {if $user_data.b_phone}
                <a href="tel:{$user_data.b_phone}"><bdi>{$user_data.b_phone}</bdi></a>
            {/if}
        {else}
            <p class="muted">{__("no_data")}</p>
        {/if}
    {else}
        <p class="muted">{__("section_is_not_completed")}</p>
        <div class="enter-data">
            {profile_enter_data_link scroll_to="profile_fields_b"}
        </div>
    {/if}
    {/hook}