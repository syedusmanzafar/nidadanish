{if $items}
{if $block.properties.open_menu == "click"}
    {script src="js/addons/sd_menu/click_toggle.js"}
{/if}

    <ul class="ty-menu__items cm-responsive-menu sd-amazon-menu sd-menu {if $block.properties.open_menu == 'click'}click{/if}">
        <div class="container-fluid">
            <li class="ty-menu__item ty-menu__menu-btn visible-phone">
                <a class="ty-menu__item-link ty-menu__item-link--main">
                    <i class="ty-icon-short-list"></i>
                    <span>{__("menu")}</span>
                </a>
            </li>

            {foreach from=$items item="item1" name="item1"}
                {assign var="item1_url" value=$item1|fn_form_dropdown_object_link:$block.type}
                {assign var="unique_elm_id" value=$item1_url|md5}
                {assign var="unique_elm_id" value="topmenu_`$block.block_id`_`$unique_elm_id`"}
                {if $subitems_count}

                {/if}
                <li class="ty-menu__item{if !$item1.$childs} ty-menu__item-nodrop{else} cm-menu-item-responsive{/if}{if $item1.active || $item1|fn_check_is_active_menu_item:$block.type} ty-menu__item-active{/if}{if $item1.class} {$item1.class}{/if}{if $block.properties.open_menu == 'click'} click{/if} {$item1.$name}">
                    {if $item1.$childs}
                        <a class="ty-menu__item-toggle visible-phone cm-responsive-menu-toggle">
                            <i class="ty-menu__icon-open ty-icon-down-open"></i>
                            <i class="ty-menu__icon-hide ty-icon-up-open"></i>
                        </a>
                    {/if}
                    <a {if $item1_url} href="{$item1_url}"{/if} class="ty-menu__item-link ty-menu__item-link--amazon{if $block.properties.open_menu == 'click'} click{/if}">

                        {if $item1.main_pair_icon.icon}
                            <div class="icon-wrapper">
                                {include file="common/image.tpl" images=$item1.main_pair_icon.icon image_height=15}
                            </div>
                        {/if}
                        <span class="ty-valign">{$item1.$name}</span>
                        {if $item1.label_text}
                            <span class="ty-menu__item-label" style="background:{$item1.label_color}">{$item1.label_text}</span>
                        {/if}

                        {if $item1.$childs && $block.properties.open_menu == "click"}
                            <span class="ty-menu__item-arrow hidden-phone">
                                <i class="ty-icon-down-open"></i>
                                <i class="ty-icon-up-open"></i>
                            </span>
                        {/if}
                    </a>
                {if $item1.$childs}
                    {if !$item1.$childs|fn_check_second_level_child_array:$childs}
                    {* Only two levels. Vertical output *}
                        <div class="ty-menu__submenu">
                            <ul class="ty-menu__submenu-items ty-menu__submenu-items-simple cm-responsive-menu-submenu">

                                {if $item1.banner_main_pair_icon}
                                    <div class="ty-menu__submenu-items-left">
                                {/if}

                                    {foreach from=$item1.$childs item="item2" name="item2"}
                                        {assign var="item_url2" value=$item2|fn_form_dropdown_object_link:$block.type}
                                        <li class="ty-menu__submenu-item{if $item2.active || $item2|fn_check_is_active_menu_item:$block.type} ty-menu__submenu-item-active{/if}{if $item2.class} {$item2.class}{/if}">

                                            <a class="ty-menu__submenu-link ty-menu__submenu-link--amazon" {if $item_url2} href="{$item_url2}"{/if}>
                                                {if $item2.main_pair_icon.icon}
                                                    {include file="common/image.tpl" images=$item2.main_pair_icon.icon image_height=15}
                                                {/if}
                                                <span class="ty-valign">{$item2.$name}</span>
                                                {if $item2.label_text}
                                                    <span class="ty-menu__item-label" style="background:{$item2.label_color}">{$item2.label_text}</span>
                                                {/if}
                                            </a>

                                        </li>
                                    {/foreach}
                                    {if $item1.show_more && $item1_url}
                                        <li class="ty-menu__submenu-item ty-menu__submenu-alt-link">
                                            <a href="{$item1_url}" class="ty-menu__submenu-alt-link">{__("text_topmenu_view_more")}</a>
                                        </li>
                                    {/if}

                                {if $item1.banner_main_pair_icon}
                                    </div>
                                    <div class="ty-menu__submenu-items-right">
                                        {if $item1.banner_url}
                                            <a href="{$item1.banner_url|fn_url}">
                                        {/if}
                                        {include file="common/image.tpl" images=$item1.banner_main_pair_icon}
                                        {if $item1.banner_url}
                                            </a>
                                        {/if}
                                    </div>
                                {/if}

                            </ul>
                        </div>
                    {else}
                        <div class="ty-menu__submenu" id="{$unique_elm_id}">
                                <ul class="ty-menu__submenu-items cm-responsive-menu-submenu">

                                    {if $item1.banner_main_pair_icon}
                                        <div class="ty-menu__submenu-items-left">
                                    {/if}

                                    {foreach from=$item1.$childs item="item2" name="item2"}
                                        <li class="ty-top-mine__submenu-col">
                                            {assign var="item2_url" value=$item2|fn_form_dropdown_object_link:$block.type}
                                            <div class="ty-menu__submenu-item-header ty-menu__submenu-item-header--amazon{if $item2.active || $item2|fn_check_is_active_menu_item:$block.type} ty-menu__submenu-item-header-active{/if}{if $item2.class} {$item2.class}{/if}">

                                                <a{if $item2_url} href="{$item2_url}"{/if} class="ty-menu__submenu-link ty-menu__submenu-link--amazon">
                                                    {if $item2.main_pair_icon.icon}
                                                        {include file="common/image.tpl" images=$item2.main_pair_icon.icon image_height=15}
                                                    {/if}
                                                    <span class="ty-valign">{$item2.$name}</span>
                                                    {if $item2.label_text}
                                                        <span class="ty-menu__item-label" style="background:{$item2.label_color}">{$item2.label_text}</span>
                                                    {/if}
                                                </a>

                                            </div>
                                            {if $item2.$childs}
                                                <a class="ty-menu__item-toggle visible-phone cm-responsive-menu-toggle">
                                                    <i class="ty-menu__icon-open ty-icon-down-open"></i>
                                                    <i class="ty-menu__icon-hide ty-icon-up-open"></i>
                                                </a>
                                            {/if}
                                            <div class="ty-menu__submenu">
                                                <ul class="ty-menu__submenu-list cm-responsive-menu-submenu">
                                                    {if $item2.$childs}
                                                        {foreach from=$item2.$childs item="item3" name="item3"}
                                                            {assign var="item3_url" value=$item3|fn_form_dropdown_object_link:$block.type}
                                                            <li class="ty-menu__submenu-item{if $item3.active || $item3|fn_check_is_active_menu_item:$block.type} ty-menu__submenu-item-active{/if}{if $item3.class} {$item3.class}{/if}">

                                                                <a{if $item3_url} href="{$item3_url}"{/if} class="ty-menu__submenu-link">
                                                                    {if $item3.main_pair_icon.icon}
                                                                        {include file="common/image.tpl" images=$item3.main_pair_icon.icon image_height=15}
                                                                    {/if}
                                                                    <span class="ty-valign">{$item3.$name}</span>
                                                                    {if $item3.label_text}
                                                                        <span class="ty-menu__item-label" style="background:{$item3.label_color}">{$item3.label_text}</span>
                                                                    {/if}
                                                                </a>

                                                            </li>
                                                        {/foreach}
                                                        {if $item2.show_more && $item2_url}
                                                            <li class="ty-menu__submenu-item ty-menu__submenu-alt-link">
                                                                <a href="{$item2_url}" class="ty-menu__submenu-link">{__("text_topmenu_view_more")}</a>
                                                            </li>
                                                        {/if}
                                                    {/if}
                                                </ul>
                                            </div>
                                        </li>
                                    {/foreach}
                                    {if $item1.show_more && $item1_url}
                                        <li class="ty-menu__submenu-dropdown-bottom">
                                            <a href="{$item1_url}">{__("text_topmenu_more", ["[item]" => $item1.$name])}</a>
                                        </li>
                                    {/if}

                                    {if $item1.banner_main_pair_icon}
                                        </div>
                                        <div class="ty-menu__submenu-items-right">
                                            {if $item1.banner_url}
                                                <a href="{$item1.banner_url|fn_url}">
                                            {/if}
                                            {include file="common/image.tpl" images=$item1.banner_main_pair_icon}
                                            {if $item1.banner_url}
                                                </a>
                                            {/if}
                                        </div>
                                    {/if}

                                </ul>
                            </div>
                        {/if}
                    {/if}
                </li>
            {/foreach}
        </div>
    </ul>
{/if}
