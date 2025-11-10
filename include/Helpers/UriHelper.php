<?php

namespace Kuuak\WordPressSettingFields\Helpers;

if (!defined('WSFD_VERSION')) {
	define('WSFD_VERSION', '1.0.0');
}

class UriHelper {

	/**
	 * Get the library URI for asset enqueuing
	 *
	 * @return string Library URI
	 */
	public static function get_lib_uri() {
		// This is really hacky, but it works for now
		// Get the include directory (parent of Helpers)
		$include_dir = dirname(__DIR__);
		return  trailingslashit(get_site_url()) . preg_replace("/(\/[^\/]+)$/", '', str_replace(ABSPATH, '', $include_dir));
	}
}
