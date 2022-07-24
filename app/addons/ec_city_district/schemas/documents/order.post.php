<?php

if (AREA == 'A'){
    $schema['user']['data']=  function (\Tygh\Template\Document\Order\Context $context) {
        $user_data = $context->getOrder()->getUser();
        if (empty($user_data['s_city_descr'])){
            $user_data =  fn_ec_get_city_district_descr($user_data);
        }
        return $user_data;
    };
    $schema['user']['attributes'] = function () {
        $attributes = ['email', 'firstname', 'lastname', 'phone'];
        $group_fields = fn_get_profile_fields('I');
        $sections = ['C', 'B', 'S'];
        foreach ($sections as $section) {
            if (isset($group_fields[$section])) {
                foreach ($group_fields[$section] as $field) {
                    if (!empty($field['field_name'])) {
                        $attributes[] = $field['field_name'];

                        if (in_array($field['field_type'], ['A', 'O', 'L', 'X'])) {
                            $attributes[] = $field['field_name'] . '_descr';
                        }
                    }
                }
            }

            $attributes[strtolower($section) . '_fields']['[0..N]'] = [
                'name',
                'value',
            ];
        }
        return $attributes;
    };
}
return $schema;