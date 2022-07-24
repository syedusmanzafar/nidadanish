<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'update_payment_pre',
    'prepare_checkout_payment_methods'
);