{if ($runtime.company_id && "ULTIMATE"|fn_allowed_for) || "MULTIVENDOR"|fn_allowed_for}
{include file="common/subheader.tpl" title=__("cp_live_search") target="#cp_live_seach_category_setting"}
<fieldset>
    <div id="cp_live_seach_category_setting" class="in collapse">
        <div class="control-group">
            <label for="elm_category_search_motivation" class="control-label">{__("cp_search_motivation")}:</label>
            <div class="controls">
                <textarea id="elm_category_search_motivation" name="category_data[cp_search_motivation]" cols="55" rows="6" class="input-large">{$category_data.cp_search_motivation}</textarea>
            </div>
        </div>
    </div>
</fieldset>
{/if}
