<?php

namespace Kuuak\WordPressSettingFields;

// Ensure WSFD_VERSION is defined (for backward compatibility)
if (!defined('WSFD_VERSION')) {
	require_once __DIR__ . '/Helpers/UriHelper.php';
}

use Kuuak\WordPressSettingFields\Fields\Text;
use Kuuak\WordPressSettingFields\Fields\Dropdown;
use Kuuak\WordPressSettingFields\Fields\SwitchField;
use Kuuak\WordPressSettingFields\Fields\PagesDropdown;
use Kuuak\WordPressSettingFields\Fields\TaxonomyDropdown;
use Kuuak\WordPressSettingFields\Fields\Button;
use Kuuak\WordPressSettingFields\Fields\Link;
use Kuuak\WordPressSettingFields\Fields\PostTypeDropdown;

class Fields {

	public static function text($args) {
		Text::render($args);
	}

	public static function dropdown($args) {
		return Dropdown::render($args);
	}

	public static function switch($args) {
		SwitchField::render($args);
	}

	/**
	 * Render a listing page setting field
	 */
	public static function pages_dropdown($args) {
		PagesDropdown::render($args);
	}

	/**
	 * Render a category setting field
	 */
	public static function taxonomy_dropdown($args) {
		TaxonomyDropdown::render($args);
	}

	public static function button($args) {
		Button::render($args);
	}

	/**
	 * Render a link picker field using WordPress's built-in link modal
	 */
	public static function link($args) {
		Link::render($args);
	}

	/**
	 * Render a dropdown with posts of the required post types.
	 */
	public static function post_type_dropdown($args) {
		return PostTypeDropdown::render($args);
	}
}
