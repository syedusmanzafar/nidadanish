{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="cp_io_logs" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}" id="cp_io_logs" >
    
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

    {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
    {assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
    {assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

    {if $logs}
        <div class="table-responsive-wrapper">
            <table width="100%" class="table table-middle table--relative table-responsive">
                <thead>
                    <tr>
                        <th width="20%"><a class="cm-ajax" href="{"`$c_url`&sort_by=start_time&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("cp_webp_start_time")}{if $search.sort_by == "start_time"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th width="20%"><a class="cm-ajax" href="{"`$c_url`&sort_by=end_time&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("cp_webp_end_time")}{if $search.sort_by == "end_time"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th width="30%">{__("cp_webp_info_txt")}</th>
                    </tr>
                </thead>
                {foreach from=$logs item="cp_log"}
                    <tr>
                        <td data-th="{__("cp_webp_start_time")}">
                            {$cp_log.end_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
                        </td>
                        <td data-th="{__("cp_webp_end_time")}">
                            {$cp_log.end_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
                        </td>
                        <td data-th="{__("cp_webp_info_txt")}">
                            {if $cp_log.info}
                                {__("cp_webp_converted_txt")} - <b>{$cp_log.info.converted}</b><br />
                                {__("cp_webp_error_txt")} - <b>{$cp_log.info.errors}</b><br />
                                {__("cp_webp_shortpixel_txt")} - <b>{$cp_log.info.shortpixel}</b>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
    {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
    {capture name="buttons"}
        {capture name="tools_list"}
            {if $logs}
                <li>{btn type="list" text=__("cp_webp_clear_logs") dispatch="dispatch[cp_webp.clear_logs]" form="cp_io_logs"}</li>
            {/if}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}
    {/capture}
    </form>
{/capture}
{include file="common/mainbox.tpl" title=__("cp_webp_logs_page") content=$smarty.capture.mainbox tools=$smarty.capture.tools sidebar=$smarty.capture.sidebar buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons}
