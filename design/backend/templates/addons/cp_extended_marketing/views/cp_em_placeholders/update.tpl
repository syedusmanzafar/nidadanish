{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" class="form-horizontal form-edit" name="placeholder_form" enctype="multipart/form-data">
    <input type="hidden" class="cm-no-hide-input" name="fake" value="1" />
    <input type="hidden" name="placeholder_id" value="{$smarty.request.placeholder_id}" />

    <div class="control-group">
        <label for="name" class="control-label cm-required">{__("name")}</label>
        <div class="controls">
            <input class="input-large" type="text" name="placeholder_data[name]" id="name" size="55" value="{$placeholder_data.name}" />
        </div>
    </div>
    {if "ULTIMATE"|fn_allowed_for}
        {include file="views/companies/components/company_field.tpl"
            name="placeholder_data[company_id]"
            id="elm_notice_data_`$id`"
            selected=$placeholder_data.company_id
        }
    {/if}
    <div class="control-group">
        <label for="image_width" class="control-label cm-required">{__("cp_em_image_width")}"</label>
        <div class="controls">
            <input class="input-small" type="text" name="placeholder_data[image_width]" id="image_width" size="55" value="{$placeholder_data.image_width|default:$settings.Thumbnails.product_lists_thumbnail_width}" />
        </div>
    </div>
    <div class="control-group">
        <label for="image_height" class="control-label cm-required">{__("cp_em_image_height")}:</label>
        <div class="controls">
            <input class="input-small" type="text" name="placeholder_data[image_height]" id="image_height" size="55" value="{$placeholder_data.image_height|default:$settings.Thumbnails.product_lists_thumbnail_height}" />
        </div>
    </div>
    <div class="control-group">
        <label for="placeholder" class="control-label cm-required">{__("cp_em_placeholder_txt")}{if $need_tooltip_tpl}{include file="common/tooltip.tpl" tooltip={__("ttc_cp_em_viewed_period_send")} params="ty-subheader__tooltip"}{/if}:</label>
        <div class="controls">
            <input class="input-short" type="text" name="placeholder_data[placeholder]" id="placeholder" size="55" placeholder="placeholder_1" value="{$placeholder_data.placeholder}" />
        </div>
    </div>
    
    {include file="common/subheader.tpl" title=__("products") target="#assign_to"}
    <div id="assign_to" class="collapse in">
        {include file="pickers/products/picker.tpl" input_name="placeholder_data[product_ids]" item_ids=$placeholder_data.product_ids type="links" placement="right"}
    </div>

    {capture name="buttons"}
        {if !$placeholder_data.placeholder_id}
            {include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="placeholder_form" but_name="dispatch[cp_em_placeholders.update]"}
        {else}
            {include file="buttons/save_cancel.tpl" but_name="dispatch[cp_em_placeholders.update]" but_role="submit-link" but_target_form="placeholder_form" save=$placeholder_data.placeholder_id}
        {/if}
    {/capture}
    </form>
{/capture}

{if $placeholder_data.name}
    {assign var="title" value="{__("cp_em_editing_placeholder")}: `$placeholder_data.name`"}
{else}
    {assign var="title" value=__("cp_em_new_placeholder")}
{/if}

{include file="common/mainbox.tpl" title=$title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons select_languages=true}
