<?php

namespace Kuuak\WordPressSettingFields\Fields;

use Kuuak\WordPressSettingFields\Helpers\HtmlHelper;

class Text {

	public static function render($args) {
		$tple = ((isset($args['type']) && $args['type'] === 'textarea')
			? '<textarea id="%1$s" name="%2$s" placeholder="%3$s" style="min-width:400px;min-height: 100px;"%5$s>%4$s</textarea>%6$s'
			: sprintf('<input type="%s" id="%%1$s" name="%%2$s" value="%%3$s" %%4$s/>%%5$s', $args['type'] ?? 'text')
		);


		if (!isset($args['attrs']) || !is_array($args['attrs'])) $args['attrs'] = [];

		if (isset($args['required']) && $args['required']) $args['attrs']['required'] = true;
		if (isset($args['placeholder']) && !empty($args['placeholder'])) $args['attrs']['placeholder'] = true;

		printf(
			$tple,
			$args['id'] ?? $args['name'],
			$args['name'],
			$args['value'] ?? '',
			HtmlHelper::html_attrs($args['attrs'] ?? []),
			(empty($args['help'])
				? ''
				: sprintf('<p class="description">%s</p>', $args['help'])
			)
		);
	}
}
