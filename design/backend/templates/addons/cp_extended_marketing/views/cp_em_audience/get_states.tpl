<div class="control-group" id="cp_get_states">
    {if !$_country}
        {$_country = $search.country|default:$settings.General.default_country}
    {/if}
    <label for="elm_company_state" class="control-label">{__("state")}:</label>
    <div class="controls">
        <select id="elm_company_state" name="state" class=" {if !$states.$_country}hidden{/if}">
            <option value="">- {__("select_state")} -</option>
            {if $states.$_country}
                {foreach from=$states.$_country item=state}
                    <option {if $search.state == $state.code}selected="selected"{/if} value="{$state.code}">{$state.state}</option>
                {/foreach}
            {/if}
        </select>
    <input type="text" id="elm_company_state_d" name="state" size="32" maxlength="64" {if $states.$_country}disabled="disabled"{/if}  value="{$search.state}" class="{if $states.$_country}hidden{/if} cm-skip-avail-switch" />
    </div>
<!--cp_get_states--></div>