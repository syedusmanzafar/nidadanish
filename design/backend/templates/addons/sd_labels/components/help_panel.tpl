{$panel_type = $panel_type|default:"info"}
{$icons_size = $icons_size|default:24}

<div class="sd-help-panel sd-help-panel--type-{$panel_type}">
    <div class="sd-help-panel__icon-container">
        {include "addons/sd_labels/common/icons.tpl"
            icon_name  = "panel_"|cat:$panel_type
            icon_meta  = "sd-help-panel__icon sd-help-panel__icon--name-"|cat:$panel_type
            icons_size = $icons_size
        }
    </div>
    <div class="sd-help-panel__content">
        {$panel_content nofilter}
    </div>
</div>