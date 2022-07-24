{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="cp_io_logs" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}" id="cp_io_logs" >
    
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

    {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
    {assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
    {assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

    {if $webp_logs}
        <div class="table-responsive-wrapper">
            <table width="100%" class="table table-middle table--relative table-responsive">
                <thead>
                    <tr>
                        <th width="40%"><a class="cm-ajax" href="{"`$c_url`&sort_by=image_path&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("cp_web_original_txt")}{if $search.sort_by == "image_path"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a> /&nbsp;&nbsp;&nbsp; <a class="cm-ajax" href="{"`$c_url`&sort_by=webp_path&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("cp_web_webp_txt")}{if $search.sort_by == "webp_path"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        
                        <th width="20%"><a class="cm-ajax" href="{"`$c_url`&sort_by=timestamp&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("date")}{if $search.sort_by == "timestamp"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th width="10%" class="right"><a class="cm-ajax" href="{"`$c_url`&sort_by=image_size&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("cp_web_before_txt")}{if $search.sort_by == "image_size"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th width="15%" class="left"><a class="cm-ajax" href="{"`$c_url`&sort_by=webp_size&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("cp_web_after_txt")}{if $search.sort_by == "webp_size"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th width="5%" class="mobile-hide">&nbsp;</th>
                    </tr>
                </thead>
                {foreach from=$webp_logs item="cp_log"}
                    <tr>
                        <td data-th="{__("image")}">
                            <div>
                                <span class="cp-web__img-type">{__("cp_web_original_txt")}:&nbsp;</span><a class="cp-web__img-link" href="{$cp_log.img_http_path}">{$cp_log.image_path}</a>
                            </div>
                            <div>
                                <span class="cp-web__img-type">{__("cp_web_webp_txt")}:&nbsp;</span><a class="cp-web__img-link" href="{$cp_log.webp_http_path}">{$cp_log.webp_path}</a>
                            </div>
                        </td>
                        <td data-th="{__("date")}">
                            {$cp_log.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
                        </td>
                        <td class="right cp_web_imglist__old_size" data-th="{__("cp_web_before_txt")}">
                            {$cp_log.image_size} Mb&nbsp;/&nbsp;
                        </td>
                        <td class="left cp_web_imglist__new_size" data-th="{__("cp_web_after_txt")}">
                            {$cp_log.webp_size} Mb {if $cp_log.compress_percent}({$cp_log.compress_percent}%){/if}
                        </td>
                        <td class="center mobile-hide">
                            <div class="hidden-tools">
                                {capture name="tools_list"}
                                    <li>{btn type="list" text=__("cp_web_add_to_ignor_list") class="cm-post" href="cp_webp.add_to_ignor?image_crc=`$cp_log.image_crc`"}</li>
                                {/capture}
                                {dropdown content=$smarty.capture.tools_list}
                            </div>
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
            {if $webp_logs}
                <li>{btn type="list" text=__("cp_webp_clear_logs") dispatch="dispatch[cp_webp.clear_webp_logs]" form="cp_io_logs"}</li>
            {/if}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}
    {/capture}
    </form>
    {capture name="sidebar"}
        {include file="addons/cp_webp/components/webp_images_search.tpl" dispatch="cp_webp.webp_logs" view_type="images"}
    {/capture}
{/capture}
{include file="common/mainbox.tpl" title=__("cp_web_menu_webp_logs") content=$smarty.capture.mainbox tools=$smarty.capture.tools sidebar=$smarty.capture.sidebar buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons}
