<?php

use config\generales;


$token = '5423352510:AAHJ86F7ru7OZHXG0E4joj89ji4DmZdMZFI';
$website = 'https://api.telegram.org/bot'.$token;


$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];


switch($message) {
    case '/start':
        $response = 'Me has iniciado';
        sendMessage($chatId, $response);
        break;
    case '/info':
        $response = 'Hola! Soy @trecno_bot';
        sendMessage($chatId, $response);
        break;
    default:
        $response = getResponse(message: $message);
        $response = json_decode($response)->queryResult->responseMessages[0]->text->text[0];
        sendMessage($chatId, $response);
        break;
}

function sendMessage($chatId, $response) {
    $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
}

function getResponse($message){
    $generales = new generales();
    $json = '{
          "queryInput": {
            "text": {
              "text": "'. $message .'"
            },
            "languageCode": "en"
          },
          "queryParams": {
            "timeZone": "America/Los_Angeles"
          }
        }';

    $ch = curl_init('https://us-central1-dialogflow.googleapis.com/v3/projects/easyacces-378204/locations/us-central1/agents/0e785973-9af6-431b-bbc6-e5b351364eb8/sessions/SESSION_ID:detectIntent');

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $generales->google_auth, 'x-goog-user-project: easyacces-378204','Content-Type: application/json; charset=utf-8', ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


?>