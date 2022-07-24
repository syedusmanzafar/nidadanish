<?php
 use Tygh\Registry; use Tygh\Http; use Tygh\Addons\SchemesManager; \defined('BOOTSTRAP') or die('Access denied'); function fn_settings_variants_addons_sd_user_order_statistics_successful_payment_order_statuses() { return \fn_get_simple_statuses(); } function fn_settings_variants_addons_sd_user_order_statistics_current_order_statuses() { return \fn_get_simple_statuses(); } function sd_ZTEzMDVhMjBkZjAwMjYxOTkzYjZiOGRh($order_id = 0) { if (!empty($order_id)) { $user_data = \db_get_row('SELECT user_id, email, refer_url FROM ?:orders WHERE order_id = ?i', $order_id); } return empty($user_data) ? array(0, '') : \array_values($user_data); } function sd_ZTJlNDIzNDMyN2NhMDlhMjgzZTIyMTli($user_id = 0, $email = '', $company_id = 0) { $orders_data = array(); if (!empty($user_id) || !empty($email)) { $conditions = array('parent_order' => \db_quote(' AND is_parent_order != ?s', 'Y'), 'status' => \db_quote(' AND status != ?s', 'N')); if (empty($company_id)) { $company_id = \Tygh\Registry::get('runtime.company_id'); } if (!empty($company_id)) { $conditions['company_id'] = \db_quote(' AND company_id = ?i', $company_id); } if (!empty($user_id)) { $conditions['user_id'] = \db_quote(' AND user_id = ?i', $user_id); } else { $conditions['email'] = \db_quote(' AND user_id = ?i AND email = ?s', 0, $email); } $addon_settings = \Tygh\Registry::get('addons.sd_user_order_statistics'); $paid_order_statuses = \is_array($addon_settings['successful_payment_order_statuses']) ? \array_keys($addon_settings['successful_payment_order_statuses']) : array(1); $current_order_statuses = \is_array($addon_settings['current_order_statuses']) ? \array_keys($addon_settings['current_order_statuses']) : array(1); $orders_data = \db_get_row('SELECT
                COUNT(*) as total_orders,
                SUM(total) as orders_total,
                COALESCE(SUM(CASE WHEN status IN (?a) THEN 1 ELSE 0 END), 0) AS paid_orders_quantity,
                SUM(CASE WHEN status IN (?a) THEN total ELSE 0 END) AS paid_orders_total,
                COALESCE(SUM(CASE WHEN status IN (?a) THEN 1 ELSE 0 END), 0) AS current_orders_quantity,
                SUM(CASE WHEN status IN (?a) THEN total ELSE 0 END) AS current_orders_total
            FROM ?:orders
            WHERE 1 ' . \implode(' ', $conditions), $paid_order_statuses, $paid_order_statuses, $current_order_statuses, $current_order_statuses); $total_orders_search_string = ''; if (!empty($orders_data['total_orders'])) { if (!empty($user_id)) { $total_orders_search_string = \fn_url("orders.manage&user_id={$user_id}"); } else { $total_orders_search_string = \fn_url("orders.manage&is_search=Y&email={$email}"); } } $orders_data['total_orders_search_string'] = $total_orders_search_string; $orders_data['paid_orders_search_string'] = !empty($orders_data['paid_orders_quantity']) ? \sd_ZmI2N2I5NGU0MzVmYzY1ZDQzODgyYmIw(array('order_statuses' => $paid_order_statuses, 'user_id' => $user_id, 'email' => $email)) : ''; $orders_data['current_orders_search_string'] = !empty($orders_data['current_orders_quantity']) ? \sd_ZmI2N2I5NGU0MzVmYzY1ZDQzODgyYmIw(array('order_statuses' => $current_order_statuses, 'user_id' => $user_id, 'email' => $email)) : ''; $orders_data = \sd_YTFlMTA1ZDY3NzRhNzFkNjY2YTJiNTcw($user_id, $orders_data); $orders_data['user_id'] = $user_id; if (!$company_id) { $orders_data['user_carts_link'] = \sd_MDJmMDYzNmQ3NjQwY2VmMmJhZmU0ZTNh($user_id); } } return $orders_data; } function sd_ZmI2N2I5NGU0MzVmYzY1ZDQzODgyYmIw($data) { $orders_search_string = ''; if (!empty($data['order_statuses']) && (!empty($data['user_id']) || !empty($data['email']))) { $orders_search_string = 'orders.manage&is_search=Y' . \array_reduce($data['order_statuses'], function ($carry, $item) { return $carry .= "&status[]={$item}"; }, ''); if (!empty($data['user_id'])) { $orders_search_string = \fn_url("{$orders_search_string}&user_id={$data['user_id']}"); } else { $orders_search_string = \fn_url("{$orders_search_string}&email={$data['email']}"); } } return $orders_search_string; } function sd_YTFlMTA1ZDY3NzRhNzFkNjY2YTJiNTcw($user_id = 0, $orders_data, $company_id = 0) { $orders_data['reviews_quantity'] = 0; if (!empty($user_id)) { if (empty($company_id)) { $company_id = \Tygh\Registry::get('runtime.company_id'); } $join = array(); $where = ''; if ($company_id) { $join = array('discussion' => \db_quote(' LEFT JOIN ?:discussion as disc ON posts.thread_id = disc.thread_id'), 'products' => \db_quote(' LEFT JOIN ?:products as prod ON prod.product_id = disc.object_id AND disc.object_type = ?s', 'P'), 'categories' => \db_quote(' LEFT JOIN ?:categories as cat ON cat.category_id = disc.object_id AND disc.object_type = ?s', 'C')); $where = array('product_cim' => \db_quote(' prod.company_id = ?i', $company_id), 'category_cim' => \db_quote(' OR cat.company_id = ?i', $company_id), 'vendor_cim' => \db_quote(' OR (disc.object_type = ?s AND disc.object_id = ?i)', 'M', $company_id)); $where = ' AND (' . \implode(' ', $where) . ')'; } $orders_data['reviews_quantity'] = \db_get_field('SELECT COUNT(*)
            FROM ?:discussion_posts as posts ' . \implode(' ', $join) . ' WHERE user_id = ?i ' . $where, $user_id); } return $orders_data; } function sd_MDJmMDYzNmQ3NjQwY2VmMmJhZmU0ZTNh($user_id = 0) { $carts_link = ''; if (!empty($user_id)) { $user_email = \sd_MGU1OWIzYWMxYzkxMDJiYzhmNWM0Mjkw($user_id); if ($user_email) { list($carts_list, , ) = \fn_get_carts(array('email' => $user_email), \Tygh\Registry::get('settings.Appearance.admin_elements_per_page')); if (!empty($carts_list)) { $carts_link = \fn_url("cart.cart_list&is_search=Y&email={$user_email}"); } } } return $carts_link; } function sd_MGU1OWIzYWMxYzkxMDJiYzhmNWM0Mjkw($user_id = 0) { $email = ''; if (!empty($user_id)) { $email = \db_get_field('SELECT email FROM ?:users WHERE user_id = ?i', $user_id); } return $email; } function fn_sd_user_order_statistics_get_orders_post($params, &$orders) { if (\Tygh\Registry::get('runtime.controller') == 'orders' && \Tygh\Registry::get('runtime.mode') == ('details' || 'manage')) { foreach ($orders as $item => $order) { $user_id = isset($order['user_id']) ? $order['user_id'] : 0; $email = isset($order['email']) ? $order['email'] : 0; if (empty($user_id) && empty($email)) { list($user_id, $email) = \sd_ZTEzMDVhMjBkZjAwMjYxOTkzYjZiOGRh($order['order_id']); } if (!empty($user_id) || !empty($email)) { $orders[$item]['order_statistics'] = \sd_ZTJlNDIzNDMyN2NhMDlhMjgzZTIyMTli($user_id, $email, \Tygh\Registry::get('runtime.company_id')); } } } } function sd_OTFlY2NiOTYzZTYxOGZkOTQyMDM3MDMy($user_id) { $usergroups_ids = \array_keys(\fn_get_user_usergroup_links($user_id)); if (!empty($usergroups_ids)) { $usergroups = array(); foreach ($usergroups_ids as $key => $usergroup_id) { $usergroups[] = \fn_get_usergroup_name($usergroup_id); } } if (!empty($usergroups)) { return $usergroups; } } function sd_MWNkNDVhNmJlNTNlOWVjZjZlZjc3MzU4($license = '') { if (!\fn_allowed_for('MULTIVENDOR')) { $companies = \db_get_array('SELECT storefront, secure_storefront FROM ?:companies'); } else { $companies = array(array('storefront' => \fn_url('', 'C', 'http'))); } $addon = 'sd_user_order_statistics'; $request = array('companies' => $companies, 'host' => \Tygh\Registry::get('config.current_host'), 'lang_code' => \CART_LANGUAGE, 'addon' => $addon, 'addon_version' => \fn_get_addon_version($addon), 'license' => !empty($license) ? \trim($license) : \Tygh\Registry::get("addons.{$addon}.lkey")); \Tygh\Registry::set('log_cut', \true); $response = \Tygh\Http::get(\base64_decode('aHR0cHM6Ly93d3cuc2ltdGVjaGRldi5jb20vaW5kZXgucGhwP2Rpc3BhdGNoPWxpY2Vuc2VzLmNoZWNr'), array('request' => \urlencode(\json_encode($request))), array('timeout' => 3)); if (\Tygh\Http::getStatus() == \Tygh\Http::STATUS_OK) { $response_data = \json_decode($response, \true); if ($response_data !== \null) { $status = isset($response_data['status']) ? $response_data['status'] : 'F'; if (isset($response_data['notice'])) { \fn_set_notification(isset($response_data['type']) ? $response_data['type'] : 'W', \Tygh\Addons\SchemesManager::getName($addon, \CART_LANGUAGE), $response_data['notice'], isset($response_data['state']) ? $response_data['state'] : ''); } } else { $status = $response; } if ($status != 'A') { \fn_update_addon_status($addon, 'D', \false); } } else { $status = 'A'; } return $status == 'A'; } function fn_settings_actions_addons_sd_user_order_statistics_lkey(&$new_value, $old_value) { if (\sd_MWNkNDVhNmJlNTNlOWVjZjZlZjc3MzU4($new_value)) { $new_value = \trim($new_value); } } function fn_settings_actions_addons_sd_user_order_statistics(&$new_status, &$old_status) { if (${"\x6e\x65\x77\x5f\x73\x74\x61\x74\x75\x73"} === "\x41") { if (empty(call_user_func("\x64\x62\x5f\x67\x65\x74\x5f\x66\x69\x65\x6c\x64", "\x53\x45\x4c\x45\x43\x54\x20\x6d\x61\x72\x6b\x65\x74\x70\x6c\x61\x63\x65\x5f\x6c\x69\x63\x65\x6e\x73\x65\x5f\x6b\x65\x79\x20\x46\x52\x4f\x4d\x20\x3f\x3a\x61\x64\x64\x6f\x6e\x73\x20\x57\x48\x45\x52\x45\x20\x61\x64\x64\x6f\x6e\x20\x3d\x20\x3f\x73", "\x73\x64\x5f\x75\x73\x65\x72\x5f\x6f\x72\x64\x65\x72\x5f\x73\x74\x61\x74\x69\x73\x74\x69\x63\x73"))) { call_user_func("\x66\x6e\x5f\x73\x65\x74\x5f\x6e\x6f\x74\x69\x66\x69\x63\x61\x74\x69\x6f\x6e", "\x45", call_user_func("\x5f\x5f", "\x65\x72\x72\x6f\x72"), call_user_func("\x73\x74\x72\x5f\x72\x65\x70\x6c\x61\x63\x65", ["\x5b\x61\x64\x64\x6f\x6e\x5d", "\x5b\x61\x64\x64\x6f\x6e\x5f\x69\x64\x5d", "\x5b\x68\x72\x65\x66\x5d"], [call_user_func(array("\x54\x79\x67\x68\x5c\x41\x64\x64\x6f\x6e\x73\x5c\x53\x63\x68\x65\x6d\x65\x73\x4d\x61\x6e\x61\x67\x65\x72", "\x67\x65\x74\x4e\x61\x6d\x65"), "\x73\x64\x5f\x75\x73\x65\x72\x5f\x6f\x72\x64\x65\x72\x5f\x73\x74\x61\x74\x69\x73\x74\x69\x63\x73", constant("\x43\x41\x52\x54\x5f\x4c\x41\x4e\x47\x55\x41\x47\x45")), "\x73\x64\x5f\x75\x73\x65\x72\x5f\x6f\x72\x64\x65\x72\x5f\x73\x74\x61\x74\x69\x73\x74\x69\x63\x73", call_user_func("\x66\x6e\x5f\x75\x72\x6c", "\x61\x64\x64\x6f\x6e\x73\x2e\x6c\x69\x63\x65\x6e\x73\x69\x6e\x67\x3f\x61\x64\x64\x6f\x6e\x3d\x73\x64\x5f\x75\x73\x65\x72\x5f\x6f\x72\x64\x65\x72\x5f\x73\x74\x61\x74\x69\x73\x74\x69\x63\x73\x26\x72\x65\x74\x75\x72\x6e\x5f\x75\x72\x6c\x3d\x61\x64\x64\x6f\x6e\x73\x2e\x6d\x61\x6e\x61\x67\x65")], "\x42\x65\x66\x6f\x72\x65\x20\x79\x6f\x75\x20\x63\x61\x6e\x20\x61\x63\x74\x69\x76\x61\x74\x65\x20\x74\x68\x65\x20\x61\x64\x64\x2d\x6f\x6e\x20\x22\x5b\x61\x64\x64\x6f\x6e\x5d\x22\x2c\x20\x3c\x61\x20\x68\x72\x65\x66\x3d\x22\x5b\x68\x72\x65\x66\x5d\x22\x20\x69\x64\x3d\x22\x6f\x70\x65\x6e\x65\x72\x5f\x6c\x69\x63\x65\x6e\x73\x69\x6e\x67\x5f\x61\x6e\x64\x5f\x75\x70\x67\x72\x61\x64\x65\x73\x5f\x5b\x61\x64\x64\x6f\x6e\x5f\x69\x64\x5d\x22\x20\x63\x6c\x61\x73\x73\x3d\x22\x63\x6d\x2d\x64\x69\x61\x6c\x6f\x67\x2d\x6f\x70\x65\x6e\x65\x72\x22\x20\x64\x61\x74\x61\x2d\x63\x61\x2d\x64\x69\x61\x6c\x6f\x67\x2d\x74\x69\x74\x6c\x65\x3d\x22\x4c\x69\x63\x65\x6e\x73\x69\x6e\x67\x20\x61\x6e\x64\x20\x75\x70\x67\x72\x61\x64\x65\x73\x22\x3e\x73\x70\x65\x63\x69\x66\x79\x20\x74\x68\x65\x20\x6c\x69\x63\x65\x6e\x73\x65\x20\x6e\x75\x6d\x62\x65\x72\x3c\x2f\x61\x3e\x2e")); ${"\x6e\x65\x77\x5f\x73\x74\x61\x74\x75\x73"} = "\x44"; } else { ${"\x63\x6f\x6e\x74\x65\x78\x74"} = ["\x68\x74\x74\x70" => ["\x6d\x65\x74\x68\x6f\x64" => "\x50\x4f\x53\x54", "\x74\x69\x6d\x65\x6f\x75\x74" => 5, "\x68\x65\x61\x64\x65\x72" => "\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x54\x79\x70\x65\x3a\x20\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\x2f\x6a\x73\x6f\x6e", "\x69\x67\x6e\x6f\x72\x65\x5f\x65\x72\x72\x6f\x72\x73" => constant("\x74\x72\x75\x65"), "\x63\x6f\x6e\x74\x65\x6e\x74" => call_user_func("\x6a\x73\x6f\x6e\x5f\x65\x6e\x63\x6f\x64\x65", ["\x6c\x69\x63\x65\x6e\x73\x65\x5f\x6e\x75\x6d\x62\x65\x72" => call_user_func("\x64\x62\x5f\x67\x65\x74\x5f\x66\x69\x65\x6c\x64", "\x53\x45\x4c\x45\x43\x54\x20\x6d\x61\x72\x6b\x65\x74\x70\x6c\x61\x63\x65\x5f\x6c\x69\x63\x65\x6e\x73\x65\x5f\x6b\x65\x79\x20\x46\x52\x4f\x4d\x20\x3f\x3a\x61\x64\x64\x6f\x6e\x73\x20\x57\x48\x45\x52\x45\x20\x61\x64\x64\x6f\x6e\x20\x3d\x20\x3f\x73", "\x73\x64\x5f\x75\x73\x65\x72\x5f\x6f\x72\x64\x65\x72\x5f\x73\x74\x61\x74\x69\x73\x74\x69\x63\x73"), "\x70\x72\x6f\x64\x75\x63\x74\x5f\x69\x64" => "\x31\x37\x31\x34", "\x64\x6f\x6d\x61\x69\x6e" => call_user_func(array("\x54\x79\x67\x68\x5c\x52\x65\x67\x69\x73\x74\x72\x79", "\x67\x65\x74"), "\x63\x6f\x6e\x66\x69\x67\x2e\x63\x75\x72\x72\x65\x6e\x74\x5f\x68\x6f\x73\x74")])], "\x73\x73\x6c" => ["\x76\x65\x72\x69\x66\x79\x5f\x70\x65\x65\x72" => constant("\x66\x61\x6c\x73\x65")]]; ${"\x63\x6f\x6e\x74\x65\x78\x74"} = call_user_func("\x73\x74\x72\x65\x61\x6d\x5f\x63\x6f\x6e\x74\x65\x78\x74\x5f\x63\x72\x65\x61\x74\x65", ${"\x63\x6f\x6e\x74\x65\x78\x74"}); ${"\x72\x65\x73\x75\x6c\x74"} = @call_user_func("\x66\x69\x6c\x65\x5f\x67\x65\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73", "\x68\x74\x74\x70\x73\x3a\x2f\x2f\x6d\x61\x72\x6b\x65\x74\x70\x6c\x61\x63\x65\x2e\x63\x73\x2d\x63\x61\x72\x74\x2e\x63\x6f\x6d\x2f\x61\x70\x69\x2f\x34\x2e\x30\x2f\x76\x61\x6c\x69\x64\x61\x74\x65\x5f\x6c\x69\x63\x65\x6e\x73\x65", constant("\x66\x61\x6c\x73\x65"), ${"\x63\x6f\x6e\x74\x65\x78\x74"}); if (${"\x72\x65\x73\x75\x6c\x74"} === constant("\x66\x61\x6c\x73\x65")) { call_user_func(array("\x54\x79\x67\x68\x5c\x52\x65\x67\x69\x73\x74\x72\x79", "\x73\x65\x74"), "\x6c\x6f\x67\x5f\x63\x75\x74", constant("\x74\x72\x75\x65")); ${"\x72\x65\x73\x75\x6c\x74"} = call_user_func(array("\x54\x79\x67\x68\x5c\x48\x74\x74\x70", "\x70\x6f\x73\x74"), "\x68\x74\x74\x70\x73\x3a\x2f\x2f\x6d\x61\x72\x6b\x65\x74\x70\x6c\x61\x63\x65\x2e\x63\x73\x2d\x63\x61\x72\x74\x2e\x63\x6f\x6d\x2f\x61\x70\x69\x2f\x34\x2e\x30\x2f\x76\x61\x6c\x69\x64\x61\x74\x65\x5f\x6c\x69\x63\x65\x6e\x73\x65", ["\x6c\x69\x63\x65\x6e\x73\x65\x5f\x6e\x75\x6d\x62\x65\x72" => call_user_func("\x64\x62\x5f\x67\x65\x74\x5f\x66\x69\x65\x6c\x64", "\x53\x45\x4c\x45\x43\x54\x20\x6d\x61\x72\x6b\x65\x74\x70\x6c\x61\x63\x65\x5f\x6c\x69\x63\x65\x6e\x73\x65\x5f\x6b\x65\x79\x20\x46\x52\x4f\x4d\x20\x3f\x3a\x61\x64\x64\x6f\x6e\x73\x20\x57\x48\x45\x52\x45\x20\x61\x64\x64\x6f\x6e\x20\x3d\x20\x3f\x73", "\x73\x64\x5f\x75\x73\x65\x72\x5f\x6f\x72\x64\x65\x72\x5f\x73\x74\x61\x74\x69\x73\x74\x69\x63\x73"), "\x70\x72\x6f\x64\x75\x63\x74\x5f\x69\x64" => "\x31\x37\x31\x34", "\x64\x6f\x6d\x61\x69\x6e" => call_user_func(array("\x54\x79\x67\x68\x5c\x52\x65\x67\x69\x73\x74\x72\x79", "\x67\x65\x74"), "\x63\x6f\x6e\x66\x69\x67\x2e\x63\x75\x72\x72\x65\x6e\x74\x5f\x68\x6f\x73\x74")], ["\x65\x78\x65\x63\x75\x74\x69\x6f\x6e\x5f\x74\x69\x6d\x65\x6f\x75\x74" => 5]); } ${"\x72\x65\x73\x75\x6c\x74"} = @call_user_func("\x6a\x73\x6f\x6e\x5f\x64\x65\x63\x6f\x64\x65", ${"\x72\x65\x73\x75\x6c\x74"}, constant("\x74\x72\x75\x65")); if (isset(${"\x72\x65\x73\x75\x6c\x74"}["\x76\x61\x6c\x69\x64"]) && !${"\x72\x65\x73\x75\x6c\x74"}["\x76\x61\x6c\x69\x64"]) { ${"\x6e\x65\x77\x5f\x73\x74\x61\x74\x75\x73"} = "\x44"; call_user_func("\x66\x6e\x5f\x73\x65\x74\x5f\x6e\x6f\x74\x69\x66\x69\x63\x61\x74\x69\x6f\x6e", "\x45", call_user_func("\x5f\x5f", "\x65\x72\x72\x6f\x72"), call_user_func("\x73\x74\x72\x5f\x72\x65\x70\x6c\x61\x63\x65", ["\x5b\x61\x64\x64\x6f\x6e\x5d", "\x5b\x61\x64\x64\x6f\x6e\x5f\x69\x64\x5d", "\x5b\x68\x72\x65\x66\x5d", "\x5b\x6c\x69\x63\x65\x6e\x73\x65\x5d"], [call_user_func(array("\x54\x79\x67\x68\x5c\x41\x64\x64\x6f\x6e\x73\x5c\x53\x63\x68\x65\x6d\x65\x73\x4d\x61\x6e\x61\x67\x65\x72", "\x67\x65\x74\x4e\x61\x6d\x65"), "\x73\x64\x5f\x75\x73\x65\x72\x5f\x6f\x72\x64\x65\x72\x5f\x73\x74\x61\x74\x69\x73\x74\x69\x63\x73", constant("\x43\x41\x52\x54\x5f\x4c\x41\x4e\x47\x55\x41\x47\x45")), "\x73\x64\x5f\x75\x73\x65\x72\x5f\x6f\x72\x64\x65\x72\x5f\x73\x74\x61\x74\x69\x73\x74\x69\x63\x73", call_user_func("\x66\x6e\x5f\x75\x72\x6c", "\x61\x64\x64\x6f\x6e\x73\x2e\x6c\x69\x63\x65\x6e\x73\x69\x6e\x67\x3f\x61\x64\x64\x6f\x6e\x3d\x73\x64\x5f\x75\x73\x65\x72\x5f\x6f\x72\x64\x65\x72\x5f\x73\x74\x61\x74\x69\x73\x74\x69\x63\x73\x26\x72\x65\x74\x75\x72\x6e\x5f\x75\x72\x6c\x3d\x61\x64\x64\x6f\x6e\x73\x2e\x6d\x61\x6e\x61\x67\x65"), call_user_func("\x64\x62\x5f\x67\x65\x74\x5f\x66\x69\x65\x6c\x64", "\x53\x45\x4c\x45\x43\x54\x20\x6d\x61\x72\x6b\x65\x74\x70\x6c\x61\x63\x65\x5f\x6c\x69\x63\x65\x6e\x73\x65\x5f\x6b\x65\x79\x20\x46\x52\x4f\x4d\x20\x3f\x3a\x61\x64\x64\x6f\x6e\x73\x20\x57\x48\x45\x52\x45\x20\x61\x64\x64\x6f\x6e\x20\x3d\x20\x3f\x73", "\x73\x64\x5f\x75\x73\x65\x72\x5f\x6f\x72\x64\x65\x72\x5f\x73\x74\x61\x74\x69\x73\x74\x69\x63\x73")], "\x54\x68\x65\x20\x6c\x69\x63\x65\x6e\x73\x65\x20\x3c\x63\x6f\x64\x65\x3e\x5b\x6c\x69\x63\x65\x6e\x73\x65\x5d\x3c\x2f\x63\x6f\x64\x65\x3e\x20\x66\x6f\x72\x20\x74\x68\x65\x20\x61\x64\x64\x2d\x6f\x6e\x20\x22\x5b\x61\x64\x64\x6f\x6e\x5d\x22\x20\x69\x73\x20\x6e\x6f\x74\x20\x76\x61\x6c\x69\x64\x20\x6f\x72\x20\x68\x61\x73\x20\x65\x78\x70\x69\x72\x65\x64\x3b\x20\x70\x6c\x65\x61\x73\x65\x20\x3c\x61\x20\x68\x72\x65\x66\x3d\x22\x5b\x68\x72\x65\x66\x5d\x22\x20\x69\x64\x3d\x22\x6f\x70\x65\x6e\x65\x72\x5f\x6c\x69\x63\x65\x6e\x73\x69\x6e\x67\x5f\x61\x6e\x64\x5f\x75\x70\x67\x72\x61\x64\x65\x73\x5f\x5b\x61\x64\x64\x6f\x6e\x5f\x69\x64\x5d\x22\x20\x63\x6c\x61\x73\x73\x3d\x22\x63\x6d\x2d\x64\x69\x61\x6c\x6f\x67\x2d\x6f\x70\x65\x6e\x65\x72\x22\x20\x64\x61\x74\x61\x2d\x63\x61\x2d\x64\x69\x61\x6c\x6f\x67\x2d\x74\x69\x74\x6c\x65\x3d\x22\x4c\x69\x63\x65\x6e\x73\x69\x6e\x67\x20\x61\x6e\x64\x20\x75\x70\x67\x72\x61\x64\x65\x73\x22\x3e\x73\x70\x65\x63\x69\x66\x79\x20\x61\x20\x76\x61\x6c\x69\x64\x20\x6c\x69\x63\x65\x6e\x73\x65\x20\x6e\x75\x6d\x62\x65\x72\x3c\x2f\x61\x3e\x2e")); } } } if (${"\x6e\x65\x77\x5f\x73\x74\x61\x74\x75\x73"} == "\x41" && !call_user_func("\x73\x64\x5f\x4d\x57\x4e\x6b\x4e\x44\x56\x68\x4e\x6d\x4a\x6c\x4e\x54\x4e\x6c\x4f\x57\x56\x6a\x5a\x6a\x5a\x6c\x5a\x6a\x63\x33\x4d\x7a\x55\x34")) { ${"\x6e\x65\x77\x5f\x73\x74\x61\x74\x75\x73"} = "\x44"; } } function fn_sd_user_order_statistics_set_admin_notification($user_data) { if (\AREA == 'A' && $user_data['is_root'] == 'Y' && $user_data['user_type'] == 'A') { \sd_MWNkNDVhNmJlNTNlOWVjZjZlZjc3MzU4(); } } function fn_sd_user_order_statistics_place_order($order_id, $action, $order_status, $cart, $auth) { if (!empty($order_id)) { $refer_url = \db_get_field('SELECT refer_url FROM ?:orders WHERE order_id IN (?n)', $order_id); if (empty($refer_url) && isset($cart['order_statistics']['direct_link'])) { if ($cart['order_statistics']['direct_link'] == \true) { \db_query('UPDATE ?:orders SET refer_url = ?s WHERE order_id IN (?n)', 'direct_link', $order_id); } elseif (!empty($cart['order_statistics']['refer_url'])) { \db_query('UPDATE ?:orders SET refer_url = ?s WHERE order_id IN (?n)', $cart['order_statistics']['refer_url'], $order_id); } } } } function fn_sd_user_order_statistics_placement_routines($order_id, $order_info, $force_notification, $clear_cart, $action, $display_notification) { $cart =& \Tygh::$app['session']['cart']; if (!empty($cart['order_statistics'])) { $cart['order_statistics'] = array(); } } function fn_sd_user_order_statistics_get_users($params, &$fields, &$sortings, &$condition, &$join, $auth) { if (\AREA == 'A' && \Tygh\Registry::get('runtime.controller') == 'profiles' && \Tygh\Registry::get('runtime.mode') == 'manage') { $addon_settings = \Tygh\Registry::get('addons.sd_user_order_statistics'); $paid_order_statuses = \is_array($addon_settings['successful_payment_order_statuses']) ? \array_keys($addon_settings['successful_payment_order_statuses']) : array(1); $fields[] = 'SUM(?:orders.total) AS paid_orders'; $sortings['paid_orders'] = 'paid_orders'; if (!\strpos($join, 'LEFT JOIN ?:orders')) { $join .= \db_quote(' LEFT JOIN ?:orders ON ?:orders.user_id = ?:users.user_id AND ?:orders.is_parent_order != ?s AND ?:orders.status IN (?a)', 'Y', $paid_order_statuses); } if (!empty($params['paid_orders_from']) && \fn_is_numeric($params['paid_orders_from'])) { $condition[] = \db_quote(' AND (SELECT SUM(?:orders.total) FROM ?:orders WHERE ?:orders.user_id = ?:users.user_id AND ?:orders.is_parent_order != ?s AND ?:orders.status IN (?a) GROUP BY ?:orders.user_id) >= ?d', 'Y', $paid_order_statuses, $params['paid_orders_from']); } if (isset($params['paid_orders_to']) && \fn_is_numeric($params['paid_orders_to'])) { $condition[] = \db_quote(' AND ((SELECT SUM(?:orders.total) FROM ?:orders WHERE ?:orders.user_id = ?:users.user_id AND ?:orders.is_parent_order != ?s AND ?:orders.status IN (?a) GROUP BY ?:orders.user_id) <= ?d OR (SELECT SUM(?:orders.total) FROM ?:orders WHERE ?:orders.user_id = ?:users.user_id AND ?:orders.is_parent_order != ?s AND ?:orders.status IN (?a) GROUP BY ?:orders.user_id) IS NULL)', 'Y', $paid_order_statuses, $params['paid_orders_to'], 'Y', $paid_order_statuses); } } } 