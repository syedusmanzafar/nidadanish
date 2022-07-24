{if $runtime.mode == "update" && $notice_types}
    <div id="content_cp_em_notices">
        <form name="profile_subscribtions_form" enctype="multipart/form-data" action="{""|fn_url}" method="post">
            <input id="selected_section" type="hidden" value="cp_em_notices" name="selected_section"/>
            <div class="cp-em_prof__types">
                {foreach from=$notice_types item="notice_type"}
                    {$em_type=$notice_type.type}
                    <div class="cp-em_prof__types_item">
                        <input type="hidden" name="types[{$notice_type.type}]" value="D"  />
                        <input type="checkbox" name="types[{$notice_type.type}]" value="A" {if $user_types && $user_types.$em_type && $user_types.$em_type.status == "A"}checked="checked"{/if} /><label>{$notice_type.profile_title}</label>
                    </div>
                {/foreach}
            </div>
            <div class="ty-profile-field__buttons buttons-container">
                {include file="buttons/save.tpl" but_name="dispatch[profiles.update_em_subscr]" but_meta="ty-btn__secondary" but_id="save_profile_but"}
            </div>
        </form>
    <!--content_cp_em_notices--></div>
{/if}