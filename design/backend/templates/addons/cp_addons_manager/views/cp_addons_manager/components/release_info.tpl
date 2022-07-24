<strong>{__("cp_release_date")}:</strong>
{if $release.timestamp}
    <div>{$release.timestamp|date_format:"`$settings.Appearance.date_format`"}</div>
{else}
    <div>&mdash;</div>
{/if}
<span></span>
<strong>{__("cp_сompatibility")}:</strong>
{if $release.edition}
    <div>
        {if $release.edition == "A"}
            {__("cp_cscart")}&nbsp;/&nbsp;{__("cp_multivendor")}
        {elseif $release.edition == "C"}
            {__("cp_cscart")}
        {elseif $release.edition == "M"}
            {__("multivendor")}
        {/if}
    </div>
{/if}
{if $release.version_from}
    <div>[v{$release.version_from}&nbsp;-&nbsp;v{$release.version_to}]</div>
{else}
    <div>&mdash;</div>
{/if}
