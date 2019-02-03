<?php
require_once('./vendor/autoload.php');

// Namespace
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;

$channel_token = 'Loscy5DKyMHfMjso60zbBEybOY3oBqn5MSFNVsnGt/l6LNW5llEaldDMkv/egE1tgz049M0k3KwdSObL3uwIzVVObwZvCBKEfufsEiKedWmiC+qXtrnGIkwEzdddQ/IUYyVpCHBY+AgjINXJFyLGGAdB04t89/1O/w1cDnyilFU=';
$channel_secret = '9c55e495d3111033d3414d2ece36e079';

// Get message from Line API
$content = file_get_contents('php://input');
$events = json_decode($content, true);

if (!is_null($events['events'])) {
  // Loop through each event
  foreach ($events['events'] as $event) {
      // Line API send a lot of event type, we interested in message only.
      if ($event['type'] == 'message') {
        switch($event['message']['type']) {
          case 'text':
          // Get replyToken
          $replyToken = $event['replyToken'];
        
          // Reply message
          $respMessage = 'Hello, your message is '. $event['message']['text'];
            
          $httpClient = new CurlHTTPClient($channel_token);
          $bot = new LINEBot($httpClient, array('channelSecret' => $channel_secret));  
          $textMessageBuilder = new TextMessageBuilder($respMessage); 
          $response = $bot->replyMessage($replyToken, $textMessageBuilder);  
        break;
        }
      }
  }
}
echo 'OK';
