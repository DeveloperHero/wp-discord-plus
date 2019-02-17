<?php
/**
 * WP Discord Post Plus
 *
 * @author      Developer Hero
 * @license     GPL-2.0+
 *
 * Plugin Name: WP Discord Post Plus
 * Plugin URI:  https: //developerhero.net/plugins/wp-discord-post-plus/
 * Description: A Discord integration that sends a message on your desired Discord server and channel for every new post published. 
 * Based on the original plugin WP Discord Post by Nicola Mustone, which is available on the WordPress Directory.
 * 
 * Version:     2.1.1
 * Author:      Developer Hero
 * Author URI:  https: //developerhero.net/
 * Text Domain: wp-discord-post
 *
 * WC tested up to: 3.5
 *
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class of the plugin WP Discord Post. Handles the bot and the admin settings.
 */
class WP_Discord_Post {
	/**
	 * The single instance of the class.
	 *
	 * @var WP_Discord_Post
	 */
	protected static $_instance = null;

	/**
	 * The instance of WP_Discord_Post_Post.
	 *
	 * @var WP_Discord_Post_Post
	 */
	public $post = null;

	/**
	 * The instance of WP_Discord_Post_CF7.
	 *
	 * @var WP_Discord_Post_CF7
	 */
	public $cf7 = null;

	/**
	 * The instance of WP_Discord_Post_GF.
	 *
	 * @var WP_Discord_Post_GF
	 */
	public $gf = null;

	/**
	 * The instance of WP_Discord_Post_Jetpack_CF.
	 *
	 * @var WP_Discord_Post_Jetpack_CF
	 */
	public $jetpack_cf = null;

	/**
	 * The instance of WP_Discord_Post_WooCommerce.
	 *
	 * @var WP_Discord_Post_WooCommerce
	 */
	public $woocommerce = null;

	/**
	 * Main WP_Discord_Post Instance.
	 *
	 * Ensures only one instance of WP_Discord_Post is loaded or can be loaded.
	 *
	 * @static
	 * @return WP_Discord_Post - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-discord-post' ), '1.0.9' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-discord-post' ), '1.0.9' );
	}

	/**
	 * Adds the required hooks.
	 */
	public function __construct() {
		require_once( 'includes/functions-general.php' );
		require_once( 'includes/class-wp-discord-post-admin.php' );
		require_once( 'includes/class-wp-discord-post-http.php' );
		require_once( 'includes/class-wp-discord-post-formatting.php' );
		require_once( 'includes/class-wp-discord-bot.php' );

		if ( is_admin() ) {
			require_once( 'includes/class-wp-discord-post-dank-meme.php' );
		}

		$this->post = require_once( 'includes/class-wp-discord-post-post.php' );

		if ( 'yes' === get_option( 'wp_discord_enabled_for_cf7' ) && class_exists( 'WPCF7' ) ) {
			$this->cf7 = include_once( 'includes/class-wp-discord-post-contact-form-7.php' );
		}

		if ( 'yes' === get_option( 'wp_discord_enabled_for_jetpack_cf' ) && class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'contact-form' ) ) {
			$this->jetpack_cf = include_once( 'includes/class-wp-discord-post-jetpack-contact-form.php' );
		}

		if ( 'yes' === get_option( 'wp_discord_enabled_for_gf' ) && class_exists( 'GFForms' ) ) {
			$this->gf = include_once( 'includes/class-wp-discord-post-gravityforms.php' );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			$this->woocommerce = include_once( 'includes/class-wp-discord-post-woocommerce.php' );
		}

		$this->load_textdomain();

		do_action( 'wp_discord_post_init' );
	}

	/**
	 * Loads the plugin localization files.
	 */
	public function load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-discord-post' );
		load_textdomain( 'wp-discord-post', WP_LANG_DIR . '/wp-discord-post/discord-post-' . $locale . '.mo' );
		load_plugin_textdomain( 'wp-discord-post', false, plugin_basename( __DIR__ ) . '/languages' );
	}
}

WP_Discord_Post::instance();