<?php

namespace Kuuak\WordPressSettingFields\Fields;

use Kuuak\WordPressSettingFields\Helpers\HtmlHelper;
use Kuuak\WordPressSettingFields\Helpers\UriHelper;

class PostTypeDropdown {

	/**
	 * Render a dropdown with posts of the required post types.
	 */
	public static function render($args) {

		$defaults = [
			//  WP_Query default arguments
			'query_args' => [
				'post_type'      => ['post'],
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			],

			// Field arguments
			'selected'        => [],
			'echo'            => true,
			'show_option_all' => '',
			'name'            => 'post_type',
			'id'              => '',
			'required'        => false,
			'nice_ui'         => true,
			'multiple'        => false,
			'placeholder'     => false,
			'attrs'           => [],
		];

		$attrs = wp_parse_args($args['attrs'] ?? [], $defaults['attrs']);

		// Parse incoming $args into an array and merge it with $defaults.
		$get_args = wp_parse_args($args['query_args'] ?? [], $defaults['query_args']);
		$args = wp_parse_args($args, $defaults);

		// Retrieve posts
		$get_args['fields'] = 'ids'; // force to only retrieve the ids
		$posts = get_posts($get_args);

		if ($args['nice_ui']) self::include_select2();

		if (!is_array($args['selected'])) {
			$args['selected'] = empty($args['selected']) ? [] : [$args['selected']];
		}

		$is_multi_type = (is_array($get_args['post_type']) && count($get_args['post_type']) > 1);

		$options = array_map(function ($post_id) use ($args, $is_multi_type) {
			$title = get_the_title($post_id);

			$attrs = [];
			if (in_array($post_id, $args['selected'])) $attrs['selected'] = 'selected';
			if ($is_multi_type) $attrs['data-label'] = get_post_type($post_id);

			return sprintf(
				'<option value="%s"%s>%s</option>',
				$post_id,
				HtmlHelper::html_attrs($attrs),
				$title
			);
		}, $posts);

		if ($args['show_option_all']) {
			array_unshift(
				$options,
				sprintf(
					'<option value="0"%s>%s</option>',
					(in_array(0, $args['selected']) ? ' selected="selected"' : ''),
					$args['show_option_all']
				)
			);
		}

		if (empty($attrs['class'])) $attrs['class'] = [];
		else if (!is_array($attrs['class'])) $attrs['class'] = explode(' ', $attrs['class']);

		if ($args['multiple']) {
			$attrs[] = 'multiple';
			$args['name'] .= '[]';
		}
		if ($args['required']) $attrs[] = 'required';
		if (!empty($args['placeholder'])) {
			$attrs['data-placeholder'] = $args['placeholder'];
			if (!$args['multiple'])	array_unshift($options, '<option></option>');
		}

		if ($args['nice_ui']) $attrs['class'][] = 'wsfd-nice-ui-dropdown';

		$html = sprintf(
			'<select name="%s" id="%s" %s>%s</select>',
			esc_attr($args['name']),
			esc_attr($args['id'] ? $args['id'] : $args['name']),
			HtmlHelper::html_attrs($attrs),
			implode('', $options)
		);

		if ($args['echo']) echo $html;
		else return $html;
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
