<?php
/***************************************************************************
*                                                                          *
*   (c) 2016 ThemeHills - Premium themes and addons					       *
*                                                                          *
****************************************************************************/

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'render_block_register_cache',
    'render_block_content_after'
);
