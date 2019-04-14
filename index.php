<?php

  include "setup-database.php";

  use LineBotExamPostgreSQL\CreateTables as CreateTables;

  // Database data
  $host = 'ec2-23-21-244-254.compute-1.amazonaws.com';
  $dbname = 'dd2msimtj928n7';
  $user = 'rdrqgyesvgiigm';
  $pass = '886da5a359b454bb65e8363e746a9c400e686fce64546e2ac771600c202bc652';

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
      ['title' => '2+2=? \n\r กด 1 1 \n\r กด 2 4 \n\r กด 3 5 \n\r กด 4 6 \n\r', 'correctAnswer' => 2],
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

  echo "Line Bot Execute Success"

?>
