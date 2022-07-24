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

use Tygh\Notifications\EventIdProviders\OrderProvider;
use Tygh\Pdf;
use Tygh\Registry;
use Tygh\Shippings\Shippings;
use Tygh\Storage;
use Tygh\Tygh;
use Tygh\Tools\Url;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


if ($mode == 'print_invoice') {

    if(!empty($auth['user_type']) && $auth['user_type'] =="V"){

        $_REQUEST['template_code'] = $_GET['template_code'] = $_POST['template_code'] = 'invoice_vendor';


        if (!empty($_REQUEST['order_id'])) {
            echo(fn_print_order_invoices($_REQUEST['order_id'], array(
                    'pdf' => !empty($_REQUEST['format']) && $_REQUEST['format'] == 'pdf','template_code'=>'invoice_vendor')
            ));
        }
        exit;

    }




}