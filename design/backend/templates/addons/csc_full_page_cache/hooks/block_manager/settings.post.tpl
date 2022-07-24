{if $block.type!="main" && $block.type!="breadcrumbs"}
<div class="control-group">
    <label class="control-label" for="block_{$html_id}_fpc_exclude_cache">{__("fpc_exclude_cache")}:</label>
    <div class="controls">
        <input type="hidden" name="block_data[fpc_exclude_cache]" value="N">
        <label class="checkbox inline" for="block_{$html_id}_fpc_exclude_cache_1">
            <input type="checkbox" name="block_data[fpc_exclude_cache]" id="block_{$html_id}_fpc_exclude_cache_1" value="Y" {if $block.fpc_exclude_cache=="Y"}checked="checked"{/if} />           
        </label>
        
    </div>
</div>
{/if}
