{$graphic_label_data_id = "drag_graphic_labels"}
{$graphic_label_data_prefix_id = "grphc_lbl_"}
{$thumbnail_size = 50}

<div id="content_graphic_labels">
    {if $labels_graphic}
        {include "common/pagination.tpl"
            save_current_page=true
            save_current_url=true
            div_id="content_graphic_labels"
        }
        <input
            class="js-update-positions"
            id="{$graphic_label_data_prefix_id}{$graphic_label_data_id}_ids"
            type="hidden"
            name="graphic_labels_list"
            value="{if $labels_graphic_ids}{","|implode:$labels_graphic_ids}{/if}"
        />

        <div class="table-wrapper" id="manage_graphic_labels_list">
            <table class="sd-graphic-labels-list table table-middle table--relative table-objects">
                <thead>
                    <tr>
                        <th class="left mobile-hide" width="1%"></th>
                        <th width="80px">&nbsp;</th>
                        <th>{__("name")}</th>
                        <th class="right mobile-hide" width="9%">&nbsp;</th>
                        {if fn_allowed_for("MULTIVENDOR")}
                            <th class="right mobile-hide" width="12%">{__("sd_labels.available_for_vendors")}</th>
                        {/if}
                        <th class="right" width="9%">{__("status")}</th>
                    </tr>
                </thead>
                <tbody
                    id="{$graphic_label_data_id}"
                    class="js-sortable-container"
                    data-ca-sortable-item-class="sd-graphic-labels-list__item"
                    data-ca-data-prefix-id="{$graphic_label_data_prefix_id}"
                    data-ca-data-id="{$graphic_label_data_id}"
                >
                    {foreach $labels_graphic as $key => $label}
                        {$allow_remove = $label.label_type === "Tygh\Enum\LabelType::CUSTOM"|constant}
                        {$use_theme_label =
                            $label.label_type === "Tygh\Enum\LabelType::FREESHIPPING"|constant
                                || $label.label_type === "Tygh\Enum\LabelType::DISCOUNT"|constant
                        }
                        {capture name="tools_list"}
                            <li>{btn type="list"
                                    id="group{$label.label_id}"
                                    text=__("edit")
                                    act="link"
                                    href="sd_labels.update?label_id={$label.label_id}"
                                }
                            </li>
                            {if $allow_remove}
                                <li>
                                    {btn
                                        type="list"
                                        class="cm-confirm cm-post"
                                        text=__("delete")
                                        href="sd_labels.delete?label_id={$label.label_id}"
                                    }
                                </li>
                            {/if}
                        {/capture}

                        <tr id="{$graphic_label_data_id}_{$key}"
                            class="
                                    sd-graphic-labels-list__item
                                    cm-js-item
                                    cm-no-hide-input
                                    cm-sortable-row
                                    cm-sortable-id-{$graphic_label_data_id}
                                    cm-row-status-{$label.status|lower}
                                "
                        >
                            <td class="left mobile-hide" width="1%">
                                <input class="js-sortable-item-id" type="hidden" name="key" value="{$label.label_id}" />
                                <span class="handler"></span>
                            </td>
                            <td width="80px">
                                <a
                                    class="cm-external-click"
                                    data-ca-external-click-id="{"opener_group{$label.label_id}"}"
                                >
                                    {include "common/image.tpl"
                                        show_detailed_link=false
                                        image=$label.main_pair
                                        image_width=$thumbnail_size
                                        image_height=$thumbnail_size
                                    }
                                </a>
                            </td>
                            <td>
                                <a class="row-status" href="{fn_url("sd_labels.update?label_id={$label.label_id}")}">
                                    {$label.name}
                                </a>
                            </td>
                            <td class="right mobile-hide" width="9%">
                                <div class="hidden-tools">
                                    {dropdown content=$smarty.capture.tools_list}
                                </div>
                            </td>
                            {if fn_allowed_for("MULTIVENDOR")}
                                {if $label.attachable === "YesNo::YES"|enum}
                                    <td class="right mobile-hide row-status" width="12%">
                                        {if $label.available_for_vendors === "YesNo::YES"|enum}
                                            {__("yes")}
                                        {else}
                                            {__("no")}
                                        {/if}
                                    </td>
                                {else}
                                    <td class="right mobile-hide row-status" width="12%">
                                        {__("sd_labels.label_setting.available_by_default")}
                                    </td>
                                {/if}
                            {/if}
                            <td class="right" width="9%">
                                {include "addons/sd_labels/views/sd_labels/components/label_status.tpl"
                                    id              = $label.label_id
                                    status          = $label.status
                                    object_id_name  = "label_id"
                                    table           = "sd_labels"
                                    use_theme_label = $use_theme_label
                                }
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        <!--manage_graphic_labels_list--></div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
<!--content_graphic_labels--></div>
