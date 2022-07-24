<div class="sidebar-row">
    <h6>{__("search")}</h6>
    <form action="{""|fn_url}" name="cp_custom_logs_form" method="get">
        <input type="hidden" name="section" value="{$smarty.request.section}">

        {capture name="simple_search"}
            <div class="sidebar-field">
                <div class="control-group">
                    <label class="control-label">{__("user")}:</label>
                    <div class="controls">
                        <input type="text" name="user_name" size="30" value="{$search.user_name}">
                    </div>
                </div>
                {if $section_settings.actions}
                    <div class="control-group">
                        <label class="control-label">{__("action")}:</label>
                        <div class="controls">
                            <select name="action">
                                <option value="" {if !$search.action}selected="selected"{/if}>{__("all")}</option>
                                {foreach from=$section_settings.actions key="a_key" item="a_value"}
                                    <option value="{$a_key}"{if $search.action == $a_key} selected="selected"{/if}>{$a_value.title}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {/if}
            </div>
            {include file="common/period_selector.tpl" period=$search.period extra="" display="form" button="false"}
        {/capture}
        
        {capture name="advanced_search"}
        {/capture}
        
        {include file="common/advanced_search.tpl" advanced_search=$smarty.capture.advanced_search simple_search=$smarty.capture.simple_search dispatch="cp_custom_logs.manage" view_type="logs"}
    </form>
</div>