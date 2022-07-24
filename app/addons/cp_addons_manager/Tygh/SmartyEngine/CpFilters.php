<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

namespace Tygh\SmartyEngine;

use Tygh\Registry;
use Tygh\Languages\Values as LanguageValues;

class CpFilters
{
    public static function preFormTooltip($content, \Smarty_Internal_Template $template)
    {
        if (strpos(Registry::get('runtime.controller'), 'cp_') !== 0
            || !version_compare(PRODUCT_VERSION, '4.11.5', '>')
        ) {
            return $content;
        }
        
        $pattern = '/\<label[^>]*\>.*(\{__\("([^\}]+)"\)\}[^:\<]*).*\<\/label\>/';
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[0] as $k => $label_content) {
                if (empty($matches[1][$k]) || empty($matches[2][$k])) {
                    continue;
                }
                $label = $matches[1][$k];
                $field_name = $matches[2][$k];
                if (strpos($field_name, 'cp_') !== 0
                    || strpos($label_content, 'tooltip.tpl') !== false
                    || strpos($label_content, 'cm-tooltip') !== false
                ) {
                    continue;
                }
                preg_match("/(^[a-zA-z0-9][\.a-zA-Z0-9_]*)/", $field_name, $name_matches);
                if (@strlen($name_matches[0]) != strlen($field_name)) {
                    continue;
                }
                $common_lang_var = 'ttc_' . $field_name;

                $tooltip = __($common_lang_var);
                if (empty($tooltip) || $tooltip == '_' . $common_lang_var) {
                    continue;
                }
                $tooltip_text = '__("' . $common_lang_var . '")';
                
                if (defined('CP_AM_TTC_OLD_FORMAT')) {
                    $tooltip = '{include file="common/tooltip.tpl" tooltip=' . $tooltip_text . '}';
                    $tooltip_added = str_replace($label, $label . $tooltip, $label_content);
                } else {
                    $tooltip = '<div class="muted description">{' . $tooltip_text . '}</div>';
                    $tooltip_added = preg_replace('/' . preg_quote($label) . '(\s*:)/', $label . ': ' . $tooltip, $label_content);
                }
                $content = str_replace($label_content, $tooltip_added, $content);
            }
        }

        return $content;
    }
}
