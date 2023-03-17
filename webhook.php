<?php
/*
 * $update['fulfillmentInfo']['tag'] = intento
 * $update['sessionInfo']['parameters']['number'] = forma de obtener parametros
 * */
function processMessage($update) {
    $json = '{
       "fulfillmentResponse":{
          "messages":[
             {
                "text":{
                   "text":[
                      "Respuesta default"
                   ]
                }
             }
          ]
       }
    }';
    if($update['fulfillmentInfo']['tag'] == "servicios"){
        $json = '{
           "fulfillmentResponse":{
              "messages":[
                 {
                    "text":{
                       "text":[
                          "'.$update['fulfillmentInfo']['tag'].'"
                       ]
                    }
                 }
              ]
           }
        }';
    }
    echo $json;
}

$update_response = file_get_contents("php://input");
$update = json_decode($update_response, true);
    $json = '{
       "fulfillmentResponse":{
          "messages":[
             {
                "text":{
                   "text":[
                      "'.$update['sessionInfo']['parameters']['chatid'].'"
                   ]
                }
             }
          ]
       }
    }';
echo $json;
//processMessage($update);