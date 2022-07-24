{$display_text = "Tygh\Addons\SdLabels\Labels\Label::TEXT"|constant}
{$display_graphic = "Tygh\Addons\SdLabels\Labels\Label::GRAPHIC"|constant}

{capture name="mainbox"}
    {capture name="tabsbox"}
        <div id="content_text_labels"><div></div><!--content_text_labels--></div>
        <div id="content_graphic_labels"><div></div><!--content_graphic_labels--></div>
    {/capture}

    <form
        action="{fn_url("")}"
        method="post"
        name="sd_labels_form"
        class=" cm-hide-inputs"
        enctype="multipart/form-data"
    >
        {include "common/tabsbox.tpl" content=$smarty.capture.tabsbox}
    </form>
{/capture}

{capture name="tools_list"}
    <li>{btn type="list" text=__("sd_labels.manage_labels.button_add_text_label") href="sd_labels.update&type={$display_text}"}</li>
    <li>{btn type="list" text=__("sd_labels.manage_labels.button_add_graphic_label") href="sd_labels.update&type={$display_graphic}"}</li>
{/capture}

{capture name="adv_buttons"}
    {if fn_check_view_permissions("sd_labels.update")}
        {dropdown
            content=$smarty.capture.tools_list
            icon="icon-plus"
            no_caret=true
            placement="right"
        }
    {/if}
{/capture}

{capture name="sidebar"}
    {include "addons/sd_labels/views/sd_labels/components/labels_search_form.tpl"
        dispatch="sd_labels.manage"
    }
{/capture}

{include "common/mainbox.tpl"
    title=__("sd_labels")
    content=$smarty.capture.mainbox
    buttons=$smarty.capture.buttons
    adv_buttons=$smarty.capture.adv_buttons
    select_languages=true
    sidebar=$smarty.capture.sidebar
}

{script src="js/addons/sd_labels/pickers/labels_drag_list.js"}
{script src="js/addons/sd_labels/update_sortable.js"}
