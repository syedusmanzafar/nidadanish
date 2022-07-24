{if $no_popup}
    {capture name="mainbox"}

{/if}
    {if !$id}
        {assign var="id" value=$synonym.synonym_id|default:0}
    {/if}

    <div id="content_group{$id}">
        <form action="{""|fn_url}" method="post" name="cp_search_synonym_form_{$id}" class="form-horizontal form-edit">
            <input type="hidden" name="synonym_id" value="{$id}" />
            <input type="hidden" name="data[company_id]" value="{$runtime.company_id}" />
            
            <div class="control-group">
                <label class="control-label" for="cp_synonym_searchs{$id}">{__("cp_ls_synonym_value")}:</label>
                <div class="controls">
                    <textarea name="data[value]" cols="20" rows="3" class="input-large" id="cp_synonym_text{$id}">{$synonym.value}</textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="cp_synonym_variant{$id}">{__("cp_search_synonyms")}:</label>
                <div class="controls">
                    <input type="hidden" name="data[variants]" value="" />
                    <div class="object-selector cp-phrases-selector-wrap">
                        <select id="cp_synonym_variant{$id}"
                                class="cm-object-selector cp-phrases-selector"
                                name="data[variants][]"
                                multiple
                                data-ca-load-via-ajax="true"
                                data-ca-ajax-delay="200"
                                data-ca-placeholder="{__("search")}"
                                data-ca-enable-search="true"
                                data-ca-enable-images="true"
                                data-ca-close-on-select="false"
                                data-ca-data-url="{"cp_search_synonyms.synonyms_list?synonym_id=`$id`"|fn_url nofilter}">
                            {foreach from=$synonym.variants item="variant"}
                                <option value="{$variant}" selected="selected">{$variant}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>

            {include file="common/select_status.tpl" input_name="data[status]" id="cp_synonym_status`$id`" obj_id=$id obj=$synonym}

            {if !$no_popup}
                <div class="buttons-container">
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[cp_search_synonyms.update]" cancel_action="close" save=$id}
                </div>
            {/if}
        </form>
    <!--content_group{$id}--></div>

{if $no_popup}

    {/capture}

    {capture name="buttons"}
        {include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="cp_search_synonym_form_`$id`" but_name="dispatch[cp_search_synonyms.update]" save=$id}
    {/capture}

    {capture name="title"}{__("cp_search_synonyms")}{/capture}

    {include file="common/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons select_languages=true}
{/if}
