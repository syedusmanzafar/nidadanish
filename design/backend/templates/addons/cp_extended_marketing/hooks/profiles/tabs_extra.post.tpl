{if $notice_types && $user_type == "C"}
    <div id="content_cp_em_notices">
        <input id="selected_section" type="hidden" value="cp_em_notices" name="selected_section"/>
        <div class="control-group cp-em_prof__types">
            {foreach from=$notice_types item="notice_type"}
                {$em_type=$notice_type.type}
                <div class="controls">
                    <label class="checkbox inline" for="elm_status{$status.status}">{$notice_type.profile_title}
                        <input type="hidden" name="user_data[cp_em_types][{$notice_type.type}]" value="D"  />
                        <input type="checkbox" name="user_data[cp_em_types][{$notice_type.type}]" value="A" {if $user_types && $user_types.$em_type && $user_types.$em_type.status == "A"}checked="checked"{/if} />
                    </label>
                </div>
            {/foreach}
        </div>
    <!--content_cp_em_notices--></div>
{/if}
