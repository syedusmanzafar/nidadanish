{if $no_popup}
    {capture name="mainbox"}

{/if}
    {if !$id}
        {assign var="id" value=$phrase.phrase_id|default:0}
    {/if}

    <div id="content_group{$id}">
        <form action="{""|fn_url}" method="post" name="cp_search_phrase_form_{$id}" class="form-horizontal form-edit">
            <input type="hidden" name="phrase_id" value="{$id}" />
            <input type="hidden" name="data[company_id]" value="{$runtime.company_id}" />
            {*
            <div class="control-group">
                <label class="control-label" for="cp_phrase_searchs{$id}">{__("cp_search_phrases")}:</label>
                <div class="controls">
                    <textarea name="data[searchs]" cols="35" rows="5" class="input-large" id="cp_phrase_searchs{$id}">{$phrase.searchs}</textarea>
                </div>
            </div>
            *}
            <div class="control-group">
                <label class="control-label" for="cp_phrase_searchs{$id}">{__("cp_search_phrases")}:</label>
                <div class="controls">
                    <input type="hidden" name="data[searchs]" value="" />
                    <div class="object-selector cp-phrases-selector-wrap">
                        <select id="cp_phrase_searchs{$id}"
                                class="cm-object-selector cp-phrases-selector"
                                name="data[searchs][]"
                                multiple
                                data-ca-load-via-ajax="true"
                                data-ca-ajax-delay="200"
                                data-ca-placeholder="{__("search")}"
                                data-ca-enable-search="true"
                                data-ca-enable-images="true"
                                data-ca-close-on-select="false"
                                data-ca-data-url="{"cp_search_phrases.phrases_list"|fn_url nofilter}">
                            {foreach from=$phrase.searchs item="phrase_search"}
                                <option value="{$phrase_search}" selected="selected">{$phrase_search}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="control-group">
                <label for="cp_phrase_priority{$id}" class="control-label">{__("priority")}:</label>
                <div class="controls">
                    <input id="cp_phrase_priority{$id}" type="text" class="input-mini" name="data[priority]" value="{$phrase.priority|default:0}">
                </div>
            </div>

            {include file="common/select_status.tpl" input_name="data[status]" id="cp_phrase_status`$id`" obj_id=$id obj=$phrase}

            {include file="common/subheader.tpl" title=__("cp_suggestions")}

            <div class="control-group">
                <label class="control-label" for="cp_phrase_searchs{$id}">{__("cp_suggestions")}:</label>
                <div class="controls">
                    <textarea name="data[suggestions]" cols="35" rows="5" class="input-large" id="cp_phrase_searchs{$id}">{$phrase.suggestions}</textarea>
                </div>
            </div>

            {include file="common/subheader.tpl" title=__("cp_featured_products")}

            {include file="pickers/products/picker.tpl" positions=true data_id="cp_phrase_products`$id`" item_ids=$phrase.product_ids input_name="data[product_ids]" type="mixed" no_container=false type="links" display=true meta="pull-right"}

            {if !$no_popup}
                <div class="buttons-container">
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[cp_search_phrases.update]" cancel_action="close" save=$id}
                </div>
            {/if}
        </form>
    <!--content_group{$id}--></div>

{if $no_popup}

    {/capture}

    {capture name="buttons"}
        {include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="cp_search_phrase_form_`$id`" but_name="dispatch[cp_search_phrases.update]" save=$id}
    {/capture}

    {capture name="title"}{__("cp_search_phrases")}{/capture}

    {include file="common/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons select_languages=true}
{/if}
