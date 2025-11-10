<?php

namespace Kuuak\WordPressSettingFields\Fields;

use Kuuak\WordPressSettingFields\Helpers\UriHelper;

class Link {

	/**
	 * Render a link picker field using WordPress's built-in link modal
	 */
	public static function render($args) {

		$defaults = [
			'name'     => '',
			'id'       => '',
			'value'    => [
				'url'    => '',
				'text'   => '',
				'target' => '',
			],
			'help'     => '',
			'required' => false,
		];

		$args = wp_parse_args($args, $defaults);

		// Ensure value is an array
		if (!is_array($args['value'])) {
			$args['value'] = [
				'url'    => $args['value'] ?? '',
				'text'   => '',
				'target' => '',
			];
		}

		$field_id  = $args['id'] ?: $args['name'];
		$url_id    = $field_id . '_url';
		$text_id   = $field_id . '_text';
		$target_id = $field_id . '_target';
		$picker_id = $field_id . '_picker';

		$has_link    = !empty($args['value']['url']);
		$link_text   = $args['value']['text'] ?? '';
		$link_url    = $args['value']['url'] ?? '';
		$link_target = $args['value']['target'] ?? '';

		// Enqueue WordPress link picker assets
		self::include_link_picker_assets();

		// Output the field HTML
		printf(
			'<div class="wsfd-link-field" data-field-id="%1$s">' .
				'<input id="%2$s" name="%3$s[url]" type="hidden" value="%4$s"/>' .
				'<input id="%5$s" name="%3$s[text]" type="hidden" value="%6$s"/>' .
				'<input id="%7$s" name="%3$s[target]" type="hidden" value="%8$s"/>' .
				'<div class="wsfd-link-field__button-wrapper"%9$s>' .
				'<button type="button" class="button" id="%10$s">%11$s</button>' .
				'</div>' .
				'<div class="wsfd-link-field__link-block"%12$s>' .
				'<span class="wsfd-link-field__link-text">%13$s</span>' .
				'<span class="wsfd-link-field__link-url" target="%15$s" rel="noopener noreferrer">%16$s</span>' .
				'<div class="wsfd-link-field__actions">' .
				'<button type="button" class="wsfd-link-field__action -external" title="%17$s" aria-label="%17$s">' .
				'<span class="dashicons dashicons-external"></span>' .
				'</button>' .
				'<button type="button" class="wsfd-link-field__action -edit" title="%18$s" aria-label="%18$s">' .
				'<span class="dashicons dashicons-edit"></span>' .
				'</button>' .
				'<button type="button" class="wsfd-link-field__action -remove" title="%19$s" aria-label="%19$s">' .
				'<span class="dashicons dashicons-no-alt"></span>' .
				'</button>' .
				'</div>' .
				'</div>' .
				'</div>',
			esc_attr($field_id),
			esc_attr($url_id),
			esc_attr($args['name']),
			esc_attr($link_url),
			esc_attr($text_id),
			esc_attr($link_text),
			esc_attr($target_id),
			esc_attr($link_target),
			$has_link ? ' style="display:none;"' : '',
			esc_attr($picker_id),
			esc_html__('Select / Insert link…', 'wordpress-setting-fields'),
			$has_link ? '' : ' style="display:none;"',
			esc_html($link_text ?: $link_url),
			esc_url($link_url),
			esc_attr($link_target === '_blank' ? '_blank' : '_self'),
			esc_html($link_url),
			esc_attr__('Open link', 'wordpress-setting-fields'),
			esc_attr__('Edit link', 'wordpress-setting-fields'),
			esc_attr__('Remove link', 'wordpress-setting-fields')
		);

		if (!empty($args['help'])) {
			printf('<p class="description">%s</p>', $args['help']);
		}
	}

	/**
	 * Output the WordPress link modal HTML structure
	 * Following ACF's approach: output a hidden wp_editor() instance to ensure
	 * all editor dependencies (including link modal) are properly initialized.
	 *
	 * @see https://github.com/AdvancedCustomFields/acf/blob/796a2fdd3ed8695da3d862de6b0138fb822c8b43/includes/assets.php#L539-L556
	 */
	public static function output_link_modal_html() {
		// Only output once per page load
		static $output_done = false;
		if ($output_done) {
			return;
		}
		$output_done = true;

		// Output a hidden wp_editor instance (like ACF does)
		// This ensures all editor dependencies are properly initialized
		// including the link modal HTML and all necessary scripts
?>
		<div id="wsfd-hidden-wp-editor" style="display: none;">
			<?php wp_editor('', 'wsfd_content'); ?>
		</div>
<?php
	}

	/**
	 * Enqueue link picker assets
	 */
	private static function include_link_picker_assets() {
		if (wp_script_is('wsfd-link-picker', 'enqueued')) return;

		// Custom glue script and styles
		$lib_uri = UriHelper::get_lib_uri();
		wp_enqueue_script(
			'wsfd-link-picker',
			untrailingslashit($lib_uri) . '/src/link-picker.js',
			['jquery', 'wplink'],
			WSFD_VERSION,
			true
		);
		wp_enqueue_style(
			'wsfd-link-picker',
			untrailingslashit($lib_uri) . '/src/link-picker.css',
			null,
			WSFD_VERSION
		);

		// Output a hidden wp_editor instance in admin footer
		// This ensures all editor dependencies (including link modal) are properly initialized
		// Following ACF's approach: https://github.com/AdvancedCustomFields/acf/blob/796a2fdd3ed8695da3d862de6b0138fb822c8b43/includes/assets.php#L539-L556
		// wp_editor() will automatically enqueue wplink, wp-jquery-ui-dialog, jquery-ui-dialog, editor-buttons, etc.
		add_action('admin_footer', [self::class, 'output_link_modal_html'], 1);
	}
}
