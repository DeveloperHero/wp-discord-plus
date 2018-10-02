<?php
/**
 * WP Discord Post Plus
 *
 * @author      Nicola Mustone
 * @license     GPL-2.0+
 *
 * Plugin Name: WP Discord Post Plus
 * Plugin URI:  https://wordpress.org/plugins/wp-discord-post/
 * Description: A Discord integration that sends a message on your desired Discord server and channel for every new post published. Modified by M Yakub Mizan to add new features.
 * Version:     2.1.0
 * Author:      Nicola Mustone
 * Author URI:  https://nicola.blog/
 * Text Domain: wp-discord-post
 *
 * WC tested up to: 3.4.4
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
		add_action('wp_ajax_discord_bot_run', array($this, 'wp_ajax_bot_run'));
		add_action('wp_ajax_nopriv_discord_bot_run', array($this, 'wp_ajax_bot_run'));
	}

	/**
	 * Loads the plugin localization files.
	 */
	public function load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-discord-post' );
		load_textdomain( 'wp-discord-post', WP_LANG_DIR . '/wp-discord-post/discord-post-' . $locale . '.mo' );
		load_plugin_textdomain( 'wp-discord-post', false, plugin_basename( __DIR__ ) . '/languages' );
	}

	public function wp_ajax_bot_run()
	{
		echo "Maximum execution time is: " . ini_get('max_execution_time') . "\n"; 
		echo "You need to run the cron script before this interval if you want to keep your bot running..\n";

		$lock_file = plugin_dir_path( __FILE__ ) . 'discord.lock'; 

		$lock = fopen($lock_file, 'c');

		if (!flock($lock, LOCK_EX | LOCK_NB)) {

			wp_die("Scripts in execution already...\n");

		} else {

			require_once( 'bot-server/bot.php' );
		}
	}
}

WP_Discord_Post::instance();


// /**
// ** Ensure that lock is cleaned on shutdown
// **/
function wpdiscord_clear_lock()
{
	///echo "Bot sutting down: " . time() . "\n";
	//error_log(time() . ': Sutting down discord bot. Lock file cleaning.');
	//$lock_file = plugin_dir_path( __FILE__ ) . 'discord.lock'; //clean the lock file when scripts shutsdown
	//fclose($lock_file);
	//unlink($lock_file);
}

register_shutdown_function('wpdiscord_clear_lock'); //clean lock on shut down