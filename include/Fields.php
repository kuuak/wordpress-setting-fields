<?php

namespace Kuuak\WordPressSettingFields;

define( 'WSFD_VERSION', '1.0.0' );

class Fields {

	public static function text($args) {
		$tple = ( (isset($args['type']) && $args['type'] === 'textarea' )
			? '<textarea id="%1$s" name="%2$s" placeholder="%3$s" style="min-width:400px;min-height: 100px;"%5$s>%4$s</textarea>%6$s'
			: sprintf('<input type="%s" id="%%1$s" name="%%2$s" value="%%3$s" %%4$s/>%%5$s', $args['type'] ?? 'text')
		);


		if ( !isset($args['attrs']) || !is_array($args['attrs']) ) $args['attrs'] = [];

		if ( isset($args['required']) && $args['required'] ) $args['attrs']['required'] = true;
		if ( isset($args['placeholder']) && !empty($args['placeholder']) ) $args['attrs']['placeholder'] = true;

		printf( $tple,
			$args['id'] ?? $args['name'],
			$args['name'],
			$args['value'] ?? '',
			self::html_attrs($args['attrs'] ?? []),
			( empty($args['help'])
				? ''
				: sprintf('<p class="description">%s</p>', $args['help'])
			)
		);
	}

	public static function dropdown( $args ) {

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
		$args = wp_parse_args( $args, $defaults );

		if ( $args['multiple'] ) self::include_select2();

		if ( !is_array($args['selected']) ) {
			$args['selected'] = empty($args['selected']) ? [] : [ $args['selected'] ];
		}

		$options = [];

		if ( $args['show_option_all'] ) {
			$options[] = sprintf( '<option value="0"%s>%s</option>',
				( in_array(0, $args['selected'] ) ? ' selected="selected"' : '' ),
				$args['show_option_all']
			);
		}

		foreach ($args['options'] as $opts) {
			$options[] = sprintf( '<option value="%s"%s>%s</option>',
				$opts['value'],
				( in_array($opts['value'], $args['selected'] ) ? ' selected="selected"' : '' ),
				$opts['title']
			);
		}

		$attrs = [];
		if ( $args['multiple'] ) {
			$attrs[] = 'multiple';
			$attrs['class'] = 'wsfd-nice-ui-dropdown';
		};
		if ( $args['required'] ) $attrs[] = 'required';
		if ( !empty($args['placeholder']) ) $attrs['data-placeholder'] = $args['placeholder'];

		$name = $args['name'];
		if ( $args['multiple'] ) $name .= '[]';

		$field = sprintf(
			'<select name="%s" id="%s" %s class="regular-text">%s</select>',
			esc_attr( $name ),
			esc_attr( $args['id'] ?: $args['name'] ),
			self::html_attrs($attrs),
			implode("\n", $options)
		);

		if ( !empty($args['help']) ) $field .= sprintf( '<p class="description">%s</p>', $args['help'] );

		if ( $args['echo'] ) echo $field;
		else return $field;
	}

	public static function switch($args) {

		$defaults = [
			'name'    => '',
			'checked' => false,
			'help'    => '',
			'nice_ui' => true,
		];

		// Parse incoming $args and merge it with $defaults.
		$args = wp_parse_args( $args, $defaults );

		if ( $args['nice_ui'] ) self::include_switch_assets();

		printf(
			'<div class="wsfd-switch"><input class="wsfd-switch__chk" type="checkbox" id="%1$s" name="%2$s" %3$s><label for="%1$s" class="wsfd-switch__label" tabindex="-1"></label></div>',
			$args['id'] ?? $args['name'],
			$args['name'],
			($args['checked']  ? 'checked' : '')
		);

		if ( !empty($args['help']) ) {
			printf( '<p class="description">%s</p>', $args['help']);
		}
	}

	/**
	 * Render a listing page setting field
	 */
	public static function pages_dropdown($args) {

		$defaults = [
			'echo'       => true,
			'required'   => false,
			'show_option_none' => __( '&mdash; Select &mdash;' ),
			'option_none_value' => 0,
		];

		$parsed_args = wp_parse_args( $args, $defaults );

		wp_dropdown_pages( $parsed_args );

		if ( isset($args['help']) ) {
			printf( '<p class="description">%s</p>', $args['help']);
		}
	}

	/**
	 * Render a category setting field
	 */
	public static function taxonomy_dropdown($args) {

		$defaults = [
			'multiple'   => false,
			'echo'       => true,
			'hide_empty' => false,
			'required'   => false,
		];

		$parsed_args = wp_parse_args( $args, $defaults );

		if ( $parsed_args['multiple'] ) {
			if ( !isset($parsed_args['show_option_all']) ) $parsed_args['show_option_all'] = __( 'All' );
			self::multi_taxonomy_dropdown( $parsed_args);
		}
		else {
			if ( !isset($parsed_args['show_option_none']) ) $parsed_args['show_option_all'] = __( 'mdash; Select &mdash;' );
			if ( !isset($parsed_args['option_none_value']) ) $parsed_args['option_none_value'] = 0;
			wp_dropdown_categories( $parsed_args );
		}

		if ( isset($args['help']) ) {
			printf( '<p class="description">%s</p>', $args['help']);
		}
	}

	public static function button( $args ) {

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

		$parsed_args = wp_parse_args( $args, $defaults );

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
			self::html_attrs($parsed_args['wrapper_attrs'])
		);

