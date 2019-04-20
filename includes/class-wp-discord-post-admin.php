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
class WP_Discord_Post_Plus_Admin {
	/**
	 * Inits the admin panel.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
	}

	/**
	 * Adds the menu Settings > WP Discord Post Plus.
	 */
	public function add_menu() {
		add_options_page(
			__( 'WP Discord Post Plus Settings', 'wp-discord-post-plus' ),
			__( 'WP Discord Post Plus', 'wp-discord-post-plus' ),
			'manage_options',
			'wp-discord-post-plus',
			array( $this, 'settings_page_html' )
		);
	}

	/**
	 * Generates the settings page.
	 */
	public function settings_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		settings_errors( 'wp-discord-post-plus-messages' );
		?>

		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
			<?php
			settings_fields( 'wp-discord-post-plus' );
			do_settings_sections( 'wp-discord-post-plus' );
			submit_button( __( 'Save Settings', 'wp-discord-post-plus' ) );
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Inits the settings page.
	 */
	public function settings_init() {

		add_settings_section(
			'wp_discord_post_plus_settings',
			esc_html__( 'General', 'wp-discord-post-plus' ),
			array( $this, 'settings_callback' ),
			'wp-discord-post-plus'
		);

		add_settings_section(
			'wp_discord_post_plus_post_settings',
			esc_html__( 'Posts Settings', 'wp-discord-post-plus' ),
			array( $this, 'settings_callback' ),
			'wp-discord-post-plus'
		);

		add_settings_field(
			'wp_discord_post_plus_bot_username',
			esc_html__( 'Bot Username', 'wp-discord-post-plus' ),
			array( $this, 'print_bot_username_field' ),
			'wp-discord-post-plus',
			'wp_discord_post_plus_settings'
		);

		add_settings_field(
			'wp_discord_post_plus_avatar_url',
			esc_html__( 'Avatar URL', 'wp-discord-post-plus' ),
			array( $this, 'print_avatar_url_field' ),
			'wp-discord-post-plus',
			'wp_discord_post_plus_settings'
		);

		// add_settings_field(
		// 'wp_discord_post_plus_bot_token',
		// esc_html__( 'Discord Bot Token', 'wp-discord-post-plus' ),
		// array( $this, 'print_bot_token_field' ),
		// 'wp-discord-post-plus',
		// 'wp_discord_post_plus_settings'
		// );
		// Enable support for WooCommerce if it's active.
		if ( class_exists( 'WooCommerce' ) ) {
			add_settings_section(
				'wp_discord_post_plus_woocommerce_settings',
				esc_html__( 'WooCommerce Settings', 'wp-discord-post-plus' ),
				array( $this, 'settings_callback' ),
				'wp-discord-post-plus'
			);

			add_settings_field(
				'wp_discord_enabled_for_woocommerce_products',
				esc_html__( 'Send Products', 'wp-discord-post-plus' ),
				array( $this, 'print_enabled_for_woocommerce_products_field' ),
				'wp-discord-post-plus',
				'wp_discord_post_plus_woocommerce_settings'
			);

			if ( 'yes' === get_option( 'wp_discord_enabled_for_woocommerce_products' ) ) {
				// add_settings_field(
				// 'wp_discord_post_plus_product_webhook_url',
				// esc_html__( 'Discord Products Webhook URL', 'wp-discord-post-plus' ),
				// array( $this, 'print_product_webhook_url_field' ),
				// 'wp-discord-post-plus',
				// 'wp_discord_post_plus_woocommerce_settings'
				// );
				add_settings_field(
					'wp_discord_product_message_format',
					esc_html__( 'Product Message Format', 'wp-discord-post-plus' ),
					array( $this, 'print_product_message_format_field' ),
					'wp-discord-post-plus',
					'wp_discord_post_plus_woocommerce_settings'
				);

				register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_product_webhook_url' );
				register_setting( 'wp-discord-post-plus', 'wp_discord_product_message_format' );
			}

			add_settings_field(
				'wp_discord_enabled_for_woocommerce',
				esc_html__( 'Send Orders', 'wp-discord-post-plus' ),
				array( $this, 'print_enabled_for_woocommerce_field' ),
				'wp-discord-post-plus',
				'wp_discord_post_plus_woocommerce_settings'
			);

			if ( 'yes' === get_option( 'wp_discord_enabled_for_woocommerce' ) ) {
				// add_settings_field(
				// 'wp_discord_post_plus_order_webhook_url',
				// esc_html__( 'Discord Orders Webhook URL', 'wp-discord-post-plus' ),
				// array( $this, 'print_order_webhook_url_field' ),
				// 'wp-discord-post-plus',
				// 'wp_discord_post_plus_woocommerce_settings'
				// );
				add_settings_field(
					'wp_discord_order_plus_message_format',
					esc_html__( 'Order Message Format', 'wp-discord-post-plus' ),
					array( $this, 'print_order_message_format_field' ),
					'wp-discord-post-plus',
					'wp_discord_post_plus_woocommerce_settings'
				);

				// register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_order_webhook_url' );
				register_setting( 'wp-discord-post-plus', 'wp_discord_order_plus_message_format' );
			}

			register_setting( 'wp-discord-post-plus', 'wp_discord_enabled_for_woocommerce_products' );
			register_setting( 'wp-discord-post-plus', 'wp_discord_enabled_for_woocommerce' );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			// Webhook confiuration section
			add_settings_section(
				'wp_discord_post_plus_settings_webhooks',
				esc_html__( 'Channels & Webhooks', 'wp_discord_post_plus_settings_webhooks' ),
				array( $this, 'wp_discord_post_plus_settings_webhooks_callback' ),
				'wp-discord-post-plus'
			);

			add_settings_field(
				'wp_discord_post_plus_settings_webhooks_input',
				esc_html__( null, 'wp_discord_post_plus_settings_webhooks_input' ),
				array( $this, 'wp_discord_post_plus_settings_webhooks_input' ),
				'wp-discord-post-plus',
				'wp_discord_post_plus_settings_webhooks'
			);
		}

		add_settings_field(
			'wp_discord_post_plus_logging',
			esc_html__( 'Logging', 'wp-discord-post-plus' ),
			array( $this, 'print_logging_field' ),
			'wp-discord-post-plus',
			'wp_discord_post_plus_settings'
		);

		add_settings_field(
			'wp_discord_post_plus_mention_everyone',
			esc_html__( 'Mention Everyone', 'wp-discord-post-plus' ),
			array( $this, 'print_mention_everyone_field' ),
			'wp-discord-post-plus',
			'wp_discord_post_plus_settings'
		);

		add_settings_field(
			'wp_discord_post_plus_disable_embed',
			esc_html__( 'Disable Embed Content', 'wp-discord-post-plus' ),
			array( $this, 'print_disable_embed_field' ),
			'wp-discord-post-plus',
			'wp_discord_post_plus_settings'
		);

		add_settings_field(
			'wp_discord_post_plus_post_webhook_url',
			esc_html__( 'Webhook URL for WordPress Posts', 'wp-discord-post-plus' ),
			array( $this, 'print_post_webhook_url_field' ),
			'wp-discord-post-plus',
			'wp_discord_post_plus_post_settings'
		);

		add_settings_field(
			'wp_discord_post_plus_message_format',
			esc_html__( 'Post Message Format', 'wp-discord-post-plus' ),
			array( $this, 'print_message_format_field' ),
			'wp-discord-post-plus',
			'wp_discord_post_plus_post_settings'
		);

		register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_bot_username' );
		register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_avatar_url' );
		register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_bot_token' );
		register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_webhook_url' );
		register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_logging' );
		register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_mention_everyone' );
		register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_disable_embed' );
		register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_post_webhook_url' );
		register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_message_format' );
		register_setting( 'wp-discord-post-plus', 'wp_discord_post_plus_settings_webhooks_input' );
	}

