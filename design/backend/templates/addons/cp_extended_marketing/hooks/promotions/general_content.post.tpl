{if $promotion_data.zone == "cart" || $zone == "cart"}
    <div class="control-group">
        <label class="control-label" for="elm_cp_em_for_notices">{__("cp_em_use_for_notices")}</label>
        <div class="controls">
            <input type="hidden" name="promotion_data[cp_em_for_notices]" value="N" />
            <input type="checkbox" name="promotion_data[cp_em_for_notices]" id="elm_cp_em_for_notices" value="Y" {if $promotion_data.cp_em_for_notices == "Y"}checked="checked"{/if}/>
        </div>
    </div>
{/if}