{if "common:image.override"|fn_cp_am_check_template:"hooks"}
{hook name="common:image"}
{capture name="cp_img_class"}{hook name="cp_image:img_class"}{/hook}{/capture}
{capture name="cp_img_extra"}{hook name="cp_image:img_extra"}{/hook}{/capture}

{capture name="cp_img_main"}
<img class="ty-pict {$valign} {$class} {if $lazy_load && !$no_lazyOwl}lazyOwl{/if} {if $generate_image}ty-spinner{/if} cm-image {$smarty.capture.cp_img_class}" {if $obj_id && !$no_ids}id="det_img_{$obj_id}"{/if} {if $generate_image}data-ca-image-path="{$image_data.image_path}"{/if} {hook name="cp_image:img_src"}{if $lazy_load}data-{/if}src="{if $generate_image}{$images_dir}/icons/spacer.gif{else}{$image_data.image_path}{/if}"{/hook} {if $image_onclick}onclick="{$image_onclick}"{/if} {$image_additional_attrs|fn_cp_am_render_tag_attrs nofilter} {$smarty.capture.cp_img_extra nofilter} />
{/capture}

{if $show_detailed_link}
    <a id="det_img_link_{$obj_id}" {if $image_data.detailed_image_path && $image_id}data-ca-image-id="{$image_id}"{/if} class="{$link_class} {if $image_data.detailed_image_path}cm-previewer ty-previewer{/if}" data-ca-image-width="{$images.detailed.image_x}" data-ca-image-height="{$images.detailed.image_y}" {if $image_data.detailed_image_path}href="{$image_data.detailed_image_path}" {$image_link_additional_attrs|fn_cp_am_render_tag_attrs nofilter}{/if}>
{/if}
{if $image_data.image_path}
    {** products:product_image_object is deprecated **}
    {*hook name="products:product_image_object"*}
        {hook name="cp_image:img_main"}
            {$smarty.capture.cp_img_main nofilter}
        {/hook}
        {if $show_detailed_link}
            <svg class="ty-pict__container" aria-hidden="true" width="{$image_data.width}" height="{$image_data.height}" viewBox="0 0 {$image_data.width} {$image_data.height}" style="max-height: 100%; max-width: 100%; position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: -1;">
                <rect fill="transparent" width="{$image_data.width}" height="{$image_data.height}"></rect>
            </svg>
        {/if}
    {*/hook*}
{elseif $show_no_image}
    <span class="ty-no-image" style="height: {$image_height|default:$image_width}px; width: {$image_width|default:$image_height}px; "><i class="ty-no-image__icon ty-icon-image" title="{__("no_image")}"></i></span>
{/if}
{if $show_detailed_link}
    {if $images.detailed_id}
        <span class="ty-previewer__icon hidden-phone"></span>
    {/if}
</a>
{/if}
{/hook}
{/if}