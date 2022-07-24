{$allow_save = fn_check_permissions("addons", "manage", "admin", "POST")}
{capture name="mainbox"}
	    <form action="{""|fn_url}" method="post" name="settings_form" class="form-horizontal form-edit {if !$allow_save} cm-hide-inputs{/if} cm-check-changes" enctype="multipart/form-data">
       
    {capture name="tabsbox"}
        {include file="addons/csc_unite_orders/components/options.tpl" param_name="settings" _params=$fields prefix='cuo'}
    {/capture}
    {include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

	</form>

    {capture name="buttons"}      
       {include file="buttons/save.tpl" but_name="dispatch[cuo.settings]" but_role="submit-link" but_target_form="settings_form"}       
    {/capture}
{/capture}

{capture name="sidebar"} 
	{include file="addons/csc_unite_orders/components/submenu.tpl"}    
    {include file="addons/csc_unite_orders/components/reviews.tpl" addon="csc_unite_orders" prefix="cuo"}      
{/capture}

{include file="common/mainbox.tpl" title=__("cuo.settings") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar content_id="cuo.settings" mainbox_content_wrapper_class="csc-settings"}


