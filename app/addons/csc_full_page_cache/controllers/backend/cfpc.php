<?php
/*****************************************************************************
*                                                                            *
*          All rights reserved! CS-Commerce Software Solutions               *
* 			https://www.cs-commerce.com/license-agreement.html 				 *
*                                                                            *
*****************************************************************************/

use Tygh\Registry;
use Tygh\CscFullPageCache;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$base_name = CscFullPageCache::$base_name;
$lang_prefix = CscFullPageCache::$lang_prefix;
$_view = CscFullPageCache::_view();


if ($_SERVER['REQUEST_METHOD']=="POST"){
	if ($mode==$base_name::_('c2V0dGluZ3M=')){			
		if (!empty($_REQUEST[$base_name::_('c2V0dGluZ3M=')])){	
			CscFullPageCache::_update_option_values($_REQUEST[$base_name::_('c2V0dGluZ3M=')]);
		}
		fn_set_notification('N', __('notice'), __('text_changes_saved'));		
	}
	
	return array(CONTROLLER_STATUS_OK, 'cfpc.settings');
}

$_view->assign('addon_base_name', $base_name);
$_view->assign('lp', $lang_prefix);

if ($mode==$base_name::_z('p2I0qTyhM3Z=')){		
	$submenu = fn_get_schema($base_name, 'submenu');
	$_view->assign('submenu', $submenu);
	
	$options = CscFullPageCache::_get_option_values();
	
	
	$_view->assign('options', $options);
	
	$fields = fn_get_schema($base_name, 'settings');		  
    $_view->assign('fields', $fields);
		
	$tabs = array();
    $tabs_codes = array_keys($fields);
    foreach($tabs_codes as $tab_code) {
        $tabs[$tab_code] = array (
            'title' => __($lang_prefix.'.tab_' . $tab_code),
            'js' => true
        );
    }
	Registry::set('navigation.tabs', $tabs);		
	$_view->assign('addon_base_name', $base_name);
	$_view->assign('lp', $lang_prefix);
	$_view->assign('allow_separate_storefronts', CscFullPageCache::_allow_separate_storefronts());	
}




