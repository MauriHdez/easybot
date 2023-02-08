<?php

namespace gamboamartin\easybot\models;

use PDO;

class bot
{
    public function __construct()
    {
        $token = '5423352510:AAHJ86F7ru7OZHXG0E4joj89ji4DmZdMZFI';
        $website = 'https://api.telegram.org/bot' . $token;

    }

    public function get()
    {
        $input = file_get_contents('php://input');
        $update = json_decode($input, TRUE);

        $chatId = $update['message']['chat']['id'];
        $message = $update['message']['text'];

        $message(message: $message, chatId: $chatId);
    }

    public function message($message, $chatId){
        switch ($message) {
            case '/start':
                $response = 'Me has iniciado';
                $this->sendMessage($chatId, $response);
                break;
            case '/info':
                $response = 'Hola! Soy @trecno_bot';
                $this->sendMessage($chatId, $response);
                break;
            default:
                $response = 'No te he entendido';
                $this->sendMessage($chatId, $response);
                break;
        }
    }

    public function sendMessage($chatId, $response)
    {
        $url = $GLOBALS['website'] . '/sendMessage?chat_id=' . $chatId . '&parse_mode=HTML&text=' . urlencode($response);
        file_get_contents($url);
    }

}
