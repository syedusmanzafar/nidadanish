<?php


use Tygh\Enum\UserTypes;
use Tygh\Notifications\DataValue;
use Tygh\Notifications\Transports\Mail\MailTransport;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;
use Tygh\Registry;
use Tygh\Addons\Sc_Single_Notice\Notifications\DataProviders\RequestCreatedDataProvider;

defined('BOOTSTRAP') or die('Access denied');


$schema['sc_single_notice_email_admin'] = [
    'group'     => 'orders',
    'name'      => [
        'template' => 'sc_single_notice_email_admin',
        'params'   => [],
    ],
    'data_provider' => [RequestCreatedDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::ADMIN => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'A',
                'from'            => 'company_orders_department',
                'to'              => DataValue::create('sc_single_notice_data.email'),
                'template_code'   => 'sc_single_notice_email_admin',
                'language_code'   => DataValue::create('lang_code', CART_LANGUAGE),
            ]),
        ],
    ],
];


$schema['sc_single_notice.mail'] = [
    'group'     => 'orders',
    'name'      => [
        'template' => 'sc_single_notice_email',
        'params'   => [],
    ],
    'data_provider' => [RequestCreatedDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::CUSTOMER => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'C',
                'from'            => 'company_orders_department',
                'to'              => DataValue::create('sc_single_notice_data.email'),
                'template_code'   => 'sc_single_notice_email',
                'language_code'   => DataValue::create('lang_code', CART_LANGUAGE),
            ]),
        ],
    ],
];

return $schema;
