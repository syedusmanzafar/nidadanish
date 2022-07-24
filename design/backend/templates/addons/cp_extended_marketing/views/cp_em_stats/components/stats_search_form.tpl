<div class="sidebar-row">
    <h6>{__("search")}</h6>
    <form action="{""|fn_url}" name="logs_form" method="get">
        <input type="hidden" name="object" value="{$smarty.request.object}">
        <div class="sidebar-field">
            <label for="notice">{__("cp_em_notice_txt")}</label>
            <input type="text" name="notice" id="notice" value="{$search.notice}" size="30"/>
        </div>
        <div class="sidebar-field">
            <label for="s_type">{__("type")}</label>
            <select name="type" id="s_type">
                <option value="">{__("all")}</option>
                {foreach from=$notice_types item="n_type"}
                    <option value="{$n_type.type}" {if $search.type && in_array($n_type.type, $search.type)}selected="selected"{/if}>{$n_type.title}</option>
                {/foreach}
            </select>
        </div>
        {include file="buttons/search.tpl" but_name="dispatch[cp_em_stats.manage]"}
    </form>
</div>