{if $field.field_type == $smarty.const.EC_CITY}
    {$cities = fn_get_cities_tree()}
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
    <div class="ty-control-group ty-profile-field__item ty-{if $section == "S"}shipping{else}billing{/if}-state {$field.class}">
        {if $pref_field_name != $field.description || $field.required == "Y"}
            <label for="{$id_prefix}elm_{$field.field_id}" class="ty-control-group__title cm-profile-field {if $field.required == "Y"}cm-required {/if}">{$field.description}</label>
            <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_country_{$field.field_id}" value="{$_country}">
            <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_state_{$field.field_id}" value="{$_state}">
            <select class="ty-profile-field__select-state cm-city {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if}" data-ca-field-id="{$field.field_id}" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" {$disabled_param nofilter} data-ca-city="{$_city}">
                <option value="">- {__("select_city")} -</option>
                {if $cities && $cities.$_country.$_state}
                    {foreach from=$cities.$_country.$_state item=city}
                        <option {if $_city == $city.city_id}selected="selected"{/if} value="{$city.city_id}">{$city.code}</option>
                    {/foreach}
                {/if}
            </select>
        {/if}
    </div>
{/if}
{if $field.field_type == $smarty.const.EC_DISTRICT}
    {$cities = fn_get_cities_tree()}
    {$districts = fn_get_districts_tree()}
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
    <div class="ty-control-group ty-profile-field__item ty-{if $section == "S"}shipping{else}billing{/if}-state {$field.class}">
        {if $pref_field_name != $field.description || $field.required == "Y"}
            <label for="{$id_prefix}elm_{$field.field_id}" class="ty-control-group__title cm-profile-field {if $field.required == "Y"}cm-required {/if}">{$field.description}</label>
            <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_country_{$field.field_id}" value="{$_country}">
            <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_state_{$field.field_id}" value="{$_state}">
            <input type="hidden" class="selected_{if $section == "S"}shipping{else}billing{/if}_city_{$field.field_id}" value="{$_city}">
            <select class="ty-profile-field__select-state cm-district {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if}" id="{$id_prefix}elm_{$field.field_id}" data-ca-field-id="{$field.field_id}"  name="{$data_name}[{$data_id}]" {$disabled_param nofilter}>
                <option value="">- {__("select_district")} -</option>
                {if $districts && $districts.$_country.$_state.$_city}
                    {foreach from=$districts.$_country.$_state.$_city item=district}
                        <option {if $_district == $district.district_id}selected="selected"{/if} value="{$district.district_id}">{$district.code}</option>
                    {/foreach}
                {/if}
            </select>
        {/if}
    </div>
{/if}

  {* 
    {* {if $section == "S"}
        <script>
            var cities = {$cities|json_encode nofilter};
            (function(_, $) {
                $.ceEvent('on', 'ce.commoninit', function(context) {
                    var sstateElms = $('select.cm-state.cm-location-shipping', context);
                    if (sstateElms.length) {
                        $(document).on('change', 'select.cm-state.cm-location-shipping', function(){
                            var default_country = $('.selected_shipping_country_{$field.field_id}').val();
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            var default_state = $(this).val();
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_city")} -',null);
                            if (!cities) {
                                $.ceAjax('request', fn_url('ec_cities.get_tree'), {
                                    callback: function(data) {
                                        cities = data.cities;
                                        if(default_country in cities) {
                                            $.each(cities[default_country],function(i, states_list) {
                                                if(default_state == i) {
                                                    $.each(states_list, function(i, city_list) {
                                                        select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                                    });
                                                }
                                            });
                                        }
                                    }
                                });
                            }else{
                                if(default_country in cities) {
                                    $.each(cities[default_country],function(i, states_list) {
                                        if(default_state == i) {
                                            $.each(states_list, function(i, city_list) {
                                                select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                            });
                                        }
                                    });
                                }
                            }
                            
                        });
                    }

                    var scountryElms = $('select.cm-country.cm-location-shipping');
                    if (scountryElms.length) {
                        $(document).on('change', 'select.cm-country.cm-location-shipping', function(){
                            $('.selected_shipping_country_{$field.field_id}').val($(this).val());
                            var default_country = $('.selected_shipping_country_{$field.field_id}').val();
                            var default_state = $('.selected_shipping_state_{$field.field_id}').val();
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_city")} -',null);
                            if (!cities) {
                                $.ceAjax('request', fn_url('ec_cities.get_tree'), {
                                    callback: function(data) {
                                        cities = data.cities;
                                        if(default_country in cities) {
                                            $.each(cities[default_country],function(i, states_list) {
                                                if(default_state == i) {
                                                    $.each(states_list, function(i, city_list) {
                                                        select.options[select.options.length] = new Option(city_list['code'], city_list['code']);
                                                    });
                                                }
                                            });
                                            var opt_val = $('select.cm-city.cm-location-shipping').data('caCity');
                                            if ( $("select.cm-city.cm-location-shipping option[value='"+opt_val+"']").length != 0 ){
                                                $('select.cm-city.cm-location-shipping').val(opt_val);
                                            } else {
                                                $('select.cm-city.cm-location-shipping').val('null');
                                            }
                                        }
                                    }
                                });
                            }else{
                                if(default_country in cities) {
                                    $.each(cities[default_country],function(i, states_list) {
                                        if(default_state == i) {
                                            $.each(states_list, function(i, city_list) {
                                                select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                            });
                                        }
                                    });
                                    var opt_val = $('select.cm-city.cm-location-shipping').data('caCity');
                                    if ( $("select.cm-city.cm-location-shipping option[value='"+opt_val+"']").length != 0 ){
                                        $('select.cm-city.cm-location-shipping').val(opt_val);
                                    } else {
                                        $('select.cm-city.cm-location-shipping').val('null');
                                    }
                                }
                            }
                        });
                    }
                 });
            }(Tygh, Tygh.$));
            
        </script>
    {else}
        <script>
            var cities = {$cities|json_encode nofilter};;
            (function(_, $) {
                $.ceEvent('on', 'ce.commoninit', function(context) {
                //billing//
                var bstateElms = $('select.cm-state.cm-location-billing', context);
                if (bstateElms.length) {
                    $(document).on('change', 'select.cm-state.cm-location-billing', function(){
                        var default_country = $('.selected_billing_country_{$field.field_id}').val();
                        var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                        var default_state = $(this).val();
                        select.options.length = 0;
                        select.options[select.options.length] = new Option('- {__("select_city")} -',null);
                         if (!cities) {
                            $.ceAjax('request', fn_url('ec_cities.get_tree'), {
                                callback: function(data) {
                                    cities = data.cities;
                                    if(default_country in cities) {
                                        $.each(cities[default_country],function(i, states_list) {
                                            if(default_state == i) {
                                                $.each(states_list, function(i, city_list) {
                                                    select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                                });
                                            }
                                        });
                                    }
                                }
                            });
                        }else{
                            if(default_country in cities) {
                                $.each(cities[default_country],function(i, states_list) {
                                    if(default_state == i) {
                                        $.each(states_list, function(i, city_list) {
                                            select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                        });
                                    }
                                });
                            }
                        }
                    });
                }

                var bcountryElms = $('select.cm-country.cm-location-billing', context);
                if (bcountryElms.length) {
                    $(document).on('change', 'select.cm-country.cm-location-billing', function(){
                        $('.selected_billing_country_{$field.field_id}').val($(this).val());
                        var default_country = $('.selected_billing_country_{$field.field_id}').val();
                        var default_state = $('.selected_billing_state_{$field.field_id}').val();
                        var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                        select.options.length = 0;
                        select.options[select.options.length] = new Option('- {__("select_city")} -',null);
                        if (!cities) {
                            $.ceAjax('request', fn_url('ec_cities.get_tree'), {
                                callback: function(data) {
                                    cities = data.cities;
                                    if(default_country in cities) {
                                        $.each(cities[default_country],function(i, states_list) {
                                            if(default_state == i) {
                                                $.each(states_list, function(i, city_list) {
                                                    select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                                });
                                            }
                                        });
                                        var opt_val = $('select.cm-city.cm-location-billing').data('caCity');
                                        if ( $("select.cm-city.cm-location-billing option[value='"+opt_val+"']").length != 0 ){
                                            $('select.cm-city.cm-location-billing').val(opt_val);
                                        } else {
                                        // $('select.cm-city.cm-location-billing').val('null');
                                        }
                                    }
                                }
                            });
                        }else{
                            if(default_country in cities) {
                                $.each(cities[default_country],function(i, states_list) {
                                    if(default_state == i) {
                                        $.each(states_list, function(i, city_list) {
                                            select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                        });
                                    }
                                });
                                var opt_val = $('select.cm-city.cm-location-billing').data('caCity');
                                if ( $("select.cm-city.cm-location-billing option[value='"+opt_val+"']").length != 0 ){
                                    $('select.cm-city.cm-location-billing').val(opt_val);
                                } else {
                                // $('select.cm-city.cm-location-billing').val('null');
                                }
                            }
                        }
                    });
                }
            });
            }(Tygh, Tygh.$));
        </script>
    {/if} 
  {if $section == "S"}
        <script>
            var districts = null;
            (function(_, $) {
                ec_hide = false;
                {if $runtime.controller == 'single_step_checkout'}
                    ec_hide = true;
                {/if}
                $.ceEvent('on', 'ce.commoninit', function(context) {
                    var cityElms = $('select.cm-city.cm-location-shipping', context);
                    if (cityElms.length) {
                        $(document).on('change', 'select.cm-city.cm-location-shipping', function(){
                            var default_country = $('.selected_shipping_country_{$field.field_id}').val();
                            var default_state = $('.selected_shipping_state_{$field.field_id}').val();
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            var default_city = $(this).val();
                            if (districts){
                                select.options.length = 0;
                                select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                    for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                        select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['code']);
                                    }
                                } else {
                                    select.options.length = 0;
                                    select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                }
                            }else{
                                $.ceAjax('request', fn_url('ec_district.get_tree'),{
                                    callback:function(data) {
                                        districts = data.districts;
                                        select.options.length = 0;
                                        select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                        if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                            for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                                select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['code']);
                                            }
                                        } else {
                                            select.options.length = 0;
                                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                        }
                                    }
                                });
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
                            if(!ec_hide){
                                $('.selected_shipping_country_{$field.field_id}').val($(this).val());
                                var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                                select.options.length = 0;
                                select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                            }
                        });
                    }
                });

                $.ceEvent('on', 'ce.ajaxdone', function(context) {            
                    var cityElms = $('select.cm-city.cm-location-shipping');
                    if (cityElms.length) {
                        $(document).on('change', 'select.cm-city.cm-location-shipping', function(){
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            var default_country = $('.selected_shipping_country_{$field.field_id}').val();
                            var default_state = $('.selected_shipping_state_{$field.field_id}').val();
                            var default_city = $(this).val();
                            if (districts){
                                
                                if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                    select.options.length = 0;
                                    select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                    for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                        select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['code']);
                                    }
                                } else {
                                    select.options.length = 0;
                                    select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                }
                            }else{
                                $.ceAjax('request', fn_url('ec_district.get_tree'),{
                                    callback:function(data) {
                                        districts = data.districts;
                                        select.options.length = 0;
                                        select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                        if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                            select.options.length = 0;
                                            select.options[select.options.length] = new Option('- {__("select_district")} -', null);
                                            for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                                select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['code']);
                                            }
                                        } else {
                                            select.options.length = 0;
                                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                        }
                                    }
                                });
                            }
                        });
                    }

                    var stateElms = $('select.cm-state.cm-location-shipping');
                    if (stateElms.length) {
                        $(document).on('change', 'select.cm-state.cm-location-shipping', function(){
                            $('.selected_shipping_state_{$field.field_id}').val($(this).val());
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                        });
                    }

                    var countryElms = $('select.cm-country.cm-location-shipping');
                    if (countryElms.length) {
                        $(document).on('change', 'select.cm-country.cm-location-shipping', function(){
                            if(!ec_hide){
                                $('.selected_shipping_country_{$field.field_id}').val($(this).val());
                                var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                                select.options.length = 0;
                                select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                            }
                        });
                    }
                });
            }(Tygh, Tygh.$));
        </script>
    {else}
        <script>
            var districts = null;
            (function(_, $) {
                ec_hide = false;
                {if $runtime.controller == 'single_step_checkout'}
                    ec_hide = true;
                {/if}
                $.ceEvent('on', 'ce.commoninit', function(context) {
                    var cityElms = $('select.cm-city.cm-location-billing', context);
                    if (cityElms.length) {
                        $(document).on('change', 'select.cm-city.cm-location-billing', function(){
                            var default_country = $('.selected_billing_country_{$field.field_id}').val();
                            var default_state = $('.selected_billing_state_{$field.field_id}').val();
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            var default_city = $(this).val();
                            if (districts){
                                select.options.length = 0;
                                select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                    for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                        select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['code']);
                                    }
                                } else {
                                    select.options.length = 0;
                                    select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                }
                            }else{
                                $.ceAjax('request', fn_url('ec_district.get_tree'),{
                                    callback:function(data) {
                                        districts = data.districts;
                                        select.options.length = 0;
                                        select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                        if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                            for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                                select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['code']);
                                            }
                                        } else {
                                            select.options.length = 0;
                                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                        }
                                    }
                                });
                            }
                        });
                    }

                    var stateElms = $('select.cm-state.cm-location-billing', context);
                    if (stateElms.length) {
                        $(document).on('change', 'select.cm-state.cm-location-billing', function(){
                            $('.selected_billing_state_{$field.field_id}').val($(this).val());
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                        });
                    }

                    var countryElms = $('select.cm-country.cm-location-billing', context);
                    if (countryElms.length) {
                        $(document).on('change', 'select.cm-country.cm-location-billing', function(){
                            if(!ec_hide){
                                $('.selected_billing_country_{$field.field_id}').val($(this).val());
                                var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                                select.options.length = 0;
                                select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                            }
                        });
                    }
                });

                $.ceEvent('on', 'ce.ajaxdone', function(context) {            
                    var cityElms = $('select.cm-city.cm-location-billing');
                    if (cityElms.length) {
                        $(document).on('change', 'select.cm-city.cm-location-billing', function(){
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            var default_country = $('.selected_billing_country_{$field.field_id}').val();
                            var default_state = $('.selected_billing_state_{$field.field_id}').val();
                            var default_city = $(this).val();
                            if (districts){
                                
                                if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                    select.options.length = 0;
                                    select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                    for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                        select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['code']);
                                    }
                                } else {
                                    select.options.length = 0;
                                    select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                }
                            }else{
                                $.ceAjax('request', fn_url('ec_district.get_tree'),{
                                    callback:function(data) {
                                        districts = data.districts;
                                        select.options.length = 0;
                                        select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                        if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                            select.options.length = 0;
                                            select.options[select.options.length] = new Option('- {__("select_district")} -', null);
                                            for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                                select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['code']);
                                            }
                                        } else {
                                            select.options.length = 0;
                                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                        }
                                    }
                                });
                            }
                        });
                    }

                    var stateElms = $('select.cm-state.cm-location-billing');
                    if (stateElms.length) {
                        $(document).on('change', 'select.cm-state.cm-location-billing', function(){
                            $('.selected_billing_state_{$field.field_id}').val($(this).val());
                            var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                        });
                    }

                    var countryElms = $('select.cm-country.cm-location-billing');
                    if (countryElms.length) {
                        $(document).on('change', 'select.cm-country.cm-location-billing', function(){
                            if(!ec_hide){
                                $('.selected_billing_country_{$field.field_id}').val($(this).val());
                                var select = document.getElementById('{$id_prefix}elm_{$field.field_id}');
                                select.options.length = 0;
                                select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                            }
                        });
                    }
                });
            }(Tygh, Tygh.$));
        </script>
    {/if} *}