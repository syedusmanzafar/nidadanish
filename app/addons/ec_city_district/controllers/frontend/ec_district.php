<?php

if ($mode == 'get_tree') {
    $districts = fn_get_districts_tree();
    Tygh::$app['ajax']->assign('districts', $districts);
    die;
}