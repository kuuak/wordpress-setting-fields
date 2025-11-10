<?php

namespace Kuuak\WordPressSettingFields\Fields;

use Kuuak\WordPressSettingFields\Helpers\HtmlHelper;
use Kuuak\WordPressSettingFields\Helpers\UriHelper;

class Dropdown {

	public static function render($args) {

		$defaults = [
			'selected'        => [],
			'echo'            => true,
			'show_option_all' => '',
			'id'              => '',
			'required'        => false,
			'placeholder'     => false,
			'multiple'        => false,
		];

		// Parse incoming $args into an array and merge it with $defaults.
		$args = wp_parse_args($args, $defaults);

		if ($args['multiple']) self::include_select2();

		if (!is_array($args['selected'])) {
			$args['selected'] = empty($args['selected']) ? [] : [$args['selected']];
		}

		$options = [];

		if ($args['show_option_all']) {
			$options[] = sprintf(
				'<option value="0"%s>%s</option>',
				(in_array(0, $args['selected']) ? ' selected="selected"' : ''),
				$args['show_option_all']
			);
		}

		foreach ($args['options'] as $opts) {
			$options[] = sprintf(
				'<option value="%s"%s>%s</option>',
				$opts['value'],
				(in_array($opts['value'], $args['selected']) ? ' selected="selected"' : ''),
				$opts['title']
			);
		}

		$attrs = [];
		if ($args['multiple']) {
			$attrs[] = 'multiple';
			$attrs['class'] = 'wsfd-nice-ui-dropdown';
		};
		if ($args['required']) $attrs[] = 'required';
		if (!empty($args['placeholder'])) $attrs['data-placeholder'] = $args['placeholder'];

		$name = $args['name'];
		if ($args['multiple']) $name .= '[]';

		$field = sprintf(
			'<select name="%s" id="%s" %s class="regular-text">%s</select>',
			esc_attr($name),
			esc_attr($args['id'] ?: $args['name']),
			HtmlHelper::html_attrs($attrs),
			implode("\n", $options)
		);

		if (!empty($args['help'])) $field .= sprintf('<p class="description">%s</p>', $args['help']);

		if ($args['echo']) echo $field;
		else return $field;
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
