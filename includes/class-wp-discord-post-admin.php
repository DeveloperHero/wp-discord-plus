<?php
/**
 * WP Discord Post Admin
 *
 * @author      Nicola Mustone
 * @license     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for the admin settings of WP Discord Post.
 */
class WP_Discord_Post_Admin {
	/**
	 * Inits the admin panel.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
	}

	/**
	 * Adds the menu Settings > WP Discord Post.
	 */
	public function add_menu() {
		add_options_page(
			__( 'WP Discord Post Settings', 'wp-discord-post' ),
			__( 'WP Discord Post', 'wp-discord-post' ),
			'manage_options',
			'wp-discord-post',
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

		settings_errors( 'wp-discord-post-messages' );
		?>

		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
			<?php
			settings_fields( 'wp-discord-post' );
			do_settings_sections( 'wp-discord-post' );
			submit_button( __( 'Save Settings', 'wp-discord-post' ) );
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
			'wp_discord_post_settings',
			esc_html__( 'General', 'wp-discord-post' ),
			array( $this, 'settings_callback' ),
			'wp-discord-post'
		);

		add_settings_section(
			'wp_discord_post_post_settings',
			esc_html__( 'Posts Settings', 'wp-discord-post' ),
			array( $this, 'settings_callback' ),
			'wp-discord-post'
		);

		add_settings_field(
			'wp_discord_post_bot_username',
			esc_html__( 'Bot Username', 'wp-discord-post' ),
			array( $this, 'print_bot_username_field' ),
			'wp-discord-post',
			'wp_discord_post_settings'
		);

		add_settings_field(
			'wp_discord_post_avatar_url',
			esc_html__( 'Avatar URL', 'wp-discord-post' ),
			array( $this, 'print_avatar_url_field' ),
			'wp-discord-post',
			'wp_discord_post_settings'
		);

		// add_settings_field(
		// 	'wp_discord_post_bot_token',
		// 	esc_html__( 'Discord Bot Token', 'wp-discord-post' ),
		// 	array( $this, 'print_bot_token_field' ),
		// 	'wp-discord-post',
		// 	'wp_discord_post_settings'
		// );

		// Enable support for WooCommerce if it's active.
		if (class_exists('WooCommerce')) {
			add_settings_section(
				'wp_discord_post_woocommerce_settings',
				esc_html__('WooCommerce Settings', 'wp-discord-post'),
				array($this, 'settings_callback'),
				'wp-discord-post'
			);

			add_settings_field(
				'wp_discord_enabled_for_woocommerce_products',
				esc_html__('Send Products', 'wp-discord-post'),
				array($this, 'print_enabled_for_woocommerce_products_field'),
				'wp-discord-post',
				'wp_discord_post_woocommerce_settings'
			);

			if ('yes' === get_option('wp_discord_enabled_for_woocommerce_products')) {
				// add_settings_field(
				//     'wp_discord_post_product_webhook_url',
				//     esc_html__( 'Discord Products Webhook URL', 'wp-discord-post' ),
				//     array( $this, 'print_product_webhook_url_field' ),
				//     'wp-discord-post',
				//     'wp_discord_post_woocommerce_settings'
				// );

				add_settings_field(
					'wp_discord_product_message_format',
					esc_html__('Product Message Format', 'wp-discord-post'),
					array($this, 'print_product_message_format_field'),
					'wp-discord-post',
					'wp_discord_post_woocommerce_settings'
				);

				register_setting('wp-discord-post', 'wp_discord_post_product_webhook_url');
				register_setting('wp-discord-post', 'wp_discord_product_message_format');
			}

			add_settings_field(
				'wp_discord_enabled_for_woocommerce',
				esc_html__('Send Orders', 'wp-discord-post'),
				array($this, 'print_enabled_for_woocommerce_field'),
				'wp-discord-post',
				'wp_discord_post_woocommerce_settings'
			);

			if ('yes' === get_option('wp_discord_enabled_for_woocommerce')) {
				// add_settings_field(
				//     'wp_discord_post_order_webhook_url',
				//     esc_html__( 'Discord Orders Webhook URL', 'wp-discord-post' ),
				//     array( $this, 'print_order_webhook_url_field' ),
				//     'wp-discord-post',
				//     'wp_discord_post_woocommerce_settings'
				// );

				add_settings_field(
					'wp_discord_order_message_format',
					esc_html__('Order Message Format', 'wp-discord-post'),
					array($this, 'print_order_message_format_field'),
					'wp-discord-post',
					'wp_discord_post_woocommerce_settings'
				);

				//register_setting( 'wp-discord-post', 'wp_discord_post_order_webhook_url' );
				register_setting('wp-discord-post', 'wp_discord_order_message_format');
			}

			register_setting('wp-discord-post', 'wp_discord_enabled_for_woocommerce_products');
			register_setting('wp-discord-post', 'wp_discord_enabled_for_woocommerce');
		}


		//Webhook confiuration section
		add_settings_section(
			'wp_discord_post_settings_webhooks',
			esc_html__( 'Channels & Webhooks', 'wp_discord_post_settings_webhooks' ),
			array( $this, 'wp_discord_post_settings_webhooks_callback' ),
			'wp-discord-post'
		);

		add_settings_field(
			'wp_discord_post_settings_webhooks_input',
			esc_html__( null, 'wp_discord_post_settings_webhooks_input' ),
			array( $this, 'wp_discord_post_settings_webhooks_input' ),
			'wp-discord-post',
			'wp_discord_post_settings_webhooks'
		);

		add_settings_field(
			'wp_discord_post_logging',
			esc_html__( 'Logging', 'wp-discord-post' ),
			array( $this, 'print_logging_field' ),
			'wp-discord-post',
			'wp_discord_post_settings'
		);

		add_settings_field(
			'wp_discord_post_mention_everyone',
			esc_html__( 'Mention Everyone', 'wp-discord-post' ),
			array( $this, 'print_mention_everyone_field' ),
			'wp-discord-post',
			'wp_discord_post_settings'
		);

		add_settings_field(
			'wp_discord_post_disable_embed',
			esc_html__( 'Disable Embed Content', 'wp-discord-post' ),
			array( $this, 'print_disable_embed_field' ),
			'wp-discord-post',
			'wp_discord_post_settings'
		);

		add_settings_field(
			'wp_discord_post_post_webhook_url',
			esc_html__( 'Webhook URL for WordPress Posts', 'wp-discord-post' ),
			array( $this, 'print_post_webhook_url_field' ),
			'wp-discord-post',
			'wp_discord_post_post_settings'
		);

		add_settings_field(
			'wp_discord_post_message_format',
			esc_html__( 'Post Message Format', 'wp-discord-post' ),
			array( $this, 'print_message_format_field' ),
			'wp-discord-post',
			'wp_discord_post_post_settings'
		);

		register_setting( 'wp-discord-post', 'wp_discord_post_bot_username' );
		register_setting( 'wp-discord-post', 'wp_discord_post_avatar_url' );
		register_setting( 'wp-discord-post', 'wp_discord_post_bot_token' );
		register_setting( 'wp-discord-post', 'wp_discord_post_webhook_url' );
		register_setting( 'wp-discord-post', 'wp_discord_post_logging' );
		register_setting( 'wp-discord-post', 'wp_discord_post_mention_everyone' );
		register_setting( 'wp-discord-post', 'wp_discord_post_disable_embed' );
		register_setting( 'wp-discord-post', 'wp_discord_post_post_webhook_url' );
		register_setting( 'wp-discord-post', 'wp_discord_post_message_format' );
		register_setting( 'wp-discord-post', 'wp_discord_post_settings_webhooks_input' );
	}

