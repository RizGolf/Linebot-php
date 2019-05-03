<?php

  include "setup-database.php";

  use LineBotExamPostgreSQL\CreateTables as CreateTables;

  // Database data
  $host = '202.44.32.110';
  $dbname = 'linebot_db';
  $user = 'Linebot_Exam';
  $pass = '_3wq3Oh1';

  error_log("host: " .$host. "\n");

  // connect to the PostgreSQL database
  $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

  // Check connection
  #if ($conn->connect_error) {
      #die("Connection failed: " . $conn->connect_error);
  #} 

  try {
    
    // Pass connection to pdo in setup.database.php file
    $dbCreator = new CreateTables($conn);
    
    // create tables and query the table from the
    // database
    $tables = $dbCreator->createTables()->getTables();

    foreach ($tables as $table){
        echo "Create table: " .$table . ' Success <br>';
    }

    // insert questions
    // $id = $dbCreator->insertQuestions('Insert question', 2);
    // echo 'The question has been inserted with the id ' . $id . '<br>';

    // insert a list of stocks into the stocks table
    $list = $dbCreator->insertQuestionList([
      ['title' => '2+2= ?
      กด 1. 1  
      กด 2. 4  
      กด 3. 5  
      กด 4. 6 ', 'correctAnswer' => 2],
      ['title' => '5x5= ?
      กด 1. 10
      กด 2. 20
      กด 3. 25
      กด 4. 30', 'correctAnswer' => 3],
      ['title' => 'อวัยวะส่วนใดในร่างกายใช้สำหรับดมกลิ่น ?
      กด 1. หู
      กด 2. ตา
      กด 3. ปาก
      กด 4. จมูก', 'correctAnswer' => 4],
      ['title' => 'วันที่ 13 เมษายน ของทุกปี คือเทศกาลอะไร ?
      กด 1. สงกรานต์
      กด 2. ลอยกระทง
      กด 3. คริสต์มาส
      กด 4. ตรุษจีน', 'correctAnswer' => 1],
      ['title' => 'ไฟจราจรมีกี่สี ?
      กด 1. 2 สี
      กด 2. 5 สี
      กด 3. 1 สี
      กด 4. 3 สี', 'correctAnswer' => 4],
      ['title' => '6-3= ?
      กด 1. 1
      กด 2. 3
      กด 3. 9
      กด 4. 2', 'correctAnswer' => 2],
      ['title' => 'สัตว์ชนิดใดถูกจัดประเภทให้เป็นสัตว์เลื้อยคลาน ?
      กด 1. งู
      กด 2. สุนัข
      กด 3. แมว
      กด 4. มนุษย์', 'correctAnswer' => 1],
      ['title' => 'สุนัขมีกี่ขา ?
      กด 1. 1 ขา
      กด 2. 4 ขา
      กด 3. 3 ขา
      กด 4. 2 ขา', 'correctAnswer' => 2],
      ['title' => 'มนุษย์มีกี่ขา ?
      กด 1. 1 ขา
      กด 2. 2 ขา
      กด 3. 3 ขา
      กด 4. 4 ขา', 'correctAnswer' => 2],
      ['title' => '9/3= ?
      กด 1. 1
      กด 2. 2
      กด 3. 3
      กด 4. 4', 'correctAnswer' => 3]
    ]);

    foreach ($list as $id) {
        echo 'The question has been inserted with the id ' . $id . '<br>';
    }
    
  } catch (\PDOException $e) {
      error_log("===>>> Error: " .$e->getMessage(). "\n");
      echo $e->getMessage();
  }

  echo "Line Bot Execute Success"

?>
