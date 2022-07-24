<div class="sidebar-row">
    <h6>{__("search")}</h6>

    <form name="labels_search_form" action="{fn_url("")}" method="get">
        {if $smarty.request.redirect_url}
            <input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
        {/if}

        {if $selected_section != ""}
            <input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />
        {/if}

        {if $put_request_vars}
            {array_to_fields data=$smarty.request skip=["callback"]}
        {/if}

        {$extra nofilter}

        {capture name="simple_search"}
            <div class="sidebar-field">
                <label for="elm_name">{__("sd_labels")}</label>
                <div class="break">
                    <input type="text" name="name" id="elm_name" value="{$search.name}" />
                </div>
            </div>

            {if fn_allowed_for("MULTIVENDOR")}
                <div class="sidebar-field">
                    <label for="elm_available_for_vendors">{__("sd_labels.available_for_vendors")}</label>
                    <div class="controls">
                        <select name="available_for_vendors" id="elm_available_for_vendors">
                            <option value="">{__("all")}</option>
                            <option value="{"YesNo::YES"|enum}" {if $search.available_for_vendors === "YesNo::YES"|enum}selected="selected"{/if}>
                                {__("yes")}
                            </option>
                            <option value="{"YesNo::NO"|enum}" {if $search.available_for_vendors === "YesNo::NO"|enum}selected="selected"{/if}>
                                {__("no")}
                            </option>
                        </select>
                    </div>
                </div>
            {/if}

            <div class="sidebar-field">
                <label for="elm_type">{__("status")}</label>
                {$items_status = fn_sd_labels_get_label_statuses(true)}
                <div class="controls">
                    <select name="status" id="elm_type">
                        <option value="">{__("all")}</option>
                        {foreach $items_status as $key => $status}
                            <option value="{$key}" {if $search.status == $key}selected="selected"{/if}>{$status}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {/capture}

        {include "common/advanced_search.tpl"
            no_adv_link=true
            simple_search=$smarty.capture.simple_search
            dispatch=$dispatch
            view_type="sd_labels"
        }
    </form>
</div>
