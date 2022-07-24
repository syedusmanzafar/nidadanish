<?php

if ($mode == 'get_tree') {
    Tygh::$app['ajax']->assign('cities', fn_get_cities_tree());
    die;
}