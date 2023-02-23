<?php

use gamboamartin\easybot\models\bot;


$token = '5423352510:AAHJ86F7ru7OZHXG0E4joj89ji4DmZdMZFI';
$website = 'https://api.telegram.org/bot'.$token;

$bot = new bot(token: $token, website: $website);


$datos = $bot->get();

$bot->message(message: $datos->message, chatId: $datos->chatId);

?>