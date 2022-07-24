{capture name="mainbox"}

    <form action="{""|fn_url}" method="post" name="cities_form" class="{if $runtime.company_id} cm-hide-inputs{/if}">
        <input type="hidden" name="country_code" value="{$search.country_code}" />
        <input type="hidden" name="state_code" value="{$search.state_code}" />

        {include file="common/pagination.tpl" save_current_page=true save_current_url=true}

        {if $cities}
            <div class="table-responsive-wrapper">
                <table width="100%" class="table table-middle table-responsive">
                    <thead>
                        <tr>
                            <th width="1%" class="mobile-hide">{include file="common/check_items.tpl"}</th>
                            <th width="60%">{__("city")}</th>
                            <th width="5%">&nbsp;</th>
                            <th class="right" width="10%">{__("ec_cities_status")}</th>
                        </tr>
                    </thead>
                    {foreach from=$cities item=city}
                        <tr class="cm-row-status-{$city.status|lower}">
                            <td class="mobile-hide">
                                <input type="checkbox" name="city_ids[]" value="{$city.city_id}" class="checkbox cm-item" /></td>
                            <td  data-th="{__("city")}">
                                <input type="text" name="states[{$city.city_id}][code]" size="55" value="{$city.code}" class="input-hidden span8"/></td>
                            <td class="nowrap"  data-th="{__("tools")}">
                                {capture name="tools_list"}
                                    <li>{btn type="list" class="cm-confirm" text=__("delete") href="ec_cities.delete?city_id=`$city.city_id`&state_code=`$search.state_code`&country_code=`$search.country_code`" method="POST"}</li>
                                {/capture}
                                <div class="hidden-tools">
                                    {dropdown content=$smarty.capture.tools_list}
                                </div>
                            </td>
                            <td class="right"  data-th="{__("status")}">
                                {$has_permission = fn_check_permissions("tools", "update_status", "admin", "GET", ["table" => "ec_cities"])}
                                {include file="common/select_popup.tpl" id=$city.city_id status=$city.status hidden="" object_id_name="city_id" table="ec_cities" non_editable=!$has_permission}
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}

        {include file="common/pagination.tpl"}

    </form>

    {capture name="tools"}
        {capture name="add_new_picker"}
            <form action="{""|fn_url}" method="post" name="add_states_form" class="form-horizontal form-edit">
                <input type="hidden" name="city_data[country_code]" value="{$search.country_code}" />
                <input type="hidden" name="city_data[state_code]" value="{$search.state_code}" />
                <input type="hidden" name="city_id" value="0" />

                {foreach from=$states[$search.country_code] item="state" key="code"}
                    {if $state.code == $search.state_code}
                        {assign var="title" value="{__('new_city')} (`$state.state`)"}
                    {/if}
                {/foreach}

                <div class="cm-j-tabs">
                    <ul class="nav nav-tabs">
                        <li id="tab_new_states" class="cm-js active"><a>{__("general")}</a></li>
                    </ul>
                </div>

                <div class="table-responsive-wrapper">
                    <table class="table table-middle table-responsive" width="100%">
                    <thead class="cm-first-sibling">
                        <tr>
                            <td>{__("city")}:</td>
                            <td>&nbsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="{cycle values="table-row , " reset=1}" id="box_add_qty_discount">
                            {math equation="x+1" x=$_key|default:0 assign="new_key"}
                            <td >
                                <input type="text" id="elm_city_code" name="city_data[code][{$new_key}]" size="8" value="" />
                            </td>
                            <td>
                                {include file="buttons/multiple_buttons.tpl" item_id="add_qty_discount"}
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                
                <div class="buttons-container">
                    {include file="buttons/save_cancel.tpl" create=true but_name="dispatch[ec_cities.update]" cancel_action="close"}
                </div>
            </form>
        {/capture}
    {/capture}

    {capture name="buttons"}
        {capture name="tools_list"}
            {if $cities}
                <li>{btn type="delete_selected" dispatch="dispatch[ec_cities.m_delete]" form="cities_form"}</li>
            {/if}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}

        {if $cities}
            {include file="buttons/save.tpl" but_name="dispatch[ec_cities.m_update]" but_role="submit-link" but_target_form="cities_form"}
        {/if}
    {/capture}

    {if $allow_add_city eq 'Y'}
        {capture name="adv_buttons"}
            {include file="common/popupbox.tpl" id="new_city" action="ec_cities.add" text=$title content=$smarty.capture.add_new_picker title=__("add_city") act="general" icon="icon-plus"}
        {/capture}
    {/if}    

    {capture name="sidebar"}
        <div class="sidebar-row">
            <h6>{__("search")}</h6>
            <form action="{""|fn_url}" name="states_filter_form" method="get">
                <div class="control-group">
                    <label for="elm_company_country" class="control-label">{__("country")}:</label>
                    <div class="controls" >
                    <select class="cm-country" id="elm_company_country" name="country_code" onchange=fn_wk_cities_state_change(this);>
                        {* <option value="">- {__("select_country")} -</option> *}
                        {foreach from=$countries item="country" key="code"}
                        <option {if $search.country_code == $code}selected="selected"{/if} value="{$code}">{$country}</option>
                        {/foreach}
                    </select>
                    </div>
                </div>

                <div class="control-group">
                    {$_country = $search.country_code}
                    {$_state = $search.state_code}

                    <label for="elm_company_state" class="control-label">{__("state")}:</label>
                    <div class="controls">
                    <select id="elm_company_state" name="state_code" class="cm-state">
                        <option value="">- {__("select_state")} -</option>
                    </select>
                    {* <p id="elm_company_state_d" class="alert alert-danger">{__("states_not_available_please_add_state_first")}</p> *}
                    <br>
                </div>
                {include file="buttons/search.tpl" but_name="dispatch[ec_cities.manage]"}
            </form>
        </div>
    {/capture}
{/capture}

{include file="common/mainbox.tpl" title=__("cities") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar select_languages=true}

<script>
    function fn_wk_cities_state_change(elm) {
        var states = {$states|json_encode nofilter};
        var default_country = elm.value;
        var select = document.getElementById("elm_company_state");
        var default_state = '{$search.state_code}';
        select.options.length = 0;

        if(default_country in states) {
            document.getElementById("elm_company_state").style.display = 'block';            
            //select.options[select.options.length] = new Option('{__("select_state")}',null);
            for (var i = 0; i < states[default_country].length; i++) {
                select.options[select.options.length] = new Option(states[default_country][i]['state'], states[default_country][i]['code']);
            }
            //document.getElementById("elm_company_state_d").style.display = 'none';
            document.getElementById("elm_company_state").removeAttribute("disabled");
        } else {
           // document.getElementById("elm_company_state_d").style.display = 'block';
            document.getElementById("elm_company_state").style.display = 'none';
            document.getElementById("elm_company_state").setAttribute("disabled",'Y');
        }

        for (var i = 0; i < states[default_country].length; i++) {
            if(states[default_country][i]['code'] == default_state) {
                select.selectedIndex = i;
            }
        }
    }
</script>