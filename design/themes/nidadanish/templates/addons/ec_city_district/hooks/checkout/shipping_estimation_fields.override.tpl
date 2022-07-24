{hook name="checkout:shipping_estimation_fields"}
{$_state = $cart.user_data.s_state}
{$_country = $cart.user_data.s_country}

{$countries = fn_get_simple_countries(true)}

{$cities = fn_get_cities_tree()}
{$_city = $cart.user_data.s_city}

{$districts = fn_get_districts_tree()}
{$_district = $cart.user_data.s_district}

{if !isset($cart.user_data.s_country)}
    {$_country = $settings.Checkout.default_country}
{/if}

{if !isset($cart.user_data.s_state) && $_country == $settings.Checkout.default_country}
    {$_state = $settings.Checkout.default_state}
{/if}

<div class="ty-control-group">
    <label class="ty-control-group__label cm-required" for="{$prefix}elm_country{$id_suffix}">{__("country")}</label>
    <select id="{$prefix}elm_country{$id_suffix}" class="cm-country cm-location-estimation{$class_suffix} ty-input-text-medium" name="customer_location[country]">
        <option value="">- {__("select_country")} -</option>
        {foreach $countries as $code => $country}
            <option value="{$code}" {if $_country == $code}selected="selected"{/if}>{$country}</option>
        {/foreach}
    </select>
</div>

<div class="ty-control-group">
    <label class="ty-control-group__label" for="{$prefix}elm_state{$id_suffix}">{__("state")}</label>
    <select class="cm-state cm-location-estimation{$class_suffix} {if !$states[$_country]}hidden{/if} ty-input-text-medium" id="{$prefix}elm_state{$id_suffix}" name="customer_location[state]">
        <option value="">- {__("select_state")} -</option>
        {foreach $states[$_country] as $state}
            <option value="{$state.code}" {if $state.code == $_state}selected="selected"{/if}>{$state.state}</option>
        {/foreach}
    </select>
    <input type="text" class="cm-state cm-location-estimation{$class_suffix} ty-input-text-medium {if $states[$cart.user_data.s_country]}hidden{/if}" id="{$prefix}elm_state{$id_suffix}_d" name="customer_location[state]" size="20" maxlength="64" value="{$_state}" {if $states[$cart.user_data.s_country]}disabled="disabled"{/if} />
</div>
{* 
<div class="ty-control-group">
    <label class="ty-control-group__label" for="{$prefix}elm_city{$id_suffix}">{__("city")}</label>
    <input type="text" class="ty-input-text-medium" id="{$prefix}elm_city{$id_suffix}" name="customer_location[city]" size="32" value="{$cart.user_data.s_city}" />
</div> *}

<div class="ty-control-group">
    <label for="{$prefix}elm_{$id_suffix}" class="ty-control-group__label">{__("city")}</label>
    <input type="hidden" class="selected_shipping_country_{$id_suffix}" value="{$_country}">
    <input type="hidden" class="selected_shipping_state_{$id_suffix}" value="{$_state}">
    <select class="cm-city cm-location-estimation{$class_suffix} ty-input-text-medium" data-ca-field-id="{$id_suffix}" id="{$prefix}elm_{$id_suffix}" name="customer_location[city]" data-ca-city="{$_city}">
        <option value="">- {__("select_city")} -</option>
        {if $cities && $cities.$_country.$_state}
            {foreach from=$cities.$_country.$_state item=city}
                <option {if $_city == $city.city_id}selected="selected"{/if} value="{$city.city_id}">{$city.code}</option>
            {/foreach}
        {/if}
    </select>
</div>

