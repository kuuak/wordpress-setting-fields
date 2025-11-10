<?php

namespace Kuuak\WordPressSettingFields\Fields;

use Kuuak\WordPressSettingFields\Helpers\HtmlHelper;
use Kuuak\WordPressSettingFields\Helpers\UriHelper;

class TaxonomyDropdown {

	/**
	 * Render a category setting field
	 */
	public static function render($args) {

		$defaults = [
			'multiple'   => false,
			'echo'       => true,
			'hide_empty' => false,
			'required'   => false,
		];

		$parsed_args = wp_parse_args($args, $defaults);

		if ($parsed_args['multiple']) {
			if (!isset($parsed_args['show_option_all'])) $parsed_args['show_option_all'] = __('All');
			self::multi_taxonomy_dropdown($parsed_args);
		} else {
			if (!isset($parsed_args['show_option_none'])) $parsed_args['show_option_all'] = __('mdash; Select &mdash;');
			if (!isset($parsed_args['option_none_value'])) $parsed_args['option_none_value'] = 0;
			wp_dropdown_categories($parsed_args);
		}

		if (isset($args['help'])) {
			printf('<p class="description">%s</p>', $args['help']);
		}
	}

	private static function multi_taxonomy_dropdown($args) {

		$defaults = [
			//  WP_Term_Query default arguments
			'taxonomy'          => 'category',
			'hierarchical'      => false,

			// Field arguments
			'selected'          => [],
			'echo'              => true,
			'show_option_all'   => '',
			'name'              => 'cat',
			'id'                => '',
			'required'          => false,
			'placeholder'       => false,
		];

		// Parse incoming $args into an array and merge it with $defaults.
		$parsed_args = wp_parse_args($args, $defaults);

		$get_terms_args = $parsed_args;
		unset($get_terms_args['name']);
		$categories = get_terms($get_terms_args);

		if (!is_array($parsed_args['selected'])) {
			$parsed_args['selected'] = empty($parsed_args['selected']) ? [] : [$parsed_args['selected']];
		}

		$options = array_map(function ($cat) use ($parsed_args) {
			return sprintf(
				'<option value="%s"%s>%s</option>',
				$cat->term_id,
				(in_array($cat->term_id, $parsed_args['selected']) ? ' selected="selected"' : ''),
				$cat->name
			);
		}, $categories);

		if ($parsed_args['show_option_all']) {
			array_unshift(
				$options,
				sprintf(
					'<option value="0"%s>%s</option>',
					(in_array(0, $parsed_args['selected']) ? ' selected="selected"' : ''),
					$parsed_args['show_option_all']
				)
			);
		}

		$attrs = [];
		if ($parsed_args['required']) $attrs[] = 'required';
		if (!empty($args['placeholder'])) $attrs['data-placeholder'] = $args['placeholder'];

		self::include_select2();

		$field = sprintf(
			'<select name="%s[]" id="%s" multiple class="wsfd-nice-ui-dropdown" %s>%s</select>',
			esc_attr($parsed_args['name']),
			esc_attr($parsed_args['id'] ? $parsed_args['id'] : $parsed_args['name']),
			HtmlHelper::html_attrs($attrs),
			implode('', $options)
		);

		if ($parsed_args['echo']) {
			echo $field;
		} else {
			return $field;
		}
	}

	/**
	 * Enqueue Select2 assets and custom dropdown UI
	 */
	private static function include_select2() {

		if (wp_script_is('select2', 'enqueued')) return;

		wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
		wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery']);

		$lib_uri = UriHelper::get_lib_uri();

		wp_enqueue_script('wsfd-dropdown-nice-ui', untrailingslashit($lib_uri) . '/src/dropdown-nice-ui.js', ['select2'], WSFD_VERSION, true);
		wp_enqueue_style('wsfd-dropdown-nice-ui', untrailingslashit($lib_uri) . '/src/dropdown-nice-ui.css', null, WSFD_VERSION);
	}
}
