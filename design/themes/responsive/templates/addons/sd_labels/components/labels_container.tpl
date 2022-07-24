{strip}
    {if $items_graphic || $items_text}
        {$container_classlist = "sd-labels js-labels "|cat:$label_list.class}
        {if $label_list.area}
            {$container_classlist = $container_classlist|cat:" sd-labels--area-"|cat:$label_list.area}
        {/if}
        {if $label_list.location}
            {$container_classlist = $container_classlist|cat:" sd-labels--location-"|cat:$label_list.location}
        {/if}
        {if $label_list.position}
            {$container_classlist = $container_classlist|cat:" sd-labels--position-"|cat:$label_list.position}
        {/if}
        {if $label_list.hide_labels_on_hover}
            {$container_classlist = $container_classlist|cat:" sd-labels--hide-on-hover"}
        {/if}
        {if $label_list.area == "detail-page" && $label_list.location == "overlay"}
            {$container_classlist = $container_classlist|cat:" js-labels-update-margin"}
        {/if}

        <div class="{$container_classlist}">
            {if $items_graphic}
                <ul class="sd-labels__list sd-labels__list--type-graphic{if $items_graphic_show_in_column === "YesNo::YES"|enum} sd-labels__list--display-in-column{/if}">
                    {foreach $items_graphic as $item}
                        {if $item.status == "Tygh\Enum\LabelStatus::THEME_LABEL"|constant}
                            {$use_theme_label = "YesNo::YES"|enum}
                        {else}
                            {$use_theme_label = "YesNo::NO"|enum}
                        {/if}

                        {capture "labels_item"}
                            {if $use_theme_label === "YesNo::YES"|enum}
                                {include "addons/$item.belong/components/default_labels/{$item.label_type}.tpl"}
                            {else}
                                {include "addons/sd_labels/components/label.tpl"
                                    label = $item
                                }
                            {/if}
                        {/capture}

                        {$labels_item = $smarty.capture.labels_item|trim|strip}

                        {if $labels_item}
                            <li class="sd-labels__item">
                                {$labels_item nofilter}
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            {/if}

            {if $items_text}
                <ul class="sd-labels__list sd-labels__list--type-text{if $items_text_show_in_column === "YesNo::YES"|enum} sd-labels__list--display-in-column{/if}">
                    {foreach $items_text as $item}
                        {if $item.status == "Tygh\Enum\LabelStatus::THEME_LABEL"|constant}
                            {$use_theme_label = "YesNo::YES"|enum}
                        {else}
                            {$use_theme_label = "YesNo::NO"|enum}
                        {/if}

                        {capture "labels_item"}
                            {if $use_theme_label === "YesNo::YES"|enum}
                                {include "addons/{$item.belong}/components/default_labels/{$item.label_type}.tpl"}
                            {else}
                                {include "addons/sd_labels/components/label.tpl"
                                    label = $item
                                }
                            {/if}
                        {/capture}

                        {$labels_item = $smarty.capture.labels_item|trim|strip}

                        {if $labels_item}
                            <li class="sd-labels__item">
                                {$labels_item nofilter}
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            {/if}
        </div>
    {/if}
{/strip}
