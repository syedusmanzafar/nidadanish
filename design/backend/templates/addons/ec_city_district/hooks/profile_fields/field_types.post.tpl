{if $smarty.request.field_id}
    <option value="{$smarty.const.EC_DISTRICT}" {if $field.field_type == $smarty.const.EC_DISTRICT}selected="selected"{/if}>{__("district")}</option>
{/if}
{if $smarty.request.field_id}
    <option value="{$smarty.const.EC_CITY}" {if $field.field_type == $smarty.const.EC_CITY}selected="selected"{/if}>{__("ec_city")}</option>
{/if}