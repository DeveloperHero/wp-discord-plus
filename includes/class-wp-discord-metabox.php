<?php
/**
 * WP Discord Post Plus Metaboxes
 *
 * @author      M Yakub Mizan
 * @license     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class to show metaboxes on order, post and proudct page.
 */
class WP_Discord_Post_Plus_Metabox {
	/**
	 * Adds the hook to handle posts.
	 */
	public function __construct() {
        add_action('add_meta_boxes', array($this, 'custom_meta_boxes'));
        add_action('save_post', array($this, 'save_post_meta'));
    }
    
    /**
     * Custom metabox callback
     */
    public function custom_meta_boxes()
    {
        $screens = ['post',];
        foreach ($screens as $screen) {
            add_meta_box(
                'wp_discord_plus_metabox',          
                'WP Discord Plus',  
                array($this, 'custom_meta_boxes_html'),  
                $screen, 
                'side'
            );
        }
    }

    public function custom_meta_boxes_html($post)
    {
        $value = get_post_meta($post->ID, '_wp_discord_send_flag', true);
        $checked = '';

        if ($value) {
            $checked = 'checked="checked"';
        }
        

    ?>
        <div>
            <br />
            <input name='wp_discord_metabox_send_flag' type="checkbox" value="1" <?php echo $checked; ?>>
            <label for="editor-post-taxonomies-hierarchical-term-1">Don't send to Discord</label>
            <br /> <br />
            <p> If you do not want to send this post to discord, check the option above. It's only applicable to new posts. </p>
        </div>
    <?php
    }

    public function save_post_meta($post_id)
    {
        if (array_key_exists('wp_discord_metabox_send_flag', $_POST)) {
            $value = 1;
        } else {
            $value = 0;
        }

        update_post_meta(
            $post_id,
            '_wp_discord_send_flag',
            $value
        );
    }
}

return new WP_Discord_Post_Plus_Metabox();
