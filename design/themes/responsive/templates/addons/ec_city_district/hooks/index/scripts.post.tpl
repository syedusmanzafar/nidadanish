{if $runtime.controller == 'checkout'}
    {$cities = fn_get_cities_tree()}
    {$districts = fn_get_districts_tree()}
    <script>
        (function(_, $) {
            $.ceEvent('on', 'ce.commoninit', function(context) {
                var $new_section = "shipping";
                var districts = {$districts|json_encode nofilter};
                var countryElms = $('select.cm-country.cm-location-'+$new_section, context);
                if (countryElms.length) {
                    $(document).on('change', 'select.cm-country.cm-location-'+$new_section, function(){
                        $('.selected_'+$new_section+'_country_s_district').val($(this).val());
                        var select = document.getElementById('litecheckout_s_district');
                        select.options.length = 0;
                        select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                        $('select.cm-city.cm-location-'+$new_section, context).trigger('change'); 
                        $('#litecheckout_s_district').val($('#litecheckout_s_district').data('caDistrict'));
                    });
                }
                var stateElms = $('select.cm-state.cm-location-'+$new_section, context);
                if (stateElms.length) {
                    $(document).on('change', 'select.cm-state.cm-location-'+$new_section, function(){
                        $('.selected_'+$new_section+'_state_s_district').val($(this).val());
                        var select = document.getElementById('litecheckout_s_district');
                        select.options.length = 0;
                        select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                    });
                }
                var cityElms = $('select.cm-city.cm-location-'+$new_section, context);
                if (cityElms.length) {
                    $(document).on('change', 'select.cm-city.cm-location-'+$new_section, function(){
                        var default_country = $('.selected_'+$new_section+'_country_s_district').val();
                        var default_state = $('.selected_'+$new_section+'_state_s_district').val();
                        var select = document.getElementById('litecheckout_s_district');
                        var default_city = $(this).val();
                        if (districts){
                            select.options.length = 0;
                            select.options[select.options.length] = new Option('- {__("select_district")} -',null);
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
                                    if(districts && default_country in districts && default_state in districts[default_country] && districts[default_country][default_state] != undefined && default_city in districts[default_country][default_state]) {
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
            });
        }(Tygh, Tygh.$));
    </script>
    <script>
        var $new_section = "shipping";
        (function(_, $) {
            $.ceEvent('on', 'ce.commoninit', function(context) {
                $('.cm-district').on('change', function(){
                    $.ceLiteCheckout('lockShippingMethodSelector');
                });
            var stateElms = $('select.cm-state.cm-location-'+$new_section, context);
            if (stateElms.length) {
                $(document).on('change', 'select.cm-state.cm-location-'+$new_section, function(){
                    var default_country = $('.selected_'+$new_section+'_country_litecheckout_city').val();
                    var cities = {$cities|json_encode nofilter};
                    var select = document.getElementById('litecheckout_city');
                    var default_state = $(this).val();
                    select.options.length = 0;
                    select.options[select.options.length] = new Option('- {__("select_city")} -',"");
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
            var countryElms = $('select.cm-country.cm-location-'+$new_section, context);
            if (countryElms.length) {
                $(document).on('change', 'select.cm-country.cm-location-'+$new_section, function(){
                    $('.selected_'+$new_section+'_country_litecheckout_city').val($(this).val());
                    var default_country = $('.selected_'+$new_section+'_country_litecheckout_city').val();
                    var default_state = $('.selected_'+$new_section+'_state_litecheckout_city').val();
                    var cities = {$cities|json_encode nofilter};
                    var select = document.getElementById('litecheckout_city');
                    select.options.length = 0;
                    select.options[select.options.length] = new Option('- {__("select_city")} -',"");
                    if(default_country in cities) {
                        $.each(cities[default_country],function(i, states_list) {
                            if(default_state == i) {
                                $.each(states_list, function(i, city_list) {
                                    select.options[select.options.length] = new Option(city_list['code'], city_list['city_id']);
                                });
                            }
                        });
                        var opt_val = $('select.cm-city.cm-location-'+$new_section).data('caCity');
                        if ( $('select.cm-city.cm-location-'+$new_section+' option[value="'+opt_val+'"]').length != 0 ){
                            $('select.cm-city.cm-location-'+$new_section).val(opt_val);
                        } else {
                            $('select.cm-city.cm-location-'+$new_section).val('');
                        }
                    }
                });
            }
            });
        }(Tygh, Tygh.$));      
    </script>
{else}
{$cities = fn_get_cities_tree()}
{$districts = fn_get_districts_tree()}
<script>
(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function(context) {
        var cities = {$cities|json_encode nofilter};
        var scityelms = $('select.cm-city.cm-location-shipping', context);
        if (scityelms.length) {
            field_id = scityelms.eq(0).data('caFieldId')
            var sstateElms = $('select.cm-state.cm-location-shipping', context);
            if (sstateElms.length) {
                $(document).on('change', 'select.cm-state.cm-location-shipping', function(){
                    var default_country = $('.selected_shipping_country_'+field_id).val();
                    var select = document.getElementById('elm_'+field_id);
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

            var scountryElms = $('select.cm-country.cm-location-shipping');
            if (scountryElms.length) {
                $(document).on('change', 'select.cm-country.cm-location-shipping', function(){
                    $('.selected_shipping_country_'+field_id).val($(this).val());
                    var default_country = $('.selected_shipping_country_'+field_id).val();
                    var default_state = $('.selected_shipping_state_'+field_id).val();
                    var select = document.getElementById('elm_'+field_id);
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
                        /*var opt_val = $('select.cm-city.cm-location-shipping').data('caCity');
                        if ( $("select.cm-city.cm-location-shipping option[value='"+opt_val+"']").length != 0 ){
                            $('select.cm-city.cm-location-shipping').val(opt_val);
                        } else {
                            $('select.cm-city.cm-location-shipping').val('null');
                        }
                        */
                    }
                });
            }
        }
        var bcityelms = $('select.cm-city.cm-location-billing', context);
        if (bcityelms.length) {
            var field_id = bcityelms.eq(0).data('caFieldId')
            var sstateElms = $('select.cm-state.cm-location-billing', context);
            if (sstateElms.length) {
                $(document).on('change', 'select.cm-state.cm-location-billing', function(){
                    var default_country = $('.selected_billing_country_'+field_id).val();
                    var select = document.getElementById('elm_'+field_id);
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

            var scountryElms = $('select.cm-country.cm-location-billing');
            if (scountryElms.length) {
                $(document).on('change', 'select.cm-country.cm-location-billing', function(){
                    $('.selected_billing_country_'+field_id).val($(this).val());
                    var default_country = $('.selected_billing_country_'+field_id).val();
                    var default_state = $('.selected_billing_state_'+field_id).val();
                    var select = document.getElementById('elm_'+field_id);
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
                      /*  var opt_val = $('select.cm-city.cm-location-billing').data('caCity');
                        if ( $("select.cm-city.cm-location-billing option[value='"+opt_val+"']").length != 0 ){
                            $('select.cm-city.cm-location-billing').val(opt_val);
                        } else {
                            $('select.cm-city.cm-location-billing').val('null');
                        }
                         */
                    }
                });
            }
        }
    });
    $.ceEvent('on', 'ce.commoninit', function(context) {
        var districts = {$districts|json_encode nofilter};
        var sDistrictElms = $('select.cm-district.cm-location-shipping', context);
        if (sDistrictElms.length){
            var field_id = sDistrictElms.eq(0).data('caFieldId')
            var cityElms = $('select.cm-city.cm-location-shipping', context);
            if (cityElms.length) {
                $(document).on('change', 'select.cm-city.cm-location-shipping', function(){
                    var default_country = $('.selected_shipping_country_'+field_id).val();
                    var default_state = $('.selected_shipping_state_'+field_id).val();
                    var select = document.getElementById('elm_'+field_id);
                    var default_city = $(this).val();
                    select.options.length = 0;
                    select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                    if(districts && default_country in districts && default_state in districts[default_country] && default_city in districts[default_country][default_state]) {
                        for (var i = 0; i < districts[default_country][default_state][default_city].length; i++) {
                            select.options[select.options.length] = new Option(districts[default_country][default_state][default_city][i]['code'], districts[default_country][default_state][default_city][i]['district_id']);
                        }
                    }
                });
            }

            var stateElms = $('select.cm-state.cm-location-shipping', context);
            if (stateElms.length) {
                $(document).on('change', 'select.cm-state.cm-location-shipping', function(){
                    $('.selected_shipping_state_'+field_id).val($(this).val());
                    var select = document.getElementById('elm_'+field_id);
                    select.options.length = 0;
                    select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                });
            }

            var countryElms = $('select.cm-country.cm-location-shipping', context);
            if (countryElms.length) {
                $(document).on('change', 'select.cm-country.cm-location-shipping', function(){
                    $('.selected_shipping_country_'+field_id).val($(this).val());
                    var select = document.getElementById('elm_'+field_id);
                    select.options.length = 0;
                    select.options[select.options.length] = new Option('- {__("select_district")} -',null);
                });
            }
        }
    });
}(Tygh, Tygh.$));
</script>
{/if}