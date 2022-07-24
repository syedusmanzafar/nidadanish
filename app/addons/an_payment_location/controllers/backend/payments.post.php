<?php
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'update' || $mode == 'add') {
    //if (fn_allowed_for('ULTIMATE')) {
        $payment_id = !empty($_REQUEST['payment_id']) ? $_REQUEST['payment_id'] : 0;
       // $company_data = !empty($company_id) ? fn_get_company_data($company_id) : array();


        $payment_data = Tygh::$app['view']->getTemplateVars('payment');

        $states_list = fn_an_get_country_states(CART_LANGUAGE, $payment_id);


       // fn_print_die($states_list);

        if (!empty($payment_data['states_list'])) {
            if (!is_array($payment_data['states_list'])) {
                $company_states = explode(',', $payment_data['states_list']);
            } else {
                $company_states = $payment_data['states_list'];
            }

            $_states = array();

            foreach ($company_states as $code) {
                if (isset($states_list[$code])) {
                    $_states[$code] = $states_list[$code];
                    unset($states_list[$code]);
                }
            }

            $company_states_list = $_states;
            unset($_states, $company_states);

            Tygh::$app['view']->assign('company_states_list', $company_states_list);
        }

        Tygh::$app['view']->assign('states_list', $states_list);

        $tabs_countries = array (
            'title' => __('an_states_list'),
            'js' => true
        );

       // Registry::set('navigation.tabs.states_list', $tabs_countries);
    //}
}