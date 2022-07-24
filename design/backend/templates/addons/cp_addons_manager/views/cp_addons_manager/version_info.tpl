{assign var="id" value=$smarty.request.package_id}

<div id="content_versions_{$id}">
    <div class="cp-addon-versions-info">
        {if $releases}
            <table class="table table-middle">
                <thead>
                    <tr>
                        <th width="30%" class="left">{__("cp_version")}</th>
                        <th class="left">{__("information")}</th>
                    </tr>
                </thead>
                {foreach from=$releases item="release"}
                    <tr>
                        <td>
                            v{$release.version} {__("cp_from")} {$release.timestamp|date_format:"`$settings.Appearance.date_format`"}
                        </td>
                        <td class="left">
                            {if $release.message}
                                {$release.message|nl2br nofilter}
                            {else}
                                -
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </table>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
    </div>
<!--content_versions_{$id}--></div>