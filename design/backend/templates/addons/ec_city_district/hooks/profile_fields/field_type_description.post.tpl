{if $field.field_type == $smarty.const.EC_DISTRICT}
    <a href="{"ec_district.manage"|fn_url}" class="underlined">{__("district")}&nbsp;&rsaquo;&rsaquo;</a>
{/if}
{if $field.field_type == $smarty.const.EC_CITY}
    <a href="{"ec_cities.manage"|fn_url}" class="underlined">{__("ec_city")}&nbsp;&rsaquo;&rsaquo;</a>
{/if}
