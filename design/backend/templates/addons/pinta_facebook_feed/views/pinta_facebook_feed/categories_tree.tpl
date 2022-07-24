{if $parent_id}
<div id="cat_{$parent_id}">
    {/if}
    {foreach from=$categories item=category}

        {if $category.status == "A"}
            {assign var="comb_id" value="cat_`$category.category_id`"}
            {assign var="category_name" value=""}
            {assign var="main_category_id" value=""}
            {if !in_array($category.category_id,$company_cat) and ($pinta_facebook_feed.companies_setting > 0)}
                {assign var="hidden" value="style=display:none"}
            {else}
                {assign var="hidden" value=""}

            {/if}
            <table class="table table-tree table-middle">

                <tr class="{if $category.level > 0}multiple-table-row {/if}cm-row-status-{$category.status|lower}" {$hidden}>
                    {math equation="x*20" x=$category.level|default:"0" assign="shift"}

                    <td width="24%">
                        {strip}
                            <span style="padding-left: {$shift}px;">
                    <span class="row-status {if !$category.subcategories} normal{/if}"
                          style="padding-left: 14px;">{$category.category}</span>
                    </span>
                        {/strip}
                    </td>
                    <td width="24%" style="position: relative;">
                        {assign var="text" value=""}
                        {assign var="main_id" value=""}
                        {if $pinta_facebook_feed['google_main_category'][$category.category_id]}
                            {$main_id = $pinta_facebook_feed['google_main_category'][$category.category_id]['google_main_category_id']}
                            {$text = $pinta_facebook_feed['google_main_categories'][$main_id]['google_category_name']|replace:'&amp;':'&'}
                        {/if}
                        <input type='text' name="google_main_category_for_category_{$category.category_id}"
                               data-category_id="{$category.category_id}" value="{$text}" placeholder="Please enter one character">

                            <div class=" clear_btn" data-category_id="{$category.category_id}"> Clear</div>
                    </td>
                    <td width="48%">
                        {assign var="text2" value=""}
                        {if $pinta_facebook_feed['google_main_category'][$category.category_id] > 0}
                            {$text2 = $pinta_facebook_feed['google_main_category'][$category.category_id]['google_category_name']|replace:'&amp;':'&'}
                            {$text2 = $text2|replace:'&gt;':'>'}

                        {/if}
                        <input type='text' style="width:100%;" name="googlecategory_{$category.category_id}" placeholder="Please enter one character"
                               data-category_id="{$category.category_id}" {if $text2} value="{$text2}"
                        {elseif !$pinta_facebook_feed['google_main_category'][$category.category_id]['google_main_category_id']} disabled {/if} {if $main_id} data-main_category_id="{$main_id}" {/if} >
                    </td>
                </tr>
            </table>
            {if $category.has_children || $category.subcategories}
                <div id="{$comb_id}">
                    {if $category.subcategories}

                        {include file="addons/pinta_facebook_feed/views/pinta_facebook_feed/categories_tree.tpl" categories_tree=$category.subcategories parent_id=false}

                    {/if}
                </div>
            {/if}
        {/if}
    {/foreach}

    {if $parent_id}<!--cat_{$parent_id}--></div>{/if}
