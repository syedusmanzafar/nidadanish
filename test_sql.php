<?php
define('AREA', 'A');
define('ACCOUNT_TYPE', 'admin');    error_reporting(-1);
    ini_set('display_errors', 'on');
    ini_set('display_startup_errors', true);

$path = dirname(__FILE__) . '/error_log';
ini_set('log_errors', 'On');
ini_set('error_log', $path);

require(dirname(__FILE__) . '/init.php');

$condition = 1;
$limit=50;
$fields = array('user_id', 'firstname', 'lastname', 'email');
$fields_new = implode(', ', $fields);
fn_print_r(db_quote("SELECT SQL_CALC_FOUND_ROWS ?p FROM ?:users WHERE ?p LIMIT ?i", $fields_new, $condition, $limit));


