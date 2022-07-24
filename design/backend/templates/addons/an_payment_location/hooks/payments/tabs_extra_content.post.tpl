<div id="content_tab_an_states_list_{$id}" class="hidden">
    <fieldset>
        {include file="common/double_selectboxes.tpl"
        title=__("states")
        first_name="payment_data[states_list]"
        first_data=$company_states_list
        second_name="all_states"
        second_data=$states_list}
    </fieldset>
</div>
{** /Company regions settings section **}