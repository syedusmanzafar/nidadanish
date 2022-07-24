{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="ab__pfe_demo_data_form" id="ab__pfe_demo_data_form">
<p>{__("ab__pfe.demodata_description")}</p>
<div class="table-responsive-wrapper">
<table class="table table-middle table-responsive" width="100%">
<thead>
<tr>
<th width="60%">{__("ab__pfe.demodata.table.description")}</th>
<th width="20%">{__("ab__pfe.demodata.table.action")}</th>
</tr>
</thead>
<tbody>
<tr>
<td data-th="{__("ab__pfe.demodata.table.description")}">{__("ab__pfe.demodata.table.add_template")}</td>
<td data-th="{__("ab__pfe.demodata.table.action")}">{btn type="list" class="cm-ajax cm-post btn btn-primary" text=__("add") dispatch="dispatch[ab__pfe.demodata.template]"}</td>
</tr>
<tr>
<td data-th="{__("ab__pfe.demodata.table.description")}">{__("ab__pfe.demodata.table.add_datafeed")}</td>
<td data-th="{__("ab__pfe.demodata.table.action")}">{btn type="list" class="cm-ajax cm-post btn btn-primary" text=__("add") dispatch="dispatch[ab__pfe.demodata.datafeed]"}</td>
</tr>
</tbody>
</table>
</div>
</form>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__product_feed_export"}
{include
file="common/mainbox.tpl"
title_start = __("ab__product_feed_export")|truncate:40
title_end = __("ab__pfe.demodata")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
content_id="ab__pfe_demo_data_form"
select_storefront=true
show_all_storefront=false
}