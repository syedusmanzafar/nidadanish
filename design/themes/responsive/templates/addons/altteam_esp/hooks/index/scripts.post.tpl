{script src="js/addons/altteam_esp/func.js"}

<script type="text/javascript">
//<![CDATA[
(function(_, $) {
    _.tr({
		price: '{__("price")|escape:"javascript"|default:"Price"}',
		product: '{__("product")|escape:"javascript"|default:"Product"}',
		watch_more: '{__("watch_more")}'
    });
	esp_data = {
		req_page: {$smarty.request.page|default:1},
		num_watch_more: {$addons.altteam_esp.num_watch_more|default:1},
		flag_watch_more: "{$addons.altteam_esp.add_watch_more|default:'N'}",
		up_everywhere: "{$addons.altteam_esp.up_everywhere|default:'N'}",
		only_chosen: "{$addons.altteam_esp.only_chosen|default:'N'}",
		lang: {$ldelim}
				price: '{__("price")|escape:"javascript"|default:"Price"}',
				product: '{__("product")|escape:"javascript"|default:"Product"}',
				watch_more: '{__("watch_more")}',
				loaded_automatically: '{__("items_will_be_loaded_automatically")}'
			{$rdelim}
	};
}(Tygh, Tygh.$));
//]]>
</script>