<script>
    var cities = null;
    (function(_, $) {
        $.ceEvent('on', 'ce.commoninit', function(context) {
            var sstateElms = $('select.cm-state.cm-location-estimation{$class_suffix}', context);
            if (sstateElms.length) {
                $(document).on('change', 'select.cm-state.cm-location-estimation{$class_suffix}', function(){
                    var default_country = $('.selected_shipping_country_{$id_suffix}').val();
                    var select = document.getElementById('{$prefix}elm_{$id_suffix}');
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

            var scountryElms = $('select.cm-country.cm-location-estimation{$class_suffix}');
            if (scountryElms.length) {
                $(document).on('change', 'select.cm-country.cm-location-estimation{$class_suffix}', function(){
                    $('.selected_shipping_country_{$id_suffix}').val($(this).val());
                    var default_country = $('.selected_shipping_country_{$id_suffix}').val();
                    var default_state = $('.selected_shipping_state_{$id_suffix}').val();
                    var select = document.getElementById('{$prefix}elm_{$id_suffix}');
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
                                    var opt_val = $('select.cm-city.cm-location-estimation{$class_suffix}').data('caCity');
                                    if ( $("select.cm-city.cm-location-estimation{$class_suffix} option[value='"+opt_val+"']").length != 0 ){
                                        $('select.cm-city.cm-location-estimation{$class_suffix}').val(opt_val);
                                    } else {
                                        //$('select.cm-city.cm-location-estimation{$class_suffix}').val('null');
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
                            var opt_val = $('select.cm-city.cm-location-estimation{$class_suffix}').data('caCity');
                            if ( $("select.cm-city.cm-location-estimation{$class_suffix} option[value='"+opt_val+"']").length != 0 ){
                                $('select.cm-city.cm-location-estimation{$class_suffix}').val(opt_val);
                            } else {
                                //$('select.cm-city.cm-location-estimation{$class_suffix}').val('null');
                            }
                        }
                    }
                });
            }
            });
    }(Tygh, Tygh.$));
    
</script>

<div class="ty-control-group">
    <label for="{$prefix}elm_dist_{$id_suffix}" class="ty-control-group__label">{__("district")}</label>
    <input type="hidden" class="selected_shipping_country_{$id_suffix}" value="{$_country}">
    <input type="hidden" class="selected_shipping_state_{$id_suffix}" value="{$_state}">
    <input type="hidden" class="selected_shipping_city_{$id_suffix}" value="{$_city}">
    <select class="cm-district1 cm-location-estimation{$class_suffix} ty-input-text-medium" id="{$prefix}elm_dist_{$id_suffix}" name="customer_location[district]">
        <option value="">- {__("select_district")} -</option>
        {if $districts && $districts.$_country.$_state.$_city}
            {foreach from=$districts.$_country.$_state.$_city item=district}
                <option {if $_district == $district.district_id}selected="selected"{/if} value="{$district.district_id}">{$district.code}</option>
            {/foreach}
        {/if}
    </select>
</div>

<script>
    var districts = null;
    (function(_, $) {
        $.ceEvent('on', 'ce.commoninit', function(context) {
            var cityElms = $('select.cm-city.cm-location-estimation{$class_suffix}', context);
            if (cityElms.length) {
                $(document).on('change', 'select.cm-city.cm-location-estimation{$class_suffix}', function(){
                    var default_country = $('.selected_shipping_country_{$id_suffix}').val();
                    var default_state = $('.selected_shipping_state_{$id_suffix}').val();
                    var select = document.getElementById('{$prefix}elm_dist_{$id_suffix}');
                    var default_city = $(this).val();
                    if (districts){
                        select.options.length = 0;
                        select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                                console.log("Already",default_country, default_state, default_city,default_state in districts[default_country], default_city in districts[default_country][default_state]);

                        if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                            for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['district_id']);
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
                                console.log(default_country, default_state, default_city,default_state in districts[default_country], default_city in districts[default_country][default_state]);
                                if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                                    for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                                        select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['district_id']);
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

            var stateElms = $('select.cm-state.cm-location-estimation{$class_suffix}', context);
            if (stateElms.length) {
                $(document).on('change', 'select.cm-state.cm-location-estimation{$class_suffix}', function(){
                    $('.selected_shipping_state_{$id_suffix}').val($(this).val());
                    var select = document.getElementById('{$prefix}elm_dist_{$id_suffix}');
                    select.options.length = 0;
                    select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                });
            }

            
        });
    }(Tygh, Tygh.$));
</script>
    
<div class="ty-control-group">
    <label class="ty-control-group__label" for="{$prefix}elm_zipcode{$id_suffix}">{__("zip_postal_code")}</label>
    <input type="text" class="ty-input-text-medium" id="{$prefix}elm_zipcode{$id_suffix}" name="customer_location[zipcode]" size="20" value="{$cart.user_data.s_zipcode}" />
</div>
{/hook}