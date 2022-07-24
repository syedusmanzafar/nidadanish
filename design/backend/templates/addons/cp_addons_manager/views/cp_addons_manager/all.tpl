{script src="js/addons/cp_addons_manager/previewers/magnific.previewer.js"}

{capture name="mainbox"}
<div class="cp-all-addons" id="cp_all_addons_reload">
    {assign var="c_url" value=$config.current_url}

    {if $addon_sections}
    <div class="cp-addon-sections-wrap">
        <div class="cp-addon-section-items">
            {foreach from=$addon_sections item="section" key="section_key"}
                {$section_selected = false}
                {if $smarty.request.section == $section_key}
                    {$section_selected = true}
                {/if}
                {if !$section_selected}
                    <a class="cp-wrap-link" href="{$c_url|fn_query_remove:"section"}&section={$section_key}">
                {/if}
                <div class="cp-addon-section-item {if $section_selected}cp-selected{/if}">
                    {if $section_selected}
                         <a class="cp-clear-section" href="{$c_url|fn_query_remove:"section"}">
                            <i class="cp-am-icon-fail"></i>
                         </a>
                    {/if}
                    <div class="cp-addon-section-icon">
                        {if $section.image}
                            <img src="{$section.image}">
                        {/if}
                    </div>
                    <div class="cp-addon-section-title">{$section.name}</div>
                </div>
                {if !$section_selected}
                    </a>
                {/if}
            {/foreach}
        </div>
    </div>
    {/if}

    <form action="{""|fn_url}" method="post" name="addons_form">
        <div class="cp-addons-top-wrap">
            <a href="{$smarty.const.CP_ADDONS_ENDPOINT}" target="_blank" class="cp-store-link">
                {__("cp_go_to_cartpower")}
            </a>
            <div class="cp-list-changer-wrap">
                {if $smarty.request.only_avail != "Y"}{$check_avail = "Y"}{else}{$check_avail = "N"}{/if}
                <a href="{"cp_addons_manager.all?only_avail=`$check_avail`"|fn_url}">
                    <i class="cp-am-icon-{if $check_avail == "Y"}unchecked{else}checked{/if}"></i>
                    <span>{__("cp_display_avail_addons")}</span>
                </a>
            </div>
            <div class="clearfix"></div>
        </div>
        
        {if $addons_list}
            <div class="cp-addons-left-block">
            {foreach from=$addons_list item="addon" key="key" name="cp_addons_list"}
                {$is_selected = false}
                {if $smarty.request.selected == $key || !$smarty.request.selected && $smarty.foreach.cp_addons_list.first}
                    {$is_selected = true}
                {/if}
                
                <div class="cp-list-addon-item" id="cp_list_item_{$key}">
                    {if !$is_selected}
                        <a class="cp-am-select-item" {*class="cm-ajax" href="{"cp_addons_manager.all&selected=`$key`"|fn_url}"*} data-ca-url="{$c_url|fn_query_remove:"selected"}"|fn_url}" data-ca-selected="{$key}" data-ca-target-id="cp_all_addons_reload">
                    {/if}
                    <div class="cp-list-addon-info-wrap {if $is_selected}cp-selected{/if}">
                        <div class="cp-list-addon-icon">
                            {if $addon.image}
                                <img src="{$addon.image}">
                            {/if}
                        </div>
                        <div class="cp-list-addon-title-wrap">
                            <span class="cp-list-addon-title">{$addon.product}</span>
                        </div>
                        
                        <div class="cp-list-addon-info">    
                            <div class="cp-list-addon-description">{$addon.short_description nofilter}</div>
                            
                            <div class="cp-addon-avail-wrap">
                                {if $addon.is_avail == "Y"}
                                    <div class="cp-addon-avail-info"><i class="cp-am-icon-success"></i><span>{__("cp_avail_for_store")}</span></div>
                                {else}
                                    <div class="cp-addon-avail-info cp-not-avail"><i class="cp-am-icon-fail"></i><span>{__("cp_not_avail_for_store")}</span></div>
                                {/if}
                                <div class="cp-addon-release-info">
                                    {include file="addons/cp_addons_manager/views/cp_addons_manager/components/release_info.tpl" release=$addon}
                                </div>
                            </div>
                            
                            {assign var="is_free" value=false}
                            {if !$addon.price || $addon.price == 0.0}
                                {$is_free = true}
                            {/if}
                            <div class="cp-list-addon-bottom">
                                <div class="cp-addon-price-wrap">
                                    {if $is_free}
                                        <span class="cp-price cp-free">{__("free")}</span>
                                    {else}
                                        <span class="cp-old-price">
                                            {if $addon.str_list_price}
                                                {$addon.str_list_price nofilter}
                                            {/if}
                                        </span>
                                        <span class="cp-price">
                                            {if $addon.str_price}
                                                {$addon.str_price nofilter}
                                            {else}
                                                {$addon.price|string_format:"%.2f"}&nbsp;{$addon.currency}
                                            {/if}
                                        </span>
                                    {/if}
                                </div>
                                <div class="cp-addon-buttons">
                                    {if $is_selected}
                                        <a class="cp-am-buy-btn" href="{$addon.url}" target="_blank">{if $is_free}{__("cp_receive")}{else}{__("cp_am_buy")}{/if}</a>
                                    {/if}
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    {if !$is_selected}
                        </a>
                    {/if}
                    {if $is_selected}
                        <div class="cp-addons-main-info cp-am-check-height">
                            <div class="cp-addon-title">{$addon.product}</div>
                            {*<div class="cp-addon-description">{$addon.full_description nofilter}</div>*}
                            
                            {if $addon.video}
                                <div class="cp-addon-video">{$addon.video nofilter}</div>
                            {/if}
                            
                            <div class="cp-addon-images">
                                <div class="cp-addon-images-wrap">
                                {foreach from=$addon.addon_images item="image_data" key="img_key"}
                                    {if $image_data}
                                    <a id="det_img_link_{$key}_{$img_key}" class="cm-previewer" href="{$image_data.image_path}" data-ca-image-id="preview[images_{$key}]">
                                        <img class="cm-image" id="det_img_{$key}_{$img_key}" src="{$image_data.image_path}" width="{$image_data.width}" height="{$image_data.height}" alt="{$image_data.alt}" title="{$image_data.alt}" />
                                    </a>
                                    {/if}
                                {/foreach}
                                </div>
                            </div>
                            
                            <div class="cp-addon-extra-buttons">
                                {if $addon.documentation_link}
                                    <a href="{$addon.documentation_link}" target="_blank" class="cp-am-btn cp-documentation-btn">
                                        {__("cp_am_documentation")}
                                    </a>
                                {/if}
                                <a class="cp-am-btn cp-contact-btn {if !$addon.documentation_link}cp-full{/if}" href="{$addon.url}?ask_question=Y" target="_blank">
                                    {__("cp_ask_question")}
                                </a>
                                {if $addon.cp_demo_link}
                                    <a href="{$addon.cp_demo_link}" target="_blank" class="cp-am-btn cp-demo-btn">
                                        <i class="cp-am-icon-web-design"></i><span>{__("cp_view_demo")}</span>
                                    </a>
                                {/if}
                            </div>
                        </div>
                    {/if}
                </div>
            {/foreach}
            </div>
            <div class="cp-am-height-block"></div>
            
            {*include file="addons/cp_addons_manager/views/cp_addons_manager/components/rating.tpl"*}
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}

        {capture name="buttons"}
            {capture name="tools_list"}
            {/capture}
            {dropdown content=$smarty.capture.tools_list}
        {/capture}
        {capture name="adv_buttons"}
        {/capture}
    </form>
<!--cp_all_addons_reload--></div>
{/capture}

{capture name="title"}{__("cart_power")}: {__("cp_all_addons")}{/capture}

{include file="common/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons select_languages=false no_sidebar=true}
