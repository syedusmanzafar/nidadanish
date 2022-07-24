{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="add_ignore_img" class="form-horizontal form-edit ">
        <input type="hidden" class="cm-no-hide-input" name="iamge_id" value="0" />
        <div id="add_attribute">
            <fieldset>
                <p>{__("cp_webp_ignore_info")}</p>
                <div class="control-group">
                    <label for="image_path" class="control-label cm-required">{__("image")}:</label>
                    <div class="controls">
                        <input type="text" name="image_path" placeholder="detailed/0/test_product.jpg" id="image_path" size="25" value="" class="input-large" />
                    </div>
                </div>
                <div class="buttons-container">
                    <div class="controls">
                        {include file="buttons/button.tpl" but_text=__("add") but_role="submit" but_name="dispatch[cp_webp.add_ignore]" but_target_form="add_ignore_img"}
                    </div>
                </div>
            </fieldset>
        </div>
    </form>
    <form action="{""|fn_url}" method="post" name="cp_web_menu_ignore_list_from" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}" id="cp_web_menu_ignore_list_from" >
    
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

    {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
    {assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
    {assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

    {if $images}
        <div class="table-responsive-wrapper">
            <table width="100%" class="table table-middle table--relative table-responsive">
                <thead>
                    <tr>
                        <th width="1%">
                            {include file="common/check_items.tpl"}
                        </th>
                        <th width="60%"><a class="cm-ajax" href="{"`$c_url`&sort_by=image&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("image")}{if $search.sort_by == "image"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                        <th width="15%">&nbsp;</th>
                    </tr>
                </thead>
                {foreach from=$images item="cp_image"}
                    <tr>
                        <td>
                            <input name="image_ids[]" type="checkbox" value="{$cp_image.image_id}" class="cm-item" />
                        </td>
                        <td data-th="{__("image")}">
                            {$cp_image.image_path}
                        </td>
                        <td class="right">
                            <div class="hidden-tools">
                            {capture name="tools_list"}
                                <li>{btn type="list" text=__("delete") class="cm-confirm cm-post" href="cp_webp.delete_image?image_id=`$cp_image.image_id`"}</li>
                            {/capture}
                            {dropdown content=$smarty.capture.tools_list}
                            </div>
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
    {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
    {capture name="buttons"}
        {capture name="tools_list"}
            {if $images}
                <li>{btn type="delete_selected" dispatch="dispatch[cp_webp.m_delete]" form="cp_web_menu_ignore_list_from"}</li>
            {/if}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}
    {/capture}
    </form>
{/capture}
{include file="common/mainbox.tpl" title=__("cp_web_menu_ignore_list") content=$smarty.capture.mainbox tools=$smarty.capture.tools sidebar=$smarty.capture.sidebar buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons}
