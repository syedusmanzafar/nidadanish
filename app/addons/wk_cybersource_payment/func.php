<?php
/**
 * Cyber Source Secure Acceptance Payment Gateway
 *
 * PHP version 7.1
 *
 * @category   Addon
 * @package    Cs-Cart
 * @author     WebKul software private limited <support@webkul.com>
 * @copyright  2010 webkul.com. All Rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version    GIT: 1.2
 * @filesource http://store.webkul.com
 * @link       Technical Support:  Forum - http://webkul.com/ticket
 */

use Tygh\Embedded;
use Tygh\Http;
use Tygh\Mailer;
use Tygh\Pdf;
use Tygh\Registry;
use Tygh\Storage;
use Tygh\Session;
use Tygh\Settings;
use Tygh\Shippings\Shippings;
use Tygh\Navigation\LastView;

/**
 * This function call during installation of addon and enters data in database.
 *
 * @return void
 */
function Fn_Cyber_Source_Payment_install()
{

    $addon_name = fn_get_lang_var('wk_cybersource_payment');
    Tygh::$app['view']->assign('mode', 'notification');
    fn_set_notification('S', __('well_done'), __('wk_cybersource_payment_user_guide_content', array('[support_link]' => 'https://webkul.uvdesk.com/en/customer/create-ticket/', '[user_guide]' => 'https://webkul.com/blog/cs-cart-cybersource-payment-gateway/', '[addon_name]' => $addon_name)));

    $data = array(
                'processor' => 'CyberSource secure acceptance(Inline)',
                'processor_script' => 'secure_acceptance.php',
                'processor_template' => 'addons/wk_cybersource_payment/views/orders/components/payments/cc_cybersource.tpl',
                'admin_template' => 'secure_acceptance.tpl',
                'callback' => 'N',
                'type' => 'P',
                'addon' => 'cyber_source_payment'
                );
    db_query('INSERT INTO ?:payment_processors ?e', $data);
}

/**
 * This function call during uninstallation of addon and deletes data from database.
 *
 * @return void
 */
function Fn_Cyber_Source_Payment_uninstall()
{
    $name = "CyberSource secure acceptance(Inline)";
    $processor_id = db_get_field("SELECT processor_id FROM ?:payment_processors WHERE processor = ?s", $name);
    $payment_id = db_get_field("SELECT payment_id FROM ?:payments WHERE processor_id = ?i", $processor_id);
    db_query("DELETE FROM ?:payments WHERE payment_id = ?i", $payment_id);

    $condition = array(
                'processor' => 'CyberSource secure acceptance(Inline)',
                'processor_script' => 'secure_acceptance.php',
                'processor_template' => 'addons/wk_cybersource_payment/views/orders/components/payments/cc_cybersource.tpl',
                'admin_template' => 'secure_acceptance.tpl',
                'callback' => 'N',
                'type' => 'P'
                );
    
    db_query("DELETE FROM ?:payment_processors WHERE ?w", $condition);
}

/**
 * Function used to get processor id from data base
 *
 * @return int payment id on success and 0 on failure
 */
function Fn_Get_Cyber_Source_Payment_id()
{
    $name = "CyberSource secure acceptance(Inline)";
    $processor_id = db_get_field("SELECT processor_id FROM ?:payment_processors WHERE processor = ?s", $name);
    if (isset($processor_id) && !empty($processor_id)) {
        $payment_id = db_get_field("SELECT payment_id FROM ?:payments WHERE processor_id = ?i", $processor_id);
        if (isset($payment_id) && !empty($payment_id)) {
            return $payment_id;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}



function sign($params, $secret_Key)
{
    return signData(buildDataToSign($params), $secret_Key);
}

function signData($data, $secretKey)
{
    return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
}

function buildDataToSign($params)
{
    $signedFieldNames = explode(",", $params["signed_field_names"]);
    foreach ($signedFieldNames as $field) {
        $dataToSign[] = $field . "=" . $params[$field];
    }
    return commaSeparate($dataToSign);
}

function commaSeparate($dataToSign)
{
    return implode(",", $dataToSign);
}

function fn_get_card_type($card)
{
    $cards=array(
        'visa'=>'001',
        'mastercard'=>'002',
        'amex'=>'003',
        'discover'=>'004',
        'jcb'=>'007',
        'maestro'=>'024',
        'visa_electron'=>'033',
        'dankort'=>'034',
        'dankort'=>'034',
        'diners_club_international'=>'005',
        'diners_club_carte_blanche'=>'006'
    );
    return $cards[$card];
}

function fn_Cyber_Source_get_price_by_currency($price, $currency_code = CART_SECONDARY_CURRENCY)
{
    $currencies = Registry::get('currencies');
    $currency = $currencies[$currency_code];
    $result = fn_format_rate_value($price, 'F', $currency['decimals'], '.', '', $currency['coefficient']);
    return $result;
}
