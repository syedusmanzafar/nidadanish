<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\OrderDataTypes;
use Tygh\Enum\YesNo;
use Tygh\Registry;
use Tygh\Shippings\Shippings;
use Tygh\Storage;
use Tygh\Tygh;
use Tygh\Enum\ProfileFieldSections;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $controller */
/** @var string $mode */
/** @var array $auth */

fn_enable_checkout_mode();

fn_define('ORDERS_TIMEOUT', 60);

// Cart is empty, create it
if (empty(Tygh::$app['session']['cart'])) {
    fn_clear_cart(Tygh::$app['session']['cart']);
}

/** @var array $cart */
$cart = &Tygh::$app['session']['cart'];

/** @var \Tygh\SmartyEngine\Core $view */
$view = Tygh::$app['view'];
 
if ($mode == 'fast') {
	$_REQUEST['product_data'][$_REQUEST['obj']] = array(
		'product_id' => $_REQUEST['obj'],
		'amount' => 1
	);
	// fn_print_die($_REQUEST);
	if (empty($auth['user_id']) && Registry::get('settings.Checkout.allow_anonymous_shopping') != 'allow_shopping') {
		return [CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode($_REQUEST['return_url'])];
	}

	// Add to cart button was pressed for single product on advanced list
	if (!empty($dispatch_extra)) {
		if (empty($_REQUEST['product_data'][$dispatch_extra]['amount'])) {
			$_REQUEST['product_data'][$dispatch_extra]['amount'] = 1;
		}
		foreach ($_REQUEST['product_data'] as $key => $data) {
			if ($key != $dispatch_extra && $key != 'custom_files') {
				unset($_REQUEST['product_data'][$key]);
			}
		}
	}

	$prev_cart_products = empty($cart['products']) ? [] : $cart['products'];

	fn_add_product_to_cart($_REQUEST['product_data'], $cart, $auth);

	$previous_state = md5(serialize($cart['products']));
	$cart['change_cart_products'] = true;
	fn_calculate_cart_content($cart, $auth, 'E', true, 'F', true);
	fn_save_cart_content($cart, $auth['user_id']);

	if (md5(serialize($cart['products'])) != $previous_state && empty($cart['skip_notification'])) {
		$product_cnt = 0;
		$added_products = [];
		foreach ($cart['products'] as $key => $data) {
			if (empty($prev_cart_products[$key]) || !empty($prev_cart_products[$key]) && $prev_cart_products[$key]['amount'] != $data['amount']) {
				$added_products[$key] = $data;
				$added_products[$key]['product_option_data'] = fn_get_selected_product_options_info($data['product_options']);
				if (!empty($prev_cart_products[$key])) {
					$added_products[$key]['amount'] = $data['amount'] - $prev_cart_products[$key]['amount'];
				}
				$product_cnt += $added_products[$key]['amount'];
			}
		}

		if (!empty($added_products)) {
			$view->assign('added_products', $added_products);
			if (Registry::get('config.tweaks.disable_dhtml') && Registry::get('config.tweaks.redirect_to_cart')) {
				$view->assign('continue_url', (!empty($_REQUEST['redirect_url']) && empty($_REQUEST['appearance']['details_page'])) ? $_REQUEST['redirect_url'] : Tygh::$app['session']['continue_url']);
			}

			
			$cart['recalculate'] = true;
		} else {
			// fn_set_notification('N', __('notice'), __('product_in_cart'));
		}
	}

	unset($cart['skip_notification']);

	// if (Registry::get('config.tweaks.disable_dhtml') && Registry::get('config.tweaks.redirect_to_cart') && !defined('AJAX_REQUEST')) {
		// if (!empty($_REQUEST['redirect_url']) && empty($_REQUEST['appearance']['details_page'])) {
			Tygh::$app['session']['continue_url'] = fn_url('checkout.checkout');
		// }
		// unset($_REQUEST['redirect_url']);
	// }

	return [CONTROLLER_STATUS_REDIRECT, 'checkout.checkout'];
}