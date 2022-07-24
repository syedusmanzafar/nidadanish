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

namespace Tygh\Addons\CpExtendedMarketing\Notifications\DataProviders;


use Tygh\Exceptions\DeveloperException;
use Tygh\Notifications\DataProviders\BaseDataProvider;

class CpExtendedMarketingDataProvider extends BaseDataProvider
{
    protected $cp_em_notice = [];

    public function __construct(array $data)
    {
        if (empty($data['cp_em_notice_data'])) {
            throw new DeveloperException('The email notification must be defined.');
        }

        $this->cp_em_notice = $data['cp_em_notice_data'];
        $data['body'] = $this->getBody();
        $data['subject'] = $this->getSubject();

        parent::__construct($data);

    }
    
    protected function getSubject()
    {
        $subject = '';
        if (!empty($this->cp_em_notice['subject'])) {
            $subject = $this->cp_em_notice['subject'];
        }

        return $subject;
    }

    protected function getBody()
    {
        $body = '';
        if (!empty($this->cp_em_notice['body'])) {
            $body = $this->cp_em_notice['body'];
        }

        return $body;
    }
}