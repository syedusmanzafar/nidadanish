{capture name="mainbox"}

{include file="common/pagination.tpl"}

{if $logs}
    <div class="table-responsive-wrapper">
        <table class="table table--relative table-responsive">
        <thead>
            <tr>
                <th>{__("content")}</th>
                <th>{__("user")}</th>
                <th>{__("time")}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$logs key="log_id" item="log"}
            <tr>
                <td width="70%" class="wrap" data-th="{__("content")}">
                    <div class="cp-log-content">
                        {if $section_settings.actions[$log.action]}
                            <div class="cp-log-action">
                                {$section_settings.actions[$log.action].title}
                            </div>
                        {/if}
                        {if $log.content}
                            {if $log.content|is_array}
                                {foreach from=$log.content key="c_key" item="c_value"}
                                    <div class="cp-log-content-title strong">{__($c_key)}:</div>
                                    <div class="cp-log-content-value">
                                        {if $c_value|is_array}
                                            {$c_value|serialize}
                                        {else}
                                            {$c_value nofilter}
                                        {/if}
                                    </div>
                                {/foreach}
                            {else}
                                <div class="cp-log-content-value">{$log.content nofilter}</div>
                            {/if}
                        {else}
                            &nbsp;
                        {/if}
                    </div>
                </td>
                <td data-th="{__("user")}">
                    {if $log.user_id}
                        <a href="{"profiles.update?user_id=`$log.user_id`"|fn_url}">
                            {$log.user.lastname} {$log.user.firstname}
                        </a>
                    {else}
                        &mdash;
                    {/if}
                </td>
                <td data-th="{__("time")}">
                    <span class="nowrap">
                        {$log.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
                    </span>
                </td>
            </tr>
        {/foreach}
        </tbody>
        </table>
    </div>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}

{include file="common/pagination.tpl"}
{/capture}

{capture name="sidebar"}
    {include file="addons/cp_addons_manager/views/cp_custom_logs/components/search_form.tpl"}
{/capture}

{capture name="buttons"}
    {capture name="tools_list"}
        <li>{btn type="list" text=__("clean_logs") href="cp_custom_logs.clear?section=`$active_section`" class="cm-confirm" method="POST"}</li>
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}

{capture name="title"}{__("cart_power")}: {__("cp_custom_logs")}{/capture}

{include file="common/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar}