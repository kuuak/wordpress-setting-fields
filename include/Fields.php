<?php

namespace Kuuak\WordPressSettingFields;

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

		if ( isset($args['multiple']) && $args['multiple'] ) {
			wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
			wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'] );

			add_filter('sctx-admin-script-deps', function($deps) {
				$deps[] = 'select2';
				return $deps;
			});
		}

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

		if ( !is_array($args['selected']) ) $args['selected'] = [];

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
		if ( $args['multiple'] ) $attrs[] = 'multiple data-multidropdown';
		if ( $args['required'] ) $attrs[] = 'required';
		if ( !empty($args['placeholder']) ) $attrs[] = 'data-placeholder="'.esc_attr($args['placeholder']) .'"';

		$name = $args['name'];
		if ( $args['multiple'] ) $name .= '[]';

		$field = sprintf(
			'<select name="%s" id="%s" %s class="regular-text">%s</select>',
			esc_attr( $name ),
			esc_attr( $args['id'] ?: $args['name'] ),
			implode(' ', $attrs),
			implode("\n", $options)
		);

		if ( !empty($args['help']) ) $field .= sprintf( '<p class="description">%s</p>', $args['help'] );

		if ( $args['echo'] ) echo $field;
		else return $field;
	}

	public static function switch($args) {
		printf(
			'<div class="switch"><input class="switch__chk" type="checkbox" id="%1$s" name="%2$s" %3$s><label for="%1$s" class="switch__label" tabindex="-1"></label></div>',
			$args['id'] ?? $args['name'],
			$args['name'],
			($args['checked']  ? 'checked' : '')
		);

		if ( isset($args['help']) ) {
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
			// vars
			$attribute = "$k";

			// skip if false
			if ($val === false) continue;

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
		];

		// Parse incoming $args into an array and merge it with $defaults.
		$parsed_args = wp_parse_args( $args, $defaults );

		$get_terms_args = $parsed_args;
		unset( $get_terms_args['name'] );
		$categories = get_terms( $get_terms_args );

		if ( !is_array($parsed_args['selected']) ) $parsed_args['selected'] = [];

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

		$field = sprintf(
			'<select name="%s[]" id="%s" multiple data-multi-select%s>%s</select>',
			esc_attr( $parsed_args['name'] ),
			esc_attr( $parsed_args['id'] ? $parsed_args['id'] : $parsed_args['name'] ),
			( $parsed_args['required'] ? 'required' : '' ),
			implode('', $options)
		);

		if ( $parsed_args['echo'] ) {
			echo $field;
		}
		else {
			return $field;
		}
	}

}
