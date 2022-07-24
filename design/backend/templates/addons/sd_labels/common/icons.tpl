{strip}
    {if $icon_name == "panel_info"}
        {$icons_size = $icons_size|default:24}
        <svg class="{$icon_meta}" width="{$icons_size}" height="{$icons_size}" viewBox="0 0 24 24" focusable="false" role="presentation">
            <path d="M12 20a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm0-8.5a1 1 0 0 0-1 1V15a1 1 0 0 0 2 0v-2.5a1 1 0 0 0-1-1zm0-1.125a1.375 1.375 0 1 0 0-2.75 1.375 1.375 0 0 0 0 2.75z" fill="currentColor" fill-rule="evenodd"></path>
        </svg>

    {else if $icon_name == "panel_warning"}
        {$icons_size = $icons_size|default:24}
        <svg class="{$icon_meta}" width="{$icons_size}" height="{$icons_size}" viewBox="0 0 24 24" focusable="false" role="presentation">
            <g fill-rule="evenodd">
                <path d="M12.938 4.967c-.518-.978-1.36-.974-1.876 0L3.938 18.425c-.518.978-.045 1.771 1.057 1.771h14.01c1.102 0 1.573-.797 1.057-1.771L12.938 4.967z" fill="currentColor"></path>
                <path d="M12 15a1 1 0 0 1-1-1V9a1 1 0 0 1 2 0v5a1 1 0 0 1-1 1m0 3a1 1 0 0 1 0-2 1 1 0 0 1 0 2" fill="inherit"></path>
            </g>
        </svg>

    {else if $icon_name == "panel_success"}
        {$icons_size = $icons_size|default:24}
        <svg class="{$icon_meta}" width="{$icons_size}" height="{$icons_size}" viewBox="0 0 24 24" focusable="false" role="presentation">
            <path d="M12 20a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm1.364-10.964l-2.152 4.11-1.543-1.39a1 1 0 1 0-1.338 1.487l2.5 2.25a1 1 0 0 0 1.555-.279l2.75-5.25a1 1 0 0 0-1.772-.928z" fill="currentColor" fill-rule="evenodd"></path>
        </svg>
    {else if $icon_name == "panel_error"}
        {$icons_size = $icons_size|default:24}
        <svg class="{$icon_meta}" width="{$icons_size}" height="{$icons_size}" viewBox="0 0 24 24" focusable="false" role="presentation">
            <path d="M13.485 11.929l2.122-2.121a1 1 0 0 0-1.415-1.415l-2.12 2.122L9.95 8.393a1 1 0 0 0-1.414 1.415l2.12 2.12-2.12 2.122a1 1 0 0 0 1.414 1.414l2.121-2.12 2.121 2.12a1 1 0 1 0 1.415-1.414l-2.122-2.121zM12 20a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" fill="currentColor" fill-rule="evenodd"></path>
        </svg>
    {/if}
{/strip}