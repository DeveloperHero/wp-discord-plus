<?php
/**
 * WP Discord Post Plus Admin
 *
 * @author      Nicola Mustone
 * @license     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for the admin settings of WP Discord Post Plus.
 */
class WP_Discord_Post_Plus_Assets {
	/**
	 * adds javascripts and css assets
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_styles'), 10, 1);
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts'), 10, 1);
	}

	public function enqueue_styles($hook) {
		// Load only on ?page=mypluginname
		if($hook != 'settings_page_wp-discord-post-plus') {
			return;
		}

		wp_enqueue_style( 'wp_discord_post_plus_styles', plugins_url('../assets/main.css', __FILE__) );
	}

	public function enqueue_scripts($hook) {
		// Load only on ?page=mypluginname
		if($hook != 'settings_page_wp-discord-post-plus') {
			return;
		}

        	wp_register_script('wp_discord_post_plus_scripts', plugins_url('../assets/main.js', __FILE__), array( 'jquery' ), WP_DISCORD_POST_PLUS_VERSION, true);
		wp_enqueue_script( 'wp_discord_post_plus_scripts');
		wp_enqueue_media();
	}

}

new WP_Discord_Post_Plus_Assets();
