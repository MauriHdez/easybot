<?php
$update_response = file_get_contents("php://input");
$update = json_decode($update_response, true);
/*$res = '{  "fulfillmentResponse":{     "messages":[        {           "text":{              "text":[                 "{"detectIntentResponseId":"d6bb4a28-b37f-4733-8c81-383173f12c46",
"intentInfo":{
    "lastMatchedIntent":"projects/easyacces-378204/locations/us-central1/agents/0e785973-9af6-431b-bbc6-e5b351364eb8/intents/60cd0d61-a049-48bd-af1f-c57a19a4ce63",
    "displayName":"agendar.cita",
    "confidence":0.84075046},
"pageInfo":{
    "currentPage":"projects/easyacces-378204/locations/us-central1/agents/0e785973-9af6-431b-bbc6-e5b351364eb8/flows/00000000-0000-0000-0000-000000000000/pages/aafa1f70-a2cf-4040-a5f8-caf4078d7a77",
    "displayName":"servicios"},
"sessionInfo":{"session":"projects/easyacces-378204/locations/us-central1/agents/0e785973-9af6-431b-bbc6-e5b351364eb8/sessions/3369e7-12d-e2b-d1d-da73fa62e",
"parameters":{"number":1}},"fulfillmentInfo":{"tag":"servicios"},"messages":[{"text":{"text":["Claro tenemos estos servicios disponibles."],"redactedText":["Claro tenemos estos servicios disponibles."]},"responseType":"ENTRY_PROMPT","source":"VIRTUAL_AGENT"}],"text":"agendar cita","languageCode":"es"}"              ]           }        }     ]  }}';
$update = json_decode($res, true);
print_r($update);*/
//$update = json_encode($update);
$json = '{
   "fulfillmentResponse":{
      "messages":[
         {
            "text":{
               "text":[
                  "'.$update['fulfillmentInfo']['tag'].$update['sessionInfo']['parameters']['number'].'"
               ]
            }
         }
      ]
   }
}';

echo $json;
/*$parameters = array("fulfillment_response" => array("messages" => array("text"=>array("text"=>"en"))));
echo json_encode($parameters);*/
/*function processMessage($update) {
    //if($update["result"]["action"] == "sayHello"){
        sendMessage(array(
            "source" => $update["result"]["source"],
            "speech" => "Hello from webhook",
            "displayText" => "Hello from webhook",
            "contextOut" => array()
        ));
    //}
}

function sendMessage($parameters) {
    echo json_encode($parameters);
}

$update_response = file_get_contents("php://input");
$update = json_decode($update_response, true);
//if (isset($update["result"]["action"])) {
    processMessage($update);
//}*/