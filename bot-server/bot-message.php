<?php
//include __DIR__.'/vendor/autoload.php';

function send_dm($recepeint_id, $message)
{
	$bot_token = get_option('wp_discord_post_bot_token');
	$discord = new \RestCord\DiscordClient(['token' => $bot_token ]);
	$resp = $discord->user->createDm(['recipient_id' => $recepeint_id]);
	$discord->channel->createMessage(['channel.id' => $resp->id, 'content' => $message]);
}