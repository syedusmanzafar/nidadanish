{capture name="mainbox"}
    {assign var="c_icon" value="<i class=\"icon-`$search.sort_order_rev`\"></i>"}
    {assign var="c_dummy" value="<i class=\"icon-dummy\"></i>"}

    <form action="{""|fn_url}" method="post" name="cp_search_phrases_list_form">
        <input type="hidden" name="fake" value="1" />

        {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id="pagination_contents"}

        {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
        {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}

        <div class="items-container" id="cp_search_phrases_list">
        {if $search_phrases}
            <div class="table-responsive-wrapper">
                <table class="table table-middle table-objects table-striped">
                    <thead>
                        <tr>
                            <th width="1%" class="center">{include file="common/check_items.tpl"}</th>
                            <th width="10%" class="nowrap">
                                <a class="cm-ajax" href="{"`$c_url`&sort_by=priority&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("priority")}{if $search.sort_by == "priority"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                            </th>
                            <th width="60%" class="nowrap">
                                {__("cp_search_phrases")}
                            </th>
                            <th width="5%" class="right">&nbsp;</th>
                            <th width="5%" class="right">
                                <a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by == "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$search_phrases item=phrase name="cp_search_phrases"}
                            {assign var="edit_href" value="cp_search_phrases.update?phrase_id=`$phrase.phrase_id`"|fn_url}
                            {assign var="id" value=$phrase.phrase_id}
                            <tr class="cm-row-item cm-row-status-{$phrase.status|lower}">
                                <td class="center"><input type="checkbox" name="phrase_ids[]" value="{$phrase.phrase_id}" class="cm-item" /></td>
                                <td>
                                    <input type="hidden" name="search_phrases[{$id}][phrase_id]" size="8" value="{$id}">
                                    <input type="text" name="search_phrases[{$id}][priority]" size="8" value="{$phrase.priority|default:0}" class="input-mini">
                                </td>
                                <td>
                                    <a class="row-status cm-external-click" data-ca-external-click-id="opener_group{$id}">{$phrase.searchs}</a>
                                </td>
                                {*
                                <td>
                                    <div class="object-group-link-wrap">
                                        <a class="row-status cm-external-click link" data-ca-external-click-id="opener_group{$id}">{$phrase.name}</a>
                                    </div>
                                </td>
                                *}
                                <td class="right nowrap">
                                    <div class="pull-right hidden-tools">
                                        {capture name="items_tools"}
                                            <li>{*<a href="{$edit_href}">{__("edit")}</a>*}
                                            {include file="common/popupbox.tpl" id="group`$id`" text=__("cp_search_phrase") act="edit" link_text=__("edit") no_icon_link=true href=$edit_href phrase=$phrase}</li>
                                            <li>{btn type="text" text=__("delete") href="cp_search_phrases.delete?phrase_id=`$phrase.phrase_id`" class="cm-confirm cm-ajax cm-ajax-force cm-ajax-full-render" data=["data-ca-target-id" => cp_search_phrases_list] method="POST"}</li>
                                        {/capture}
                                        {dropdown content=$smarty.capture.items_tools class="dropleft"}
                                    </div>
                                </td>
                                <td>
                                    <div class="pull-right nowrap">
                                        {include file="common/select_popup.tpl" popup_additional_class="dropleft" id=$id status=$phrase.status hidden=false object_id_name="phrase_id" table="cp_search_phrases"}
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
        <!--cp_search_phrases_list--></div>
        {include file="common/pagination.tpl" div_id="pagination_contents"}

        {capture name="buttons"}
            {capture name="tools_list"}
                {if $search_phrases}
                    <li>{btn type="delete_selected" dispatch="dispatch[cp_search_phrases.m_delete]" form="cp_search_phrases_list_form"}</li>
                {/if}
            {/capture}
            {dropdown content=$smarty.capture.tools_list}

            {if $search_phrases}
                {include file="buttons/save.tpl" but_name="dispatch[cp_search_phrases.m_update]" but_role="action" but_target_form="cp_search_phrases_list_form" but_meta="btn-primary cm-submit"}
            {/if}
            
        {/capture}

        {capture name="adv_buttons"}
            {capture name="add_new_picker"}
                {include file="addons/cp_live_search/views/cp_search_phrases/update.tpl" phrase=[] template=[] no_popup=false id=0}
            {/capture}
            {include file="common/popupbox.tpl" id=0 text=__("cp_search_phrase") content=$smarty.capture.add_new_picker title=__("add") act="general" icon="icon-plus"}
        {/capture}

        {capture name="sidebar"}
            {include file="addons/cp_live_search/views/cp_search_phrases/components/search_form.tpl" dispatch="cp_search_phrases.manage"}
        {/capture}
    </form>
{/capture}

{capture name="title"}{__("cp_search_phrases")}{/capture}

{include file="common/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox select_languages=true buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons sidebar=$smarty.capture.sidebar}