	/**
	 * Prints the description in the settings page.
	 */
	public function settings_callback() {
		esc_html_e( 'Send WordPress post to discord', 'wp-discord-post' );
	}

	/**
	 * Prints the Bot Username settings field.
	 */
	public function print_bot_username_field() {
		$value = get_option( 'wp_discord_post_bot_username' );

		echo '<input type="text" name="wp_discord_post_bot_username" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . esc_html__( 'The username that you want to use for the bot on your Discord server.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Avatar URL settings field.
	 */
	public function print_avatar_url_field() {
		$value = get_option( 'wp_discord_post_avatar_url' );

		echo '<input type="text" name="wp_discord_post_avatar_url" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . esc_html__( 'The URL of the avatar that you want to use for the bot on your Discord server.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Bot Token settings field.
	 */
	public function print_bot_token_field() {
		$value = get_option( 'wp_discord_post_bot_token' );

		echo '<input type="text" name="wp_discord_post_bot_token" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'Bot that will listen for Discord command. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://discordapp.com/developers/docs/intro">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_webhook_url_field() {
		$value = get_option( 'wp_discord_post_webhook_url' );

		echo '<input type="text" name="wp_discord_post_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Logging settings field.
	 */
	public function print_logging_field() {
		$value = get_option( 'wp_discord_post_logging' );

		echo '<input type="checkbox" name="wp_discord_post_logging" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Save debug data to the PHP error log.', 'wp-discord-post' ) . '</span>';
	}


	/**
	 * Prints the Mention Everyone settings field.
	 */
	public function print_mention_everyone_field() {
		$value = get_option( 'wp_discord_post_mention_everyone' );

		echo '<input type="checkbox" name="wp_discord_post_mention_everyone" value="yes"' . checked( 'yes', $value, false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Mention @everyone when sending the message to Discord.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Disable embed settings field.
	 */
	public function print_disable_embed_field() {
		$value = get_option( 'wp_discord_post_disable_embed' );

		echo '<input type="checkbox" name="wp_discord_post_disable_embed" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Disable the embed content added by WP Discord Post and use the default content automatically added by Discord.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_post_webhook_url_field() {
		$value = get_option( 'wp_discord_post_post_webhook_url' );

		echo '<input type="text" name="wp_discord_post_post_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Message Format settings field.
	 */
	public function print_message_format_field() {
		$value       = get_option( 'wp_discord_post_message_format' );
		$placeholder = __( '%author% just published the %post_type% %title% on their blog: %url%', 'wp-discord-post' );

		echo '<textarea style="width:500px;height:150px;" name="wp_discord_post_message_format" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea><br />';
		echo '<span class="description">' . esc_html__( 'Change the format of the message sent to Discord. The available placeholders are %post_type%, %title%, %author%, and %url%. HTML is not supported.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Enabled for Contact Form 7 settings field.
	 */
	public function print_enabled_for_cf7_field() {
		$value = get_option( 'wp_discord_enabled_for_cf7' );

		echo '<input type="checkbox" name="wp_discord_enabled_for_cf7" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Catch emails sent via Contact Form 7 and send them to Discord.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_cf7_webhook_url_field() {
		$value = get_option( 'wp_discord_post_cf7_webhook_url' );

		echo '<input type="text" name="wp_discord_post_cf7_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Enabled for Jetpack Contact Form settings field.
	 */
	public function print_enabled_for_jetpack_cf_field() {
		$value = get_option( 'wp_discord_enabled_for_jetpack_cf' );

		echo '<input type="checkbox" name="wp_discord_enabled_for_jetpack_cf" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Catch emails sent via Jetpack Contact Form and send them to Discord.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_jetpack_webhook_url_field() {
		$value = get_option( 'wp_discord_post_jetpack_webhook_url' );

		echo '<input type="text" name="wp_discord_post_jetpack_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Enabled for Gravity Forms settings field.
	 */
	public function print_enabled_for_gf_field() {
		$value = get_option( 'wp_discord_enabled_for_gf' );

		echo '<input type="checkbox" name="wp_discord_enabled_for_gf" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Catch emails sent via Gravity Forms and send them to Discord.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_gf_webhook_url_field() {
		$value = get_option( 'wp_discord_post_gf_webhook_url' );

		echo '<input type="text" name="wp_discord_post_gf_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Send Products settings field.
	 */
	public function print_enabled_for_woocommerce_products_field() {
		$value = get_option( 'wp_discord_enabled_for_woocommerce_products' );

		echo '<input type="checkbox" name="wp_discord_enabled_for_woocommerce_products" value="yes"' . checked( 'yes', $value, false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Write in Discord when a new WooCommerce product is published.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_product_webhook_url_field() {
		$value = get_option( 'wp_discord_post_product_webhook_url' );

		echo '<input type="text" name="wp_discord_post_product_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Product Message Format settings field.
	 */
	public function print_product_message_format_field() {
		$value       = get_option( 'wp_discord_product_message_format' );
		$placeholder = __( 'A new product is available in our store. Check it out!', 'wp-discord-post' );

		echo '<textarea style="width:500px;height:150px;" name="wp_discord_product_message_format" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea><br />';
		echo '<span class="description">' . esc_html__( 'Change the format of the message sent to Discord when a new product is published. The available placeholders are %title%, %url%, and %price%.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Send Orders settings field.
	 */
	public function print_enabled_for_woocommerce_field() {
		$value = get_option( 'wp_discord_enabled_for_woocommerce' );

		echo '<input type="checkbox" name="wp_discord_enabled_for_woocommerce" value="yes"' . checked( 'yes', $value, false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Write in Discord when a new WooCommerce order is created.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_order_webhook_url_field() {
		$value = get_option( 'wp_discord_post_order_webhook_url' );

		echo '<input type="text" name="wp_discord_post_order_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Order Message Format settings field.
	 */
	public function print_order_message_format_field() {
		$value       = get_option( 'wp_discord_order_message_format' );
		$placeholder = __( 'Order #%order_number% by %order_customer% has been created. The order total is %order_total%.', 'wp-discord-post' );

		echo '<textarea style="width:500px;height:150px;" name="wp_discord_order_message_format" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea><br />';
		echo '<span class="description">' . esc_html__( 'Change the format of the message sent to Discord when a new order is created in WooCommerce. The available placeholders are %order_number%, %order_customer%, and %order_total%.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Giphy API Key settings field.
	 */
	public function print_giphy_api_key_field() {
		$value = get_option( 'wp_discord_post_giphy_api_key' );

		echo '<input type="text" name="wp_discord_post_giphy_api_key" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'Your API key from Giphy. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://developers.giphy.com/docs/#api-keys" target="_blank">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Hit Me! field.
	 */
	public function print_send_dank_meme_field() {
		echo '<a href="' . add_query_arg( 'dank_meme', 'yes' ) . '" title="' . esc_attr__( 'Send Dank Meme!', 'wp-discord-post' ) . '" class="button primary">' . esc_html__( 'Send Dank Meme!', 'wp-discord-post' ) . '</a>';
		echo '<br><br><span class="description">' . esc_html__( 'You do this at your own risk... there is no coming back from the dank world.', 'wp-discord-post' ) . '</span>';
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
		    $content .= __( 'When you place an order on this site, we send your order details to discordapp.com.', 'wp-discord-post' );
		}

		if ( 'yes' === get_option( 'wp_discord_enabled_for_jetpack_cf' ) || 'yes' === get_option( 'wp_discord_enabled_for_cf7' ) ) {
			$content .= __( 'When you use the contact forms on this site, we send their content to discordapp.com.', 'wp-discord-post' );
		}

		if ( ! empty( $content ) ) {
			$content .= sprintf( ' ' . __( 'The discordapp.com privacy policy is <a href="%s" target="_blank">here</a>.', 'wp-discord-post' ), 'https://discordapp.com/privacy' );
		}

	    wp_add_privacy_policy_content(
	        'WP Discord Post',
	        wp_kses_post( wpautop( $content, false ) )
	    );
	}

	/**
	 * Prints the description for webhook section.
	 */
	public function wp_discord_post_settings_webhooks_callback() {
		esc_html_e( 'Channel and webhook configuration for WooCommerce. All is the default channel which is used when no category is matched.', 'wp-discord-post' );
	}

	/**
	 * Prints the inputs for webhook section.
	 */
	public function wp_discord_post_settings_webhooks_input_callback() {
		esc_html_e( 'Channel and webhook configuration for WooCommerce.', 'wp-discord-post' );
	}

	/**
	 * Prints the webhook settings field.
	 */
	public function wp_discord_post_settings_webhooks_input() {
		$value = get_option( 'wp_discord_post_settings_webhooks_input' );
		$product_categories = $this->get_woocommerce_product_categories();

		if (empty($value))
		{
			$value = array(
				array(
					'chatroom' => 'general',
					'webhook'  => '',
					'category_id' => -1,
				));
		}

		echo "<div class='discord_webhook_settings_section'>";

		foreach($value as $k => $v)
		{
			$chatroom_key = 'wp_discord_post_settings_webhooks_input[' . $k . '][chatroom]'; 
			$webhook_key  = 'wp_discord_post_settings_webhooks_input[' . $k . '][webhook]'; 
			$category_key = 'wp_discord_post_settings_webhooks_input[' . $k . '][category]';

			echo "<div class='discord_webhook_settings_single_section' style='border: 1px solid lightgrey; padding: 10px; width: 90%; margin:20px 20px 0 0'>";
			echo "<a href='#' onclick=\"jQuery(this).parent().remove(); return false;\" style='display: block; float: right; font-size: 10px; position: relative; top: -5px; right: 0px;text-decoration:none;'> X </a>";
			echo "<div style='width:20%; display:inline-block;'> <label> Category </label>";
			echo "<select name='" . $category_key . "' >";
			echo "<option value='-1'> All </option>";

			if (!empty($product_categories))
			{
				foreach($product_categories as $category)
				{
					$selected = '';
					if ($v['category'] == $category->term_id) {
						$selected = ' selected="selected" ';
					}
					echo "<option value='" . $category->term_id . "' " . $selected . ">" . $category->name . " </option>";
				}
			}

			echo "</select> </div>";

			echo "<div style='width:20%;display:inline-block;'> <label> Channel </label>";
			echo "<input style='padding:5px; margin: 5px;' name='" . $chatroom_key . "' type='text' value='" . $v['chatroom'] . "'/> </div>";

			echo "<div style='width:50%; display:inline-block;'> <label> Webhook URL </label>";
			echo "<input style='padding:5px; margin: 5px; width:65%;' name='" . $webhook_key . "' type='text' value='" . $v['webhook'] . "'/> </div></div>";
		}

		echo "</div>";
		echo "<a href='#' onclick=\"var newIndex = jQuery('.discord_webhook_settings_single_section').length + 1; jQuery('.discord_webhook_settings_section').append(jQuery('.discord_webhook_settings_single_section').eq(0).clone()); jQuery('input', jQuery('.discord_webhook_settings_single_section').eq(-1)).val(''); jQuery('.discord_webhook_settings_single_section').eq(-1).children('div').children('input').eq(0).attr('name', 'wp_discord_post_settings_webhooks_input[' + newIndex + '][chatroom]'); jQuery('.discord_webhook_settings_single_section').eq(-1).children('div').children('input').eq(1).attr('name', 'wp_discord_post_settings_webhooks_input[' + newIndex + '][webhook]'); jQuery('.discord_webhook_settings_single_section').eq(-1).children('div').children('select').eq(0).attr('name', 'wp_discord_post_settings_webhooks_input[' + newIndex + '][category]');jQuery('.discord_webhook_settings_single_section').eq(-1).children('div').children('select').eq(0).val(-1);return false;\" id='discord_webhooks_add_new' style='float: right; margin-right: 113px; padding: 10px; font-size: 12px; box-shadow: none !important;'> + Add New </a> <div style='clear:both;'> </div>";

	}

	private function get_woocommerce_product_categories()
	{
		return get_terms(array(
			'taxonomy' => "product_cat",
			'hide_empty' => false,
		));
	}
}

new WP_Discord_Post_Admin();
