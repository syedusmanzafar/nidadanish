{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="cp_ls_styles_form">
        <input type="hidden" name="return_url" value="{$config.current_url}">
        {if $selected_storefront_id}
            <input type="hidden" name="storefront_id" value="{$selected_storefront_id}" />
        {/if}

        <div class="table-responsive-wrapper">
            <table width="100%" class="table table-middle table-objects table-responsive">
                <thead>
                    <tr>
                        <th width="30%">{__("name")}</th>
                        <th width="10%">{__("cp_ls_color")}</th>
                        <th width="10%">{__("cp_ls_hover_color")}</th>
                        <th width="30%">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$styles key="style_name" item="style"}
                    <tr>
                        <td>
                            {$style.descr}
                        </td>
                        <td>
                            <div class="colorpicker">
                                <input type="text" id="cp_ls_style_{$style_name}_color" name="settings[{$style_name}][color]" value="{$style.color}" class="cm-colorpicker">
                            </div>
                        </td>
                        <td>
                            <div class="colorpicker">
                                <input type="text" id="cp_ls_style_{$style_name}_hover_color" name="settings[{$style_name}][hover_color]" value="{$style.hover_color}" class="cm-colorpicker">
                            </div>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </form>

    {capture name="sidebar"}
    {/capture}
    
    {capture name="buttons"}
        {include file="buttons/button.tpl" but_text=__("save") but_name="dispatch[cp_live_search.styles_update]" but_target_form="cp_ls_styles_form" but_role="submit-link"}  
    {/capture}
{/capture}

{include file="common/mainbox.tpl"
    title=__("cp_ls_stylization")
    content=$smarty.capture.mainbox
    select_languages=false buttons=$smarty.capture.buttons
    sidebar=$smarty.capture.sidebar
    select_storefront=true
    show_all_storefront=false
    storefront_switcher_param_name="storefront_id"
}
