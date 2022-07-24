{capture name="mainbox"}
{$id = $label_data.label_id|default:0}

{$display_text = "Tygh\Addons\SdLabels\Labels\Label::TEXT"|constant}
{$display_graphic = "Tygh\Addons\SdLabels\Labels\Label::GRAPHIC"|constant}
{$default_text_color = "Tygh\Addons\SdLabels\Labels\Label::DEFAULT_TEXT_COLOR"|constant}
{$default_graphic_color = "Tygh\Addons\SdLabels\Labels\Label::DEFAULT_BACKGROUND_COLOR"|constant}
{$label_background_color = $label_data.background_color|default:$default_graphic_color}
{$label_text_color = $label_data.text_color|default:$default_text_color}
{$use_theme_label = in_array($label_data.label_type, $theme_labels)}
{if !$label_data}
    {$label_data.display_type = $display_text}
{/if}

<div class="items-container{if ""|fn_check_form_permissions} cm-hide-inputs{/if}" id="content_group_{$id}">
    <form
        class="form-horizontal form-edit"
        action="{fn_url("")}"
        method="post"
        enctype="multipart/form-data"
        name="update_label_form_{$id}"
    >
        <input type="hidden" name="label_data[label_id]" value="{$id}" />

        {capture name="tabsbox"}
            <div id="content_general_{$id}">
                <div class="control-group">
                    <label class="control-label cm-required" for="elm_label_name_{$id}">
                        {__("sd_labels.label_setting.name")}{include "common/tooltip.tpl"
                        tooltip=__("sd_labels.label_setting.tooltip_name")}
                    </label>
                    <div class="controls">
                        <input id="elm_label_name_{$id}" type="text" name="label_data[name]" value="{$label_data.name}" />
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label cm-required" for="elm_label_position_{$id}">
                        {__("sd_labels.label_setting.position")}
                    </label>
                    <div class="controls">
                        <input id="elm_label_position_{$id}"
                            type="text"
                            name="label_data[position]"
                            value="{$label_data.position}{if !$label_data.label_id}0{/if}"
                        />
                    </div>
                </div>
                {include file = "addons/sd_labels/views/sd_labels/components/status_on_update.tpl"
                    input_name      = "label_data[status]"
                    id              = "elm_label_status_{$id}"
                    obj             = $label_data
                    use_theme_label = $use_theme_label
                }
                <div class="control-group cm-required">
                    <label class="control-label" for="label_data_display_type_{$id}">
                        {__("sd_labels.label_setting.labels_type")}:
                    </label>

                    <div class="controls">
                        <select
                            id="label_data_display_type_{$id}"
                            name="label_data[display_type]"
                            onchange="
                                if ($(this).val() === '{$display_text}') {
                                    Tygh.$('#background_picker_{$id}').show(300);
                                    Tygh.$('#text_picker_{$id}').show(300);
                                    Tygh.$('#image_uploader_{$id}').hide(300);
                                } else {
                                    Tygh.$('#background_picker_{$id}').hide(300);
                                    Tygh.$('#text_picker_{$id}').hide(300);
                                    Tygh.$('#image_uploader_{$id}').show(300);
                                }
                            "
                        >
                            <option
                                value="{$display_text}"
                                {if $label_data.display_type === $display_text}selected="selected"{/if}
                            >
                                {__("sd_labels.label_setting.labels_type.text")}
                            </option>
                            <option
                                value="{$display_graphic}"
                                {if $label_data.display_type === $display_graphic}selected="selected"{/if}
                            >
                                {__("sd_labels.label_setting.labels_type.graphic")}
                            </option>
                        </select>
                    </div>
                </div>

                <div
                    class="control-group {if $label_data.display_type != $display_text}hidden{/if}"
                    id="background_picker_{$id}"
                >
                    <label class="control-label" for="elm_label_background_color_{$id}">
                        {__("sd_labels.label_setting.background_color")}:
                    </label>

                    <div class="controls">
                        {if fn_check_form_permissions("")}
                            <div class="sd-label-color">
                                <span class="sd-label-color__inner" style="background-color: {$label_background_color};"></span>
                            </div>
                        {else}
                            <div class="colorpicker">
                                <input
                                    class="cm-colorpicker"
                                    id="elm_label_background_color_{$id}"
                                    type="text"
                                    name="label_data[background_color]"
                                    value="{$label_background_color}"
                                    data-ca-spectrum-show-alpha="true"
                                    data-ca-spectrum-preferred-format="rgb"
                                />
                            </div>
                        {/if}
                    </div>
                </div>

                <div
                    class="
                        control-group
                        {if $label_data.display_type != $display_text}hidden{/if}
                    "
                    id="text_picker_{$id}"
                >
                    <label class="control-label" for="elm_label_text_color_{$id}">
                        {__("sd_labels.label_setting.text_color")}:
                    </label>
                    <div class="controls">
                        {if fn_check_form_permissions("")}
                            <div class="sd-label-color">
                                <span class="sd-label-color__inner" style="background-color: {$label_text_color};"></span>
                            </div>
                        {else}
                            <div class="colorpicker">
                                <input
                                    class="cm-colorpicker"
                                    id="elm_label_text_color_{$id}"
                                    type="text"
                                    name="label_data[text_color]"
                                    value="{$label_text_color}"
                                    data-ca-spectrum-preferred-format="rgb"
                                />
                            </div>
                        {/if}
                    </div>
                </div>

                <div
                    class="
                        control-group
                        {if $label_data.display_type != $display_graphic}hidden{/if}
                    "
                    id="image_uploader_{$label_data.label_id}"
                >
                    <label class="control-label">{__("image")}:</label>
                    <div class="controls">
                        {include "common/attach_images.tpl"
                            image_name        = "labels_main"
                            no_detailed       = true
                            image_object_type = "variant_image"
                            image_pair        = $label_data.main_pair
                            prefix            = $id
                        }
                    </div>
                </div>
                {if $id && $languages|count > 1}
                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox">
                                <input type="hidden" name="label_data[apply_tooltip_to_all_langs]"
                                       value="{"YesNo::NO"|enum}"/>
                                <input type="checkbox" id="elm_label_apply_tooltip_to_all_langs_{$id}"
                                       name="label_data[apply_tooltip_to_all_langs]" value="{"YesNo::YES"|enum}"/>
                                {__("sd_labels.label_setting.apply_tooltip_to_all_langs")}
                            </label>
                        </div>
                    </div>
                {/if}

                <div class="control-group" id="tooltip_{$id}">
                    <label class="control-label">{__("sd_labels.label_setting.tooltip")}:</label>
                    <div class="controls">
                        {strip}
                            <textarea class="cm-wysiwyg input-large"
                                id="elm_label_tooltip_{$id}"
                                name="label_data[tooltip_content]"
                            >
                                {$label_data.tooltip_content}
                            </textarea>
                        {/strip}
                    </div>
                </div>

                {if fn_allowed_for("MULTIVENDOR")}
                    <div class="control-group" id="available_for_vendors">
                        <label class="control-label" for="elm_label_available_for_vendors_{$id}">{__("sd_labels.available_for_vendors")}</label>
                        <div class="controls">
                            {if $label_data.attachable !== "YesNo::NO"|enum}
                                <input type="hidden" name="label_data[available_for_vendors]" value={"YesNo::NO"|enum} />
                            {/if}
                            <input id="elm_label_available_for_vendors_{$id}"
                                type="checkbox"
                                {if $label_data.attachable !== "YesNo::YES"|enum}
                                    disabled="disabled"
                                {else}
                                    name="label_data[available_for_vendors]"
                                    value="{"YesNo::YES"|enum}"
                                    {if $label_data.available_for_vendors === "YesNo::YES"|enum}
                                        checked="checked"
                                    {/if}
                                {/if}
                            />
                        </div>
                    </div>
                {/if}

               {if $label_data.hint}
                    <div class="widget-copy">
                        <div class="widget-copy__body">
                            <b class="widget-copy__title">{__("tip")}:</b>
                            <span class="widget-copy__text">{$label_data.hint}</span>
                        </div>
                    </div>
                {/if}
            </div>

            {if $label_data.additional_settings}
                <div id="content_additional_settings_{$id}">
                    {foreach $label_data.additional_settings as $additional_setting}
                        {if !empty($additional_setting.type)}
                            {include "addons/sd_labels/views/sd_labels/components/additional_settings/{$additional_setting.type}.tpl"
                                additional_setting=$additional_setting
                                label_data=$label_data
                                label_id=$id
                            }
                        {/if}
                    {/foreach}
                </div>
            {/if}
        {/capture}

        {include "common/tabsbox.tpl" content = $smarty.capture.tabsbox}

        {capture name="buttons"}
            {if $id}
                {capture name="tools_list"}
                    <li>
                        {btn
                            type="list"
                            text=__("delete")
                            class="cm-confirm"
                            href="sd_labels.delete?label_id={$id}"
                            method="POST"
                        }
                    </li>
                {/capture}
                {dropdown content=$smarty.capture.tools_list}
            {/if}

            {include "buttons/save_cancel.tpl"
                but_name="dispatch[sd_labels.update]"
                but_target_form="update_label_form_{$id}"
                save=$id
            }
        {/capture}
    </form>
<!--content_group_{$id}--></div>
{/capture}

{if !$id}
    {$title = __("sd_labels.manage_labels.new_label")}
{else}
    {$title_start = __("sd_labels.manage_labels.editing_label")}
    {$title_end = $label_data.name}
{/if}

{include "common/mainbox.tpl"
    title_start=$title_start
    title_end=$title_end
    title=$title
    content=$smarty.capture.mainbox
    select_languages=true
    buttons=$smarty.capture.buttons
}