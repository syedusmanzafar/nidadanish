{$status          = $obj.status|default:""}
{$items_status    = fn_sd_labels_get_label_statuses($use_theme_label)}

<div class="control-group">
    <label class="control-label cm-required">{__("status")}:</label>
    <div class="controls">
        {foreach from=$items_status item="status_name" key="status_id" name="status_cycle"}
            <label class="radio inline" for="{$id}_{$obj_id|default:0}_{$status_id|lower}">
                <input type="radio"
                       name="{$input_name}"
                       class="product__status-switcher"
                       id="{$id}_{$obj_id|default:0}_{$status_id|lower}"
                       {if $status === $status_id || (!$status && $smarty.foreach.status_cycle.first)}checked="checked"{/if}
                       value="{$status_id}"
                />
                {$status_name}
            </label>
        {/foreach}
    </div>
</div>