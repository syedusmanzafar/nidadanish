<?php
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    if ($mode == 'import') {
		define('FPC_IMPORT_RUNING', true);
	}
}
