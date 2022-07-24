<?php

use Tygh\Registry;
use Tygh\Enum\ProductFeatures;
use Tygh\Navigation\LastView;
/***************************************************************************
*   -----------------------------------CORE-----------------------------    *
****************************************************************************/
function cmp($a, $b) {
  return $a['pos'] > $b['pos'];
}

function fn_get_all_brands_cat($category_id = false)
{
    $params = array(
        'exclude_group' => true,
        'get_descriptions' => true,
        'feature_types' => array(ProductFeatures::EXTENDED),
        'variants' => true,
        'plain' => true,
        'category_id' => $category_id,
        'skip_variants_threshould' => true,
    );

    list($features) = fn_get_product_features_cat($params, 0);

    $variants = array();

    foreach ($features as $feature) {
        if (!empty($feature['variants'])) {
            $variants = array_merge($variants, $feature['variants']);
        }
    }
	usort($variants, "cmp");

    return $variants;
}

function fn_get_brand_categories($params = Array(),$lang_code = DESCR_SL) {
	$default_params = Array(
		'page' => 1,
        'items_per_page' => $per_page
	);

	$condition = '';
    $limit = '';

    // Define sort fields
    $sortings = array (
    	'id' => 'id',
        'name' => 'name',
    );
    $sorting = db_sort($params, $sortings, 'id', 'desc');

    $params = array_merge($default_params, $_REQUEST);

	if(!empty($params['id']) && fn_is_numeric($params['id'])) {
		$condition = db_quote(" AND id = ?i",$params['id']);
	}

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_found_rows(db_get_field("SELECT * FROM ?:brand_categories WHERE 1 $condition"));
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

	$query = db_get_array("SELECT * FROM ?:brand_categories WHERE 1 $condition $sorting $limit",$lang_code);

	return array($query, $params);
}

function fn_get_brand_category_data($scroller_id,$lang_code = DESCR_SL) {
    $condition = db_quote(" AND id = ?i",$scroller_id);

    $data = db_get_row("SELECT * FROM ?:brand_categories WHERE 1 $condition");


    return $data;
}
// fn_print_r(fn_get_schema('menu','menu'));

function fn_update_brand_category($request) {

    $data = $request['brand_data'];

    $val['name'] = $data['name'];    

    if($data['id'] == 0) {
        $catid = db_query("REPLACE INTO ?:brand_categories ?e",$val);
    } else {
        db_query("UPDATE ?:brand_categories SET ?u WHERE id = ?i", $val, $data['id']);
        foreach($data['variants'] as $k => $v) {
			$cat[$k] = db_get_field("SELECT category_id FROM ?:product_feature_variants WHERE variant_id=?i",$k);
            if($v['checked'] == 1) {
				db_query("UPDATE ?:product_feature_variants SET category_id=?i,pos=?i WHERE variant_id = ?i", $data['id'],$v['pos'], $k);
			} else {
				if($cat[$k] == $data['id']) db_query("UPDATE ?:product_feature_variants SET category_id=?i,pos=?i WHERE variant_id = ?i", 0,$v['pos'], $k);
			}
        }
    }

    return $catid;
}

function fn_brand_category_delete($id) {
    if(is_array($id)) {
        foreach($id as $item) {
            $checking = db_get_field("SELECT id FROM ?:brand_categories WHERE id = ?i",$item);
            if($checking) {
                db_query("DELETE FROM ?:brand_categories WHERE id=?i",$item);
                db_query("UPDATE ?:product_feature_variants SET category_id=?i WHERE category_id=?i",0,$item);
            }
        }
    } else {
		$checking = db_get_field("SELECT id FROM ?:brand_categories WHERE id = ?i",$id);
		if($checking) {
			db_query("DELETE FROM ?:brand_categories WHERE id=?i",$id);
			db_query("UPDATE ?:product_feature_variants SET category_id=?i WHERE category_id=?i",0,$id);
		}
    }
}


