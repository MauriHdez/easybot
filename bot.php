<?php

require "init.php";
require 'vendor/autoload.php';

use base\conexion;
use config\generales;
use gamboamartin\administrador\models\adm_session;
use gamboamartin\easybot\models\easy_cita;
use gamboamartin\easybot\models\easy_cliente;
use gamboamartin\easybot\models\easy_etapa_cita;
use gamboamartin\easybot\models\easy_horario;
use gamboamartin\easybot\models\easy_servicio;
use gamboamartin\errores\errores;

$con = new conexion();
$link = conexion::$link;

$token = '5423352510:AAHJ86F7ru7OZHXG0E4joj89ji4DmZdMZFI';
$website = 'https://api.telegram.org/bot'.$token;

$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);


$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];

/*
$chatId = '5655914615';
$message = 'Si';
*/

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

        //Respuesta normal de dialogflow
        $response = $respuesta->queryResult->responseMessages[0]->text->text[0];

        //Acciones de base de datos
        $resultado = acciones_bd($respuesta, $link);
        $response = $response."\n".$resultado;

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
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ya29.a0Ael9sCOGj6XZMlpAYs_gddF37JaaoWGBTfEqeTyWVepjGXU5Z3EzCQG9iQkTOd6rcqIQYpjmOCavKgEQTFcpOUvCLk1kaXd6hju-4LbMDlNwVGhCRuYxoyD2omzkkqcd1Gpyv6IgjdaK9sIiRxPt_sOZ7ZXUmAqNIztW1CgaCgYKARISARESFQF4udJhgO9MsX5yL4g_szEaJLCV3g0174', 'x-goog-user-project: easyacces-378204','Content-Type: application/json; charset=utf-8', ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function acciones_bd($respuesta, $link){
    /*
     * $respuesta->queryResult->intent->displayName  =  intento
     * */

    /*if($respuesta->queryResult->intent->displayName === "agendar.cita"){
        $servicios = (new easy_servicio($link))->registros_activos();
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error obtener registros',data:  $servicios);
            print_r($error);
            die('Error');
        }

        $text_servicios = '';
        foreach ($servicios as $servicio){
            $text_servicios = "     - ".$servicio['easy_servicio_nombre']."\n";
        }

        return $text_servicios;
    }*/


    if($respuesta->queryResult->intent->displayName === "horarios") {
        $dia = $respuesta->queryResult->parameters->fecha_cita->day;
        $year = $respuesta->queryResult->parameters->fecha_cita->year;
        $mes = $respuesta->queryResult->parameters->fecha_cita->month;

        $fecha = $year."-".$mes."-".$dia;
        $date = date_create($fecha);
        $fecha = date_format($date,"Y-m-d");

        $dias = array('DOMINGO','LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO');
        $dia_semana = $dias[date('N', strtotime($fecha))];

        $filtro_horarios['easy_dia_semana.descripcion'] = $dia_semana;
        $horarios = (new easy_horario($link))->filtro_and(filtro: $filtro_horarios);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error obtener registros',data:  $horarios);
            print_r($error);
            die('Error');
        }
        
        $filtro_citas['easy_cita.fecha_cita'] = $fecha;
        $filtro_citas['easy_status_cita.descripcion'] = 'agendada';
        $citas = (new easy_etapa_cita($link))->filtro_and(filtro: $filtro_citas);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error obtener registros',data:  $citas);
            print_r($error);
            die('Error');
        }

        $text_horarios = '';
        if($horarios->n_registros < 1){
            return 'Una disculpa para ese dia no existen horarios';
        }

        $res_disponibles = array();
        if($citas->n_registros > 0){
            foreach ($horarios->registros as $horario){
                $existe = false;
                foreach ($citas->registros as $cita){
                    if($horario['easy_horario_id'] === $cita['easy_horario_id']){
                        $existe = true;
                    }
                }
                if (!$existe){
                    $res_disponibles[] = $horario;
                }
            }

            foreach ($res_disponibles as $res_disponible){
                $text_horarios .= "     - ".$res_disponible['easy_horario_descripcion']."\n";
            }

            return $text_horarios;
        }

        foreach ($horarios->registros  as $horario){
            $text_horarios .= "     - ".$horario['easy_horario_descripcion']."\n";
        }

        return $text_horarios;
    }

    if($respuesta->queryResult->intent->displayName === "ingresa.nombre") {
        $dia = $respuesta->queryResult->parameters->fecha_cita->day;
        $year = $respuesta->queryResult->parameters->fecha_cita->year;
        $mes = $respuesta->queryResult->parameters->fecha_cita->month;

        $fecha = $year."-".$mes."-".$dia;
        $date = date_create($fecha);
        $fecha = date_format($date,"Y-m-d");

        $dias = array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado');
        $dia_semana = $dias[date('N', strtotime($fecha))];

        $meses = array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
            '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
        $mes = $meses[date('m', strtotime($fecha))];

        $hora_inicio = $respuesta->queryResult->parameters->hora_inicio->hours;
        $hora_fin = $respuesta->queryResult->parameters->hora_fin->hours;

        $nombre = $respuesta->queryResult->parameters->nombre;

        return $dia_semana." ".$dia." de ".$mes." del ".$year.", de ".$hora_inicio.":00 a ".
            $hora_fin.":00 a nombre de ".$nombre.", me podria confirmar? por favor";
    }

    if($respuesta->queryResult->intent->displayName === "cita.confirmada") {
        $respuesta_confir = $respuesta->queryResult->parameters->respuesta_confir;

        $respuesta_confir = strtolower($respuesta_confir);
        if($respuesta_confir === 'si'){
            $session = (new adm_session($link))->carga_data_session();
            if(errores::$error){
                $error = (new errores())->error(mensaje: 'Error al asignar session',data: $session);
                print_r($error);
                die('Error');

            }
            $_SESSION['activa'] = 1;
            $_SESSION['grupo_id'] = '2';
            $_SESSION['usuario_id'] ='2';

            $registro_cliente['nombre'] = $respuesta->queryResult->parameters->nombre;
            $registro_cliente['adm_genero_id'] = "3";
            $easy_cliente = (new easy_cliente($link))->alta_registro(registro: $registro_cliente);
            if(errores::$error){
                $error = (new errores())->error(mensaje: 'Error insertar cliente',data:  $easy_cliente);
                print_r($error);
                die('Error');
            }
print_r($easy_cliente);exit;
            $dia = $respuesta->queryResult->parameters->fecha_cita->day;
            $year = $respuesta->queryResult->parameters->fecha_cita->year;
            $mes = $respuesta->queryResult->parameters->fecha_cita->month;

            $fecha = $year."-".$mes."-".$dia;
            $date = date_create($fecha);
            $fecha = date_format($date,"Y-m-d");

            $hora_inicio = $respuesta->queryResult->parameters->hora_inicio->hours;
            $min_inicio = $respuesta->queryResult->parameters->hora_inicio->minutes;
            $seg_inicio = $respuesta->queryResult->parameters->hora_inicio->seconds;
            $horario_inicio = $hora_inicio.':'.$min_inicio.':'.$seg_inicio;
            $horario_inicio =date($horario_inicio,"H:i:s");

            $hora_fin = $respuesta->queryResult->parameters->hora_fin->hours;
            $min_fin = $respuesta->queryResult->parameters->hora_fin->minutes;
            $seg_fin = $respuesta->queryResult->parameters->hora_fin->seconds;
            $horario_fin = $hora_fin.':'.$min_fin.':'.$seg_fin;
            $horario_fin =date($horario_fin,"H:i:s");

            $filtro_horarios_cita['easy_horario.hora_inicio'] = $horario_inicio;
            $filtro_horarios_cita['easy_horario.hora_fin'] = $horario_fin;
            $horarios = (new easy_horario($link))->filtro_and(filtro: $filtro_horarios_cita);
            if(errores::$error){
                $error = (new errores())->error(mensaje: 'Error obtener registros',data:  $horarios);
                print_r($error);
                die('Error');
            }

            $registro_cita['fecha_cita'] = $fecha;
            $registro_cita['easy_horario_id'] = $horarios->registros[0]->easy_horario_id;
            $registro_cita['easy_cliente_id'] = $easy_cliente->registro_id;
            $easy_cita = (new easy_cita($link))->alta_registro(registro: $registro_cita);
            if(errores::$error){
                $error = (new errores())->error(mensaje: 'Error al insertar registro cita',data:  $easy_cita);
                print_r($error);
                die('Error');
            }            
            
            $registro_etapa_cita['easy_status_cita_id'] = '1';
            $registro_etapa_cita['easy_cita_id'] = $easy_cita->registro_id;
            $easy_etapa_cita = (new easy_etapa_cita($link))->alta_registro(registro: $registro_etapa_cita);
            if(errores::$error){
                $error = (new errores())->error(mensaje: 'Error al insertar registro etapa_cita',data:  $easy_etapa_cita);
                print_r($error);
                die('Error');
            }
        }
    }
    /*$filtro['easy_telegram.id_telegram_message'] = '';
    $filtro['easy_status_cita.descripcion'] = 'agendada';
    $citas_activas = (new easy_etapa_cita($link))->filtro_and(filtro: $filtro);
    if(errores::$error){
        $error = (new errores())->error(mensaje: 'Error obtener registros cita',data:  $citas_activas);
        print_r($error);
        die('Error');
    }*/

    return "";
}

?>
