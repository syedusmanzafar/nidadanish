{capture name="mainbox"}
	    <form action="{""|fn_url}" method="post" name="settings_form" class="form-horizontal form-edit cm-check-changes" enctype="multipart/form-data">
        <input type="hidden" name="selected_section">
       
    {capture name="tabsbox"}
    	{if $allow_separate_storefronts && !$runtime.company_id}
           {assign var="disable_input" value=true}
           {assign var="show_update_for_all" value=true}          
        {/if}
        {if $fields}
        	{include file="addons/`$addon_base_name`/components/options.tpl" param_name="settings" _params=$fields prefix=$lp}       
        {else}
        	<p class="no-items">{__("no_data")}</p>       
        {/if}
    {/capture}
    {include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

	</form>    
    
    {capture name="buttons"}
    	{if $fields}
       		{include file="buttons/save.tpl" but_name="dispatch[cfpc.settings]" but_role="submit-link" but_target_form="settings_form"}
        {/if}      
    {/capture}
{/capture}

{capture name="sidebar"}	
    {include file="addons/`$addon_base_name`/components/reviews.tpl" addon=$addon_base_name prefix=$lp}      
{/capture}


{include file="common/mainbox.tpl" title=__($addon_base_name) content=$smarty.capture.mainbox buttons=$smarty.capture.buttons  content_id="`$addon_base_name`.settings" mainbox_content_wrapper_class="csc-settings" sidebar=$smarty.capture.sidebar}