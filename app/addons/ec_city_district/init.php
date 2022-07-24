<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'update_destination_pre',
    'update_destination_post',
    'get_available_destination_post',
    'get_states_pre',
    'get_order_info',
    'get_user_info',
    'fill_user_fields'
);