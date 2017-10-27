<?php
/**
 * Admin notices.
 *
 * @package     @@pkg.name
 * @link        @@pkg.plugin_uri
 * @author      @@pkg.author
 * @copyright   @@pkg.copyright
 * @license     @@pkg.license
 * @version     @@pkg.version
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login_Designer_Minimal_Backgrounds_Notice Class
 */
class Login_Designer_Minimal_Backgrounds_Notice {

	/**
	 * Constructor
	 */
	public function __construct() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Check to see if the Login Designer plugin is installed.
		$login_designer_active = is_plugin_active( 'login-designer/login-designer.php' );

		if ( ! $login_designer_active ) {
			add_action( 'admin_notices', array( $this, 'double_install_admin_notice' ) );
			add_action( 'network_admin_notices', array( $this, 'double_install_admin_notice' ) );
			return;
		}
	}

	/**
	 * Renders an admin notice.
	 *
	 * @access private
	 * @param string|string $message The notice content.
	 * @param string|string $type The type of admin notice.
	 * @param string|string $dismissable Is this dismissable.
	 * @return void
	 */
	static private function render_admin_notice( $message, $type = 'update', $dismissable = false ) {

		if ( ! is_admin() ) {
			return;
		} else if ( ! is_user_logged_in() ) {
			return;
		} else if ( ! current_user_can( 'update_core' ) ) {
			return;
		}

		$dismissable = ( false === $dismissable ) ? null : ' is-dismissible';

		$allowed_html_array = array(
			'a' => array(
				'href' => array(),
			),
		);

		echo '<div class="notice ' . esc_attr( $type . $dismissable ) . '">';
			echo '<p>' . wp_kses( $message, $allowed_html_array ) . '</p>';
		echo '</div>';
	}

	/**
	 * Shows an admin notice if another portfolio post type plugin installed after-the-fact.
	 *
	 * @return void
	 */
	public function double_install_admin_notice() {

		global $pagenow;
		if ( 'plugins.php' !== $pagenow ) {
			return;
		}

		$message = __( 'Please install and activate <a href="%s" target="_blank">Login Designer</a> to use any Login Designer extensions.', '@@textdomain' );

		$this->render_admin_notice( sprintf( $message, esc_url( 'https://logindesigner.com' ) ), 'notice-warning', true );
	}
}

return new Login_Designer_Minimal_Backgrounds_Notice();
