{if $runtime.controller == "products" && $runtime.mode == "search"
    && $block.type == "main" && $cp_search_result_title
}
    {assign var="title_extra" value="{__("products_found")}: `$search.total_items`"}
    <span class="ty-mainbox-title__left">{$cp_search_result_title}</span><span class="ty-mainbox-title__right" id="products_search_total_found_{$block.block_id}">{$title_extra nofilter}<!--products_search_total_found_{$block.block_id}--></span>
{/if}