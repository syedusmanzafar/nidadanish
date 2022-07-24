<?php
/*****************************************************************************
*                                                        © 2013 Cart-Power   *
*           __   ______           __        ____                             *
*          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
*      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
*     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
*    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
*                                                                            *
*                                                                            *
* -------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license *
* and  accept to the terms of the License Agreement can install and use this *
* program.                                                                   *
* -------------------------------------------------------------------------- *
* website: https://store.cart-power.com                                      *
* email:   sales@cart-power.com                                              *
******************************************************************************/

use Tygh\Registry;
use Tygh\ExSimpleXmlElement;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


function fn_cp_ac_install_mail_tpl()
{
    if (version_compare(PRODUCT_VERSION, '4.4', '>=')) {
        $file = Registry::get('config.dir.addons') . 'cp_automaticaly_create_account/resources/email_templates.xml';
        
        $xml = ExSimpleXmlElement::loadFromFile($file);
        $email_templates = $xml->toArray();

        if ($email_templates) {
            /** @var \Tygh\Template\Mail\Exim $email_exim */
            $email_exim = \Tygh::$app['template.mail.exim'];
            $email_exim->import($email_templates);
        }
    }
    return true;
}