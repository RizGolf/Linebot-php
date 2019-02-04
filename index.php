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
   
      // Get replyToken
      $replyToken = $event['replyToken'];
      $ask = $event['message']['text'];
    
          switch(strtolower($ask)) {
              case 'm':
                $respMessage = 'What sup man. Go away!';
                break;
              case 'f':
                $respMessage = 'Love you lady.';
                break;
              default:
                $respMessage = 'What is your sex? M or F';
                break;
              
              
              
          $httpClient = new CurlHTTPClient($channel_token);
          $bot = new LINEBot($httpClient, array('channelSecret' => $channel_secret));  
          $textMessageBuilder = new TextMessageBuilder($respMessage); 
          $response = $bot->replyMessage($replyToken, $textMessageBuilder);  
        break;
        }
      }
  }
echo 'OK';
