<?php
/***************************************************************************
*                                                                          *
*   (c) 2016 ThemeHills - Premium themes and addons					       *
*                                                                          *
****************************************************************************/

use Tygh\Registry;

$anim_effects = fn_get_anim_effects_list();

Tygh::$app['view']->assign('anim_effects', $anim_effects);
