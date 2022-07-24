<?php

use Tygh\Enum\NotificationSeverity;
use Tygh\Notifications\EventIdProviders\OrderProvider;
use Tygh\Registry;
use Tygh\Shippings\Shippings;
use Tygh\Storage;
use Tygh\Tools\Url;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */


if ($mode == 'details') {
    $_REQUEST['order_id'] = empty($_REQUEST['order_id']) ? 0 : $_REQUEST['order_id'];

    $order_info = Tygh::$app['view']->getTemplateVars('order_info');
    $parent_order_id = $order_info['parent_order_id'];
    //check if exist special separaet prderr fpr delivery
    $special_order_data = db_get_row("SELECT order_id,total FROM ?:orders WHERE parent_order_id =?i and is_sc_united_ship_order =?s",$parent_order_id,"Y");


    if(empty($special_order_data)){
        $special_order_data = false;
    }
    Tygh::$app['view']->assign('special_order_data', $special_order_data);
}