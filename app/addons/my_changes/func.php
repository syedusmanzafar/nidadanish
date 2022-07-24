<?php

use Tygh\Registry;

if ( !defined('AREA') ) { die('Access denied'); }

// Redirect customer to checkout when registering before checkout
function fn_my_changes_login_user_post($user_id, $cu_id, $udata, $auth, $condition, $result)
{
    if (AREA == 'C' && !empty($udata['last_login'])) {
        if ($udata['last_login'] == 0 && $cu_id != "") {
            $_REQUEST['return_url'] = "checkout.checkout";
        }
    }
}

 function fn_my_changes_mailer_create_message_before($mailer, &$message, $area, $lang_code, $transport, $builder)
{
    $message['reply_to'] = 'default_company_orders_department';
    $message['from'] = 'default_company_orders_department';
}

?>
