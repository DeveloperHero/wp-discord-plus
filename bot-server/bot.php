<?php
ignore_user_abort(true);
set_time_limit(0);
include __DIR__.'/vendor/autoload.php';
include __DIR__.'/bot-message.php';

use Discord\Discord;

$bot_token = get_option('wp_discord_post_bot_token');

if (empty($bot_token))
{
	wp_die('Bot token missing.');
}

$discord = new Discord([
	'token' => $bot_token
]);

echo "Bot start: " . time() . "\n";
error_log("Bot start: " . time() . "\n");

$discord->on('ready', function ($discord) {
	echo "Bot listening for new message", PHP_EOL;

	// Listen for messages.
	$discord->on('message', function ($message, $discord) {
		echo "User: " .  $message->author->username . "\n";
		echo "User ID: " .  $message->author->id . "\n";
		echo "Message: " .  $message->content . "\n";

		if ($message->author->user->bot == 0)
		{
			$data = array(
				'action'    => 'discord_webhook', 
				'author' 	=> $message->author->username, 
				'is_bot' 	=> $message->author->user->bot, 
				'author_id' => $message->author->id,
				'message'  	=> $message->content, 
			);

			$options = array(
			    'http' => array(
			        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			        'method'  => 'POST',
			        'content' => http_build_query($data)
			    )
			);

			$context  = stream_context_create($options);

			try {

				$wp_discord_bot_url = admin_url( 'admin-ajax.php' );
				$result = file_get_contents($wp_discord_bot_url, false, $context);
				echo "Response Received: " . $result . "\n";

				if ($result === FALSE) {
					echo "No response received from WP Discord\n";
				}

				if ($result != 'null' || !empty($result))
				{
					//$message->reply($result);
					//$message->author->send('testing');
					send_dm((int) $message->author->id, $result);
				}

			} catch(Exception $e) {
				echo "WP Error: " . $e->getMessage() . "\n";
			}
		}
	});
});

$discord->run();
