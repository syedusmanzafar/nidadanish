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

use Tygh\Enum\SiteArea;
use Tygh\Notifications\DataValue;
use Tygh\Notifications\Transports\Mail\MailTransport;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;
use Tygh\Addons\CpExtendedMarketing\Notifications\DataProviders\CpExtendedMarketingDataProvider;
use Tygh\Enum\UserTypes;

defined('BOOTSTRAP') or die('Access denied');

$schema['cp_extended_marketing.cp_em_notice'] = [
    'group'     => 'cp_extended_marketing',
    'name'      => [
        'template' => 'cp_em_notice.event.name',
        'params'   => [],
    ],
    'data_provider' => [CpExtendedMarketingDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::CUSTOMER => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => SiteArea::STOREFRONT,
                'from'            => DataValue::create('cp_em_notice_data.from_email'),
                'to'              => DataValue::create('cp_em_notice_data.to_email'),
                'reply_to'        => DataValue::create('cp_em_notice_data.reply_to'),
                'template_code'   => 'cp_em_notice',
                'legacy_template' => 'addons/cp_extended_marketing/notification.tpl',
                'language_code'   => DataValue::create('cp_em_notice_data.lang_code', CART_LANGUAGE),
                'storefront_id'   => DataValue::create('cp_em_notice_data.company_id'),
            ]),
        ],
    ],
];
