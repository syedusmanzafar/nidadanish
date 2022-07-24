<?php

use Tygh\Tygh;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
   'login_user_post',
   'mailer_create_message_before'
);

?>
