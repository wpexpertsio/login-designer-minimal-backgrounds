<?php
/**
 * Activation handler
 *
 * The following is a derivative work from Easy Digital Downloads.
 *
 * @package   @@pkg.name
 * @author    @@pkg.author
 * @license   @@pkg.license
 * @version   @@pkg.version
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login Designer Extension Activation Handler Class
 */
class Login_Designer_Extension_Activation {

	/**
	 * Plugin Name.
	 *
	 * @var string $plugin_name
	 */
	public $plugin_name;

	/**
	 * Plugin Path.
	 *
	 * @var string $plugin_path
	 */
	public $plugin_path;

	/**
	 * Plugin File.
	 *
	 * @var string $plugin_file
	 */
	public $plugin_file;

	/**
	 * Login Designer check.
	 *
	 * @var string $has_login_designer
	 */
	public $has_login_designer;

	/**
	 * Login Design base.
	 *
	 * @var string $login_designer_base
	 */
	public $login_designer_base;

	/**
	 * Setup the activation class.
	 *
	 * @param string|string $plugin_path Path relative to the plugin.
	 * @param string|string $plugin_file File path relative to the plugin.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $plugin_path, $plugin_file ) {

		// We need plugin.php.
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugins = get_plugins();

		// Set the plugin directory.
		$plugin_path = array_filter( explode( '/', $plugin_path ) );
		$this->plugin_path = end( $plugin_path );

		// Set the plugin file.
		$this->plugin_file = $plugin_file;

		// Set the plugin name.
		if ( isset( $plugins[ $this->plugin_path . '/' . $this->plugin_file ]['Name'] ) ) {
			$this->plugin_name = str_replace( 'Login Designer — ', '', $plugins[ $this->plugin_path . '/' . $this->plugin_file ]['Name'] );
		} else {
			$this->plugin_name = __( 'This plugin', '@@textdomain' );
		}

		// Check if Login Designer is installed.
		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( 'Login Designer' === $plugin['Name'] ) {
				$this->has_login_designer = true;
				$this->login_designer_base = $plugin_path;
				break;
			}
		}
	}

	/**
	 * Process the notice.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function run() {
		add_action( 'admin_notices', array( $this, 'missing_login_designer_notice' ) );
	}

	/**
	 * Display notice if Login Designer is not installed or activated.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function missing_login_designer_notice() {

		// Array of allowed HTML.
		$allowed_html_array = array(
			'a' => array(
				'href' => array(),
				'target' => array(),
			),
		);

		if ( $this->has_login_designer ) {
			$url  = esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $this->login_designer_base ), 'activate-plugin_' . $this->login_designer_base ) );
			$link = '<a href="' . $url . '">' . esc_html__( 'activate the plugin', '@@textdomain' ) . '</a>';
		} else {
			$url  = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=login-designer' ), 'install-plugin_login-designer' ) );
			$link = '<a href="' . $url . '">' . esc_html__( 'install the plugin', '@@textdomain' ) . '</a>';
		}

		echo '<div class="error"><p>' . esc_html( $this->plugin_name ) . wp_kses( sprintf( __( ' requires Login Designer. Please %s to continue.', '@@textdomain' ), $link ) , $allowed_html_array ) . '</p></div>';
	}
}
