<?php

namespace Kuuak\WordPressSettingFields\Fields;

use Kuuak\WordPressSettingFields\Helpers\UriHelper;

class SwitchField {

	public static function render($args) {

		$defaults = [
			'name'    => '',
			'checked' => false,
			'help'    => '',
			'nice_ui' => true,
		];

		// Parse incoming $args and merge it with $defaults.
		$args = wp_parse_args($args, $defaults);

		if ($args['nice_ui']) self::include_switch_assets();

		printf(
			'<div class="wsfd-switch"><input class="wsfd-switch__chk" type="checkbox" id="%1$s" name="%2$s" %3$s><label for="%1$s" class="wsfd-switch__label" tabindex="-1"></label></div>',
			$args['id'] ?? $args['name'],
			$args['name'],
			($args['checked']  ? 'checked' : '')
		);

		if (!empty($args['help'])) {
			printf('<p class="description">%s</p>', $args['help']);
		}
	}

	/**
	 * Enqueue switch nice UI assets
	 */
	private static function include_switch_assets() {
		$lib_uri = UriHelper::get_lib_uri();
		wp_enqueue_style('wsfd-switch-nice-ui', untrailingslashit($lib_uri) . '/src/switch-nice-ui.css', null, WSFD_VERSION);
	}
}
