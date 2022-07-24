<?php


use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


if ($mode == 'get_o') {
	
	
	//fn_sc_send_single_notice();
	
	
	fn_sc_send_single_notice(5928,false,false,true);
	
	exit('dfdf');
	

    $o =  db_get_fields("SELECT * FROM ?:orders ORDER BY order_id DESC LIMIT 200");
    fn_print_die($o);
}
