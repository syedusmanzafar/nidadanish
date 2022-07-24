<div id="content_cp_search_history">

    {if $cp_search_history}
        <table width="100%" class="table table-middle">
            <thead>
                <tr>
                    <th class="left" width="20%">{__("cp_search_phrase")}</th>
                    <th class="center" width="20%">{__("cp_product_clicks")}</th>
                    <th width="40%">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$cp_search_history item="history_item" key="key"}
                <tr class="cm-row-status">  
                    <td>{$history_item.search}</td>
                    <td class="center">{$history_item.product_clicks}</td>
                    <th>&nbsp;</th>
                </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

</div>
