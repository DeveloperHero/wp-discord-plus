<?php
/**
 * WP Discord Bot
 *
 * @author     M Yakub Mizan
 * @license     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for the admin settings of WP Discord Post.
 */
class WP_Discord_Bot {

	public function __construct()
	{
		add_action( 'wp_ajax_discord_webhook', array($this, 'receive_message_from_discord'));
		add_action( 'wp_ajax_nopriv_discord_webhook', array($this, 'receive_message_from_discord'));
	}

	public function receive_message_from_discord()
	{
		$message = trim($_POST['message']);

		if (!empty($_POST['author_id']) && !empty($_POST['author']) && !empty($_POST['message']))
		{
			if (substr($message, 0, 6) == '!claim')
			{
				$this->claim_order();
			}

			if (substr($message, 0, 9) == '!complete' || substr($message, 0, 10) == '!completed')
			{
				$this->complete_order();
			}

		}

		wp_die(); // this is required to terminate immediately and return a proper response
	}

	public function claim_order()
	{

		$message = explode(" ", trim($_POST['message']));

		if (count($message) >=2)
		{
			$order = wc_get_order((int) $message[1]);

			if (!$order)
			{
				echo "Order ID didn't match.";
				wp_die();
			}

			if ($order->get_status() == 'completed')
			{
				echo "Order has been completed already.";
				wp_die();
			}

			if ($order->get_status() == 'boosting')
			{
				echo "Order has been claimed already.";
				wp_die();
			}

			if (($current_boosting_order = get_option("discord_" . $_POST['author_id'])))
			{
				echo "Currently claimed order: " . $current_boosting_order;
				wp_die();
			}

			if ($order)
			{
				$order->set_status('boosting');
				$order->save();
				update_option("discord_" . $_POST['author_id'], $message[1]);


				$order_data = $order->get_data();
				$extra_tags = array();

				$output = "\n";

				foreach($order_data['meta_data'] as $m)
				{
					if ($m->key == 'billing_username' ||
						$m->key == 'billing_password' ||
						$m->key == 'billing_gamertag_field' ||
						$m->key == 'billing_gamertag'
					)
					{
						$extra_tags[$m->key] = $m->key . ": " . $m->value . "\n";
					}
				}

				foreach($extra_tags as $ex)
				{
					$output .= $ex;
				}

				$output .= 'Customer Notes: ' . $order->customer_note . "\n";

				echo $output;
			}
		} else {
			wp_die('Order id missing');
		}

	}

	public function complete_order()
	{

		$current_boosting_order = get_option("discord_" . $_POST['author_id']);
		
		$order = wc_get_order($current_boosting_order);

		if ($order->get_id())
		{
			echo "You have currently no order claimed.";
			wp_die();
		}

		$order->set_status('completed');
		$order->save();
		$author = delete_option("discord_" . $_POST['author_id']);
		echo "Order #" . $current_boosting_order. " completed\n";
	}
}

new WP_Discord_Bot();