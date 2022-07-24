<?php
use Tygh\BlockManager\Layout;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\ProductTracking;
use Tygh\Enum\ProfileTypes;
use Tygh\Enum\StorefrontStatuses;
use Tygh\Enum\VendorPayoutApprovalStatuses;
use Tygh\Enum\VendorStatuses;
use Tygh\Enum\YesNo;
use Tygh\Helpdesk;
use Tygh\Http;
use Tygh\Languages\Languages;
use Tygh\Navigation\LastView;
use Tygh\Providers\VendorServicesProvider;
use Tygh\Registry;
use Tygh\Settings;
use Tygh\Themes\Styles;
use Tygh\Themes\Themes;
use Tygh\Tools\DateTimeHelper;
use Tygh\Tygh;
use Tygh\VendorPayouts;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $suffix = '';

    if ($mode == 'update'){

        if(!empty($_REQUEST['company_id']) && !empty($_REQUEST['company_data']['sc_united_use_vendor']) && $_REQUEST['company_data']['sc_united_use_vendor'] == "Y"){

            $check_exists = db_get_fields("SELECT company_id FROM ?:companies WHERE sc_united_use_vendor =?s","Y");


            if(count($check_exists) == 1 && in_array($_REQUEST['company_id'],$check_exists) ){
                return ;
            }

            if(!empty($check_exists)){
                $company_names = array();
                foreach ($check_exists as $c_id){
                    $company_names[] = fn_get_company_name($c_id);
                }
                $company_names = implode("///",$company_names);
                db_query("UPDATE ?:companies SET sc_united_use_vendor = ?s WHERE company_id in (?n)","Y",$check_exists);
                fn_set_notification('E', __('error'), __('sc_united_shipping_duplicate_united_vendor',['[company_name]'=>$company_names]));
            }
        }
    }
}