{if $no_popup}
    {capture name="mainbox"}
{/if}
    {if !$id}
        {assign var="id" value=$smarty.request.search_id}
    {/if}

    <div id="content_search_info{$id}">

        {if $smarty.request.phrases}
            {foreach from=$search_phrases item="phrase" name="history_search_phrases"}
                {$phrase}{if !$smarty.foreach.history_search_phrases.last}, {/if}
            {/foreach}
        {else}
            <table width="100%" class="table table-middle first-transition">
                <thead>
                    <tr>
                        <th width="5%">&nbsp;</th>
                        <th>{__("product")}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$products item="product"}
                    <tr class="cm-row-status">
                        <td>
                            {include file="common/image.tpl" image=$product.main_pair.icon|default:$product.main_pair.detailed image_id=$product.main_pair.image_id image_width=$settings.Thumbnails.product_admin_mini_icon_width image_height=$settings.Thumbnails.product_admin_mini_icon_height href="products.update?product_id=`$product.product_id`"|fn_url}
                        </td>
                        <td>
                            <div><a target="_blank" href="{"products.update?product_id=`$product.product_id`"|fn_url}">{$product.product nofilter}</a></div>
                            <div><span class="product-code__label">{$product.product_code}</span></div>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        {/if}
    <!--content_search_info{$id}--></div>

{if $no_popup}
    {/capture}

    {capture name="buttons"}
    {/capture}

    {capture name="title"}{__("cp_product_clicks")}{/capture}

    {include file="common/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons}
{/if}