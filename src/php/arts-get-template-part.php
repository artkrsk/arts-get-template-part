<?php
/**
 * Global template part loader with theme override support.
 *
 * @package Arts\GetTemplatePart
 */

// No ABSPATH guard: this file is composer files-autoloaded, so an `exit` here silently
// kills any CLI process that includes vendor/autoload.php (Strauss, PHPStan, Composer
// scripts). It only defines a guarded function — direct access yields no output anyway.
if ( ! function_exists( 'arts_get_template_part' ) ) {
	/**
	 * Load a template part with theme override support.
	 *
	 * Search order:
	 * 1. Child theme: {stylesheet_dir}/{theme_subdir}/{slug}.php
	 * 2. Parent theme: {template_dir}/{theme_subdir}/{slug}.php
	 * 3. Plugin default: {default_path}/{slug}.php
	 *
	 * @param string               $slug         Template slug (e.g., 'Media/Media').
	 * @param array<string, mixed> $args         Variables available to the template.
	 * @param string               $default_path Absolute path to plugin's templates directory.
	 * @param string               $theme_subdir Subdirectory inside theme for overrides.
	 */
	function arts_get_template_part(
		string $slug,
		array $args = array(),
		string $default_path = '',
		string $theme_subdir = 'arts'
	): void {
		$template_name = $slug . '.php';

		// Child theme → parent theme (via WP's locate_template)
		$template = locate_template(
			trailingslashit( $theme_subdir ) . $template_name
		);

		// Plugin's own templates directory
		if ( ! $template && $default_path ) {
			$fallback = trailingslashit( $default_path ) . $template_name;
			if ( file_exists( $fallback ) ) {
				$template = $fallback;
			}
		}

		if ( $template ) {
			include $template;
		}
	}
}
