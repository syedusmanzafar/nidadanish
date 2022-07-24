{if $field.field_type == $smarty.const.EC_DISTRICT}
    {if $user_data}

        {if $section == "S"}
            {$_country = $user_data.s_country|default:$settings.Checkout.default_country}
            {$_state   = $user_data.s_state|default:$settings.Checkout.default_state}
            {$_city    = $user_data.s_city|default:$cities.$_country.$_state.0.code}
        {else}
            {$_country = $user_data.b_country|default:$settings.Checkout.default_country}
            {$_state   = $user_data.b_state|default:$settings.Checkout.default_state}
            {$_city    = $user_data.b_city|default:$cities.$_country.$_state.0.code}        
        {/if}
    {else if $company_data}
        {$_country = $company_data.country|default:$settings.Checkout.default_country}
        {$_state   = $company_data.state|default:$settings.Checkout.default_state}
        {$_city    = $company_data.city|default:$cities.$_country.$_state.0.code}
    {/if}
    {$_district = $value}
    <div class="control-group profile-field-product_groups {$field.class}">
        {if $pref_field_name != $field.description || $field.required == "Y"}
            <label for="{$id_prefix}elm_{$field.field_id}" class="control-label cm-profile-field {if $field.required == "Y"}cm-required {/if}">{$field.description}</label>
            <div class="controls">
                <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_country_{$field.field_id}" value="{$_country}">
                <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_state_{$field.field_id}" value="{$_state}">
                <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_city_{$field.field_id}" value="{$_city}">
                <select class="cm-district {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if}" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" {$disabled_param nofilter}>
                    <option value="">- {__("select_district")} -</option>
                    {if $districts && $districts.$_country.$_state.$_city}
                        {foreach from=$districts.$_country.$_state.$_city item=district}
                            <option {if $_district == $district.district_id || $_district == $district.code}selected="selected"{/if} value="{$district.district_id}">{$district.code}</option>
                        {/foreach}
                    {/if}
                </select>
            </div>
        {/if}
    </div>
    {if $section == "S"}
        <script>
            (function(_, $) {
                $.ceEvent('on', 'ce.commoninit', function(context) {
                    var cityElms = $('select.cm-city.cm-location-shipping', context);
                    if (cityElms.length) {
                        $(document).on('change', 'select.cm-city.cm-location-shipping', function(){
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            var districts = {$districts|json_encode nofilter};
                            var default_country = $('.selected_shipping_country_{$field.field_id}').val();
                            var default_state = $('.selected_shipping_state_{$field.field_id}').val();
                            var default_city = $(this).val();
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                            if(default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                //console.log(default_city, districts[default_country][default_state], districts[default_country][default_state][default_city].length);
                                for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                    select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['district_id']);
                                }
                            } else {
                                select.options.length = 0;
                                select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                            }
                        });
                    }

                    var stateElms = $('select.cm-state.cm-location-shipping', context);
                    if (stateElms.length) {
                        $(document).on('change', 'select.cm-state.cm-location-shipping', function(){
                            $('.selected_shipping_state_{$field.field_id}').val($(this).val());
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                        });
                    }

                    var countryElms = $('select.cm-country.cm-location-shipping', context);
                    if (countryElms.length) {
                        $(document).on('change', 'select.cm-country.cm-location-shipping', function(){
                            $('.selected_shipping_country_{$field.field_id}').val($(this).val());
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                        });
                    }
                });
            }(Tygh, Tygh.$));
        </script>
    {else}
        <script>
            (function(_, $) {
                $.ceEvent('on', 'ce.commoninit', function(context) {
                    //billing//
                    var bcityElms = $('select.cm-city.cm-location-billing', context);
                    if (bcityElms.length) {
                        $(document).on('change', 'select.cm-city.cm-location-billing', function(){
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');

                            var districts = {$districts|json_encode nofilter};
                            var default_country = $('.selected_billing_country_{$field.field_id}').val();
                            var default_state = $('.selected_billing_state_{$field.field_id}').val();
                            var default_city = $(this).val();

                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                            if(default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                    select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['code']);
                                }
                            } else {
                                select.options.length = 0;
                                select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                            }
                        });
                    }

                    var bstateElms = $('select.cm-state.cm-location-billing', context);
                    if (bstateElms.length) {
                        $(document).on('change', 'select.cm-state.cm-location-billing', function(){
                            $('.selected_billing_state_{$field.field_id}').val($(this).val());      
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');  
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                        });
                    }

                    var bcountryElms = $('select.cm-country.cm-location-billing', context);
                    if (bcountryElms.length) {
                        $(document).on('change', 'select.cm-country.cm-location-billing', function(){
                            $('.selected_billing_country_{$field.field_id}').val($(this).val());      
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                        });
                    }
                });
            }(Tygh, Tygh.$));
        </script>
    {/if}
{/if}
{if $field.field_type == $smarty.const.EC_CITY}
    {if $user_data}
        {if $section == "S"}
            {$_country = $user_data.s_country|default:$settings.Checkout.default_country}
            {$_state = $user_data.s_state|default:$settings.Checkout.default_state}
        {else}
            {$_country = $user_data.b_country|default:$settings.Checkout.default_country}
            {$_state = $user_data.b_state|default:$settings.Checkout.default_state}
        {/if}
    {else if $company_data}
        {$_country = $company_data.country|default:$settings.Checkout.default_country}
        {$_state = $company_data.state|default:$settings.Checkout.default_state}
    {/if}
    {$_city = $value}
    <div class="control-group profile-field-product_groups {$field.class}">
        {if $pref_field_name != $field.description || $field.required == "Y"}
            <label for="{$id_prefix}elm_{$field.field_id}" class="control-label cm-profile-field {if $field.required == "Y"}cm-required {/if}">{$field.description}</label>
            <div class="controls">
                <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_country_{$field.field_id}" value="{$_country}">
                <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_state_{$field.field_id}" value="{$_state}">
                <select class="cm-city {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if}" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" {$disabled_param nofilter}>
                    <option value="">- {__("select_city")} -</option>
                    {if $cities && $cities.$_country.$_state}
                        {foreach from=$cities.$_country.$_state item=city}
                            <option {if $_city == $city.city_id}selected="selected"{/if} value="{$city.city_id}">{$city.code}</option>
                        {/foreach}
                    {/if}
                </select>
            </div>
        {/if}
    </div>
    <script>
        (function(_, $) {
            $.ceEvent('on', 'ce.commoninit', function(context) {
                var stateElms = $('select.cm-state.cm-location-shipping', context);
                if (stateElms.length) {
                    $(document).on('change', 'select.cm-state.cm-location-shipping', function(){
                        var default_country = $('.selected_shipping_country_{$field.field_id}').val();

                        var states = {$states|json_encode nofilter};
                        var cities = {$cities|json_encode nofilter};

                        var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                        var default_state = $(this).val();
                        select.options.length = 0;
                        select.options[select.options.length] = new Option('- {__("select_city")} -',null);
                        if(default_country in cities) {
                            $.each(cities[default_country],function(i, states_list) {
                                if(default_state == i) {
                                    $.each(states_list, function(i, city_list) {
                                        select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                    });
                                }
                            });
                        }
                    });
                }

                var countryElms = $('select.cm-country.cm-location-shipping', context);
                if (countryElms.length) {
                    $(document).on('change', 'select.cm-country.cm-location-shipping', function(){
                        $('.selected_shipping_country_{$field.field_id}').val($(this).val());
                        var default_country = $('.selected_shipping_country_{$field.field_id}').val();
                        var default_state = $('.selected_shipping_state_{$field.field_id}').val();

                        var states = {$states|json_encode nofilter};
                        var cities = {$cities|json_encode nofilter};

                        var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                        select.options.length = 0;
                        select.options[select.options.length] = new Option('- {__("select_city")} -',null);
                        if(default_country in cities) {
                            $.each(cities[default_country],function(i, states_list) {
                                if(default_state == i) {
                                    $.each(states_list, function(i, city_list) {
                                        select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                    });
                                }
                            });
                        }
                    });
                }

                //billing//
                var bstateElms = $('select.cm-state.cm-location-billing', context);
                if (bstateElms.length) {
                    $(document).on('change', 'select.cm-state.cm-location-billing', function(){
                        var default_country = $('.selected_billing_country_{$field.field_id}').val();

                        var states = {$states|json_encode nofilter};
                        var cities = {$cities|json_encode nofilter};

                        var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                        var default_state = $(this).val();
                        select.options.length = 0;
                        select.options[select.options.length] = new Option('- {__("select_city")} -',null);
                        if(default_country in cities) {
                            $.each(cities[default_country],function(i, states_list) {
                                if(default_state == i) {
                                    $.each(states_list, function(i, city_list) {
                                        select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                    });
                                }
                            });
                        }
                    });
                }

                var bcountryElms = $('select.cm-country.cm-location-billing', context);
                if (bcountryElms.length) {
                    $(document).on('change', 'select.cm-country.cm-location-billing', function(){
                        $('.selected_billing_country_{$field.field_id}').val($(this).val());
                        var default_country = $('.selected_billing_country_{$field.field_id}').val();
                        var default_state = $('.selected_billing_state_{$field.field_id}').val();

                        var states = {$states|json_encode nofilter};
                        var cities = {$cities|json_encode nofilter};

                        var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                        select.options.length = 0;
                        select.options[select.options.length] = new Option('- {__("select_city")} -',null);
                        if(default_country in cities) {
                            $.each(cities[default_country],function(i, states_list) {
                                if(default_state == i) {
                                    $.each(states_list, function(i, city_list) {
                                        select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                    });
                                }
                            });
                        }
                    });
                }
            });
        }(Tygh, Tygh.$));
    </script>
{/if}