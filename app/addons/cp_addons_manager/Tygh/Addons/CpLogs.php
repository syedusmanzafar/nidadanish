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

class CpLogs
{
    private $user_id;
    private $company_id;
    private $section;

    public function __construct($section, $params = array())
    {
        $user_id = \Tygh::$app['session']['auth']['user_id'];
        $this->user_id = !empty($user_id) ? intval($user_id) : 0;
        $this->company_id = isset($params['company_id']) ? intval($params['company_id']) : fn_get_runtime_company_id();
        $this->section = $section;
    }

    public function get($params, $items_per_page = 0)
    {
        $default_params = array (
            'page' => 1,
            'items_per_page' => $items_per_page
        );

        $params = array_merge($default_params, $params);

        $condition = db_quote(' AND section = ?s', $this->section);
        if (!empty($this->company_id)) {
            $condition .= db_quote(' AND company_id = ?i', $this->company_id);
        }
        if (!empty($params['period']) && $params['period'] != 'A') {
            list($time_from, $time_to) = fn_create_periods($params);
            $condition .= db_quote(' AND (?:logs.timestamp >= ?i AND ?:logs.timestamp <= ?i)', $time_from, $time_to);
        }
        if (!empty($params['action'])) {
            $condition .= db_quote(' AND action = ?s', $params['action']);
        }
        if (!empty($params['user_id'])) {
            $condition .= db_quote(' AND user_id = ?i', $params['user_id']);
        }

        if (!empty($params['user_name'])) {
            $user_condition = '';
            $user_names = array_filter(explode(' ', $params['user_name']));
            foreach ($user_names as $user_name) {
                $user_condition .= db_quote(' AND (firstname LIKE ?l OR lastname LIKE ?l)', "%{$user_name}%", "%{$user_name}%");
            }
            $user_ids = db_get_fields('SELECT DISTINCT user_id FROM ?:users WHERE 1 ?p', $user_condition);
            if (empty($user_ids)) {
                return array(array(), $params); // no results by user search
            }
            $condition .= db_quote(' AND user_id IN (?n)', $user_ids);
        }

        $sortings = array (
            'action' => 'action',
            'timestamp' => 'timestamp'
        );

        $limit = '';
        $sorting = db_sort($params, $sortings, 'timestamp', 'desc');

        if (!empty($params['items_per_page'])) {
            $params['total_items'] = db_get_field('SELECT COUNT(log_id) FROM ?:cp_custom_logs WHERE 1 ?p', $condition);
            $limit = db_paginate($params['page'], $params['items_per_page']);
        }

        $items = db_get_hash_array(
            'SELECT * FROM ?:cp_custom_logs WHERE 1 ?p ?p ?p',
            'log_id', $condition, $sorting, $limit
        );
        
        if (!empty($items)) {
            $user_ids = array_unique(array_column($items, 'user_id'));
            $users = db_get_hash_array(
                'SELECT user_id, firstname, lastname, email FROM ?:users WHERE user_id IN (?n)',
                'user_id', $user_ids
            );
            foreach ($items as &$item) {
                $item['content'] = !empty($item['content']) ? unserialize($item['content']) : array();
                $item['content'] = $this->prepareSectionContent($item);
                $item['extra'] = !empty($item['extra']) ? json_decode($item['extra'], true) : array();
                if (!empty($item['user_id'])) {
                    $item['user'] = !empty($users[$item['user_id']]) ? $users[$item['user_id']] : array();
                }
            }
        }

        return array($items, $params);
    }

    public function add($action, $content, $extra = array())
    {
        if (!$this->checkSection()) {
            return;
        }
        $data = array(
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'timestamp' => time(),
            'section' => $this->section,
            'action' => $action,
            'content' => !empty($content) ? serialize($content) : '',
            'extra' => !empty($extra) ? json_encode($extra) : ''
        );
        db_query('INSERT INTO ?:cp_custom_logs ?e', $data);
    }

    public function clear($params = array())
    {
        if (!$this->checkSection()) {
            return;
        }
        $condition = db_quote(' AND section = ?s', $this->section);
        if (!empty($this->company_id)) {
            $condition .= db_quote(' AND company_id = ?i', $this->company_id);
        }
        db_query('DELETE FROM ?:cp_custom_logs WHERE 1 ?p', $condition);
    }

    public function deleteRecords($item_ids)
    {
        if (!$this->checkSection()) {
            return;
        }
        $condition = db_quote(' AND section = ?s', $this->section);
        $item_ids = is_array($item_ids) ? $item_ids : explode(',', $item_ids);
        db_query('DELETE FROM ?:cp_custom_logs WHERE log_id IN (?n)', $item_ids);
    }

    public function prepareSectionContent($item)
    {
        $content = '';
        $sections = self::getSections();
        $section = !empty($sections[$this->section]) ? $sections[$this->section] : array();
        if (!empty($section['content_function'])) {
            $content = call_user_func($section['content_function'], $item);
        }
        if (empty($content) && !empty($item['content'])) {
            $content = $item['content'];
        }
        return $content;
    }

    private function checkSection()
    {
        $sections = self::getSections();
        return isset($sections[$this->section]) ? true : false;
    }

    public static function getSections()
    {
        static $sections = null;
        if (!isset($sections)) {
            $table_exists = db_get_field('SHOW TABLES LIKE ?l', Registry::get('config.table_prefix') . 'cp_custom_logs');
            if (!$table_exists) {
                $sections = array();
            } else {
                $sections = fn_get_schema('cp_logs', 'sections');
                $used_logs = Registry::get('addons.cp_addons_manager.use_logs');
                if (!empty($sections) && !empty($used_logs)) {
                    $sections = array_intersect_key($sections, $used_logs);
                }
            }
        }
        return $sections;
    }

    public static function allowShow($params = array())
    {
        $sections = self::getSections();
        return !empty($sections) ? true : false;
    }
}