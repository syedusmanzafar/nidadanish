{capture name="mainbox"}
<form action="{""|fn_url}" method="post" class="form-horizontal form-edit" name="cp_search_motivation_form" enctype="multipart/form-data">
    <input type="hidden" name="fake" value="1"/>
    <input type="hidden" name="company_id" value="{$runtime.company_id}"/>
    <input type="hidden" name="object_id" value="0"/>
    <input type="hidden" name="object_type" value="D"/>
    {if $selected_storefront_id}
        <input type="hidden" name="storefront_id" value="{$selected_storefront_id}" />
    {/if}

    <div id="cp_live_seach_setting">
        <div class="control-group">
            <label for="elm_search_motivation" class="control-label">{__("cp_motivation_phrases")}:</label>
            <div class="controls">
                <textarea id="elm_search_motivation" name="settings[content]" cols="55" rows="6" class="input-large">{$search_motivation}</textarea>
            </div>
        </div>
    </div>

    {capture name="buttons"}
        {include file="buttons/save.tpl" but_name="dispatch[cp_live_search.motivation_update]" but_role="submit-link" but_target_form="cp_search_motivation_form"}
    {/capture}
</form>
{/capture}

{assign var="title" value={__("cp_search_motivation")}}

{include file="common/mainbox.tpl"
    title=$title content=$smarty.capture.mainbox
    buttons=$smarty.capture.buttons
    select_languages=true
    select_storefront=true
    show_all_storefront=false
    storefront_switcher_param_name="storefront_id"
}
