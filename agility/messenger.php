<?php

// parameters
$hubVerifyToken = 'AgilityContest';
$accessToken =   "EAAbb0lTgsr0BAIzdfP6fZB9fgDcHy4ZC600ZANS3rZBiBELd2sZCNcAZCbxFidm4OXcRlqZBeeM31PomXK24Ps0U0oMZAV6X6CJpXziRk6vv9rt89H7t0NTZASRaxsOc3WPVA5cZAgYykygj4lFIltfWOpASkOl8ZBpwyPbDohchOiUHwZDZD";

// check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}

// handle bot's anwser
$input = json_decode(file_get_contents('php://input'), true);
$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
$response = null;

//set Message
if($messageText == "hi") {
  $answer = "Hello";
}

//send message to facebook bot
$response = [
    'recipient' => [ 'id' => $senderId ],
    'message' => [ 'text' => $answer ]
];

$ch = curl_init('https://graph.facebook.com/v2.9/me/messages?access_token='.$accessToken);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
if(!empty($input)){
    $result = curl_exec($ch);
}
curl_close($ch);
