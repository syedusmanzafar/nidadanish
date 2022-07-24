{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="fields_form" class="form-horizontal form-edit">
        <table class="table table-middle">
            <thead>
                <tr class="first-sibling">
                    <th>{__('product_field')}</th>
                    <th>{__('field_weight_none')}</th>
                    <th>{__('field_weight_before')}</th>
                    <th>{__('field_weight_after')}</th>
                    <th>{__('field_weight_any')}</th>
                    <th width="10%">&nbsp;</th>
                </tr>
            </thead>
            {foreach from=$rules item=rule}
                {assign var="num" value=$rule@iteration}
                <tbody class="hover cm-row-item" id="field_row_{$num}">
                    <tr>
                        <td class="cm-non-cb">
                            <select name="fields[{$num}][field]">
                                <option value="">{__('none')}</option>
                                {foreach from=$fields item=name key=id}
                                    <option value="{$id}" {if $id == $rule.field}selected="selected"{/if}>{$name}</option>
                                {/foreach}
                            </select>
                        </td>
                        <td class="cm-non-cb">
                            <input type="text" name="fields[{$num}][none]" value="{$rule.none}" size="5" class="input-mini"/>
                        </td>
                        <td class="cm-non-cb">
                            <input type="text" name="fields[{$num}][before]" value="{$rule.before}" size="5" class="input-mini"/>
                        </td>
                        <td class="cm-non-cb">
                            <input type="text" name="fields[{$num}][after]" value="{$rule.after}" size="5" class="input-mini"/>
                        </td>
                        <td class="cm-non-cb">
                            <input type="text" name="fields[{$num}][any]" value="{$rule.any}" size="5" class="input-mini"/>
                        </td>
                        <td class="right cm-non-cb">
                            {include file="buttons/multiple_buttons.tpl" item_id="field_row_`$num`" tag_level="1" only_delete="Y"}
                        </td>
                    </tr>
                </tbody>
            {/foreach}
            {math equation="x + 1" assign="num" x=$num|default:0}
            <tbody class="hover cm-row-item" id="box_add_field_row">
                <tr>
                    <td class="cm-non-cb">
                        <select name="fields[{$num}][field]">
                            <option value="">{__('none')}</option>
                            {foreach from=$fields item=name key=id}
                                <option value="{$id}">{$name}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td class="cm-non-cb">
                        <input type="text" name="fields[{$num}][none]" value="0.00" size="5" class="input-mini"/>
                    </td>
                    <td class="cm-non-cb">
                        <input type="text" name="fields[{$num}][before]" value="0.00" size="5" class="input-mini"/>
                    </td>
                    <td class="cm-non-cb">
                        <input type="text" name="fields[{$num}][after]" value="0.00" size="5" class="input-mini"/>
                    </td>
                    <td class="cm-non-cb">
                        <input type="text" name="fields[{$num}][any]" value="0.00" size="5" class="input-mini"/>
                    </td>
                    <td class="right cm-non-cb">
                        {include file="buttons/multiple_buttons.tpl" item_id="add_field_row" tag_level="1"}
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    {include file="common/subheader.tpl" title=__('fields_weight_explanation_title')}
    <table class="table">
        <thead>
            <th>{__('type_of_match')}</th>
            <th>{__('search_query')}: "samsung"</th>
            <th>{__('search_query')}: "sams"</th>
            <th>{__('search_query')}: "sung"</th>
            <th>{__('search_query')}: "amsun"</th>
        </thead>
        <tr>
            <td>{__('field_weight_none')}</td>
            <td>
                <a class="icon-ok cp-color-green"></a>
                <span class="cp-color-red">Samsung</span> Smart TV
            </td>
            <td>
                <a class="icon-remove cp-color-red"></a>
                <span class="cp-color-red">Sams</span>ung Smart TV
            </td>
            <td>
                <a class="icon-remove cp-color-red"></a>
                Sam<span class="cp-color-red">sung</span> Smart TV
            </td>
            <td>
                <a class="icon-remove cp-color-red"></a>
                S<span class="cp-color-red">amsun</span>g Smart TV
            </td>
        </tr>
        <tr>
            <td>{__('field_weight_before')}</td>
            <td>
                <a class="icon-ok cp-color-green"></a>
                <span class="cp-color-red">Samsung</span> Smart TV
            </td>
            <td>
                <a class="icon-ok cp-color-green"></a>
                <span class="cp-color-red">Sams</span>ung Smart TV
            </td>
            <td>
                <a class="icon-remove cp-color-red"></a>
                Sam<span class="cp-color-red">sung</span> Smart TV
            </td>
            <td>
                <a class="icon-remove cp-color-red"></a>
                S<span class="cp-color-red">amsun</span>g Smart TV
            </td>
        </tr>
        <tr>
            <td>{__('field_weight_after')}</td>
            <td>
                <a class="icon-ok cp-color-green"></a>
                <span class="cp-color-red">Samsung</span> Smart TV
            </td>
            <td>
                <a class="icon-remove cp-color-red"></a>
                <span class="cp-color-red">Sams</span>ung Smart TV
            </td>
            <td>
                <a class="icon-ok cp-color-green"></a>
                Sam<span class="cp-color-red">sung</span> Smart TV
            </td>
            <td>
                <a class="icon-remove cp-color-red"></a>
                S<span class="cp-color-red">amsun</span>g Smart TV
            </td>
        </tr>
        <tr>
            <td>{__('field_weight_any')}</td>
            <td>
                <a class="icon-ok cp-color-green"></a>
                <span class="cp-color-red">Samsung</span> Smart TV
            </td>
            <td>
                <a class="icon-ok cp-color-green"></a>
                <span class="cp-color-red">Sams</span>ung Smart TV
            </td>
            <td>
                <a class="icon-ok cp-color-green"></a>
                Sam<span class="cp-color-red">sung</span> Smart TV
            </td>
            <td>
                <a class="icon-ok cp-color-green"></a>
                S<span class="cp-color-red">amsun</span>g Smart TV
            </td>
        </tr>
    </table>
    <div>
        <a class="icon-ok cp-color-green"></a> - {__('product_will_be_in_search_results')}
    </div>
    <div>
        <a class="icon-remove cp-color-red"></a> - {__('product_will_not_be_in_search_results')}
    </div>
{/capture}

{capture name="buttons"}       
    <div class="cm-tab-tools pull-right shift-left">
        {include file="buttons/button.tpl" but_text=__("save") but_name="dispatch[cp_search_fields_weight.update]" but_target_form="fields_form" but_role="submit-link"}        
    </div>
{/capture}

{include file="common/mainbox.tpl" title=__("fields_weight") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons}
