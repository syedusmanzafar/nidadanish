{if $products}
    <div style="display: inline-block;background-color: #ffffff;width:100%;text-align: center;">
        {foreach from=$products item="product"}
            <div style="width: 163px;float: left;padding: 0 8px; margin-bottom:12px;">
                <div style="text-align: center;">
                    <a target="_blank" href="{"products.view?product_id=`$product.product_id`&hash=`$link_hash``$link_store`"|fn_url:"C"}">
                        {$image_data=$product.main_pair|fn_image_to_display:$image_width:$image_height}
                        {if $image_data}
                            <img src="{$image_data.image_path}" width="{$image_data.width}" />
                        {else}
                            <img src="{"images/no_image.png"|fn_url:"C"}" height="{$image_height}"/>
                        {/if}  
                    </a>
                </div>
                <div style="text-align: left; font-size: 14px; font-family: Arial;padding: 8px 8px 8px 0px;height: 38px;overflow: hidden;width:100%;">
                    <a target="_blank" style="font-size: 14px;color: #000;text-decoration: none;" href="{"products.view?product_id=`$product.product_id`&hash=`$link_hash``$link_store`"|fn_url:"C"}">{$product.product|truncate:44:"...":true nofilter}</a>
                </div>
                <div style="text-align: left; font-size: 14px; font-family: Arial;width:100%;">
                    <span {if $product.discount || ($product.list_price && $product.list_price > $product.price)}style="color: red;"{/if}>
                        {include file="common/price.tpl" value=$product.original_price|default:$product.price}
                    </span>
                    {if $product.discount}
                        <span style="font-size: 14px; font-weight: 500; color: #999; text-decoration: line-through; margin: 0;">
                            {include file="common/price.tpl" value=$product.original_price|default:$product.base_price}
                        </span>
                    {elseif $product.list_price && $product.list_price > $product.price}
                        <span style="font-size: 14px; font-weight: 500; color: #999; text-decoration: line-through; margin: 0;">
                            {include file="common/price.tpl" value=$product.list_price}
                        </span>
                    {/if}
                </div>
                {if $cp_is_reviews}
                    <div style="padding: 8px 0px; background-color: #ffffff; text-align: left; font-size: 13px; font-family: Arial;vertical-align: top;">
                        {if $cp_review_btn}
                            {if $is_new_reviews}
                                {$rev_href="products.view?product_id=`$product.product_id`&hash=`$link_hash``$link_store`&selected_section=product_reviews#product_reviews"|fn_url:"C"}
                            {else}
                                {$rev_href="products.view?product_id=`$product.product_id`&hash=`$link_hash``$link_store`&selected_section=discussion#discussion"|fn_url:"C"}
                            {/if}
                            {$cp_review_btn_for_view = str_replace("cp_em_btn_here","href=`$rev_href`", $cp_review_btn)}
                            {$cp_review_btn_for_view nofilter}
                        {else}
                            {if $is_new_reviews}
                                <a target="_blank" href="{"products.view?product_id=`$product.product_id`&hash=`$link_hash``$link_store`&selected_section=product_reviews#product_reviews"|fn_url:"C"}">{__("cp_em_page_leave_review")}</a>
                            {else}
                                <a target="_blank" href="{"products.view?product_id=`$product.product_id`&hash=`$link_hash``$link_store`&selected_section=discussion#discussion"|fn_url:"C"}">{__("cp_em_page_leave_review")}</a>
                            {/if}
                        {/if}
                    </div>
                {/if}
            </div>
        {/foreach}
    </div>
{/if}