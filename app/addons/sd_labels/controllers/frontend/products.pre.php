<?php
 defined('BOOTSTRAP') || die('Access denied'); if (defined('AJAX_REQUEST') && (($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'options') || ($_SERVER['REQUEST_METHOD'] === 'GET' && $mode === 'view')) ) { $view_mode = empty($_REQUEST['appearance']['quick_view']) ? 'view' : 'quick_view'; $sd_labels_display_settings = fn_sd_labels_get_display_settings($controller, $view_mode); $is_variation = $_REQUEST['is_ajax'] && $_REQUEST['result_ids']; Tygh::$app['view']->assign(compact('sd_labels_display_settings', 'is_variation')); return [CONTROLLER_STATUS_OK]; } 