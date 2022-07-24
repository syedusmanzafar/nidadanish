<?php

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'view' || $mode == 'catalog' || $mode == 'products') {
    return [CONTROLLER_STATUS_NO_PAGE];
}
