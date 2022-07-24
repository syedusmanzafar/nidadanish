<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }


function smarty_modifier_rf_render_tag_attrs($attributes)
{
	$attributes = (array) $attributes;
	$result = [];

	foreach ($attributes as $name => $value) {
		if (is_bool($value)) {
			if ($value) {
				$result[] = $name;
			}
			continue;
		} elseif (is_array($value)) {
			$value = json_encode($value);
		}

		$result[] = sprintf('%s="%s"', $name, htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE));
	}

	return implode(' ', $result);
}