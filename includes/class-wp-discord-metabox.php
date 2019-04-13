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
        add_action('add_meta_boxes', array($this, 'custom_meta_boxes'), 10, 1);
        add_action('save_post', array($this, 'save_post_meta'), 10, 1);
        add_action('publish_post', array($this, 'publish_post'), 20, 2);
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
        $value = get_post_meta($post->ID, 'wp_discord_send_flag', true);
        $checked = 'checked="checked"';

        if ($value) {
            $checked = '';
        }
        
    ?>
        <div>
            <br />
            <input name='wp_discord_metabox_send_flag' type="checkbox" value="1" <?php echo $checked; ?>>
            <label for="editor-post-taxonomies-hierarchical-term-1">Send to Discord</label>
            <br /> <br />
            <p> If you do not want to send this post to discord, uncheck the option above. It's only applicable when publishing new posts. </p>
        </div>
    <?php
    }

    public function save_post_meta($post_id)
    {
        if (isset($_POST['wp_discord_metabox_send_flag'])) {
            $value = 1;
        } else {
            $value = 0;
        }

        update_post_meta(
            $post_id,
            'wp_discord_send_flag',
            $value
        );
    }

    public function publish_post($id, $post)
    {
        $discord_flag = get_post_meta($id, 'wp_discord_send_flag', true);
        $post_variable = 0;

        if (isset($_POST['wp_discord_metabox_send_flag'])) 
        {
            $post_variable = 1;
        }

        if ($discord_flag == 1 || $post_variable == 1)
        {
            do_action('send_post_to_discord', $id, $post);
        }
    }
}

return new WP_Discord_Post_Plus_Metabox();
