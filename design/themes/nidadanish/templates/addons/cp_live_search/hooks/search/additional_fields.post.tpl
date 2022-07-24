{$search_input_id = $search_input_id|default:$smarty.capture.search_input_id}
{include file="addons/cp_live_search/components/result.tpl" search_input_id=$search_input_id}

{if $addons.cp_live_search.show_ajax_loader == "Y"}
    <div id="cp_ls_ajax_loader{$search_input_id}" class="live-search-loader-wrap" style="display: none;">
        <img src="{$images_dir}/addons/cp_live_search/loaders/{$addons.cp_live_search.ajax_loader}.png">
    </div>
{/if}