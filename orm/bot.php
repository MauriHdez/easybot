<?php

namespace gamboamartin\easybot\models;

use PDO;
use stdClass;

class bot
{
    private $token;
    private $website;
    private $input;

    public function __construct($token, $website, $input)
    {
        $this->token = $token;
        $this->website = $website;
        $this->input = $input;

    }

    public function get()
    {
        $update = json_decode($this->input, TRUE);

        $chatId = $update['message']['chat']['id'];
        $message = $update['message']['text'];

        $datos = new stdClass();

        $datos->message = $message;
        $datos->chatId = $chatId;

        return $datos;
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
        $url = $this->website . '/sendMessage?chat_id=' . $chatId . '&parse_mode=HTML&text=' . urlencode($response);
        file_get_contents($url);
    }

}
