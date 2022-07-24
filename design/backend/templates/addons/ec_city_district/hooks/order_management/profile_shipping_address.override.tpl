{hook name="order_management:profile_shipping_address"}
{if !fn_is_empty($user_data)}
    {if $profile_fields.S}
        {if $user_data.s_firstname || $user_data.s_lastname}
            <p class="strong">{$user_data.s_firstname} {$user_data.s_lastname}</p>
        {/if}
        {if $user_data.s_address}
            <p>{$user_data.s_address}</p>
        {/if}
        {if $user_data.s_address_2}
            <p>{$user_data.s_address_2}</p>
        {/if}
        {if $user_data.s_city || $user_data.s_state_descr || $user_data.s_zipcode}
            <p>{$user_data.s_district_descr} {$user_data.s_city_descr}{if $user_data.s_city && ($user_data.s_state_descr || $user_data.s_zipcode)},{/if}  {$user_data.s_state_descr} {$user_data.s_zipcode}</p>
        {/if}
        {if $user_data.s_country_descr}<p>{$user_data.s_country_descr}</p>{/if}
        {include file="views/profiles/components/profile_fields_info.tpl" fields=$profile_fields.S}
        {if $user_data.s_phone}
            <a href="tel:{$user_data.s_phone}"><bdi>{$user_data.s_phone}</bdi></a>
        {/if}
        {if $user_data.s_address_type}
            <p>{__("address_type")}: {$user_data.s_address_type}</p>
        {/if}
    {else}
        <p class="muted">{__("no_data")}</p>
    {/if}
{else}
    <p class="muted">{__("section_is_not_completed")}</p>
    <div class="enter-data">
        {profile_enter_data_link scroll_to="profile_fields_s"}
    </div>
{/if}
{/hook}