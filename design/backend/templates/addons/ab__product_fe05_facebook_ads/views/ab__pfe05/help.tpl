{capture name="mainbox"}
<p>{__('ab__pfe05_help.docs')}</p>
{/capture}
{include file="addons/ab__addons_manager/views/ab__am/components/menu.tpl" addon="ab__product_fe05_facebook_ads"}
{include
file="common/mainbox.tpl"
title_start=__("ab__product_fe05_facebook_ads")|truncate:40
title_end=__("ab__pfe.help")
content=$smarty.capture.mainbox
buttons=$smarty.capture.buttons
adv_buttons=$smarty.capture.adv_buttons
sidebar=$smarty.capture.sidebar
}