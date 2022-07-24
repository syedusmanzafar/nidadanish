{include "addons/sd_labels/common/labels_params.tpl"}

{if $show_sd_labels === "YesNo::YES"|enum && $is_overlay === "YesNo::YES"|enum}
    {$sd_product_labels = "sd_product_labels_{$obj_prefix}{$obj_id}"}

    {if $smarty.capture.$sd_product_labels|trim}
        {$smarty.capture.$sd_product_labels nofilter}
    {/if}
{/if}
