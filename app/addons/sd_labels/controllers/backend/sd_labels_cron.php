<?php
 use Tygh\Addons\SdLabels\CronManager; use Tygh\Registry; defined('BOOTSTRAP') or die('Access denied'); if ($_SERVER['REQUEST_METHOD'] === 'POST') { if ($mode === 'assign') { if (isset($_REQUEST['cron_password']) && $_REQUEST['cron_password'] === Registry::get('settings.Security.cron_password') ) { $cron_manager = Tygh::$app['addons.sd_labels.cron_manager']; $cron_manager->autoDeleteLabels(); $cron_manager->autoAssignLabels(); fn_echo(__('done')); exit; } } return [CONTROLLER_STATUS_OK]; } 