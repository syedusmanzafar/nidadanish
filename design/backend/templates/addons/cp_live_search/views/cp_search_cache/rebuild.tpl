{capture name="mainbox"}
<div id="cp_live_search_rebuild">
    <form action="{""|fn_url}" method="post" name="cp_search_cache_form" class="form-horizontal form-edit cm-ajax">
        <input type="hidden" name="result_ids" value="cp_live_search_rebuild">

        {if $all_cron_command}
            <br>
            <div class="control-group {$promo_class}">
                <strong>{__("cp_all_cron_command")}:</strong>
                <div>{$all_cron_command nofilter}</div>
            </div>
        {/if}
        {foreach from=$cache_info key="company_id" item="cache"}
            {include file="common/subheader.tpl" title=$cache.company_name}
            {if $cache.cron_command}
                <div class="control-group {$promo_class}">
                    <strong>{__("cp_cron_command")}:</strong>
                    <div>{$cache.cron_command nofilter}</div>
                </div>
            {/if}
            {foreach from=$cache.cached_products key="lang" item="cached_count"}
                <div class="control-group {$promo_class}">
                    <strong>{__("cached_products")} ({$lang}):</strong>&nbsp;{$cached_count|default:0}&nbsp;{__("of")}&nbsp;{$cache.total_products.$lang|default:0}
                </div>
            {foreachelse}
                <div class="control-group {$promo_class}">
                    <strong>{__("cached_products")}:</strong>&nbsp;0
                </div>
            {/foreach}
            <div class="control-group {$promo_class}">
                <strong>{__("total_cached_strings")}:</strong>&nbsp;{$cache.total_cached_strings|default:0}
            </div>      
        {/foreach}
        <div class="control-group {$promo_class}">
            <strong>{__("make_search_better")}</strong>
        </div>
    </form>
<!--cp_live_search_rebuild--></div>
{/capture}

{capture name="buttons"}
    <div class="cm-tab-tools pull-right shift-left">
        {include file="buttons/button.tpl" but_text=__("rebuild_cache") but_name="dispatch[cp_search_cache.rebuild]" but_target_form="cp_search_cache_form" but_meta="cm-comet" but_role="submit-link"}
    </div>
    <div class="cm-tab-tools pull-right shift-left">    
        {include file="buttons/button.tpl" but_text=__("optimize_cache_table") but_name="dispatch[cp_search_cache.optimize]" but_target_form="cp_search_cache_form" but_meta="cm-comet" but_role="submit-link"}        
    </div>        
    <div class="cm-tab-tools pull-right shift-left">
        {include file="buttons/button.tpl" but_text=__("repair_cache_table") but_name="dispatch[cp_search_cache.repair]" but_target_form="cp_search_cache_form" but_meta="cm-comet" but_role="submit-link"}        
    </div>
    {capture name="tools_list"}
        <li>{btn type="list" class="cm-ajax cm-confirm" text=__("delete_cache_table") dispatch="dispatch[cp_search_cache.drop]" form="cp_search_cache_form"}</li>
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}

{include file="common/mainbox.tpl" title=__("cp_live_search_cache") content=$smarty.capture.mainbox box_id="cp_search_cache_rebuild" adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons}
