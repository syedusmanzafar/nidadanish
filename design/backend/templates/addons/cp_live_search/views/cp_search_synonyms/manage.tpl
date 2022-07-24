{capture name="mainbox"}
    {assign var="c_icon" value="<i class=\"icon-`$search.sort_order_rev`\"></i>"}
    {assign var="c_dummy" value="<i class=\"icon-dummy\"></i>"}

    <form action="{""|fn_url}" method="post" name="cp_search_synonyms_list_form">
        <input type="hidden" name="fake" value="1" />

        {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id="pagination_contents"}

        {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
        {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}

        <div class="items-container" id="cp_search_synonyms_list">
            <div class="table-responsive-wrapper">
            {if $search_synonyms}
                <table class="table table-middle table-objects table-striped">
                    <thead>
                        <tr>
                            <th width="1%" class="center">
                                {include file="common/check_items.tpl"}
                            </th>
                            <th width="20%" class="nowrap">
                                <a class="cm-ajax" href="{"`$c_url`&sort_by=value&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_ls_synonym_value")}{if $search.sort_by == "value"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                            </th>
                            <th width="60%" class="nowrap">
                                {__("cp_search_synonyms")}
                            </th>
                            <th class="right">&nbsp;</th>
                            <th width="10%" class="right">
                                <a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by == "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$search_synonyms item="synonym" name="cp_search_synonyms"}
                        {$id = $synonym.synonym_id}
                        <tr class="cm-row-item cm-row-status-{$synonym.status|lower}">
                            <td width="1%" class="center">
                                <input type="checkbox" name="synonym_ids[]" value="{$synonym.synonym_id}" class="cm-item" />
                            </td>
                            <td>
                                <input type="hidden" name="search_synonyms[{$id}][synonym_id]" value="{$synonym.synonym_id}" />
                                <textarea name="search_synonyms[{$id}][value]" cols="20" rows="3" class="input row-status" id="cp_synonym_phrase{$id}">{$synonym.value}</textarea>
                            </td>
                            <td>
                                <div class="object-selector cp-phrases-selector-wrap row-status">
                                    <select id="cp_synonym_variant{$id}"
                                            class="cm-object-selector cp-phrases-selector"
                                            name="search_synonyms[{$id}][variants][]"
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
                            </td>
                            <td class="right nowrap">
                                <div class="pull-right hidden-tools">
                                    {capture name="items_tools"}
                                        <li>{btn type="text" text=__("delete") href="cp_search_synonyms.delete?synonym_id=`$synonym.synonym_id`" class="cm-confirm cm-ajax cm-ajax-force cm-ajax-full-render" data=["data-ca-target-id" => cp_search_synonyms_list] method="POST"}</li>
                                    {/capture}
                                    {dropdown content=$smarty.capture.items_tools class="dropleft"}
                                </div>
                            </td>
                            <td width="10%">
                                <div class="pull-right nowrap">
                                    {include file="common/select_popup.tpl" popup_additional_class="dropleft" id=$id status=$synonym.status hidden=false object_id_name="synonym_id" table="cp_search_synonyms"}
                                </div>
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            {else}
                <p class="no-items">{__("no_data")}</p>
            {/if}
            </div>
        <!--cp_search_synonyms_list--></div>
        {include file="common/pagination.tpl" div_id="pagination_contents"}

        {capture name="buttons"}
            {capture name="tools_list"}
                {if $search_synonyms}
                    <li>{btn type="delete_selected" dispatch="dispatch[cp_search_synonyms.m_delete]" form="cp_search_synonyms_list_form"}</li>
                {/if}
            {/capture}
            {dropdown content=$smarty.capture.tools_list}

            {include file="buttons/save.tpl" but_name="dispatch[cp_search_synonyms.m_update]" but_role="action" but_target_form="cp_search_synonyms_list_form" but_meta="btn-primary cm-submit"}
        {/capture}

        {capture name="adv_buttons"}
            {capture name="add_new_picker"}
                {include file="addons/cp_live_search/views/cp_search_synonyms/update.tpl" synonym=[] no_popup=false id=0}
            {/capture}
            {include file="common/popupbox.tpl" id=0 text=__("cp_ls_synonym") content=$smarty.capture.add_new_picker title=__("add") act="general" icon="icon-plus"}
        {/capture}

        {capture name="sidebar"}
            {include file="addons/cp_live_search/views/cp_search_synonyms/components/search_form.tpl" dispatch="cp_search_synonyms.manage"}
        {/capture}
    </form>
{/capture}

{capture name="title"}{__("cp_search_synonyms")}{/capture}

{include file="common/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox select_languages=true buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons sidebar=$smarty.capture.sidebar}
