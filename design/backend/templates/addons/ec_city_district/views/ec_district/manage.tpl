{capture name="mainbox"}
    {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
    <form action="{""|fn_url}" method="post" name="districts_form" class="{if $runtime.company_id} cm-hide-inputs{/if}">
        <input type="hidden" name="country_code" value="{$search.country_code}" />
        <input type="hidden" name="state_code" value="{$search.state_code}" />
        <input type="hidden" name="city_id" value="{$search.city_id}" />
        
        {$ec_url = rawurlencode($c_url)}
        <input type="hidden" name="redirect_url" value="{$c_url}" />
        
        {include file="common/pagination.tpl" save_current_page=true save_current_url=true}

        {if $districts}
            <div class="table-responsive-wrapper">
                <table width="100%" class="table table-middle table-responsive">
                    <thead>
                        <tr>
                            <th width="1%" class="mobile-hide">{include file="common/check_items.tpl"}</th>
                            <th width="60%">{__("district")}</th>
                            <th width="5%">&nbsp;</th>
                            <th class="right" width="10%">{__("status")}</th>
                        </tr>
                    </thead>
                    {foreach from=$districts item=district}
                        <tr class="cm-row-status-{$district.status|lower}">
                            <td class="mobile-hide">
                                <input type="checkbox" name="district_ids[]" value="{$district.district_id}" class="checkbox cm-item" /></td>
                            <td  data-th="{__("district")}">
                                <input type="text" name="districts[{$district.district_id}][code]" size="55" value="{$district.code}" class="input-hidden span8"/></td>
                            <td class="nowrap"  data-th="{__("tools")}">
                                {capture name="tools_list"}
                                    <li>{btn type="list" class="cm-confirm" text=__("delete") href="ec_district.delete?district_id=`$district.district_id`&redirect_url=$ec_url" method="POST"}</li>
                                {/capture}
                                <div class="hidden-tools">
                                    {dropdown content=$smarty.capture.tools_list}
                                </div>
                            </td>
                            <td class="right"  data-th="{__("status")}">
                                {$has_permission = fn_check_permissions("tools", "update_status", "admin", "GET", ["table" => "ec_district"])}
                                {include file="common/select_popup.tpl" id=$district.district_id status=$district.status hidden="" object_id_name="district_id" table="ec_district" non_editable=!$has_permission}
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
            <form action="{""|fn_url}" method="post" name="add_district_form" class="form-horizontal form-edit">
                <input type="hidden" name="district_data[country_code]" value="{$search.country_code}" />
                <input type="hidden" name="district_data[state_code]" value="{$search.state_code}" />
                <input type="hidden" name="district_data[city_id]" value="{$search.city_id}" />
                <input type="hidden" name="district_id" value="0" />
                <input type="hidden" name="redirect_url" value="{$c_url}" />

                {assign var="title" value="{__('new_district')} ($city_name)"}

                <div class="cm-j-tabs">
                    <ul class="nav nav-tabs">
                        <li id="tab_new_states" class="cm-js active"><a>{__("general")}</a></li>
                    </ul>
                </div>

                <div class="table-responsive-wrapper">
                    <table class="table table-middle table-responsive" width="100%">
                    <thead class="cm-first-sibling">
                        <tr>
                            <td>{__("district")}:</td>
                            <td>&nbsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="{cycle values="table-row , " reset=1}" id="box_add_qty_discount">
                            {math equation="x+1" x=$_key|default:0 assign="new_key"}
                            <td >
                                <input type="text" id="elm_city_code" name="district_data[code][{$new_key}]" size="8" value="" />
                            </td>
                            <td>
                                {include file="buttons/multiple_buttons.tpl" item_id="add_qty_discount"}
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                {if $search.city_id}
                    <div class="buttons-container">
                        {include file="buttons/save_cancel.tpl" create=true but_name="dispatch[ec_district.update]" cancel_action="close"}
                    </div>
                {/if}
            </form>
        {/capture}
    {/capture}

    {capture name="buttons"}
        {capture name="tools_list"}
            {if $districts}
                <li>{btn type="delete_selected" dispatch="dispatch[ec_district.m_delete]" form="districts_form"}</li>
            {/if}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}

        {if $districts}
            {include file="buttons/save.tpl" but_name="dispatch[ec_district.m_update]" but_role="submit-link" but_target_form="districts_form"}
        {/if}
    {/capture}

    {capture name="adv_buttons"}
        {include file="common/popupbox.tpl" id="new_city" action="ec_district.add" text=$title content=$smarty.capture.add_new_picker title=__("add_district") act="general" icon="icon-plus"}
    {/capture}

    {capture name="sidebar"}
        <div class="sidebar-row">
            <h6>{__("search")}</h6>
            <form action="{""|fn_url}" name="states_filter_form" method="get">
                <div class="control-group">
                    <label for="elm_company_country" class="control-label">{__("country")}:</label>
                    <div class="controls" >
                    <select class="cm-country" id="elm_company_country" name="country_code" onchange=fn_ec_district_state_change(this);>
                        {foreach from=$countries item="country" key="code"}
                            <option {if $search.country_code == $code}selected="selected"{/if} value="{$code}">{$country}</option>
                        {/foreach}
                    </select>
                    </div>
                </div>

                <div class="control-group">
                    <label for="elm_company_state" class="control-label">{__("state")}:</label>
                    <div class="controls">
                    <select id="elm_company_state" name="state_code" class="cm-state" onchange=fn_ec_district_city_change(this);>
                        {if $states[$search.country_code]}
                            <option value="">- {__("select_state")} -</option>
                            {foreach from=$states[$search.country_code] item="state"}
                                <option {if $search.state_code == $state.code}selected="selected"{/if} value="{$state.code}">{$state.state}</option>
                            {/foreach}
                        {else}
                            <option value="">- {__("select_state")} -</option>
                        {/if}
                    </select>
                    <br>
                </div>

                <div class="control-group">
                    <label for="elm_company_city" class="control-label">{__("city")}:</label>
                    <div class="controls">
                    <select id="elm_company_city" name="city_id" class="">
                        {if $cities[$search.country_code][$search.state_code]}
                            <option value="">- {__("select_city")} -</option>
                            {foreach from=$cities[$search.country_code][$search.state_code] item="city"}
                                <option {if $search.city_id == $city.city_id}selected="selected"{/if} value="{$city.city_id}">{$city.code}</option>
                            {/foreach}
                        {else}
                            <option value="">- {__("select_city")} -</option>
                        {/if}
                    </select>
                    <br>
                </div>
                {include file="buttons/search.tpl" but_name="dispatch[ec_district.manage]"}
            </form>
        </div>
    {/capture}
{/capture}