function fn_get_product_features_cat($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    /**
     * Changes params before getting products features
     *
     * @param array  $params         Products features search params
     * @param int    $items_per_page Items per page
     * @param string $lang_code      2-letters language code
     */
    fn_set_hook('get_product_features_pre', $params, $items_per_page, $lang_code);

    // Init filter
    $params = LastView::instance()->update('product_features', $params);

    $default_params = array(
        'product_id' => 0,
        'category_ids' => array(),
        'statuses' => AREA == 'C' ? array('A') : array(),
        'plain' => false,
        'feature_types' => array(),
        'feature_id' => 0,
        'display_on' => '',
        'exclude_group' => false,
        'exclude_filters' => false,
        'page' => 1,
        'items_per_page' => $items_per_page,

        // Whether to load only features that have variants assigned or value applied to given product.
        // Parameter is only used if "product_id" is given.
        'existent_only' => false,

        // Whether to load variants for loaded features.
        'variants' => false,
        'variant_images' => true,

        // Whether to load only variants that are assigned for given product.
        // Parameter is only used if "product_id" is given and "variants" is set to true.
        'variants_selected_only' => false,

        // Whether to skip restriction on maximal count of variants to be loaded.
        'skip_variants_threshould' => false,

        // List of variant IDs that should be loaded in case of count of variants to be loaded is more
        // than specified variants threshold. Format: [feature_id => [variant_id, ...], ...].
        // Parameter is only used if "variants" param is set to true and "skip_variants_threshould" is set to false.
        'variants_only' => null,
    );

    $params = array_merge($default_params, $params);

    $base_fields = $fields = array (
        'pf.feature_id',
        'pf.company_id',
        'pf.feature_type',
        'pf.parent_id',
        'pf.display_on_product',
        'pf.display_on_catalog',
        'pf.display_on_header',
        '?:product_features_descriptions.description',
        '?:product_features_descriptions.lang_code',
        '?:product_features_descriptions.prefix',
        '?:product_features_descriptions.suffix',
        'pf.categories_path',
        '?:product_features_descriptions.full_description',
        'pf.status',
        'pf.comparison',
        'pf.position'
    );

    $condition = $join = $group = '';
    $group_condition = '';

    $fields[] = 'pf_groups.position AS group_position';
    $join .= db_quote(" LEFT JOIN ?:product_features AS pf_groups ON pf.parent_id = pf_groups.feature_id");
    $join .= db_quote(" LEFT JOIN ?:product_features_descriptions ON ?:product_features_descriptions.feature_id = pf.feature_id AND ?:product_features_descriptions.lang_code = ?s", $lang_code);

    if (empty($params['feature_types']) || $params['feature_types'] != ProductFeatures::GROUP) {
        $condition .= db_quote(" AND pf.feature_type != ?s", ProductFeatures::GROUP);
    }

    if (!empty($params['product_id'])) {
        $feature_values_join_type = empty($params['existent_only']) ? 'LEFT' : 'INNER';
        $join .= db_quote(
            " {$feature_values_join_type} JOIN ?:product_features_values"
            . " ON ?:product_features_values.feature_id = pf.feature_id"
            . " AND ?:product_features_values.product_id = ?i"
            . " AND ?:product_features_values.lang_code = ?s",
            $params['product_id'],
            $lang_code
        );

        $fields[] = '?:product_features_values.value';
        $fields[] = '?:product_features_values.variant_id';
        $fields[] = '?:product_features_values.value_int';

        $group = ' GROUP BY pf.feature_id';
    }

    if (!empty($params['feature_id'])) {
        $condition .= db_quote(" AND pf.feature_id IN (?n)", $params['feature_id']);
    }

    if (isset($params['description']) && fn_string_not_empty($params['description'])) {
        $condition .= db_quote(" AND ?:product_features_descriptions.description LIKE ?l", "%" . trim($params['description']) . "%");
    }

    if (!empty($params['statuses'])) {
        $condition .= db_quote(" AND pf.status IN (?a) AND (pf_groups.status IN (?a) OR pf_groups.status IS NULL)", $params['statuses'], $params['statuses']);
    }

    if (isset($params['parent_id']) && $params['parent_id'] !== '') {
        $condition .= db_quote(" AND pf.parent_id = ?i", $params['parent_id']);
        $group_condition .= db_quote(" AND pf.feature_id = ?i", $params['parent_id']);
    }

    if (!empty($params['display_on']) && in_array($params['display_on'], array('product', 'catalog', 'header'))) {
        $condition .= " AND pf.display_on_$params[display_on] = 'Y'";
        $group_condition .= " AND pf.display_on_$params[display_on] = 'Y'";
    }

    if (!empty($params['feature_types'])) {
        $condition .= db_quote(" AND pf.feature_type IN (?a)", $params['feature_types']);
    }

    if (!empty($params['category_ids'])) {
        $c_ids = is_array($params['category_ids']) ? $params['category_ids'] : fn_explode(',', $params['category_ids']);
        $find_set = array(
            " pf.categories_path = '' OR ISNULL(pf.categories_path)"
        );

        if (!empty($params['search_in_subcats'])) {
            $child_ids = db_get_fields("SELECT a.category_id FROM ?:categories as a LEFT JOIN ?:categories as b ON b.category_id IN (?n) WHERE a.id_path LIKE CONCAT(b.id_path, '/%')", $c_ids);
            $c_ids = fn_array_merge($c_ids, $child_ids, false);
        }

        foreach ($c_ids as $k => $v) {
            $find_set[] = db_quote(" FIND_IN_SET(?i, pf.categories_path) ", $v);
        }

        $find_in_set = db_quote(" AND (?p)", implode('OR', $find_set));
        $condition .= $find_in_set;
        $group_condition .= $find_in_set;
    }

    if (!fn_allowed_for('ULTIMATE:FREE')) {
        if (!empty($params['exclude_filters'])) {
            $_condition = ' WHERE 1 ';

            if (fn_allowed_for('ULTIMATE')) {
                $_condition .= fn_get_company_condition('?:product_filters.company_id');
            }

            $exclude_feature_id = db_get_fields("SELECT ?:product_filters.feature_id FROM ?:product_filters $_condition GROUP BY ?:product_filters.feature_id");
            if (!empty($exclude_feature_id)) {
                $condition .= db_quote(" AND pf.feature_id NOT IN (?n)", $exclude_feature_id);
                unset($exclude_feature_id);
            }
        }
    }

    /**
     * Change SQL parameters before product features selection
     *
     * @param array  $fields    List of fields for retrieving
     * @param string $join      String with the complete JOIN information (JOIN type, tables and fields) for an SQL-query
     * @param string $condition String containing SQL-query condition possibly prepended with a logical operator (AND or OR)
     */
    fn_set_hook('get_product_features', $fields, $join, $condition, $params);

    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field(
            "SELECT COUNT(DISTINCT pf.feature_id) FROM ?:product_features AS pf $join WHERE 1 $condition"
        );
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $data = db_get_hash_array(
        "SELECT " . implode(', ', $fields)
        . " FROM ?:product_features AS pf"
        . " $join WHERE 1 $condition $group"
        . " ORDER BY group_position, pf.position, ?:product_features_descriptions.description $limit",
        'feature_id'
    );

    $has_ungroupped = false;

    // Fetch variants for loaded features
    if (!empty($data) && $params['variants']) {

        // Only fetch variants for selectable features
        $feature_ids = array();
        foreach ($data as $feature_id => $feature_data) {
            if (strpos(ProductFeatures::getSelectable(), $feature_data['feature_type']) !== false) {
                $feature_ids[] = $feature_id;
                $data[$feature_id]['variants'] = array(); // initialize variants
            }
        }

        // Variants to load if count of variants to be loaded is more than threshold
        // [feature_id => [variant_id, ...], ...]
        $variant_ids_to_load = isset($params['variants_only']) ? (array) $params['variants_only'] : array();

        foreach ($feature_ids as $feature_id) {
            $variants_params = array(
                'feature_id' => $feature_id,
                'product_id' => $params['product_id'],
                'category_id' => $params['category_id'],
                'get_images' => $params['variant_images'],
                'selected_only' => $params['variants_selected_only']
            );

            if (AREA == 'A' && empty($params['skip_variants_threshould'])) {
                // Fetch count of variants to be loaded
                $variants_params['fetch_total_count_only'] = true;
                $total_variants_count = fn_get_product_feature_variants($variants_params, 0, $lang_code);
                $variants_params['fetch_total_count_only'] = false;

                if ($total_variants_count > PRODUCT_FEATURE_VARIANTS_THRESHOLD) {
                    // AJAX variants loader will be used
                    $data[$feature_id]['use_variant_picker'] = true;

                    // Fetch only selected variants for given product (if it is given).
                    // These variants would be used for displaying preselection at AJAX variants loader.
                    if (!empty($params['product_id'])) {
                        $variants_params['selected_only'] = true;
                    }
                    // Load specific variants (for example for preselection at AJAX loader at search form)
                    elseif (!empty($variant_ids_to_load[$feature_id])) {
                        // Restrict selection to specified variant IDs
                        $variants_params['variant_id'] = $variant_ids_to_load[$feature_id];
                    }
                    // Skip loading variants.
                    else {
                        continue;
                    }
                }
            }

            list($variants, $search) = fn_get_product_feature_variants_cat($variants_params, 0, $lang_code);

            foreach ($variants as $variant) {
                $data[$variant['feature_id']]['variants'][$variant['variant_id']] = $variant;
            }
        }
    }

    foreach ($data as $feature_data) {
        if (empty($feature_data['parent_id'])) {
            $has_ungroupped = true;
            break;
        }
    }

    // Get groups
    if (empty($params['exclude_group'])) {

        $group_ids = array();
        foreach ($data as $feature_data) {
            if (!empty($feature_data['parent_id'])) {
                $group_ids[$feature_data['parent_id']] = true;
            }
        }

        $groups = db_get_hash_array("SELECT " . implode(', ', $base_fields) . " FROM ?:product_features AS pf LEFT JOIN ?:product_features_descriptions ON ?:product_features_descriptions.feature_id = pf.feature_id AND ?:product_features_descriptions.lang_code = ?s WHERE pf.feature_type = 'G' AND (pf.feature_id IN (?n) OR pf.feature_id NOT IN (SELECT parent_id FROM ?:product_features)) ?p ORDER BY pf.position, ?:product_features_descriptions.description", 'feature_id', $lang_code, array_keys($group_ids), $group_condition);

        // Insert groups before appropriate features
        $new_data = $groups;
        foreach ($data as $feature_id => $feature_data) {
            if (!empty($feature_data['parent_id']) && !empty($groups[$feature_data['parent_id']])) {
                $new_data[$feature_data['parent_id']] = $groups[$feature_data['parent_id']];
                unset($groups[$feature_data['parent_id']]);
            }
            $new_data[$feature_id] = $feature_data;
        }
        $data = $new_data;
    }

    if ($params['plain'] == false) {
        $delete_keys = array();
        foreach ($data as $k => $v) {
            if (!empty($v['parent_id']) && !empty($data[$v['parent_id']])) {
                $data[$v['parent_id']]['subfeatures'][$v['feature_id']] = $v;
                $data[$k] = & $data[$v['parent_id']]['subfeatures'][$v['feature_id']];
                $delete_keys[] = $k;
            }

            if (!empty($params['get_descriptions']) && empty($v['parent_id'])) {
                $d = fn_get_categories_list($v['categories_path']);
                $data[$k]['feature_description'] = __('display_on') . ': <span>' . implode(', ', $d) . '</span>';
            }
        }

        foreach ($delete_keys as $k) {
            unset($data[$k]);
        }
    }

    /**
     * Change products features data
     *
     * @param array   $data           Products features data
     * @param array   $params         Products features search params
     * @param boolean $has_ungroupped Flag determines if there are features without group
     */
    fn_set_hook('get_product_features_post', $data, $params, $has_ungroupped);

    LastView::instance()->processResults('product_features', $data, $params);

    return array($data, $params, $has_ungroupped);
}

