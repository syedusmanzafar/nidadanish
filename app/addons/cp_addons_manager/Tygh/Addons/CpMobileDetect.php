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

namespace Tygh\Addons;

use Tygh\Registry;

class CpMobileDetect
{
    public $detect;

    public function __construct()
    {
        \Tygh::$app['class_loader']->addClassMap(array(
            'Mobile_Detect' => Registry::get('config.dir.addons') . 'cp_addons_manager/lib/Mobile_Detect.php'
        ));
        
        $this->detect = new \Mobile_Detect();
    }

    public function device()
    {
        $device = 'desktop';
        if ($this->detect->isTablet()) {
            $device = 'tablet';
        } elseif ($this->detect->isMobile()) {
            $device = 'mobile';
        }
        return $device;
    }
}