{include file="common/mainbox.tpl" title=__("ec_districts") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar select_languages=true}

<script>
    function fn_ec_district_state_change(elm) {
        var states = {$states|json_encode nofilter};
        var default_country = elm.value;
        var select = document.getElementById("elm_company_state");
        var default_state = '{$search.state_code}';
        select.options.length = 0;

        if(default_country in states) {
            for (var i = 0; i < states[default_country].length; i++) {
                select.options[select.options.length] = new Option(states[default_country][i]['state'], states[default_country][i]['code']);
            }
        } else {
            select.options.length = 0;
            select.options[select.options.length] = new Option('- {__("select_state")} -',null);
        }

        if(default_country in states) {
            for (var i = 0; i < states[default_country].length; i++) {
                if(states[default_country][i]['code'] == default_state) {
                    select.selectedIndex = i;
                }
            }
        }
        fn_ec_district_city_change(document.getElementById("elm_company_state"));
    }

    function fn_ec_district_city_change(elm) {
        var cities = {$cities|json_encode nofilter};
        var default_country = document.getElementById("elm_company_country").value;
        var default_state = elm.value;
        var select = document.getElementById("elm_company_city");
        var default_city = '{$search.city_id}';
        select.options.length = 0;

        if(default_country in cities && default_state in cities[default_country]) {
            for (var i = 0; i < cities[default_country][default_state].length; i++) {
                select.options[select.options.length] = new Option(cities[default_country][default_state][i]['code'], cities[default_country][default_state][i]['city_id']);
            }
        } else {
            select.options.length = 0;
            select.options[select.options.length] = new Option('- {__("select_city")} -',null);
        }

        if(default_country in cities && default_state in cities[default_country]) {
            for (var i = 0; i < cities[default_country][default_state].length; i++) {
                if(cities[default_country][default_state][i]['city_id'] == default_city) {
                    select.selectedIndex = i;
                }
            }
        }
    }
</script>