function fn_get_product_feature_variants_cat($params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    /**
     * Changes parameters for getting product feature variants
     *
     * @param array  $params         array with search parameters
     * @param int    $items_per_page Items per page
     * @param string $lang_code      2-letters language code
     */
    fn_set_hook('get_product_feature_variants_pre', $params, $items_per_page, $lang_code);

    // Set default values to input params
    $default_params = array (
        'page' => 1,
        'product_id' => 0,
        'feature_id' => 0,
        'feature_type' => '',
        'get_images' => false,
        'items_per_page' => $items_per_page,
        'selected_only' => false,
        'fetch_total_count_only' => false,
        'search_query' => null,

        // An ID or list of IDs of variants that should be loaded.
        'variant_id' => null,
    );

    $params = array_merge($default_params, $params);

    if (is_array($params['feature_id'])) {
        $fields = array(
            '?:product_feature_variant_descriptions.variant',
            '?:product_feature_variants.variant_id',
            '?:product_feature_variants.feature_id',
        );
    } else {
        $fields = array(
            '?:product_feature_variant_descriptions.*',
            '?:product_feature_variants.*',
        );
    }

    $condition = $group_by = $sorting = '';
    $feature_id = is_array($params['feature_id']) ? $params['feature_id'] : array($params['feature_id']);

    $join = db_quote(" LEFT JOIN ?:product_feature_variant_descriptions ON ?:product_feature_variant_descriptions.variant_id = ?:product_feature_variants.variant_id AND ?:product_feature_variant_descriptions.lang_code = ?s", $lang_code);
    $condition .= db_quote(" AND ?:product_feature_variants.feature_id IN (?n)", $feature_id);
    $sorting = db_quote("?:product_feature_variants.position, ?:product_feature_variant_descriptions.variant");

    if (!empty($params['variant_id'])) {
        $condition .= db_quote(' AND ?:product_feature_variants.variant_id IN (?n)', (array)$params['variant_id']);
    }

    if (!empty($params['product_id'])) {
        $fields[] = '?:product_features_values.variant_id as selected';
        $fields[] = '?:product_features.feature_type';

        if (!empty($params['selected_only'])) {
            $join .= db_quote(" INNER JOIN ?:product_features_values ON ?:product_features_values.variant_id = ?:product_feature_variants.variant_id AND ?:product_features_values.lang_code = ?s AND ?:product_features_values.product_id = ?i", $lang_code, $params['product_id']);
        } else {
            $join .= db_quote(" LEFT JOIN ?:product_features_values ON ?:product_features_values.variant_id = ?:product_feature_variants.variant_id AND ?:product_features_values.lang_code = ?s AND ?:product_features_values.product_id = ?i", $lang_code, $params['product_id']);
        }

        $join .= db_quote(" LEFT JOIN ?:product_features ON ?:product_features.feature_id = ?:product_feature_variants.feature_id");
        $group_by = db_quote(" GROUP BY ?:product_feature_variants.variant_id");
    }

    if (!empty($params['search_query'])) {
        $condition .= db_quote(' AND ?:product_feature_variant_descriptions.variant LIKE ?l',
            '%' . trim($params['search_query']) . '%'
        );
    }

    $limit = '';
	if($params['category_id']) {
		$condition .= db_quote(' AND ?:product_feature_variants.category_id = ?i',$params['category_id']);
	}
    if ($params['fetch_total_count_only'] || !empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:product_feature_variants $join WHERE 1 $condition");

        if ($params['fetch_total_count_only']) {
            return $params['total_items'];
        } elseif ($params['items_per_page']) {
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }
    }

    /**
     * Changes  SQL parameters for getting product feature variants
     *
     * @param array  $fields    List of fields for retrieving
     * @param string $join      String with the complete JOIN information (JOIN type, tables and fields) for an SQL-query
     * @param string $condition String containing SQL-query condition possibly prepended with a logical operator (AND or OR)
     * @param string $group_by  String containing the SQL-query GROUP BY field
     * @param string $sorting   String containing the SQL-query ORDER BY clause
     * @param string $lang_code 2-letters language code
     * @param string $limit     String containing the SQL-query LIMIT clause
     */
    fn_set_hook('get_product_feature_variants', $fields, $join, $condition, $group_by, $sorting, $lang_code, $limit);

    $vars = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:product_feature_variants $join WHERE 1 $condition $group_by ORDER BY $sorting $limit", 'variant_id');

    if ($params['get_images'] == true) {
        $image_pairs = $vars
            ? fn_get_image_pairs(array_keys($vars), 'feature_variant', 'V', true, true, $lang_code)
            : array();

        foreach ($image_pairs as $variant_id => $image_pair) {
            $vars[$variant_id]['image_pair'] = array_pop($image_pair);
        }
    }

    /**
     * Changes feature variants data
     *
     * @param array  $vars      Product feature variants
     * @param array  $params    array with search params
     * @param string $lang_code 2-letters language code
     */
    fn_set_hook('get_product_feature_variants_post', $vars, $params, $lang_code);

    return array($vars, $params);
}