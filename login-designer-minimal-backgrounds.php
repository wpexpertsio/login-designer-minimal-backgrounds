<?php
/**
 * Plugin Name: Login Designer — Minimal Backgrounds
 * Plugin URI: @@pkg.plugin_uri
 * Description: @@pkg.description
 * Author: @@pkg.author
 * Author URI: @@pkg.author_uri
 * Version: @@pkg.version
 * Text Domain: @@textdomain
 * Domain Path: languages
 * Requires at least: @@pkg.requires
 * Tested up to: @@pkg.tested_up_to
 *
 * @@pkg.name is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * @@pkg.name is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with @@pkg.name. If not, see <http://www.gnu.org/licenses/>.
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

if ( ! class_exists( 'Login_Designer_Minimal_Backgrounds' ) ) :

	/**
	 * Main Login Designer Minimal Backgrounds Extension Class.
	 *
	 * @since 1.0.0
	 */
	final class Login_Designer_Minimal_Backgrounds {
		/** Singleton *************************************************************/

		/**
		 * Login_Designer_Minimal_Backgrounds The one true Login_Designer_Minimal_Backgrounds
		 *
		 * @var string $instance
		 */
		private static $instance;

		/**
		 * Plugin Version
		 *
		 * @var string $version
		 */
		private static $version = '@@pkg.version';

		/**
		 * Plugin Download ID
		 *
		 * @var string $id
		 */
		private static $id = '@@pkg.downloadid';

		/**
		 * Main Login_Designer_Minimal_Backgrounds Instance.
		 *
		 * Insures that only one instance of Login_Designer_Minimal_Backgrounds exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static
		 * @staticvar array $instance
		 * @uses Login_Designer_Minimal_Backgrounds::setup_constants() Setup the constants needed.
		 * @uses Login_Designer_Minimal_Backgrounds::load_textdomain() load the language files.
		 * @see LOGIN_DESIGNER_MINIMAL_BACKGROUNDS()
		 * @return object|Login_Designer_Minimal_Backgrounds The one true Login_Designer_Minimal_Backgrounds
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Login_Designer_Minimal_Backgrounds ) ) {
				self::$instance = new Login_Designer_Minimal_Backgrounds();
				self::$instance->constants();
				self::$instance->actions();
				self::$instance->load_textdomain();
			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', '@@textdomain' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', '@@textdomain' ), '1.0' );
		}

		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @return void
		 */
		private function constants() {
			$this->define( 'LOGIN_DESIGNER_MINIMAL_BACKGROUNDS_VERSION', '@@pkg.version' );
			$this->define( 'LOGIN_DESIGNER_MINIMAL_BACKGROUNDS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'LOGIN_DESIGNER_MINIMAL_BACKGROUNDS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'LOGIN_DESIGNER_MINIMAL_BACKGROUNDS_PLUGIN_FILE', __FILE__ );
			$this->define( 'LOGIN_DESIGNER_MINIMAL_BACKGROUNDS_ABSPATH', dirname( __FILE__ ) . '/' );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string|string $name Name of the definition.
		 * @param  string|bool   $value Default value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Load the actions & filters.
		 *
		 * @return void
		 */
		public function actions() {
			// Actions.
			add_action( 'admin_init', array( $this, 'updater' ) );
			add_action( 'login_enqueue_scripts', array( $this, 'customizer_css' ) );

			// Filters.
			add_filter( 'login_designer_backgrounds', array( $this, 'minimal_backgrounds' ) );
			add_filter( 'login_designer_extension_background_options', array( $this, 'extended_backgrounds_array' ) );
			add_filter( 'login_designer_extension_color_options', array( $this, 'extended_bg_colors_array' ) );
			add_filter( 'login_designer_control_localization', array( $this, 'control_localization' ) );
		}

		/**
		 * Adds the minimal background images to the custom gallery Customizer control.
		 *
		 * @param  array $backgrounds Default backgrounds from the Login Designer plugin.
		 * @return array of default fonts, plus the new typekit additions.
		 */
		public function minimal_backgrounds( $backgrounds ) {

			$image_dir = LOGIN_DESIGNER_MINIMAL_BACKGROUNDS_PLUGIN_URL . 'assets/images/';

			// Change the "winter-01" key and leave the background images in the plugin folder (at least for month or so).
			$minimal_backgrounds = array(
				'minimal-01' => array(
					'title' => esc_html__( 'Minimal 01', '@@textdomain' ),
					'image' => esc_url( $image_dir ) . 'minimal-01-sml.jpg',
				),
				'minimal-02' => array(
					'title' => esc_html__( 'Minimal 02', '@@textdomain' ),
					'image' => esc_url( $image_dir ) . 'minimal-02-sml.jpg',
				),
				'minimal-03' => array(
					'title' => esc_html__( 'Minimal 03', '@@textdomain' ),
					'image' => esc_url( $image_dir ) . 'minimal-03-sml.jpg',
				),
				'minimal-04' => array(
					'title' => esc_html__( 'Minimal 04', '@@textdomain' ),
					'image' => esc_url( $image_dir ) . 'minimal-04-sml.jpg',
				),
				'minimal-05' => array(
					'title' => esc_html__( 'Minimal 05', '@@textdomain' ),
					'image' => esc_url( $image_dir ) . 'minimal-05-sml.jpg',
				),
			);

			// Combine the two arrays.
			$backgrounds = array_merge( $backgrounds, $minimal_backgrounds );

			return $backgrounds;
		}

		/**
		 * Option titles.
		 *
		 * @return array of default fonts, plus the new typekit additions.
		 */
		public function options() {

			// Change the colors whenever needed.
			$options = array(
				'minimal_option_01' => 'minimal-01',
				'minimal_option_02' => 'minimal-02',
				'minimal_option_03' => 'minimal-03',
				'minimal_option_04' => 'minimal-04',
				'minimal_option_05' => 'minimal-05',
			);

			return $options;
		}

		/**
		 * Colors.
		 *
		 * @return array of default fonts, plus the new typekit additions.
		 */
		public function colors() {

			// Change the colors whenever needed.
			$options = array(
				'minimal-01' => '#ff0000',
				'minimal-02' => '#333333',
				'minimal-03' => '#444444',
				'minimal-04' => '#ddd333',
				'minimal-05' => '#ffffff',
			);

			return $options;
		}

		/**
		 * Filters currrent backgrounds options and adds new backgrounds.
		 *
		 * @param  array $backgrounds Current backgrounds.
		 * @return array of default fonts, plus the new typekit additions.
		 */
		public function extended_backgrounds_array( $backgrounds ) {

			// Get the option values.
			$options = $this->options();

			// Combine the two arrays.
			$backgrounds = array_merge( $backgrounds, $options );

			return $backgrounds;
		}

		/**
		 * Filters currrent backgrounds options and adds new backgrounds.
		 *
		 * @param array|array $colors Current colors.
		 * @return array of default fonts, plus the new typekit additions.
		 */
		public function extended_bg_colors_array( $colors ) {

			// Get the color values.
			$extension_colors = $this->colors();

			// Combine the two arrays.
			$colors = array_merge( $colors, $extension_colors );

			return $colors;
		}

		/**
		 * Adds corresponding minimal option titles and background colors for the controls javascript file.
		 *
		 * @param  array $localize Default control localization.
		 * @return array of default fonts, plus the new typekit additions.
		 */
		public function control_localization( $localize ) {

			// Get the option values.
			$options = $this->options();

			// Change the colors whenever needed.
			$colors = array(
				'minimal_bg_color_01' => '#ffffff',
				'minimal_bg_color_02' => '#fff000',
				'minimal_bg_color_03' => '#fff333',
				'minimal_bg_color_04' => '#333fff',
				'minimal_bg_color_05' => '#fff222',
			);

			// Combine the three arrays.
			$localize = array_merge( $localize, $options, $colors );

			return $localize;
		}

		/**
		 * Enqueue the stylesheets required.
		 *
		 * @access public
		 */
		public function customizer_css() {

			// Get the options.
			$options = get_option( 'login_designer' );

			// Start CSS Variable.
			$css = '';

			if ( ! empty( $options ) ) :

				// Background image gallery. Only display if there's no custom background image.
				if ( isset( $options['bg_image_gallery'] ) && 'none' !== $options['bg_image_gallery'] && empty( $options['bg_image'] ) ) {

					$extension_backgrounds = null;

					// Check first if one of this extension's background is selected.
					if ( in_array( $options['bg_image_gallery'], $this->options(), true ) ) {

						$image_dir = LOGIN_DESIGNER_MINIMAL_BACKGROUNDS_PLUGIN_URL . 'assets/images/';

						// Get the image's url.
						$url = $image_dir . $options['bg_image_gallery'] . '.jpg';

						$css .= 'body.login, #login-designer-background { background-image: url(" ' . esc_url( $url ) . ' "); }';
					}
				}

				// Combine the values from above and minifiy them.
				$css = preg_replace( '#/\*.*?\*/#s', '', $css );
				$css = preg_replace( '/\s*([{}|:;,])\s+/', '$1', $css );
				$css = preg_replace( '/\s\s+(.*)/', '$1', $css );

				// Add inline style.
				wp_add_inline_style( 'login', wp_strip_all_tags( $css ) );

			endif;
		}

		/**
		 * Custom plugin updater.
		 *
		 * @access private
		 * @return void
		 */
		public function updater() {

			if ( ! is_admin() ) {
				return;
			}

			// Check for required classes.
			if ( ! class_exists( 'Login_Designer_License_Handler' ) || ! class_exists( 'Login_Designer_Extension_Updater' ) ) {
				return;
			}

			// Retrieve license information.
			$handler  = new Login_Designer_License_Handler();
			$key      = trim( $handler->key() );
			$shop_url = esc_url( $handler->shop_url() );
			$author   = esc_attr( $handler->author() );

			$updater = new Login_Designer_Extension_Updater( $shop_url, __FILE__, array(
				'version' => self::$version,
				'license' => $key,
				'author'  => $author,
				'item_id' => self::$id,
				'beta'    => false,
			) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( '@@textdomain', false, dirname( plugin_basename( LOGIN_DESIGNER_MINIMAL_BACKGROUNDS_PLUGIN_DIR ) ) . '/languages/' );
		}
	}

endif; // End if class_exists check.

/**
 * The main function for that returns Login_Designer_Minimal_Backgrounds
 *
 * The main function responsible for returning the one true Login_Designer_Minimal_Backgrounds
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $login_designer_minimal_backgrounds = login_designer_minimal_backgrounds(); ?>
 *
 * @since 1.0.0
 * @return object|Login_Designer_Minimal_Backgrounds The one true Login_Designer_Minimal_Backgrounds Instance.
 */
function login_designer_minimal_backgrounds() {

	// Check for Login Designer.
	if ( ! class_exists( 'Login_Designer' ) ) {

		// Check for the activation class.
		if ( ! class_exists( 'Login_Designer_Extension_Activation' ) ) {
			require_once 'includes/login-designer-extension-activation.php';
		}

		$activation = new Login_Designer_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();

	} else {
		return Login_Designer_Minimal_Backgrounds::instance();
	}
}
add_action( 'plugins_loaded', 'login_designer_minimal_backgrounds' );
