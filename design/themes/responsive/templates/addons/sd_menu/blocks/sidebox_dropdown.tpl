{strip}
{assign var="foreach_name" value="item_`$iid`"}

{foreach from=$items item="item" name=$foreach_name}

    <li class="ty-menu__item cm-menu-item-responsive {if $item.$childs}dropdown-vertical__dir{/if}{if $item.active || $item|fn_check_is_active_menu_item:$block.type} ty-menu__item-active{/if} menu-level-{$level}{if $item.class} {$item.class}{/if}{if $block.properties.open_menu == 'click'} click{/if}">
        {if $item.$childs}
            <div class="ty-menu__item-toggle visible-phone cm-responsive-menu-toggle">
                <i class="ty-menu__icon-open ty-icon-down-open"></i>
                <i class="ty-menu__icon-hide ty-icon-up-open"></i>
            </div>
            <div class="ty-menu__item-arrow hidden-phone">
                <i class="ty-icon-right-open"></i>
                <i class="ty-icon-left-open"></i>
            </div>
        {/if}

        {assign var="item_url" value=$item|fn_form_dropdown_object_link:$block.type}
        <div class="ty-menu__submenu-item-header">

            <a{if $item_url} href="{$item_url}"{/if} {if $item.new_window}target="_blank"{/if} class="ty-menu__item-link {if $block.properties.open_menu == 'click'}click{/if}">
                {if $item.main_pair_icon.icon}
                    <div class="icon-wrapper">
                        {include file="common/image.tpl" images=$item.main_pair_icon.icon image_height=15}
                    </div>
                {/if}
                <span class="ty-valign">{$item.$name}&nbsp;</span>
                {if $item.label_text}
                    <span class="ty-menu__item-label" style="background:{$item.label_color}">{$item.label_text}</span>
                {/if}
            </a>

        </div>

        {if $item.$childs}
            <div class="ty-menu__submenu">
                <ul class="ty-menu__submenu-items cm-responsive-menu-submenu">
                    {include file="addons/sd_menu/blocks/sidebox_dropdown.tpl" items=$item.$childs separated=true submenu=true iid=$item.$item_id level=$level+1}

                    {if $item.show_more && $item_url}
                        <li class="ty-menu__item cm-menu-item-responsive ty-menu__submenu-alt-link {if $block.properties.open_menu == 'click'}click{/if}">
                            <div class="ty-menu__submenu-item-header">
                                <a href="{$item_url}" class="ty-menu__item-link {if $block.properties.open_menu == 'click'}click{/if}">{__("text_topmenu_view_more")}</a>
                            </div>
                        </li>
                    {/if}

                    {if $item.banner_main_pair_icon}
                        <div class="ty-menu__submenu-banner">
                            {if $item.banner_url}
                                <a href="{$item.banner_url|fn_url}">
                            {/if}
                                {include file="common/image.tpl" images=$item.banner_main_pair_icon}
                            {if $item.banner_url}
                                </a>
                            {/if}
                        </div>
                    {/if}

                </ul>
            </div>
        {/if}
    </li>
{/foreach}
{/strip}