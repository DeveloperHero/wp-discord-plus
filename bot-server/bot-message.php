<?php
//include __DIR__.'/vendor/autoload.php';

//define("BOT_TOKEN", 'NDkyMjU5ODA3MjQ4NTE1MDcy.DoT0Pg.VUy8DsyUeZ7X_fTbOmdjdjEKA44');

function send_dm($recepeint_id, $message)
{
	$discord = new \RestCord\DiscordClient(['token' => BOT_TOKEN]);
	$resp = $discord->user->createDm(['recipient_id' => $recepeint_id]);
	$discord->channel->createMessage(['channel.id' => $resp->id, 'content' => $message]);
}

//send_dm(481169882860224533, 'testing ' . rand());