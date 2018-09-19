<?php
//ignore_user_abort(true);
//set_time_limit(0);
include __DIR__.'/vendor/autoload.php';

use Discord\Discord;

$discord = new Discord([
	'token' => 'NDkxOTc0OTUwODgyNzcwOTY4.DoQYlw.2b5G6rkyAA7EDWq53RpvUb3XPp8',
]);

$discord->on('ready', function ($discord) {
	echo "Bot is ready!", PHP_EOL;

	// Listen for messages.
	$discord->on('message', function ($message, $discord) {
		echo "New message from: " .  $message->author->username . "\n";

		if ($message->author->user->bot == 0)
		{
			$data = array(
				'action'    => 'discord_webhook', 
				'author' 	=> $message->author->username, 
				'is_bot' 	=> $message->author->user->bot, 
				'author_id' => $message->author->id,
				'message'  	=> $message->content, 
			);

			$URL = 'http://localhost/wp-admin/admin-ajax.php';
			$options = array(
			    'http' => array(
			        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			        'method'  => 'POST',
			        'content' => http_build_query($data)
			    )
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($URL, false, $context);
			if ($result === FALSE) {
				//echo "No response form WP Bot";
			}

			$message->reply($result);
		}
	});
});

$discord->run();