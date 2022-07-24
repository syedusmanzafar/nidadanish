{hook name="blocks:sidebox_dropdown"}{strip}
{assign var="foreach_name" value="item_`$iid`"}

{foreach from=$items item="item" name=$foreach_name}
{hook name="blocks:sidebox_dropdown_element"}
<div class="vertical-menu-asim">

    <li class="adi_menu_vertical_item ty-menu__item cm-menu-item-responsive vertical-menu-asim2 {if $item.$childs}dropdown-vertical__dir{/if}{if $item.active || $item|fn_check_is_active_menu_item:$block.type} {/if} menu-level-{$level}{if $item.class} {$item.class}{/if}">
        
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
            <a{if $item_url} href="{$item_url}"{/if} {if $item.new_window}target="_blank"{/if} class="asim-no-hover ty-menu__item-link">{$item.$name}</a>
        </div>

        {if $item.$childs}
            {hook name="blocks:sidebox_dropdown_childs"}
            <div class="ty-menu__submenu">
                <ul class="ty-menu__submenu-items cm-responsive-menu-submenu">
                </ul>
            </div>
            {/hook}
        {/if}
       

        
           
                
                
                
                
                
                

            {assign var="item_url" value=$item|fn_form_dropdown_object_link:$block.type}
            {assign var="unique_elm_id" value=$item_url|md5}
            {assign var="unique_elm_id" value="topmenu_`$block.block_id`_`$unique_elm_id`"}
           
            {if $subitems_count}

            {/if}
                  
                    
                
                    
                    
                    
                    
                    
                    
                    
                {if $item.$childs}

                    {if !$item.$childs|fn_check_second_level_child_array:$childs}
                    {* Only two levels. Vertical output *}
                        <div class="ty-menu__submenu">
                            <ul class="ty-menu__submenu-items ty-menu__submenu-items-simple menu-column2 cm-responsive-menu-submenu">
                                {hook name="blocks:topmenu_dropdown_2levels_elements"}

                                {foreach from=$item.$childs item="item2" name="item2"}
                                    {assign var="item_url2" value=$item2|fn_form_dropdown_object_link:$block.type}
                                    <li class="ty-menu__submenu-item{if $item2.active || $item2|fn_check_is_active_menu_item:$block.type} ty-menu__submenu-item-active{/if}{if $item2.class} {$item2.class}{/if}">
                                        <a class="ty-menu__submenu-link" {if $item_url2} href="{$item_url2}"{/if} {if $item2.new_window}target="_blank"{/if}>{$item2.$name}</a>
                                    </li>
                                {/foreach}
                                {if $item.show_more && $item_url}
                                    <li class="ty-menu__submenu-item ty-menu__submenu-alt-link">
                                        <a href="{$item_url}"
                                           class="ty-menu__submenu-alt-link">{__("text_topmenu_view_more")}</a>
                                    </li>
                                {/if}

                                {/hook}
                            </ul>
                        </div>
                    {else}
                        <div class="ty-menu__submenu" id="{$unique_elm_id}">
                            {hook name="blocks:topmenu_dropdown_3levels_cols"}
                                <ul class="ty-menu__submenu-items cm-responsive-menu-submenu asim-menu">
                                
                                
                                
                                
                                    {foreach from=$item.$childs item="item2" name="item2"}
                                    <div class="menu-column">
                                    
                                        <li class="ty-top-mine__submenu-col">
                                            {assign var="item2_url" value=$item2|fn_form_dropdown_object_link:$block.type}
                                            
                                            
                                            
                                            
                                            
                                            <div class="ty-menu__submenu-item-header{if $item2.active || $item2|fn_check_is_active_menu_item:$block.type} ty-menu__submenu-item-header-active{/if}{if $item2.class} {$item2.class}{/if}">
                                                <a{if $item2_url} href="{$item2_url}"{/if} class="ty-menu__submenu-link" {if $item2.new_window}target="_blank"{/if}>{$item2.$name}</a>
                                           </div>
                                           
                                           
                                           
                                            {if $item2.$childs}
                                                        {hook name="blocks:topmenu_dropdown_3levels_col_elements"}
                                                        {foreach from=$item2.$childs item="item3" name="item3"}
                                                            {assign var="item3_url" value=$item3|fn_form_dropdown_object_link:$block.type}
                                                            <li class="ty-menu__submenu-item{if $item3.active || $item3|fn_check_is_active_menu_item:$block.type} ty-menu__submenu-item-active{/if}{if $item3.class} {$item3.class}{/if}">
                                                                <a{if $item3_url} href="{$item3_url}"{/if}
                                                                        class="ty-menu__submenu-link" {if $item3.new_window}target="_blank"{/if}>{$item3.$name}</a>
                                                            </li>
                                                        {/foreach}
                                                        {if $item2.show_more && $item2_url}
                                                            <li class="ty-menu__submenu-item ty-menu__submenu-alt-link">
                                                                <a href="{$item2_url}"
                                                                   class="ty-menu__submenu-link" {if $item2.new_window}target="_blank"{/if}>{__("text_topmenu_view_more")}</a>
                                                            </li>
                                                        {/if}
                                                        {/hook}
                                                    {/if}
                                            
                                            
                                            
                                            
                                            {if $item2.$childs}
                                                <a class="ty-menu__item-toggle visible-phone cm-responsive-menu-toggle">
                                                    <i class="ty-menu__icon-open ty-icon-down-open"></i>
                                                    <i class="ty-menu__icon-hide ty-icon-up-open"></i>
                                                </a>
                                            {/if}
                                            
                                            
                                            <div class="ty-menu__submenu">
                                                <ul class="ty-menu__submenu-list cm-responsive-menu-submenu">
                                                    {if $item2.$childs}
                                                        {hook name="blocks:topmenu_dropdown_3levels_col_elements"}
                                                        {foreach from=$item2.$childs item="item3" name="item3"}
                                                            {assign var="item3_url" value=$item3|fn_form_dropdown_object_link:$block.type}
                                                            <li class="ty-menu__submenu-item{if $item3.active || $item3|fn_check_is_active_menu_item:$block.type} ty-menu__submenu-item-active{/if}{if $item3.class} {$item3.class}{/if}">
                                                                <a{if $item3_url} href="{$item3_url}"{/if}
                                                                        class="ty-menu__submenu-link" {if $item3.new_window}target="_blank"{/if}>{$item3.$name}</a>
                                                            </li>
                                                        {/foreach}
                                                        {if $item2.show_more && $item2_url}
                                                            <li class="ty-menu__submenu-item ty-menu__submenu-alt-link">
                                                                <a href="{$item2_url}"
                                                                   class="ty-menu__submenu-link" {if $item2.new_window}target="_blank"{/if}>{__("text_topmenu_view_more")}</a>
                                                            </li>
                                                        {/if}
                                                        {/hook}
                                                    {/if}
                                                </ul>
                                            </div>
                                            
                                            
                                        </li>
                                        </div>
                                    {/foreach}
                                    
                                    
                                    
                                    
                                    {if $item.show_more && $item_url}
                                        <li class="ty-menu__submenu-dropdown-bottom">
                                            <a href="{$item_url}" {if $item.new_window}target="_blank"{/if}>{__("text_topmenu_more", ["[item]" => $item.$name])}</a>
                                        </li>
                                    {/if}
                                </ul>
                            {/hook}
                        </div>
                    {/if}

                {/if}
                
                
                
                
                
                
           
                
                
                
                
                
                
                
            
    </li>
    </div>
{/hook}

{/foreach}

{/strip}{/hook}