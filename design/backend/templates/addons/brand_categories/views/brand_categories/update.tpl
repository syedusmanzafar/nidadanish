{capture name="mainbox"}
    {capture name="tabsbox"}
    <div id="content_general">
        <form action="{""|fn_url}" method="post" enctype="multipart/form-data" name="brand_form" class="form-horizontal form-edit cm-disable-empty-files  cm-processed-form cm-check-changes">
            <h4 class="subheader">{$data.name}</h4>
            <input type="hidden" name="brand_data[id]" value="{$data.id|default:0}">
            <div class="control-group">
                <label class="control-label cm-required" for="elm_name">{__("name")}:</label>
                <div class="controls">
                    <input id="elm_name" type="text" name="brand_data[name]" value="{$data.name}" class="input-medium">
                </div>
            </div>
			
			{if $data.id > 0}
			{$brands = fn_get_all_brands_cat()}
			<table class="table table-middle">
			<thead>
				<tr>
					<th class="left" width="1%"></th>
					<th class="left" width="1%">Pos</th>
					<th class="left" width="3%">#</th>
					<th class="nowrap left" width="30%">Name</th>
				</tr>
			</thead>

			{foreach from=$brands item="brand"}
				{if $brand.variant_id>0}
					<tr class="cm-row-status-{$item.status|lower}">
						<td class="left" width="1%">
							<input type="hidden" name="brand_data[variants][{$brand.variant_id}][checked]" value="0" class="checkbox cm-item cm-item-status-a user-success">
							<input type="checkbox" name="brand_data[variants][{$brand.variant_id}][checked]" value="1" {if $brand.category_id == $data.id} checked {/if} class="checkbox cm-item cm-item-status-a user-success">
						</td>
						<td class="left" width="3%"><input type="text" name="brand_data[variants][{$brand.variant_id}][pos]" value="{$brand.pos}" size="4" class="input-micro input-hidden user-success"></td>
						<td class="left">#{$brand.variant_id}</td>
						<td class="left">{$brand.variant}</td>

					</tr>
				{/if}
			{/foreach}
			{/if}
			</table>
        </form>
    </div>
    {/capture}
    {include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox group_name=$runtime.controller active_tab=$smarty.request.selected_section track=true}
{/capture}

{capture name="mainbox_title"}
    {__("brand_categories")}
{/capture}

{capture name="buttons"}
    {include file="buttons/save_cancel.tpl" but_role="submit-link" but_name="dispatch[brand_categories.update]" but_target_form="brand_form" save=true}
{/capture}

{include file="common/mainbox.tpl"
    title=$smarty.capture.mainbox_title
    content=$smarty.capture.mainbox
    buttons=$smarty.capture.buttons
}