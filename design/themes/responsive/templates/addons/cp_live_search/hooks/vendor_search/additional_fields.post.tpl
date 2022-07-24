{assign var="vendor_input_id" value="_vendor`$company_id`"}
{include file="addons/cp_live_search/components/result.tpl" search_input_id=$vendor_input_id company_id=$company_id}

{if $addons.cp_live_search.show_ajax_loader == "Y"}
    <div id="cp_ls_ajax_loader{$vendor_input_id}" class="live-search-loader-wrap" style="display: none;">
        <img src="{$images_dir}/addons/cp_live_search/loaders/{$addons.cp_live_search.ajax_loader}.png">
    </div>
{/if}
