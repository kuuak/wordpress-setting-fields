<?php

namespace Kuuak\WordPressSettingFields\Fields;

class PagesDropdown {

	/**
	 * Render a listing page setting field
	 */
	public static function render($args) {

		$defaults = [
			'echo'       => true,
			'required'   => false,
			'show_option_none' => __('&mdash; Select &mdash;'),
			'option_none_value' => 0,
		];

		$parsed_args = wp_parse_args($args, $defaults);

		wp_dropdown_pages($parsed_args);

		if (isset($args['help'])) {
			printf('<p class="description">%s</p>', $args['help']);
		}
	}
}
