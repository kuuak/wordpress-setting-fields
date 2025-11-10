<?php

namespace Kuuak\WordPressSettingFields\Helpers;

class HtmlHelper {

	/**
	 * Transform an associative array into a string
	 * to be used as HTML attributes.
	 *
	 * @param array $data Associative array.
	 *                    i.e: `[ 'attr' => true, 'attr2' => 'value1', 'attr3' => false, 'attr4' => 0 ]`
	 *
	 * @return string HTML attribute string.
	 *                i.e: `attr attr2="value1" attr4="0"`
	 */
	public static function html_attrs($data = []) {

		if (!is_array($data) || empty($data)) return '';

		$attrs = [];
		foreach ($data as $k => $val) {

			if (is_int($k)) {
				$k = $val;
				$val = true;
			}

			// vars
			$attribute = "$k";

			// skip if false
			if ($val === false) continue;

			if (is_array($val)) $val = implode(' ', $val);

			// append value if anything but bool(true)
			if ($val !== true) $attribute .= '="' . esc_attr($val) . '"';

			// add to results
			$attrs[] = $attribute;
		}

		// return
		return implode(' ', $attrs);
	}
}
