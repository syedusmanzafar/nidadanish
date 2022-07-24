<?php
/***************************************************************************
*                                                                          *
*   (c) 2017 ThemeHills - Premium themes and addons					       *
*                                                                          *
****************************************************************************/

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update_status' && $_REQUEST['addon'] == 'ath_animate' ) {
	    fn_check_license_ath_animate('return');
	}
	if ($mode == 'update' && $_REQUEST['addon'] == 'ath_animate' ) {
		fn_check_license_ath_animate('return');
	}
}
