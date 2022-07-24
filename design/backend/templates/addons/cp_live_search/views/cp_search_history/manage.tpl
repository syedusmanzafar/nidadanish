{capture name="mainbox"}
{capture name="sidebar"}
    {include file="addons/cp_live_search/components/history_search.tpl" dispatch="cp_search_history.manage"}
{/capture}

<form action="{""|fn_url}" method="post" name="search_history_form">

    {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

    {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
    {assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
    {assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}
    
    <input type="hidden" name="company_id" value="{$runtime.company_id}"/>
    {if $selected_storefront_id}
        <input type="hidden" name="storefront_id" value="{$selected_storefront_id}" />
    {/if}

    {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}

    {if $history}
    <div id="content_history">
        {if $search.section == "all"}
            <table width="100%" class="table table-middle first-transition">
                <thead>
                    <tr>
                        <th  class="left">{include file="common/check_items.tpl"}</th>
                        <th class="left" width="20%"><a class="cm-ajax" href="{"`$c_url`&sort_by=search&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_search_phrase")}{if $search.sort_by == "search"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th class="center" width="15%"><a class="cm-ajax" href="{"`$c_url`&sort_by=result&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("result")}{if $search.sort_by == "result"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th class="center" width="15%"><a class="cm-ajax" href="{"`$c_url`&sort_by=search_type&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("type")}{if $search.sort_by == "search_type"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th class="left" width="20%"><a class="cm-ajax" href="{"`$c_url`&sort_by=timestamp&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("time")}{if $search.sort_by == "timestamp"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th class="center" width="4%">{__("language")}</th>
                        <th class="center" width="10%">{__("cp_product_clicks")}</th>
                        <th width="10%">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$history item="history_item"}
                    <tr class="cm-row-status">
                        <td class="left"><input type="checkbox" name="search_ids[]" value="{$history_item.search_id}" class="cm-item" /></td>    
                        <td>{$history_item.search}</td>
                        <td class="center">{$history_item.result}</td>
                        <td class="center">{if $history_item.search_type == "S"}{__("simple_search")}{else}{__("cp_live_search")}{/if}</td>
                        <td>
                            {$history_item.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
                        </td>
                        <td class="center">{$history_item.lang_code}</td>
                        <td class="center">
                            {if $history_item.product_clicks}
                                {include file="common/popupbox.tpl" id="search_info`$history_item.search_id`" text=__("cp_product_clicks") act="edit" link_text=$history_item.product_clicks no_icon_link=true href="cp_search_history.info&search_id=`$history_item.search_id`"|fn_url}
                            {else}
                                {$history_item.product_clicks}
                            {/if}
                        </td>
                        <td width="5%" class="center">
                            {capture name="tools_items"}
                                {assign var="current_redirect_url" value=$config.current_url|escape:url}
                                <li>{btn type="list" href="cp_search_history.delete?search_id=`$history_item.search_id`&redirect_url=`$current_redirect_url`" class="cm-confirm" text={__("delete")}}</li>
                            {/capture}
                            <div class="hidden-tools">
                                {dropdown content=$smarty.capture.tools_items}
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {elseif $search.section == "phrase_group"}
            <table width="100%" class="table table-middle first-transition">
                <thead>
                    <tr>
                        <th class="left" width="40%"><a class="cm-ajax" href="{"`$c_url`&sort_by=search&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_search_phrase")}{if $search.sort_by == "search"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th class="center" width="30%"><a class="cm-ajax" href="{"`$c_url`&sort_by=count&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("count")}{if $search.sort_by == "count"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th class="center" width="30%">{__("cp_product_clicks")}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$history item="history_item" key="key"}
                    <tr class="cm-row-status">  
                        <td>{$history_item.search}</td>
                        <td class="center">{$history_item.count}</td>
                        <td class="center">
                            {if $history_item.product_clicks}
                                {include file="common/popupbox.tpl" id="search_info`$key`" text=__("cp_product_clicks") act="edit" link_text=$history_item.product_clicks no_icon_link=true href="cp_search_history.info&search=`$history_item.search`"|fn_url}
                            {else}
                                {$history_item.product_clicks}
                            {/if}
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        {elseif $search.section == "product_clicks"}
            <table width="100%" class="table table-middle first-transition">
                <thead>
                    <tr>
                        <th width="5%">&nbsp;</th>
                        <th class="left" width="40%">{__("product")}</th>
                        <th class="center" width="30%"><a class="cm-ajax" href="{"`$c_url`&sort_by=product_clicks&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_product_clicks")}{if $search.sort_by == "product_clicks"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th class="center" width="30%">{__("cp_search_phrases")}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$history item="history_item" key="key"}
                    <tr class="cm-row-status">
                        <td>
                            {include file="common/image.tpl" image=$history_item.main_pair.icon|default:$history_item.main_pair.detailed image_id=$history_item.main_pair.image_id image_width=$settings.Thumbnails.product_admin_mini_icon_width image_height=$settings.Thumbnails.product_admin_mini_icon_height href="products.update?product_id=`$history_item.product_id`"|fn_url}
                        </td>
                        <td>
                            <div><a href="{"products.update?product_id=`$history_item.product_id`"|fn_url}">{$history_item.product nofilter}</a></div>
                            <div><span class="product-code__label">{$history_item.product_code}</span></div>
                        </td>
                        <td class="center">{$history_item.product_clicks}</td>
                        <td class="center">
                            {if $history_item.phrases_count}
                                {include file="common/popupbox.tpl" id="search_info`$key`" text=__("cp_search_phrases") act="edit" link_text=$history_item.phrases_count no_icon_link=true href="cp_search_history.info&product_id=`$history_item.product_id`&phrases=1"|fn_url}
                            {else}
                                {$history_item.phrases_count}
                            {/if}
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        {/if}
    <!--content_history--></div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    <div class="clearfix">
        {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
    </div>
</form>
{/capture}

{capture name="buttons"}
    {if $history && $search.section == "all"}
    <div class="cm-tab-tools pull-right shift-left" id="tools_backup">
        {include file="buttons/button.tpl" but_text=__("clear_history") but_name="dispatch[cp_search_history.clear]" but_target_form="search_history_form" but_meta="cm-comet cm-confirm" but_role="submit-link"}
    </div>
    {/if}

    {capture name="tools_list"}
        {if $history && $search.section == "all"}
             <li>{btn type="delete_selected" dispatch="dispatch[cp_search_history.m_delete]" form="search_history_form"}</li>
             <li>{btn type="list" href="exim.export?section=cp_ls_search_history" text={__("export")}}</li>
        {/if}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}

{include file="common/mainbox.tpl"
    title=__("search_history")
    sidebar=$smarty.capture.sidebar
    content=$smarty.capture.mainbox
    buttons=$smarty.capture.buttons
    content_id="manage_history_search"
    select_storefront=true
    show_all_storefront=true
    storefront_switcher_param_name="storefront_id"    
}