		if ( isset($args['help']) ) {
			printf( '<p class="description">%s</p>', $args['help']);
		}
	}

	/**
	 * Render a link picker field using WordPress's built-in link modal
	 */
	public static function link($args) {

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
	 * Render a dropdown with posts of the required post types.
	 */
	public static function post_type_dropdown($args) {

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

		$attrs = wp_parse_args( $args['attrs'] ?? [], $defaults['attrs'] );

		// Parse incoming $args into an array and merge it with $defaults.
		$get_args = wp_parse_args( $args['query_args'] ?? [], $defaults['query_args'] );
		$args = wp_parse_args( $args, $defaults );

		// Retrieve posts
		$get_args['fields'] = 'ids'; // force to only retrieve the ids
		$posts = get_posts( $get_args );

		if ( $args['nice_ui'] ) self::include_select2();

		if ( !is_array($args['selected']) ) {
			$args['selected'] = empty($args['selected']) ? [] : [ $args['selected'] ];
		}

		$is_multi_type = ( is_array($get_args['post_type']) && count($get_args['post_type']) > 1 );

		$options = array_map(function($post_id) use ($args, $is_multi_type) {
			$title = get_the_title($post_id);

			$attrs = [];
			if (in_array($post_id, $args['selected'] )) $attrs['selected'] = 'selected';
			if ( $is_multi_type ) $attrs['data-label'] = get_post_type($post_id);

			return sprintf(
				'<option value="%s"%s>%s</option>',
				$post_id,
				self::html_attrs($attrs),
				$title
			);
		}, $posts);

		if ( $args['show_option_all'] ) {
			array_unshift(
				$options,
				sprintf(
					'<option value="0"%s>%s</option>',
					( in_array(0, $args['selected'] ) ? ' selected="selected"' : '' ),
					$args['show_option_all']
				)
			);
		}

		if ( empty($attrs['class']) ) $attrs['class'] = [];
		else if ( !is_array($attrs['class']) ) $attrs['class'] = explode(' ', $attrs['class']);

		if ( $args['multiple'] ) {
			$attrs[] = 'multiple';
			$args['name'] .= '[]';
		}
		if ( $args['required'] ) $attrs[] = 'required';
		if ( !empty($args['placeholder']) ) {
			$attrs['data-placeholder'] = $args['placeholder'];
			if ( !$args['multiple'] )	array_unshift( $options, '<option></option>' );
		}

		if ( $args['nice_ui'] ) $attrs['class'][] = 'wsfd-nice-ui-dropdown';

		$html = sprintf(
			'<select name="%s" id="%s" %s>%s</select>',
			esc_attr( $args['name'] ),
			esc_attr( $args['id'] ? $args['id'] : $args['name'] ),
			self::html_attrs($attrs),
			implode('', $options)
		);

		if ( $args['echo'] ) echo $html;
		else return $html;
	}

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
	private static function html_attrs( $data = [] ) {

		if ( !is_array($data) || empty($data) ) return '';

		$attrs = [];
		foreach ($data as $k => $val) {

			if ( is_int($k) ) {
				$k = $val;
				$val = true;
			}

			// vars
			$attribute = "$k";

			// skip if false
			if ($val === false) continue;

			if ( is_array($val) ) $val = implode(' ', $val);

			// append value if anything but bool(true)
			if ($val !== true) $attribute .= '="'. esc_attr($val) .'"';

			// add to results
			$attrs[] = $attribute;
		}

		// return
		return implode( ' ', $attrs );
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
		$parsed_args = wp_parse_args( $args, $defaults );

		$get_terms_args = $parsed_args;
		unset( $get_terms_args['name'] );
		$categories = get_terms( $get_terms_args );

		if ( !is_array($parsed_args['selected']) ) {
			$parsed_args['selected'] = empty($parsed_args['selected']) ? [] : [ $parsed_args['selected'] ];
		}

		$options = array_map(function($cat) use ($parsed_args) {
			return sprintf(
				'<option value="%s"%s>%s</option>',
				$cat->term_id,
				( in_array($cat->term_id, $parsed_args['selected'] ) ? ' selected="selected"' : '' ),
				$cat->name
			);
		}, $categories);

		if ( $parsed_args['show_option_all'] ) {
			array_unshift(
				$options,
				sprintf(
					'<option value="0"%s>%s</option>',
					( in_array(0, $parsed_args['selected'] ) ? ' selected="selected"' : '' ),
					$parsed_args['show_option_all']
				)
			);
		}

		$attrs = [];
		if ( $parsed_args['required'] ) $attrs[] = 'required';
		if ( !empty($args['placeholder']) ) $attrs['data-placeholder'] = $args['placeholder'];

		$field = sprintf(
			'<select name="%s[]" id="%s" multiple class="wsfd-nice-ui-dropdown" %s>%s</select>',
			esc_attr( $parsed_args['name'] ),
			esc_attr( $parsed_args['id'] ? $parsed_args['id'] : $parsed_args['name'] ),
			self::html_attrs($attrs),
			implode('', $options)
		);

		if ( $parsed_args['echo'] ) {
			echo $field;
		}
		else {
			return $field;
		}
	}

	private static function get_lib_uri() {
		// This is really hacky, but it works for now
		return  trailingslashit(get_site_url()) . preg_replace("/(\/[^\/]+)$/", '', str_replace(ABSPATH, '', __DIR__) );
	}

	private static function include_switch_assets() {
		$lib_uri = self::get_lib_uri();
		wp_enqueue_style('wsfd-switch-nice-ui', untrailingslashit($lib_uri). '/src/switch-nice-ui.css', null, WSFD_VERSION );
	}

	private static function include_select2() {

		if ( wp_script_is('select2', 'enqueued') ) return;

		wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
		wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'] );

		$lib_uri = self::get_lib_uri();

		wp_enqueue_script('wsfd-dropdown-nice-ui', untrailingslashit($lib_uri). '/src/dropdown-nice-ui.js', ['select2'], WSFD_VERSION, true );
		wp_enqueue_style('wsfd-dropdown-nice-ui', untrailingslashit($lib_uri). '/src/dropdown-nice-ui.css', null, WSFD_VERSION );
	}

	private static function include_link_picker_assets() {
		if (wp_script_is('wsfd-link-picker', 'enqueued')) return;

		// Custom glue script and styles
		$lib_uri = self::get_lib_uri();
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
		add_action('admin_footer', [__CLASS__, 'output_link_modal_html'], 1);
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
}
