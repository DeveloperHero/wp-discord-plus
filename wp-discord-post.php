<?php
/**
 * WP Discord Post Plus
 *
 * @author      Developer Hero
 * @license     GPL-2.0+
 *
 * Plugin Name: WP Discord Post Plus
 * Plugin URI:  https://developerhero.net/plugins/wp-discord-post-plus/
 * Description: A Discord integration that sends a message on your desired Discord server and channel for every new post published.
 * Based on the original plugin WP Discord Post by Nicola Mustone, which is available on the WordPress Directory.
 *
 * Version:     1.0.0
 * Author:      Developer Hero
 * Author URI:  https://developerhero.net/
 * Text Domain: wp-discord-post-plus
 *
 * WC tested up to: 3.5
 *
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//define version and plugin name
define('WP_DISCORD_POST_PLUS_VERSION', '1.0.0');
define('WP_DISCORD_POST_PLUS_PLUGINNAME', 'WP Discord Post Plus');

/**
 * Main class of the plugin WP Discord Post. Handles the bot and the admin settings.
 */
class WP_Discord_Post_Plus {
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
		doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-discord-post-plus' ), WP_DISCORD_POST_PLUS_VERSION );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-discord-post-plus' ), WP_DISCORD_POST_PLUS_VERSION );
	}

	/**
	 * Adds the required hooks.
	 */
	public function __construct() {
		require_once 'includes/functions-general.php';
		require_once 'includes/class-wp-discord-post-admin.php';
		require_once 'includes/class-wp-discord-post-http.php';
		require_once 'includes/class-wp-discord-post-formatting.php';
		require_once 'includes/class-wp-discord-metabox.php';

		if (is_admin()) {
			require_once 'includes/class-wp-discord-enqueue-assets.php';
		}

		$this->post = require_once 'includes/class-wp-discord-post-post.php';

		if ( class_exists( 'WooCommerce' ) ) {
			$this->woocommerce = include_once 'includes/class-wp-discord-post-woocommerce.php';
		}

		$this->load_textdomain();

		do_action( 'wp_discord_post_plus_init' );

		//add_action('wp_ajax_test', array($this, 'test'));
	}

	/**
	 * Loads the plugin localization files.
	 */
	public function load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-discord-post-plus' );
		load_textdomain( 'wp-discord-post-plus', WP_LANG_DIR . '/wp-discord-post/discord-post-plus-' . $locale . '.mo' );
		load_plugin_textdomain( 'wp-discord-post-plus', false, plugin_basename( __DIR__ ) . '/languages' );
	}

	// public function test()
	// {
	// 	//$post = get_post(69);
	// 	//$product = wc_get_product( $id );
	// 	//$order = wc_get_order( 24 );
	// 	var_dump($this->woocommerce->send_order(68));
	// }
}

WP_Discord_Post_Plus::instance();
