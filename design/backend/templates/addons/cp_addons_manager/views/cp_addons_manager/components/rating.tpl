<div class="cp-addon-rating-wrap">
    {if $addon.rating}
        {assign var="stars" value=$addon.rating}

        <span class="nowrap">
        {if $link}<a href="{$link}">{/if}
        {strip}
            {section name="full_star" loop=$stars.full}<i class="icon-star"></i>{/section}
            {if $stars.part}<i class="icon-star-half-empty"></i>{/if}
            {section name="full_star" loop=$stars.empty}<i class="icon-star-empty"></i>{/section}
        {/strip}
        {if $link}</a>{/if}
        </span>

        &nbsp;

        <a href="{$addon.url}#discussion_section" target="_blank" class="cp-testimonials-link">
        {__("cp_text_reviews", ["[n]" => $addon.reviews_count|default:0])}</a>
    {/if}
</div>