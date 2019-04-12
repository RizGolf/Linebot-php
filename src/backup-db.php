<?php

  include "setup-database.php";
  use LineBotExamPostgreSQL\CreateTables as CreateTables;

  // Database data
  $host = '';
  $dbname = '';
  $user = '';
  $pass = '';

  error_log("host: " .$host. "\n");

  // connect to the PostgreSQL database
  $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);

  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  } 

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
      ['title' => 'Question 1', 'correctAnswer' => 2],
      ['title' => 'Question 2', 'correctAnswer' => 3],
      ['title' => 'Question 3', 'correctAnswer' => 4],
      ['title' => 'Question 4', 'correctAnswer' => 1],
      ['title' => 'Question 5', 'correctAnswer' => 4],
      ['title' => 'Question 6', 'correctAnswer' => 2],
      ['title' => 'Question 7', 'correctAnswer' => 1],
      ['title' => 'Question 8', 'correctAnswer' => 2],
      ['title' => 'Question 9', 'correctAnswer' => 2],
      ['title' => 'Question 10', 'correctAnswer' => 3]
    ]);

    foreach ($list as $id) {
        echo 'The question has been inserted with the id ' . $id . '<br>';
    }
    
  } catch (\PDOException $e) {
      error_log("===>>> Error: " .$e->getMessage(). "\n");
      echo $e->getMessage();
  }

  // $result = $connection->query("SELECT * FROM questions");

  // error_log("result: ".$result."\n");

  // if($result !== null) {
  //   echo $result->rowCount();
  // }

  echo "Line Bot Execute Success"

?>