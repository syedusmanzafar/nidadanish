{capture name="mainbox"}
    {if $pinta_facebook_feed}
        <div id="ajax_loading_box2" class="ajax-loading-box"></div>
        <div id="content_general">
            <form action="{""|fn_url}" method="post" class="form-horizontal form-edit" name="pinta_facebook_feed_form"
                  enctype="multipart/form-data">
                <div class="control-group">
                    <label for="feed_companies" class="control-label">
                        {__("memcached_status")}
                    </label>
                    <div class="controls">
                        {if $memcached}
                            <div class="memcached_flush">
                                {__("memcached_status_on")}
                            </div>
                        {else}
                            <div style="margin: 5px 0;color:red;">
                                {__("memcached_status_off")}
                            </div>
                        {/if}
                    </div>
                </div>
                <div class="control-group">
                    <label for="feed_companies" class="control-label">
                        {__("company")}
                        <a class="cm-tooltip" title="{__("company_info")}"><i class="icon-question-sign"></i></a>
                    </label>
                    <div class="controls">
                        <input type="hidden" id="company_id" value="{$pinta_facebook_feed.companies_setting}">
                        <select name="feed_companies" id="feed_companies">
                            <option value="0">{__("select_companies")}</option>
                            {foreach from=$pinta_facebook_feed.companies[0] item=companies}
                                <option {if $pinta_facebook_feed.companies_setting == $companies.company_id}selected="selected"{/if}
                                        value="{$companies.company_id}">{$companies.company}</option>
                            {/foreach}
                        </select>
                        <a id="company_settings" class="btn" data-href="{"pinta_facebook_feed.manage"|fn_url}&company_id=">{__("select_companies_settings")}</a>
                    </div>
                </div>

                <div class="control-group">
                    <label for="feed_language" class="control-label">
                        {__("language")}
                        <a class="cm-tooltip" title="{__("select_language")}"><i class="icon-question-sign"></i></a>
                    </label>
                    <div class="controls">
                        <select name="feed_language">
                            {foreach from=$pinta_facebook_feed.languages item=language}
                                <option {if $pinta_facebook_feed.languages_setting == $language.lang_code}selected="selected"{/if}
                                        value="{$language.lang_code}">{$language.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label for="feed_currency" class="control-label">
                        {__("currency")}
                        <a class="cm-tooltip" title="{__("select_currency")}"><i class="icon-question-sign"></i></a>
                    </label>
                    <div class="controls">
                        <select name="feed_currency">
                            {foreach from=$pinta_facebook_feed.currency item=currency}
                                <option {if $pinta_facebook_feed.currency_setting == $currency.currency_code}selected="selected"{/if}
                                        value="{$currency.currency_code}">{$currency.description}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">
                        {__("options")}
                        <a class="cm-tooltip" title="{__("options_info")}"><i class="icon-question-sign"></i></a>
                    </label>
                    <label class="control-label">
                        {__("select_matching")}
                        <a class="cm-tooltip" title="{__("matching_info")}"><i class="icon-question-sign"></i></a>
                    </label>
                </div>
                <div class="control-group">
                    <label for="feed_maping" class="control-label">{__("feed_maping")}</label>
                    <div class="controls">
                        <label class="radio inline" for="feed_mapping_options">
                            <input type="radio" name="feed_maping" id="feed_mapping_options" value="options"{if $pinta_facebook_feed.feed_maping == 'options'} checked="checked"{/if}>{__("feed_options")}
                        </label>
                        <label class="radio inline" for="feed_mapping_feature">
                            <input type="radio" name="feed_maping" id="feed_mapping_feature" value="feature"{if $pinta_facebook_feed.feed_maping == 'feature'} checked="checked"{/if}>{__("feed_feature")}
                        </label>
                    </div>
                </div>
                <div class="control-group">
                    <label for="feed_size" class="control-label">{__("size")}</label>
                    <div class="controls">
                        <select name="feed_size">
                            <option value="">{__("select_option")}</option>
                            {foreach from=$pinta_facebook_feed.options[0] item=options}
                                <option {if $pinta_facebook_feed.size_setting == $options.option_name}selected="selected"{/if}
                                        value="{$options.option_name}">{$options.option_name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label for="feed_color" class="control-label">{__("color")}</label>
                    <div class="controls">
                        <select name="feed_color">
                            <option value="">{__("select_option")}</option>
                            {foreach from=$pinta_facebook_feed.options[0] item=options}
                                <option {if $pinta_facebook_feed.color_setting == $options.option_name}selected="selected"{/if}
                                        value="{$options.option_name}">{$options.option_name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label for="feed_pattern" class="control-label">{__("pattern")}</label>
                    <div class="controls">
                        <select name="feed_pattern">
                            <option value="">{__("select_option")}</option>
                            {foreach from=$pinta_facebook_feed.options[0] item=options}
                                <option {if $pinta_facebook_feed.pattern_setting == $options.option_name}selected="selected"{/if}
                                        value="{$options.option_name}">{$options.option_name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label for="feed_material" class="control-label">{__("material")}</label>
                    <div class="controls">
                        <select name="feed_material">
                            <option value="">{__("select_option")}</option>
                            {foreach from=$pinta_facebook_feed.options[0] item=options}
                                <option {if $pinta_facebook_feed.material_setting == $options.option_name}selected="selected"{/if}
                                        value="{$options.option_name}">{$options.option_name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">
                        {__("standard_field")}
                        <a class="cm-tooltip" title="{__("standard_field_info")}"><i class="icon-question-sign"></i></a>
                    </label>
                    <label class="control-label">
                        {__("replaced_by")}
                        <a class="cm-tooltip" title="{__("replaced_by_info")}"><i
                                    class="icon-question-sign"></i></a>
                    </label>
                </div>
                <div class="control-group">
                    <label for="feed_mapping_title" class="control-label">{__("mapping_title")}</label>
                    <div class="controls">
                        <select name="feed_mapping_title">
                            <option value="">{__("select_option")}</option>
                            {foreach from=$pinta_facebook_feed.mapping_fields item=mapping_field}
                                <option {if $pinta_facebook_feed.mapping_title == $mapping_field}selected="selected"{/if}
                                        value="{$mapping_field}">{$mapping_field}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label for="feed_mapping_description" class="control-label">{__("mapping_description")}</label>
                    <div class="controls">
                        <select name="feed_mapping_description">
                            <option value="">{__("select_option")}</option>
                            {foreach from=$pinta_facebook_feed.mapping_fields item=mapping_field}
                                <option {if $pinta_facebook_feed.mapping_description == $mapping_field}selected="selected"{/if}
                                        value="{$mapping_field}">{$mapping_field}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label for="feed_mapping_image_link" class="control-label">{__("mapping_image_link")}</label>
                    <div class="controls">
                        <select name="feed_mapping_image_link">
                            <option value="">{__("select_option")}</option>
                            {foreach from=$pinta_facebook_feed.mapping_fields item=mapping_field}
                                <option {if $pinta_facebook_feed.mapping_image_link == $mapping_field}selected="selected"{/if}
                                        value="{$mapping_field}">{$mapping_field}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label for="feed_mapping_brand" class="control-label">{__("mapping_brand")}</label>
                    <div class="controls">
                        <select name="feed_mapping_brand">
                            <option value="">{__("select_option")}</option>
                            {foreach from=$pinta_facebook_feed.mapping_fields item=mapping_field}
                                <option {if $pinta_facebook_feed.mapping_brand == $mapping_field}selected="selected"{/if}
                                        value="{$mapping_field}">{$mapping_field}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label for="feed_mapping_availability"
                           class="control-label">{__("mapping_availability")}</label>
                    <div class="controls">
                        <select name="feed_mapping_availability">
                            <option value="">{__("select_option")}</option>
                            {foreach from=$pinta_facebook_feed.mapping_fields item=mapping_field}
                                <option {if $pinta_facebook_feed.mapping_availability == $mapping_field}selected="selected"{/if}
                                        value="{$mapping_field}">{$mapping_field}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="hidden" name="feed_upload_without_img" value="0">
                        <input id="feed_upload_without_img" type="checkbox" name="feed_upload_without_img" value="1"
                               {if $pinta_facebook_feed.upload_without_img_setting}checked{/if} class="user-success"
                               style="width: 17px; height: 17px;">
                        <span>
                            {__("upload_without_img")}
                            <a class="cm-tooltip" title="{__("upload_without_img_info")}"><i
                                    class="icon-question-sign"></i></a>
                        </span>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="hidden" name="feed_turn_off_categories" value="0">
                        <input id="feed_turn_off_categories" type="checkbox" name="feed_turn_off_categories"
                               value="1"
                               {if $pinta_facebook_feed.turn_off_categories_setting}checked{/if}
                               class="user-success"
                               style="width: 17px; height: 17px;">
                        <span>
                            {__("turn_off_categories")}
                            <a class="cm-tooltip" title="{__("turn_off_categories_info")}"><i
                                    class="icon-question-sign"></i></a>
                        </span>
                    </div>
                </div>
            </form>
            <form action="{""|fn_url}" method="post" name="form_up" class="form-horizontal form-edit"
                  enctype="multipart/form-data">
                <div class="control-group">
                    <label for="feed_update" class="control-label">{__("feed_update")}
                        <a class="cm-tooltip" title="{__("feed_update_info")}"><i class="icon-question-sign"></i></a>
                        {include file="buttons/save.tpl" but_role="submit-link" but_target_form="form_up" but_name="dispatch[pinta_facebook_feed.feed_update]"}
                    </label>
                    <div class="controls">
                        <textarea cols="100" rows="1" name="feed_update" style="width:auto;"></textarea>
                        <p>Example:<i>https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt</i></p>
                    </div>
                </div>
            </form>
            <table class="table table-tree table-middle">
                <tr>
                    <th width="24%">
                        {__("category")}
                        <a class="cm-tooltip" title="{__("category_info")}"><i class="icon-question-sign"></i></a>
                    </th>
                    <th width="24%">
                        {__("google_main_category")}
                        <a class="cm-tooltip" title="{__("google_main_category_info")}"><i
                                    class="icon-question-sign"></i></a>
                    </th>
                    <th width="48%">
                        {__("google_subcategory")}
                        <a class="cm-tooltip" title="{__("google_subcategory_info")}"><i class="icon-question-sign"></i></a>
                    </th>
                </tr>
                {include file="addons/pinta_facebook_feed/views/pinta_facebook_feed/categories_tree.tpl"}
            </table>

            {if $pinta_facebook_feed.vendor}
                {$company_name_link = "&company="}
                {$company_name_link = $company_name_link|cat:$pinta_facebook_feed.company_name}
                {$company_name_file = $pinta_facebook_feed.company_name|cat:"_"}
            {else}
                {$company_name_link = ''}
                {$company_name_file = ''}
            {/if}
            {if $pinta_facebook_feed.companies_setting}
                {assign var='company_get_id' value="&company_id=`$pinta_facebook_feed.companies_setting`"}
                {assign var='company_get_field' value="_company_`$pinta_facebook_feed.companies_setting`"}
            {else}
                {assign var='company_get_id' value=""}
                {assign var='company_get_field' value=""}
            {/if}
            <table class="table table-tree table-middle">
                <tr>
                    <td width="25%">
                        <a id="generate_page" target="_blank" class="btn btn-primary" href="{$pinta_facebook_feed.root_link}&action=generate{$company_get_id}"> Generate xml to web page</a>
                    </td>
                    <td width="75%">
                        <div class="info">
                            <h3>
                                {__("link_to_copy")}
                            </h3>
                            <p>
                                {$pinta_facebook_feed.root_link}&action=generate{$company_get_id}
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <a id="generate_file" target="_blank" class="btn btn-primary" href="{$pinta_facebook_feed.root_link}&action=save{$company_get_id}"> Generate xml to file</a>
                    </td>
                    <td width="75%">
                        <div class="info">
                            <h3>
                                {__("command_for_cron")}
                            </h3>
                            <p>
                                0 * * * * curl "{$pinta_facebook_feed.root_link}&action=save{$company_get_id}"
                            </p>
                        </div>
                        <div class="info">
                            <h3>
                                {__("url_to_feed_file")}
                            </h3>
                            <p>
                                {$pinta_facebook_feed.current_location}/pinta_feed{$company_get_field}.xml
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="table table-tree table-middle">
                <tr>
                    <td colspan="4">
                        <h3>{__("text_generator_description")}</h3>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label class="control-label">{__("text_generation_link")}</label>
                    </td>

                    <td style="position: relative" width="25%">
                        <div class="alert alert-danger alert-dismissible alert-disable"><i
                                    class="fa fa-exclamation-circle"></i> {__("error_selected_fields")}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <select id="generation_lang" style="width: 100%;">
                            <option value="0">{__("text_select ")}</option>
                            {foreach from=$pinta_facebook_feed.languages item=language}
                                <option {if $pinta_facebook_feed.languages_setting == $language.lang_code}selected="selected"{/if}
                                        value="{$language.lang_code}">{$language.name}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td style="position: relative" width="25%">
                        <div class="alert alert-danger alert-dismissible alert-disable"><i
                                    class="fa fa-exclamation-circle"></i> {__("error_selected_fields")}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <select id="generation_curr" style="width: 100%;" >
                            <option value="0">{__("text_select ")}</option>
                            {foreach from=$pinta_facebook_feed.currency item=currency}
                                <option {if $pinta_facebook_feed.currency_setting == $currency.currency_code}selected="selected"{/if}
                                        value="{$currency.currency_code}">{$currency.description}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td style="position: relative" width="25%">
                        <a href="javascript:void(0)" class="btn btn-primary" id="create_link" onclick="Generation.link()">{__("text_generation_btn")}</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <label class="" id="result_link"></label>
                    </td>
                </tr>
            </table>
        </div>
        {capture name="buttons"}
            {if !$id}
                {include file="buttons/save.tpl" but_role="submit-link" but_target_form="pinta_facebook_feed_form" but_name="dispatch[pinta_facebook_feed.update]"}
            {/if}
        {/capture}
        {$but_href = fn_get_storefront_url()}
    {/if}
{/capture}
{include file="common/mainbox.tpl" title="Facebook Feed Product" content=$smarty.capture.mainbox buttons=$smarty.capture.buttons}
<style>
    .alert-disable {
        display:none;
    }
    .error > .alert-disable {
        display:block;
    }
    #result_link {
        font-size: 15px;
        padding-top: 20px;
    }
    a.disabled_button, a.disabled_button:hover, a.disabled_button:focus, a.disabled_button:active:focus, a.disabled_button:active:hover {
        color: #000;
        cursor: unset;
        background-color: #a8afb3;
        border-color: #a8afb3;
        outline: 0px auto -webkit-focus-ring-color;
        text-shadow: unset;
        background-repeat: none;
        background-image: none;
    }

    ul.ui-autocomplete {
        height: 75%;
        overflow: auto;
    }

    .clear_btn {
        position: absolute;
        right: 5%;
        bottom: 18%;
        border: 1px solid #ff3921;
        padding: 5px;
        border-radius: 8px;
        background-color: #ff3921;
        color: white;
        font-weight: bold;
        cursor: pointer;
    }
    .memcached_flush {
        cursor: pointer;
        margin: 5px 0;
        color:green;
    }
    .memcached_flush:hover {
        font-size: 18px
    }
</style>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $('#ajax_loading_box2').addClass("hidden");
    });
    let Generation = {
        'link':function () {
            let host_url = '{$pinta_facebook_feed.root_link}&action=generate{$company_get_id}';
            let cur = "&cur=";
            let lang = "&lang=";
            let customer_curr = Generation.get('generation_curr');
            let customer_lang = Generation.get('generation_lang');
            if ((customer_curr != 0) && (customer_lang != 0)) {
                host_url += lang + customer_lang + cur + customer_curr;
                let a = document.createElement('a');
                a.href = host_url;
                a.innerHTML = host_url;
                a.setAttribute('target', "_blank");

                document.getElementById('result_link').textContent = '';
                document.getElementById('result_link').appendChild(a);
            }
        },
        'get':function (name) {
            let element = document.getElementById(name);
            let val = element.value;
            if (val == 0) {
                element.parentNode.classList.add('error');
            } else {
                element.parentNode.classList.remove('error');
            }
            return val;
        }
    };

    $('#company_settings').on('click',function (e) {
        e.preventDefault();
        let link = $(this).attr('data-href');
        let id = document.getElementById('feed_companies').value;
        if (id === '') {
            id = 0;
        }
        location.href = link + id;
    });

    $('#generate_page').removeClass("cm-submit");
    $('#generate_file').removeClass("cm-submit");
    var elementis;
    var category_id;
    var company_id = $('#company_id').val();

    var google_main_category_id;
    var arr = $('input[name^=google_main_category_for_category]');
    var a = 0;
    for (var i = 0; i < arr.length; i++) {
        let item = arr[i];
        if (item.value) {
            a++;
        }
    }
    $('div.memcached_flush').on('click',function () {
        $.ceAjax('request', fn_url("pinta_facebook_feed.memcached_flush"), {
            method: 'POST',
            data: {
                flush: true,
            },
            callback: function() {}
        });
    });
    if (a) {
        $('#generate_page').removeClass("disabled_button");
        $('#generate_file').removeClass("disabled_button");
    } else if ($('input[name^=feed_turn_off_categories]').is(":checked")) {
        $('#generate_page').removeClass("disabled_button");
        $('#generate_file').removeClass("disabled_button");
    } else {
        $('#generate_page').addClass("disabled_button");
        $('#generate_file').addClass("disabled_button");
    }
    $('.clear_btn').on('click', function () {
        category_id = $(this).attr('data-category_id');
        $.ceAjax('request', fn_url('pinta_facebook_feed.clear_category?category_id=' + category_id + '&company_id=' + company_id), {
            method: 'POST',
            callback: function (data) {
                if (data.clear_category) {
                    $('input[data-category_id="' + category_id + '"]').each(function () {
                        $(this).val('');
                        if (typeof $(this).attr('data-main_category_id') !== "undefined") {
                            $(this).attr('data-main_category_id', '');
                            $(this).attr('disabled', 'disabled');
                        }
                    });
                }
            }
        });
    });
    $(document).on("click", 'input[name^=google_main_category_for_category]', function (e) {
        e.preventDefault();
        $(this).autocomplete({
            source: function (request, response) {
                category_id = $(this).attr('data-category_id');
                elementis = $(this.element);
                google_main_category_id = elementis.attr('data-main_category_id');
                if (typeof google_main_category_id == "undefined") {
                    google_main_category_id = 0;
                }
                var q = encodeURIComponent(request.term);
                if (q == '') {
                    q = ' ';
                }
                $.ceAjax('request', fn_url('pinta_facebook_feed.autocomplete?q=' + q + '&google_main_category_id=' + google_main_category_id + '&company_id='+ company_id), {
                    method: "POST",
                    callback: function (data) {
                        response(data.autocomplete);
                        var in_w = $('input[name="google_main_category_for_category_' + category_id + '"]').outerWidth();
                        $("ul.ui-autocomplete").css('width', in_w + 'px');
                    }
                });
            },
            minLength: 0,
            scroll: true,
            select: function (event, data) {
                category_id = $(this).attr('data-category_id');

                if (typeof data.item == 'null') {
                    google_main_category_id = 0;
                } else {
                    google_main_category_id = data.item.google_category_id;
                }
                $('input[name="googlecategory_' + category_id + '"]').val('');
                $.ceAjax('request', fn_url("pinta_facebook_feed.save_main_google_category"), {
                    method: 'POST',
                    data: {
                        category_id: category_id,
                        google_main_category_id: google_main_category_id,
                        company_id: company_id
                    },
                    callback: function () {
                        if (google_main_category_id != "") {
                            $('input[name="googlecategory_' + category_id + '"]').prop('disabled', false);
                            $('input[name="googlecategory_' + category_id + '"]').attr('data-main_category_id', google_main_category_id);
                        }
                        if (google_main_category_id == "") {
                            $('input[name="googlecategory_' + category_id + '"]').prop('disabled', true);
                            $('input[name="googlecategory_' + category_id + '"]').removeAttr('data-main_category_id');
                            $('input[name="googlecategory_' + category_id + '"]').removeAttr('value');
                        }
                        var a = 0;
                        for (var i = 0; i < arr.length; i++) {
                            let item = arr[i];
                            if (item.value) {
                                a++;
                            }
                        }

                        if (a) {
                            $('#generate_page').attr("href", location.origin + "/index.php?dispatch=pinta_facebook_feed&action=generate");
                            $('#generate_page').removeClass("disabled_button");
                            $('#generate_file').attr("href", location.origin + "/index.php?dispatch=pinta_facebook_feed&action=save{$company_name_link nofilter}");
                            $('#generate_file').removeClass("disabled_button");
                        } else {
                            $('#generate_page').removeAttr("href");
                            $('#generate_page').addClass("disabled_button");
                            $('#generate_file').removeAttr("href");
                            $('#generate_file').addClass("disabled_button");
                        }
                    },
                });
            }
        }).focus(function () {
            $(this).autocomplete("search", "");
        });
    });


    $(document).on('click', 'input[name^=googlecategory_]', function (e) {
        e.preventDefault();
        $(this).autocomplete({
            minLength: 0,
            scroll: true,
            source: function (request, response) {
                elementis = $(this.element);
                google_main_category_id = elementis.attr('data-main_category_id');
                var q = encodeURIComponent(request.term);
                if (q == '') {
                    q = ' ';
                }
                $.ceAjax('request', fn_url('pinta_facebook_feed.autocomplete?q=' + q + '&google_main_category_id=' + google_main_category_id + '&company_id=' + company_id), {
                    method: "POST",
                    callback: function (data) {
                        response(data.autocomplete);
                        var in_w = $('input[name="googlecategory_' + elementis.attr('data-category_id') + ']"').outerWidth();
                        $("ul.ui-autocomplete").css('width', in_w + 'px');
                    }
                });
            },
            select: function (event, data) {
                $.ceAjax('request', fn_url("pinta_facebook_feed.save_google_category"), {
                    method: 'POST',
                    data: {
                        category_id: elementis.attr('data-category_id'),
                        google_category_id: data.item.google_category_id,
                        company_id: company_id
                    },
                    callback: function () {
                        $('input[name=googlecategory_' + elementis.attr('data-category_id') + ']').attr('value', data.item.label);
                    },
                });

            },
        }).focus(function () {
            $(this).autocomplete("search", "");
        });
    });
</script>
