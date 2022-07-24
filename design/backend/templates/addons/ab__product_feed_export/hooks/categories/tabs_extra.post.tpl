<div id="content_ab__pfe_feature_names">
{capture name="ab__pfe_add_item"}
{include file="addons/ab__product_feed_export/views/ab__pfe_feature_names/update.tpl"
return_url=$config.current_url
category_id=$category_data.category_id
}
{/capture}
<div class="btn-toolbar clearfix cm-toggle-button">
<div class="pull-right shift-left">
{include file="common/popupbox.tpl"
id="ab__pfe_add_item"
content=$smarty.capture.ab__pfe_add_item
text=__("add")
act="create"
but_text=__("add")
icon='icon-plus'
}
</div>
</div>
{if $ab__pfe_feature_names}
<table class="table table-middle cm-progressbar-status" width="100%">
<thead class="cm-first-sibling">
<tr>
<th width="30%">{__("feature")}</th>
<th width="30%">{__("ab__pfe.datafeed")}</th>
<th width="30%">{__("name")}</th>
<th width="10%">&nbsp;</th>
</tr>
</thead>
<tbody>
{foreach from=$ab__pfe_feature_names item="i" key="k"}
<tr class="cm-row-item">
<td>{$i.feature_id|fn_get_feature_name:$smarty.const.DESCR_SL}</td>
<td>{$ab__pfe_datafeeds.{$i.datafeed_id}.name}</td>
<td>{$i.name}</td>
<td class="right nowrap">
<div class="hidden-tools">
{capture name="tools_list"}
<li>{include file="common/popupbox.tpl"
id="ab__pfe_add_item_`$k`"
text=__("edit")
act="edit"
href="ab__pfe_feature_names.update?item_id=`$k`&category_id=`$category_data.category_id`&return_url=`$config.current_url|escape:url`"
}
</li>
<li>{btn type="list" text=__("delete") class="cm-confirm" href="ab__pfe_feature_names.delete?item_id=`$k`&return_url=`$config.current_url|escape:url`"}</li>
{/capture}
{dropdown content=$smarty.capture.tools_list}
</div>
</td>
</tr>
{/foreach}
</tbody>
</table>
{else}
<p class="no-items">{__("no_data")}</p>
{/if}
</div>