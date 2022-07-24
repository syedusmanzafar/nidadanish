{if $in_popup}
    <div class="adv-search">
    <div class="group">
{else}
    <div class="sidebar-row">
    <h6>{__("search")}</h6>
{/if}
{if $page_part}
    {assign var="_page_part" value="#`$page_part`"}
{/if}

<form action="{""|fn_url}{$_page_part}" name="{$product_search_form_prefix}search_form" method="get" class="cp_io_imglist_s cm-disable-empty {$form_meta}">

<input type="hidden" name="type" value="simple" autofocus="autofocus" />
{if $smarty.request.redirect_url}
    <input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
{/if}
{$extra nofilter}
{capture name="simple_search"}
    <div class="sidebar-field">
        <label>{__("cp_web_original_txt")}</label>
        <input type="text" name="s_image_path" size="20" value="{$search.s_image_path}" />
    </div>
    <div class="sidebar-field">
        <label>{__("cp_web_webp_txt")}</label>
        <input type="text" name="s_webp_path" size="20" value="{$search.s_webp_path}" />
    </div>
    <div class="sidebar-field">
        <label>{__("time")} ({__("from")})</label>
        {include file="common/calendar.tpl" date_id="from_date_holder" date_name="s_from_date" date_val=$search.s_from_date}
    </div>
    <div class="sidebar-field">
        <label>{__("time")} ({__("to")})</label>
        {include file="common/calendar.tpl" date_id="to_date_holder" date_name="s_to_date" date_val=$search.s_to_date}
    </div>
{/capture}
{include file="common/advanced_search.tpl" simple_search=$smarty.capture.simple_search advanced_search=$smarty.capture.advanced_search dispatch=$dispatch view_type="images" in_popup=$in_popup}

</form>
{if $in_popup}
    </div></div>
{else}
    </div><hr>
{/if}
