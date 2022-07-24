{if $in_popup}
    <div class="adv-search">
    <div class="group">
{else}
    <div class="sidebar-row">
    <h6>{__("search")}</h6>
{/if}

<form action="{""|fn_url}" name="transition_search_form" method="get" class="{$form_meta}">
    {if $smarty.request.redirect_url}
        <input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
    {/if}

    <input type="hidden" id="section" name="section" value="{$smarty.request.section}" />

    {capture name="simple_search"}

        {$extra nofilter}
        <div class="sidebar-field">
            <label for="search">{__("search")}</label>
            <input type="text" name="search" id="search" value="{$search.search}" size="10"/>
        </div>
    {/capture}

    {capture name="advanced_search"}
        {if $section == "all"}
            <div class="group form-horizontal">
                <div class="control-group">
                    <label class="control-label">{__("period")}</label>
                    <div class="controls">
                        {include file="common/period_selector.tpl" period=$search.period form_name="search_history_form"}
                    </div>
                </div>
            </div>
        {/if}
    {/capture}

    {include file="common/advanced_search.tpl" simple_search=$smarty.capture.simple_search advanced_search=$smarty.capture.advanced_search dispatch=$dispatch view_type="orders" in_popup=$in_popup}
</form>

{if $in_popup}
    </div></div>
{else}
    </div><hr>
{/if}