	/**
	 * Prints the description in the settings page.
	 */
	public function settings_callback() {
		esc_html_e( 'Send WordPress post to discord', 'wp-discord-post-plus' );
	}

	/**
	 * Prints the Bot Username settings field.
	 */
	public function print_bot_username_field() {
		$value = get_option( 'wp_discord_post_plus_bot_username' );

		echo '<input type="text" name="wp_discord_post_plus_bot_username" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . esc_html__( 'The username that you want to use for the bot on your Discord server.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Avatar URL settings field.
	 */
	public function print_avatar_url_field() {
		$value = get_option( 'wp_discord_post_plus_avatar_url' );

		echo '<input type="text" name="wp_discord_post_plus_avatar_url" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . esc_html__( 'The URL of the avatar that you want to use for the bot on your Discord server.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Bot Token settings field.
	 */
	public function print_bot_token_field() {
		$value = get_option( 'wp_discord_post_plus_bot_token' );

		echo '<input type="text" name="wp_discord_post_plus_bot_token" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'Bot that will listen for Discord command. %1$sLearn more%2$s', 'wp-discord-post-plus' ), '<a href="https://discordapp.com/developers/docs/intro">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_webhook_url_field() {
		$value = get_option( 'wp_discord_post_plus_webhook_url' );

		echo '<input type="text" name="wp_discord_post_plus_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post-plus' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Logging settings field.
	 */
	public function print_logging_field() {
		$value = get_option( 'wp_discord_post_plus_logging' );

		echo '<input type="checkbox" name="wp_discord_post_plus_logging" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Save debug data to the PHP error log.', 'wp-discord-post-plus' ) . '</span>';
	}


	/**
	 * Prints the Mention Everyone settings field.
	 */
	public function print_mention_everyone_field() {
		$value = get_option( 'wp_discord_post_plus_mention_everyone' );

		echo '<input type="checkbox" name="wp_discord_post_plus_mention_everyone" value="yes"' . checked( 'yes', $value, false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Mention @everyone when sending the message to Discord.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Disable embed settings field.
	 */
	public function print_disable_embed_field() {
		$value = get_option( 'wp_discord_post_plus_disable_embed' );

		echo '<input type="checkbox" name="wp_discord_post_plus_disable_embed" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Disable the embed content added by WP Discord Post Plus and use the default content automatically added by Discord.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_post_webhook_url_field() {
		$value = get_option( 'wp_discord_post_plus_post_webhook_url' );

		$product_categories = get_categories( array(
			'orderby' => 'name',
			'order'   => 'ASC',
			'hide_empty' => false,
		));

		if ( empty( $value ) ) {
			$value = array(
				array(
					'chatroom'    => 'general',
					'webhook'     => '',
					'category_id' => -1,
				),
			);
		}

		echo "<div class='discord_webhook_settings_section_post'>";

		$count = 0;

		foreach ( $value as $k => $v ) {
			$chatroom_key = 'wp_discord_post_plus_post_webhook_url[' . $count . '][chatroom]';
			$webhook_key  = 'wp_discord_post_plus_post_webhook_url[' . $count . '][webhook]';
			$category_key = 'wp_discord_post_plus_post_webhook_url[' . $count . '][category]';

			echo "<div data-index='" . $count . "' class='wp_discord_post_plus_post_webhook_url_single_section' style='border: 1px solid lightgrey; padding: 10px; width: 90%; margin:20px 20px 0 0'>";
			echo "<a href='#' onclick=\"jQuery(this).parent().remove(); return false;\" style='display: block; float: right; font-size: 10px; position: relative; top: -5px; right: 0px;text-decoration:none;'> X </a>";
			echo "<div style='width:20%; display:inline-block;'> <label> Category </label>";
			echo "<select name='" . $category_key . "' >";
			echo "<option value='-1'> All </option>";

			if ( ! empty( $product_categories ) ) {
				foreach ( $product_categories as $category ) {
					$selected = '';
					if ( $v['category'] == $category->term_id ) {
						$selected = ' selected="selected" ';
					}
					echo "<option value='" . $category->term_id . "' " . $selected . '>' . $category->name . ' </option>';
				}
			}

			echo '</select> </div>';

			echo "<div style='width:20%;display:inline-block;'> <label> Channel </label>";
			echo "<input style='padding:5px; margin: 5px;' name='" . $chatroom_key . "' type='text' value='" . $v['chatroom'] . "'/> </div>";

			echo "<div style='width:50%; display:inline-block;'> <label> Webhook URL </label>";
			echo "<input style='padding:5px; margin: 5px; width:65%;' name='" . $webhook_key . "' type='text' value='" . $v['webhook'] . "'/> </div></div>";
			$count++;
		}

		echo '</div>';
		echo "<a href='#' id='post_webhook_add_new' style='float: right; margin-right: 113px; padding: 10px; font-size: 12px; box-shadow: none !important;'> + Add New </a> <div style='clear:both;'> </div>";
	}

	/**
	 * Prints the Message Format settings field.
	 */
	public function print_message_format_field() {
		$value       = get_option( 'wp_discord_post_plus_message_format' );
		$placeholder = __( '%author% just published the %post_type% %title% on their blog: %url%', 'wp-discord-post-plus' );

		echo '<textarea style="width:500px;height:150px;" name="wp_discord_post_plus_message_format" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea><br />';
		echo '<span class="description">' . esc_html__( 'Change the format of the message sent to Discord. The available placeholders are %post_type%, %title%, %author%, and %url%. HTML is not supported.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Enabled for Contact Form 7 settings field.
	 */
	public function print_enabled_for_cf7_field() {
		$value = get_option( 'wp_discord_enabled_for_cf7' );

		echo '<input type="checkbox" name="wp_discord_enabled_for_cf7" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Catch emails sent via Contact Form 7 and send them to Discord.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_cf7_webhook_url_field() {
		$value = get_option( 'wp_discord_post_plus_cf7_webhook_url' );

		echo '<input type="text" name="wp_discord_post_plus_cf7_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post-plus' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Enabled for Jetpack Contact Form settings field.
	 */
	public function print_enabled_for_jetpack_cf_field() {
		$value = get_option( 'wp_discord_enabled_for_jetpack_cf' );

		echo '<input type="checkbox" name="wp_discord_enabled_for_jetpack_cf" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Catch emails sent via Jetpack Contact Form and send them to Discord.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_jetpack_webhook_url_field() {
		$value = get_option( 'wp_discord_post_plus_jetpack_webhook_url' );

		echo '<input type="text" name="wp_discord_post_plus_jetpack_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post-plus' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Enabled for Gravity Forms settings field.
	 */
	public function print_enabled_for_gf_field() {
		$value = get_option( 'wp_discord_enabled_for_gf' );

		echo '<input type="checkbox" name="wp_discord_enabled_for_gf" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Catch emails sent via Gravity Forms and send them to Discord.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_gf_webhook_url_field() {
		$value = get_option( 'wp_discord_post_plus_gf_webhook_url' );

		echo '<input type="text" name="wp_discord_post_plus_gf_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post-plus' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Send Products settings field.
	 */
	public function print_enabled_for_woocommerce_products_field() {
		$value = get_option( 'wp_discord_enabled_for_woocommerce_products' );

		echo '<input type="checkbox" name="wp_discord_enabled_for_woocommerce_products" value="yes"' . checked( 'yes', $value, false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Write in Discord when a new WooCommerce product is published.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_product_webhook_url_field() {
		$value = get_option( 'wp_discord_post_plus_product_webhook_url' );

		echo '<input type="text" name="wp_discord_post_plus_product_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post-plus' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Product Message Format settings field.
	 */
	public function print_product_message_format_field() {
		$value       = get_option( 'wp_discord_product_message_format' );
		$placeholder = __( 'A new product is available in our store. Check it out!', 'wp-discord-post-plus' );

		echo '<textarea style="width:500px;height:150px;" name="wp_discord_product_message_format" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea><br />';
		echo '<span class="description">' . esc_html__( 'Change the format of the message sent to Discord when a new product is published. The available placeholders are %title%, %url%, and %price%.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Send Orders settings field.
	 */
	public function print_enabled_for_woocommerce_field() {
		$value = get_option( 'wp_discord_enabled_for_woocommerce' );

		echo '<input type="checkbox" name="wp_discord_enabled_for_woocommerce" value="yes"' . checked( 'yes', $value, false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Write in Discord when a new WooCommerce order is created.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_order_webhook_url_field() {
		$value = get_option( 'wp_discord_post_plus_order_webhook_url' );

		echo '<input type="text" name="wp_discord_post_plus_order_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post-plus' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Order Message Format settings field.
	 */
	public function print_order_message_format_field() {
		$value       = get_option( 'wp_discord_order_plus_message_format' );
		$placeholder = __( 'Order #%1$order_number% by %2$order_customer% has been created. The order total is %3$order_total%.', 'wp-discord-post-plus' );

		echo '<textarea style="width:500px;height:150px;" name="wp_discord_order_plus_message_format" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea><br />';
		echo '<span class="description">' . esc_html__( 'Change the format of the message sent to Discord when a new order is created in WooCommerce. The available placeholders are %1$order_number%, %2$order_customer%, and %3$order_total%.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Prints the Giphy API Key settings field.
	 */
	public function print_giphy_api_key_field() {
		$value = get_option( 'wp_discord_post_plus_giphy_api_key' );

		echo '<input type="text" name="wp_discord_post_plus_giphy_api_key" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'Your API key from Giphy. %1$sLearn more%2$s', 'wp-discord-post-plus' ), '<a href="https://developers.giphy.com/docs/#api-keys" target="_blank">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Hit Me! field.
	 */
	public function print_send_dank_meme_field() {
		echo '<a href="' . add_query_arg( 'dank_meme', 'yes' ) . '" title="' . esc_attr__( 'Send Dank Meme!', 'wp-discord-post-plus' ) . '" class="button primary">' . esc_html__( 'Send Dank Meme!', 'wp-discord-post-plus' ) . '</a>';
		echo '<br><br><span class="description">' . esc_html__( 'You do this at your own risk... there is no coming back from the dank world.', 'wp-discord-post-plus' ) . '</span>';
	}

	/**
	 * Adds some content to the Privacy Policy default content.
	 */
	public function add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content = '';

		if ( 'yes' === get_option( 'wp_discord_enabled_for_woocommerce' ) ) {
			$content .= __( 'When you place an order on this site, we send your order details to discordapp.com.', 'wp-discord-post-plus' );
		}

		if ( 'yes' === get_option( 'wp_discord_enabled_for_jetpack_cf' ) || 'yes' === get_option( 'wp_discord_enabled_for_cf7' ) ) {
			$content .= __( 'When you use the contact forms on this site, we send their content to discordapp.com.', 'wp-discord-post-plus' );
		}

		if ( ! empty( $content ) ) {
			$content .= sprintf( ' ' . __( 'The discordapp.com privacy policy is <a href="%s" target="_blank">here</a>.', 'wp-discord-post-plus' ), 'https://discordapp.com/privacy' );
		}

		wp_add_privacy_policy_content(
			'WP Discord Post Plus',
			wp_kses_post( wpautop( $content, false ) )
		);
	}

	/**
	 * Prints the description for webhook section.
	 */
	public function wp_discord_post_plus_settings_webhooks_callback() {
		esc_html_e( 'Channel and webhook configuration for WooCommerce. All is the default channel which is used when no category is matched.', 'wp-discord-post-plus' );
	}

	/**
	 * Prints the inputs for webhook section.
	 */
	public function wp_discord_post_plus_settings_webhooks_input_callback() {
		esc_html_e( 'Channel and webhook configuration for WooCommerce.', 'wp-discord-post-plus' );
	}

	/**
	 * Prints the webhook settings field.
	 */
	public function wp_discord_post_plus_settings_webhooks_input() {
		$value   = get_option( 'wp_discord_post_plus_settings_webhooks_input' );
		
		$product_categories = $this->get_woocommerce_product_categories();

		if ( empty( $value ) ) {
			$value = array(
				array(
					'chatroom'    => 'general',
					'webhook'     => '',
					'category_id' => -1,
				),
			);
		}

		echo "<div class='discord_webhook_settings_section_woocommerce'>";

		$count = 0; 

		foreach ( $value as $k => $v ) {
			$chatroom_key = 'wp_discord_post_plus_settings_webhooks_input[' . $count . '][chatroom]';
			$webhook_key  = 'wp_discord_post_plus_settings_webhooks_input[' . $count . '][webhook]';
			$category_key = 'wp_discord_post_plus_settings_webhooks_input[' . $count . '][category]';

			echo "<div data-index='" . $count . "' class='discord_webhook_settings_single_section' style='border: 1px solid lightgrey; padding: 10px; width: 90%; margin:20px 20px 0 0'>";
			echo "<a href='#' onclick=\"jQuery(this).parent().remove(); return false;\" style='display: block; float: right; font-size: 10px; position: relative; top: -5px; right: 0px;text-decoration:none;'> X </a>";
			echo "<div style='width:20%; display:inline-block;'> <label> Category </label>";
			echo "<select name='" . $category_key . "' >";
			echo "<option value='-1'> All </option>";

			if ( ! empty( $product_categories ) ) {
				foreach ( $product_categories as $category ) {
					$selected = '';
					if ( $v['category'] == $category->term_id ) {
						$selected = ' selected="selected" ';
					}
					echo "<option value='" . $category->term_id . "' " . $selected . '>' . $category->name . ' </option>';
				}
			}

			echo '</select> </div>';

			echo "<div style='width:20%;display:inline-block;'> <label> Channel </label>";
			echo "<input style='padding:5px; margin: 5px;' name='" . $chatroom_key . "' type='text' value='" . $v['chatroom'] . "'/> </div>";

			echo "<div style='width:50%; display:inline-block;'> <label> Webhook URL </label>";
			echo "<input style='padding:5px; margin: 5px; width:65%;' name='" . $webhook_key . "' type='text' value='" . $v['webhook'] . "'/> </div></div>";
			$count++;
		}

		echo '</div>';
		echo "<a href='#' id='discord_webhooks_add_new' style='float: right; margin-right: 113px; padding: 10px; font-size: 12px; box-shadow: none !important;'> + Add New </a> <div style='clear:both;'> </div>";

	}

	private function get_woocommerce_product_categories() {
		return get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);
	}
}

new WP_Discord_Post_Plus_Admin();
