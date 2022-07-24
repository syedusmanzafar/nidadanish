{script src="js/tygh/tabs.js"}
{script src="js/tygh/fileuploader_scripts.js"}

{capture name="mainbox"}
    <div class="cp-addons-manage" id="addons_list_reload">

        {include file="addons/cp_addons_manager/views/cp_addons_manager/components/correct_permissions.tpl"}
        
        {assign var="c_url" value=$config.current_url|escape:"url"}
        
        <div class="table-responsive-wrapper">
        {if $addons_list}
            <table class="table table-middle table-responsive">
                {* Addon manager row *}
                {if $extra.am_id && $addons_list[$extra.am_id]}
                    {$addon = $addons_list[$extra.am_id]}
                    <tr>
                        <td class="right" width="4%">
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_tools.tpl" addon=$addons_list[$extra.am_id] c_url=$c_url is_am=true}
                        </td>
                        <td>
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_info.tpl" addon=$addons_list[$extra.am_id]}
                        </td>
                        <td class="left cp-addon-right-wrap" width="40%">
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_action.tpl" addon=$addons_list[$extra.am_id] without_subscr=true is_am=true extra=$extra key=$extra.am_id}
                        </td>
                    </tr>
                {/if}
                {* Addon sets rows *}
                {$in_sets = []}
                {$set_expand_all = true}
                {foreach $sets as $set_id => $set}
                    <tr>
                        <td colspan="3">
                            <div class="cp-addon-set-name-wrap">
                                <span title="{__("expand_sublist_of_items")}" id="on_cp_addon_set_wrap_{$set_id}" class="cm-combination {if $set_expand_all}hidden{/if}">
                                    <span class="cp-caret-icon icon-caret-right"></span>
                                    <span class="cp-addon-set-name">{$set.name}</span>
                                </span>
                                <span title="{__("collapse_sublist_of_items")}" id="off_cp_addon_set_wrap_{$set_id}" class="cm-combination {if !$set_expand_all}hidden{/if}">
                                    <span class="cp-caret-icon icon-caret-down"></span>
                                    <span class="cp-addon-set-name">{$set.name}</span>
                                </span>
                            </div>
                            <div class="cp-addon-set-wrap {if !$set_expand_all}hidden{/if}" id="cp_addon_set_wrap_{$set_id}">
                                <table class="table table-middle table-responsive">
                                    {foreach from=$set.addon_ids item="addon_id"}
                                        {if !$addons_list[$addon_id]}
                                            {continue}
                                        {/if}
                                        {$in_sets[] = $addon_id}
                                        {$addon = $addons_list[$addon_id]}
                                        <tr>
                                            <td class="right" width="4%">
                                                {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_tools.tpl" addon=$addon c_url=$c_url}
                                            </td>
                                            <td>
                                                {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_info.tpl" addon=$addon}
                                            </td>
                                            <td class="left cp-addon-right-wrap" width="40%">
                                                {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_action.tpl" addon=$addon key=$addon_id}
                                            </td>
                                        </tr>
                                    {/foreach}
                                </table>
                            <!--cp_addon_set_wrap_{$set_id}--></div>
                        </td>
                    </tr>
                {/foreach}
                {* Other addons rows *}
                {foreach from=$addons_list item="addon" key="key"}
                    {if $key == $extra.am_id || $key|in_array:$in_sets}
                        {continue}
                    {/if}
                    <tr>
                        <td class="right" width="4%">
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_tools.tpl" addon=$addon c_url=$c_url}
                        </td>
                        <td>
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_info.tpl" addon=$addon}
                        </td>
                        <td class="left cp-addon-right-wrap" width="40%">
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_action.tpl" addon=$addon}
                        </td>
                    </tr>
                {/foreach}
            </table>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
        </div>
        {capture name="buttons"}
        {/capture}
        {capture name="adv_buttons"}
        {/capture}
        </form>
    <!--addons_list_reload--></div>
{/capture}

{capture name="title"}{__("cart_power")}: {__("cp_my_addons")}{/capture}

{include file="common/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons select_languages=false no_sidebar=true}
