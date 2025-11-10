<?php

namespace Kuuak\WordPressSettingFields\Fields;

use Kuuak\WordPressSettingFields\Helpers\HtmlHelper;

class Button {

	public static function render($args) {

		$defaults = [
			'id'      => null,
			'name'    => '',
			'label'   => '',
			'variant' => 'secondary',
			'action'  => [
				'name'  => '',
				'value' => '',
			],
			'wrapper_attrs' => [],
		];

		$parsed_args = wp_parse_args($args, $defaults);

		printf(
			'<div %7$s>' .
				'<button id="%1$s" name="%2$s" class="button button-%4$s">%3$s</button>' .
				'<input type="hidden" name="%5$s" value="%6$s" disabled/>' .
				'</div>',
			$parsed_args['id'] ?? $parsed_args['name'],
			$parsed_args['name'],
			$parsed_args['label'],
			$parsed_args['variant'],
			$parsed_args['action']['name'],
			$parsed_args['action']['value'],
			HtmlHelper::html_attrs($parsed_args['wrapper_attrs'])
		);

		if (isset($args['help'])) {
			printf('<p class="description">%s</p>', $args['help']);
		}
	}
}
