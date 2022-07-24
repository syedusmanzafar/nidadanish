{** block-description:sd_dropdown_vertical **}

{if $block.properties.open_menu == "click"}
    {script src="js/addons/sd_menu/click_toggle.js"}
{/if}

<div class="ty-menu ty-menu-vertical ty-menu-vertical__dropdown sd-amazon-menu sd-menu-vert {if $block.properties.open_menu == 'click'}click{/if}">
    <ul id="vmenu_{$block.block_id}" class="ty-menu__items cm-responsive-menu{if $block.properties.right_to_left_orientation =="Y"} rtl{/if}">
        <li class="ty-menu__item ty-menu__menu-btn visible-phone">
            <a class="ty-menu__item-link">
                <i class="ty-icon-short-list"></i>
                <span>{__("menu")}</span>
            </a>
        </li>
        {include file="addons/sd_menu/blocks/sidebox_dropdown.tpl" items=$items separated=true submenu=false name="item" item_id="param_id" childs="subitems"}
    </ul>
</div>