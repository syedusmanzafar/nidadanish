<div class="main-content-grid">
    {if $addons.cp_power_reviews.status == "A"}
        {$cp_pow_rev_active=true}
        {if $addons.cp_power_reviews.show_image_uploader == "Y"}
            {$allow_img=true}
        {/if}
        {if $addons.cp_power_reviews.allow_message_title == "Y"}
            {$allow_title=true}
        {/if}
        {if $addons.cp_power_reviews.allow_adv_disv == "Y"}
            {$allow_adv_disadv=true}
        {/if}
        {if $addons.cp_power_reviews.show_video_uploader == "Y"}
            {$allow_videos=true}
        {/if}
    {else}
        {$allow_img=false}
        {$allow_title=false}
        {$allow_adv_disadv=false}
        {$cp_pow_rev_active=false}
        {$allow_videos=false}
    {/if}
    <div class="cp-em__products ty-profile-field cp-order-rev-products" id="cp_order_rate_prod_{$order_info.order_id}">
        {if $order_info && $order_info.products}
            {foreach from=$order_info.products key="cart_id" item="prod_data"}
                {$prod_id=$prod_data.product_id}
                <form name="cp_add_new_rev_ord_form_{$cart_id}" action="{""|fn_url}" class="cm-ajax" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="post_data[product_id]" value="{$prod_data.product_id}"/>
                    <input type="hidden" name="post_data[thread_id]" value="{$prod_data.thread_id}"/>
                    <input type="hidden" name="redirect_url" value="{$config.current_url}"/>
                    <input type="hidden" name="hash" value="{$order_info.hash}" />
                    <input type="hidden" name="no_suffix" value=true />
                    <input type="hidden" name="cp_skip_verf" value="Y"/>
                    <input type="hidden" name="cp_item_id" value="{$prod_data.cp_item_id}"/>
                    <input type="hidden" name="order_id" value="{$order_info.order_id}"/>
                    <input type="hidden" name="result_ids" value="cp_order_rate_prod_{$order_info.order_id}" />
                    
                    <div class="cp-review-main-block">
                        <div class="cp-review-left-block">
                            <div class="cp-review-prod-name-bl"><strong>{$prod_data.product|truncate:"120"}</strong></div>
                            {if $prod_data.main_pair}
                                <div class="cp-review-left-block-img">
                                    <a target="_blunk" href="{"products.view&product_id=`$prod_data.product_id`"|fn_url}">
                                    {include file="common/image.tpl" images=$prod_data.main_pair image_width="140" image_height="140" image_id="ord_rev_`$cart_id`"}
                                    </a>
                                </div>
                            {/if}
                        </div>
                        <div class="cp-review-right-block">
                            <div class="cp-review-right-block-info">
                                <label for="cp_dsc_name_{$prod_data.product_id}" class="ty-control-group__title cm-required">{__("your_name")}:</label>
                                <input type="text" name="post_data[name]" id="cp_dsc_name_{$prod_data.product_id}" value="{if $order_info.firstname || $order_info.lastname}{if $order_info.firstname}{$order_info.firstname} {/if}{if $order_info.lastname}{$order_info.lastname}{/if}{else}{/if}"/>
                            </div>
                            {if $prod_data.discussion_type == "R" || $prod_data.discussion_type == "B"}
                                <div class="cp-review-right-block-info">
                                    {if $prod_data.cp_all_prod_attrs}
                                        {foreach from=$prod_data.cp_all_prod_attrs item="cp_attr"}
                                            {$m_attr_id=$cp_attr.cp_attr_id}
                                            <div class="cp-new-rew-attr-block">
                                            {$rate_id = "rating_`$cart_id``$cp_attr.cp_attr_id`"}
                                                <label for="{$rate_id}" class="ty-control-group__label cm-required cm-multiple-radios">{$cp_attr.cp_attr_name}</label>
                                                <div class="cp-new-rew-stars-block">
                                                    {include file="addons/discussion/views/discussion/components/rate.tpl" rate_id=$rate_id rate_name="post_data[ratings][{$cp_attr.cp_attr_id}]"}
                                                </div>
                                            </div>
                                        {/foreach}
                                    {else}
                                        <div class="cp-new-rew-attr-block">
                                            {$rate_id = "rating_`$cart_id`"}
                                            <label for="{$rate_id}" class="ty-control-group__label cm-required cm-multiple-radios">{__("your_rating")}</label>
                                            <div class="cp-new-rew-stars-block">
                                                {include file="addons/discussion/views/discussion/components/rate.tpl" rate_id=$rate_id rate_name="post_data[ratings][{$cp_attr.cp_attr_id}]"}
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                            {/if}
                            {if $allow_title}
                                <div class="cp-review-right-block-msg">
                                    <label for="cp_dsc_title_{$prod_data.product_id}" class="ty-control-group__title cm-required">{__("cp_pr_title_label")}:</label>
                                    <input type="text" name="post_data[cp_pr_title]" id="cp_dsc_title_{$prod_data.product_id}" value="" class="cp-em__rev-input" >
                                </div>
                            {/if}
                            {if $prod_data.discussion_type == "C" || $prod_data.discussion_type == "B"}
                                <div class="cp-review-right-block-msg">
                                    <label for="cp_dsc_message_{$prod_data.product_id}" class="ty-control-group__title cm-required">{__("your_message")}</label>
                                    <textarea id="cp_dsc_message_{$prod_data.product_id}" name="post_data[message]" class="ty-input-textarea" rows="5" cols="72"></textarea>
                                </div>
                                {if $allow_adv_disadv}
                                    <div class="cp-review-right-block-msg">
                                        <label for="cp_dsc_cp_pr_advantages_{$prod_data.product_id}" class="cm-required ty-control-group__title">{__("cp_pr_advantages")}:</label>
                                        <textarea name="post_data[cp_pr_advantages]" id="cp_dsc_cp_pr_advantages_{$prod_data.product_id}" class="ty-input-textarea" rows="3" cols="72" ></textarea>
                                    </div>
                                    <div class="cp-review-right-block-msg">
                                        <label for="cp_dsc_cp_pr_disadvantages_{$prod_data.product_id}" class="cm-required ty-control-group__title">{__("cp_pr_disadvantages")}:</label>
                                        <textarea name="post_data[cp_pr_disadvantages]" id="cp_dsc_cp_pr_disadvantages_{$prod_data.product_id}" class="ty-input-textarea" rows="3" cols="72" ></textarea>
                                    </div>
                                {/if}
                            {/if}
                            {if $allow_img}
                                <div class="cp-review-right-block-info">
                                    <label class="ty-control-group__title">{__("cp_add_images")}:</label>
                                    <div class="cp-add-rev-post-img">
                                        <div id="box_cp_new_image_{$cart_id}" class="cp-add-img-rev-block ty-control-group clearfix">
                                            <div class="cm-row-item">
                                                <div class="image-upload-wrap pull-left">
                                                    {include file="addons/cp_power_reviews/components/attach_images.tpl" image_name="cp_review_post" image_object_type="cp_rev_post" image_object_id="0" image_type="A" no_thumbnail=true hide_images=true hide_alt=true more_index=$more_index}
                                                    <div class="cp-pull-right cp-mult-but-img">
                                                        {include file="addons/cp_power_reviews/components/multiple_buttons.tpl" item_id="cp_new_image_{$cart_id}"}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                            {if $allow_videos}
                                <div class="ty-control-group">
                                    <label class="ty-control-group__title" for="elm_video_code_{$prod_data.product_id}" title="">{__("cp_pr_youtube_id")}{include file="common/tooltip.tpl" tooltip=__("cp_pr_youtube_id_tooltip")}:</label>
                                    <div class="input-prepend input-prepend--mobile-fullwidth">
                                        <span class="cm-field-prefix add-on">http://youtube.com?v=</span>
                                        <input type="text" class="input-medium" value="" name="post_data[youtube_id]" id="elm_video_code_{$prod_data.product_id}"/>
                                    </div>
                                </div>
                                <div class="ty-control-group">
                                    <label class="ty-control-group__title">{__("cp_pr_preview_txt")}</label>
                                    <label class="checkbox inline">
                                    <input type="hidden" name="post_data[upload_from_youtube]" value="N" />
                                    <input type="checkbox" name="post_data[upload_from_youtube]" value="Y" checked="checked" onclick="Tygh.$('#attach_image_video_box').toggle();">{__("cp_pr_get_youtube_preview")}{include file="common/tooltip.tpl" tooltip=__("cp_pr_upload_preview_text")}</label>
                                        
                                    <div id="attach_image_video_box" class="cp-add-rev-post-img hidden">
                                        {include file="addons/cp_power_reviews/components/attach_images.tpl" image_name="cp_pr_video_preview" image_object_type="cp_pr_video_preview" image_object_id="0" image_type="M" no_thumbnail=true hide_images=true hide_alt=true more_index=$more_index}
                                    </div>
                                </div>
                            {/if}
                            <div class="cp-add_prod_rate_but">
                                <div class="controls">
                                    {include file="buttons/button.tpl" but_text=__("cp_em_page_leave_review") but_role="submit" but_meta="ty-btn ty-btn__primary" but_name="dispatch[discussion.cp_em_add_rew]" but_target_form="cp_add_new_rev_ord_form_{$cart_id}"}
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            {/foreach}
        {else}
            <div class="cp-reviews-no-items">{__("no_available_prods_for_review")}</div>
        {/if}
    <!--cp_order_rate_prod_{$order_info.order_id}--></div>
</div>