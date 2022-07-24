{$text_label_data_id = "drag_text_labels"}
{$text_label_data_prefix_id = "txt_lbl_"}

<div id="content_text_labels">
    {if $labels_text}
        {include "common/pagination.tpl"
            save_current_page=true
            save_current_url=true
            div_id="content_text_labels"
        }
        <input
            class="js-update-positions"
            id="{$text_label_data_prefix_id}{$text_label_data_id}_ids"
            type="hidden"
            name="text_labels_list"
            value="{if $labels_text_ids}{","|implode:$labels_text_ids}{/if}"
        />

        <div class="items-container" id="manage_text_labels">
            <div class="table-wrapper" id="manage_text_labels_list">
                <table class="sd-text-labels-list table table-middle table--relative table-objects">
                    <thead>
                        <tr>
                            <th class="left mobile-hide" width="1%" class="no-padding-td"></th>
                            <th>{__("name")}</th>
                            <th class="mobile-hide" width="15%">{__("sd_labels.label_setting.background_color")}</th>
                            <th class="mobile-hide" width="15%">{__("sd_labels.label_setting.text_color")}</th>
                            <th class="right mobile-hide" width="9%">&nbsp;</th>
                            {if fn_allowed_for("MULTIVENDOR")}
                                <th class="right mobile-hide" width="12%">{__("sd_labels.available_for_vendors")}</th>
                            {/if}
                            <th class="right" width="9%">{__("status")}</th>
                        </tr>
                    </thead>
                    <tbody
                        id="{$text_label_data_id}"
                        class="sd-text-labels-list__body js-sortable-container"
                        data-ca-sortable-item-class="sd-text-labels-list__item"
                        data-ca-data-prefix-id="{$text_label_data_prefix_id}"
                        data-ca-data-id="{$text_label_data_id}"
                    >
                        {foreach $labels_text as $key => $label}
                            {$allow_remove = $label.label_type === "Tygh\Enum\LabelType::CUSTOM"|constant}
                            {$use_theme_label = in_array($label.label_type, $theme_labels)}
                            {capture name="tools_list"}
                                <li>
                                    {btn
                                        type="list"
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

                            <tr id="{$text_label_data_id}_{$key}"
                                class="
                                    sd-text-labels-list__item
                                    cm-js-item
                                    cm-no-hide-input
                                    cm-sortable-row
                                    cm-sortable-id-{$text_label_data_id}
                                    cm-row-status-{$label.status|lower}
                                "
                            >
                                <td class="left mobile-hide" width="1%">
                                    <input class="js-sortable-item-id" type="hidden" name="key" value="{$label.label_id}" />
                                    <span class="handler"></span>
                                </td>
                                <td>
                                    <a class="row-status" href="{"sd_labels.update?label_id={$label.label_id}"|fn_url}">{$label.name}</a>
                                </td>
                                <td class="mobile-hide" width="15%">
                                    <a class="sd-label-color cm-external-click "
                                        href="{fn_url("sd_labels.update?label_id={$label.label_id}")}"
                                    >
                                        <span class="sd-label-color__inner" style="background-color: {$label.background_color};"></span>
                                    </a>
                                </td>
                                <td class="mobile-hide" width="15%">
                                    <a class="sd-label-color cm-external-click"
                                        href="{fn_url("sd_labels.update?label_id={$label.label_id}")}"
                                    >
                                        <span class="sd-label-color__inner" style="background-color: {$label.text_color};"></span>
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
                                <td class="right nowrap" width="9%">
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
            <!--manage_text_labels_list--></div>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
<!--content_text_labels--></div>