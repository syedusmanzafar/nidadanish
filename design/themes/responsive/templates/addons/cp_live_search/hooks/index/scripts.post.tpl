<script type="text/javascript">
    var letters_to_start = {$addons.cp_live_search.letters_to_start};
    var ls_search_delay = {$addons.cp_live_search.search_delay};
    var ls_show_ajax_loader = {if $addons.cp_live_search.show_ajax_loader == "Y"}false{else}true{/if};
    var ls_search_motivation = {if $cp_search_motivation}{$cp_search_motivation nofilter}{else}[]{/if};
</script>

{script src="js/addons/cp_live_search/func.js"}
{script src="js/addons/cp_live_search/jquery.highlight.js"}
{script src="js/addons/cp_live_search/typed.min.js"}

{if $runtime.controller == "products" && $runtime.mode == "search"}
    {script src="js/addons/cp_live_search/change_links.js"}
{/if}