<?php

use Tygh\Api;
use Tygh\Enum\UserTypes;
use Tygh\Enum\YesNo;
use Tygh\Registry;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

}
if ($mode == 'manage' || $mode == 'update' || $mode == 'add') {
    Tygh::$app['view']->assign('cities', fn_get_cities_tree());
    Tygh::$app['view']->assign('districts', fn_get_districts_tree());
} 