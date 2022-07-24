<div class="cm-popup-box {if !$hide_for_vendor}dropdown{/if} {$popup_additional_class}">
    {if !$hide_for_vendor}
        <a href="#" {if $id}id="sw_select_{$id}_wrap"{/if} class="btn-text btn dropdown-toggle{if $id} cm-combination{/if} {if $text_wrap}dropdown-toggle--text-wrap{/if}" data-toggle="dropdown">
    {/if}

    {$label_statuses = fn_sd_labels_get_label_statuses($use_theme_label)}
    {if $label_statuses[$status]}
        {__("sd_labels.status.{$status}")}
    {/if}
    {if !$hide_for_vendor}
        <span class="caret"></span>
        </a>
    {/if}
    {if $id && !$hide_for_vendor}
        {$_update_controller = $update_controller|default:"tools"}
        {if $table && $object_id_name}{capture name="_extra"}&table={$table}&id_name={$object_id_name}{/capture}{/if}
        <ul class="dropdown-menu">
            {if !$items_status}
                {$items_status = $label_statuses}
                {$extra_params = "&table={$table}&id_name={$object_id_name}"}
            {else}
                {$extra_params = "{$smarty.capture._extra}{$extra}"}
            {/if}
            {if $st_return_url}
                {$return_url   = $st_return_url|escape:url}
                {$extra_params = "{$extra_params}&redirect_url={$return_url}"}
            {/if}
            {if $items_status}
                {foreach $items_status as $st => $val}
                    <li {if $status == $st}class="disabled"{/if}>
                        <a class="{if $text_wrap}dropdown--text-wrap{/if}
                            {if $confirm}cm-confirm {/if}status-link-{$st|lower}
                            {if $status == $st}active{else}cm-ajax cm-post{if $ajax_full_render} cm-ajax-full-render{/if}{/if}
                            {if $status_meta}{$status_meta}{/if}"
                        {if $status_target_id} data-ca-target-id="{$status_target_id}"{/if}
                        href="{"{$_update_controller}.update_status?id={$id}&status={$st}{$extra_params}{$dynamic_object}"|fn_url}"
                        onclick="return fn_check_object_status(this,
                                '{$st|lower}',
                                '{if $statuses}{$statuses[$st].params.color|default:''}{/if}'
                            );"
                        {if $st_result_ids}data-ca-target-id="{$st_result_ids}"{/if}
                        data-ca-event="ce.update_object_status_callback" title="{$val}">
                            {$val}
                        </a>
                    </li>
                {/foreach}
            {/if}
        </ul>
        {if !$smarty.capture.avail_box}
        {script src="js/tygh/select_popup.js"}
        {capture "avail_box"}Y{/capture}
        {/if}
    {/if}
</div>
