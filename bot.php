<?php

require "init.php";
require 'vendor/autoload.php';

use base\conexion;
use config\generales;
use gamboamartin\easybot\models\easy_servicio;
use gamboamartin\errores\errores;

$con = new conexion();
$link = conexion::$link;

$token = '5423352510:AAHJ86F7ru7OZHXG0E4joj89ji4DmZdMZFI';
$website = 'https://api.telegram.org/bot'.$token;

$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

/*
$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];
*/
$chatId = '5655914615';
$message = 'agendar una cita';


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
        $respuesta = json_decode($response);
        $response = $respuesta->queryResult->responseMessages[0]->text->text[0];
        $resultado = acciones_bd($respuesta, $link);
        //$response = $response.$resultado;
        sendMessage($chatId, $response);
        break;
}

function sendMessage($chatId, $response) {
    $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
}


function getResponse($message){
    $json = '{
          "queryInput": {
            "text": {
              "text": "'. $message .'"
            },
            "languageCode": "en"
          },
          "queryParameters": {
            "timeZone": "America/Los_Angeles",
          }
        }';

    $ch = curl_init('https://us-central1-dialogflow.googleapis.com/v3/projects/easyacces-378204/locations/us-central1/agents/0e785973-9af6-431b-bbc6-e5b351364eb8/sessions/SESSION_ID:detectIntent');

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ya29.a0AVvZVsp-Vp1zP6B4cVH-HytLplpsxHPNeENgHqWJhP04loncPGVLfyyx68DVfJESHUiZm7UmE1WHik9b4-4-CQiqbkV7ZwfECsh28uxMukXVJTUHJMRjTlK_227Ix2zlKtgrnWtHRKyPOrn4Lh_ds2TcG7IucihoRO1E0ToaCgYKATESARESFQGbdwaIWYaBrcztQJLyOzkwsOtvWw0174', 'x-goog-user-project: easyacces-378204','Content-Type: application/json; charset=utf-8', ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function acciones_bd($repuesta, $link){
    if($repuesta->queryResult->intent->displayName = "servicios"){
        $servicios = (new easy_servicio($link))->registros_activos();
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error obtener registros',data:  $servicios);
            print_r($error);
            die('Error');
        }
        print_r($servicios);exit;
        $text_servicios = '';
        foreach ($servicios as $servicio){
            $text_servicios = $servicio['easy_servicio_nombre'];
        }

        return $text_servicios;
    }
    return "default";
}

?>
