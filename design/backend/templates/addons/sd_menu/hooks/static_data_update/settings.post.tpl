{if $id}
    {include file="common/subheader.tpl" title=__("addons.sd_menu.sd_menu") target="#sd_menu_`$id`"}
    <fieldset>
        <div id="sd_menu_{$id}" class="in collapse">

            <div class="control-group">
                <label for="label_{$id}" class="control-label">{__("addons.sd_menu.label")}:</label>
                <div class="controls">
                    <input type="text" size="40" id="label_{$id}" name="static_data[label_text]" value="{$static_data_descr.label_text}" class="input-text-large">
                </div>
            </div>

            <div class="control-group">
                <label for="label_color_{$id}" class="control-label">{__("addons.sd_menu.label_color")}:</label>
                <div class="controls">
                    {include file="common/colorpicker.tpl" cp_name="static_data[label_color]" cp_id="label_color_`$id`" cp_value=$static_data_descr.label_color}
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">{__("addons.sd_menu.item_icon")}:</label>
                <div class="controls">
                    {include file="common/attach_images.tpl" image_name="menu_item_icon" image_object_type="menu_icon" image_pair=$static_data_descr.main_pair_icon no_detailed="Y" hide_titles="Y" image_object_id=$id icon_text=__("text_category_icon")}
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">{__("addons.sd_menu.banner_image")}:</label>
                <div class="controls">
                    {include file="common/attach_images.tpl" image_name="menu_banner_image" image_object_type="menu_banner" image_pair=$static_data_descr.banner_main_pair_icon no_thumbnail="Y" image_object_id=$id}
                </div>
            </div>

            <div class="control-group">
                <label for="banner_url_{$id}" class="control-label">{__("addons.sd_menu.banner_url")}:</label>
                <div class="controls">
                    <input type="text" size="40" id="banner_url_{$id}" name="static_data[banner_url]" value="{$static_data_descr.banner_url}" class="input-text-large">
                </div>
            </div>
        </div>
    </fieldset>
{else}
    <div class="control-group">
        <p>{__("addons.sd_menu.new_menu_notice")}</p>
    </div>
{/if}
