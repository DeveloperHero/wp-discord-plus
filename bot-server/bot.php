<?php
//ignore_user_abort(true);
//set_time_limit(0);
include __DIR__.'/vendor/autoload.php';

define("BOT_URL", 'http://localhost/wp-admin/admin-ajax.php');
define("BOT_TOKEN", 'NDkyMjU5ODA3MjQ4NTE1MDcy.DoT0Pg.VUy8DsyUeZ7X_fTbOmdjdjEKA44');

use Discord\Discord;

$discord = new Discord([
	'token' => BOT_TOKEN
]);

$discord->on('ready', function ($discord) {
	echo "Bot listening for new message", PHP_EOL;

	// Listen for messages.
	$discord->on('message', function ($message, $discord) {
		echo "New message from: " .  $message->author->username . "\n";
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

				$result = file_get_contents(BOT_URL, false, $context);
				echo "Response Received: " . $result . "\n";

				if ($result === FALSE) {
					echo "No response received from WP Discord\n";
				}

				if ($result != 'null' || !empty($result))
				{
					$message->reply($result);
				}

			} catch(Exception $e) {
				echo "WP Error: " . $e->getMessage() . "\n";
			}
		}
	});
});

$discord->run();
