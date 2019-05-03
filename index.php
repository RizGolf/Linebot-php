<?php
require_once('./vendor/autoload.php');

include "./src/setup-database.php";
include "./src/question.php";
include "./src/user.php";

// Project Namespace
use LineBotExamPostgreSQL\Database as Database;
use LineBotExamUser\User as User;
use LineBotExamQuestion\Question as Question;

// Line Namespace
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;

$channel_token = '991qpaGTUL0XF50XYeBaLyNj2ZIjtQdf2alfMlIjs0Kq/e6kh5MCEnuAbQE0HyAPgz049M0k3KwdSObL3uwIzVVObwZvCBKEfufsEiKedWmhFPtCve3r2uP7jcMN6mzficDTW+f2RGR+ZWKNMy8dIAdB04t89/1O/w1cDnyilFU=';
$channel_secret = '9c55e495d3111033d3414d2ece36e079';

// Get message from Line API
$content = file_get_contents('php://input');
$events = json_decode($content, true);

$host = 'localhost';
$dbname = 'linebot_db';
$user = 'root';
$pass = '';
$connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

// New class to use method in class
$dbCreator = new Database($connection);
$userCreator = new User($connection);
$questionCreator = new Question($connection);

if (!is_null($events['events'])) {
  foreach ($events['events'] as $event) {
    error_log("============================== Event ==============================");

    $userToken = $event['source']['userId'];
    $replyToken = $event['replyToken'];

    if ($event['type'] == 'unfollow') {
      // Delet user token when user block line bot
      $userCreator->deleteUser($userToken);
      error_log('Delete user from database when that user unfollow line bot: ' .$userToken);
      error_log("============================== UNFOLLOW ==============================");
    } else {
      if ($event['type'] == 'follow') {
        // First user add line bot
        $respMessage = "ยินดีต้อนรับเข้าสู่ห้องสอบออนไลน์ 📝 จะมีคำถามทั้งหมด 10 ข้อ ถ้าคุณตอบคำถามครบแล้ว จะมีสรุปผลคะแนนที่คุณทำได้บอกไว้หลังจากการตอบคำถามสุดท้ายเสร็จสิ้น มาเริ่มคำถามแรกกันเลย \n\nกรุณาพิมพ์ข้อความคำว่า \"Startquiz\" เพื่อเริ่มทำข้อสอบ";
        error_log("============================== FOLLOW ==============================");
      } else if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
        // Check user already answered 10 questions
        $sql = sprintf("SELECT * FROM users WHERE token='%s' ", $event['source']['userId']);
        $result = $connection->query($sql);

        if ($result != false && $result->rowCount() == 11) {
          if (
            $event['message']['text'] == '1' || $event['message']['text'] == '2' ||
            $event['message']['text'] == '3' || $event['message']['text'] == '4'
          ) {
              $userAnswer = $event['message']['text'];
              $lastUserData = $userCreator->getLastUser($userToken);
              error_log("last user id -> ".$lastUserData['id']);
              $updateUserAnswer = $userCreator->updateUser($lastUserData['id'], $userAnswer);
              error_log('Insert user token: ' . $userToken . ' | with id: ' .$userId);

              // Insert user complete exam by nine hundred ninety nine number
              $userId = $userCreator->insertUser($userToken, 999, 999);
              error_log('Insert user complete exam with token: ' . $userToken . ' | with id: ' .$userId);

              $score = $questionCreator->calculateQuestions($userToken);
              $respMessage = 'จำนวนข้อที่คุณตอบถูกทั้งหมด '.$score.' ข้อ';
          } else {
            $respMessage =  "กรุณากดคำตอบให้ถูกต้อง สามารถตอบได้เฉพาะหมายเลข 1 - 4 เท่านั้น";
          }
        } else if ($result != false && $result->rowCount() == 12) {
            $score = $questionCreator->calculateQuestions($userToken);
            $respMessage = 'คุณได้ตอบคำถามครบ 10 ข้อแล้ว คะแนนของคุณคือ '.$score. ' คะแนน';
        } else {
          $questionData = $questionCreator->getQuestion($userToken);
          error_log("Receive question data with id: ".$questionData['id']);

          if (($event['message']['text'] == "Startquiz" || $event['message']['text'] == "startquiz") && $result->rowCount() == 0) {
            // insert user already by zero number
            $userId = $userCreator->insertUser($userToken, 0, 0);
            error_log('Insert user already with token: ' . $userToken . ' | with id: ' .$userId);
            if ($questionData != false) {
              // Prepare insert first answer
              $userId = $userCreator->insertUser($userToken, 99, $questionData["id"]);
              error_log('Insert user token: ' . $userToken . ' | with id: ' .$userId);

              $respMessage = $questionData["title"];
            } else {
              $respMessage = 'เกิดข้อผิดพลาด ในการดึงข้อมูลคำถาม ขออภัยในความไม่สะดวก';
            }

          } else if ($result->rowCount() > 0 && $result->rowCount() < 11) {
              switch($event['message']['text']) {
                case '1':
                  $userAnswer = 1;
                  if ($questionData != false) {
                    $lastUserData = $userCreator->getLastUser($userToken);
                    error_log("last user id -> ".$lastUserData['id']);
                    $updateUserAnswer = $userCreator->updateUser($lastUserData['id'], $userAnswer);
                    $userId = $userCreator->insertUser($userToken, $userAnswer, $questionData["id"]);
                    error_log('Insert user token: ' . $userToken . ' | with id: ' .$userId);
                    $respMessage = $questionData["title"];
                  } else {
                    $respMessage = 'เกิดข้อผิดพลาด ในการดึงข้อมูลคำถาม ขออภัยในความไม่สะดวก';
                  }
                break;
                case '2':
                  $userAnswer = 2;
                  if ($questionData != false) {
                    $lastUserData = $userCreator->getLastUser($userToken);
                    error_log("last user id -> ".$lastUserData['id']);
                    $updateUserAnswer = $userCreator->updateUser($lastUserData['id'], $userAnswer);
                    $userId = $userCreator->insertUser($userToken, $userAnswer, $questionData["id"]);
                    error_log('Insert user token: ' . $userToken . ' | with id: ' .$userId);
                    $respMessage = $questionData["title"];
                  } else {
                    $respMessage = 'เกิดข้อผิดพลาด ในการดึงข้อมูลคำถาม ขออภัยในความไม่สะดวก';
                  }
                break;
                case '3':
                  $userAnswer = 3;
                  if ($questionData != false) {
                    $lastUserData = $userCreator->getLastUser($userToken);
                    error_log("last user id -> ".$lastUserData['id']);
                    $updateUserAnswer = $userCreator->updateUser($lastUserData['id'], $userAnswer);
                    $userId = $userCreator->insertUser($userToken, $userAnswer, $questionData["id"]);
                    error_log('Insert user token: ' . $userToken . ' | with id: ' .$userId);
                    $respMessage = $questionData["title"];
                  } else {
                    $respMessage = 'เกิดข้อผิดพลาด ในการดึงข้อมูลคำถาม ขออภัยในความไม่สะดวก';
                  }
                break;
                case '4':
                  $userAnswer = 4;
                  if ($questionData != false) {
                    $lastUserData = $userCreator->getLastUser($userToken);
                    error_log("last user id -> ".$lastUserData['id']);
                    $updateUserAnswer = $userCreator->updateUser($lastUserData['id'], $userAnswer);
                    $userId = $userCreator->insertUser($userToken, $userAnswer, $questionData["id"]);
                    error_log('Insert user token: ' . $userToken . ' | with id: ' .$userId);
                    $respMessage = $questionData["title"];
                  } else {
                    $respMessage = 'เกิดข้อผิดพลาด ในการดึงข้อมูลคำถาม ขออภัยในความไม่สะดวก';
                  }
                break;            
                default:
                  $respMessage =  "กรุณากดคำตอบให้ถูกต้อง สามารถตอบได้เฉพาะหมายเลข 1 - 4 เท่านั้น";
                  break;   
              }
          } else {
            $respMessage = 'กรุณาพิมพ์ข้อความคำว่า "Startquiz" เพื่อเริ่มทำข้อสอบ';
          }
          error_log("============================== Message ==============================");
        }
      }

      $httpClient = new CurlHTTPClient($channel_token);
      $bot = new LINEBot($httpClient, array('channelSecret' => $channel_secret));  
      $textMessageBuilder = new TextMessageBuilder($respMessage); 
      $response = $bot->replyMessage($replyToken, $textMessageBuilder);

    }
  }
}

echo 'Running Kmutnb Exam Line Bot Success';
