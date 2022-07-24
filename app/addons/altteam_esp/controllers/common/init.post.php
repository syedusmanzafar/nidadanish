<?php

/*****************************************************************************
 * This is a commercial software, only users who have purchased a  valid
 * license and accepts the terms of the License Agreement can install and use  
 * this program.
 *----------------------------------------------------------------------------
 * @copyright  LCC Alt-team: http://www.alt-team.com
 * @module     "Alt-team: Easy scroll pagination"
 * @version    4.0.x 
 * @license    http://www.alt-team.com/addons-license-agreement.html
 ****************************************************************************/


if ( !defined('BOOTSTRAP') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if (isset($_REQUEST['ibug']) && $_REQUEST['ibug'] == 'Y') {
	fn_set_notification('N', __('notice'), 'Alt-team: Easy scroll pagination, version : 1.1.3.3, #Irlandec');
}