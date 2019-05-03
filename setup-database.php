<?php
 
namespace LineBotExamPostgreSQL;
/**
 * Create table in PostgreSQL from PHP demo
 */
class CreateTables {

  /**
   * PDO object
   * @var \PDO
   */
  private $pdo;

  /**
   * init the object with a \PDO object
   * @param type $pdo
   */
  public function __construct($pdo) {
      $this->pdo = $pdo;
  }

  /**
   * create tables 
   */
  public function createTables() {
    $sqlList = ['CREATE TABLE IF NOT EXISTS users (
                    id serial PRIMARY KEY,
                    token TEXT NOT NULL,
                    answer integer NOT NULL,
                    question integer NOT NULL
                  ) DEFAULT CHARSET=utf8',
                'CREATE TABLE IF NOT EXISTS questions (
                    id serial PRIMARY KEY,
                    title TEXT NOT NULL,
                    correct_answer integer NOT NULL
                ) DEFAULT CHARSET=utf8'];

    // execute each sql statement to create new tables
    foreach ($sqlList as $sql) {
        $this->pdo->exec($sql);
    }
    
    return $this;
  }

  /**
   * return tables in the database
   */
  public function getTables() {
    $table = $this->pdo->query("SELECT table_name 
                                FROM information_schema.tables 
                                WHERE table_schema= 'public' 
                                AND table_type='BASE TABLE'
                                ORDER BY table_name");
    $tableList = [];
    while ($row = $table->fetch(\PDO::FETCH_ASSOC)) {
        $tableList[] = $row['table_name'];
    }

    return $tableList;
  }

  /**
   * insert questions
   */
  public function insertQuestions($title, $correctAnswer) {
    // prepare statement for insert
    $sql = 'INSERT INTO questions(title, correct_answer) VALUES(:title, :correctAnswer)';
    $stmt = $this->pdo->prepare($sql);
    
    // pass values to the statement
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':correctAnswer', $correctAnswer);
    
    // execute the insert statement
    $stmt->execute();
    
    // return generated id
    return $this->pdo->lastInsertId();
  }

  /**
   * Insert multiple question into the questions table
   * @param array $questions
   * @return a list of inserted ID
   */
  public function insertQuestionList($questions) {
    $sql = 'INSERT INTO questions(title, correct_answer) VALUES(:title, :correctAnswer)';
    $stmt = $this->pdo->prepare($sql);

    $idList = [];
    foreach ($questions as $question) {
        $stmt->bindValue(':title', $question['title']);
        $stmt->bindValue(':correctAnswer', $question['correctAnswer']);
        $stmt->execute();
        $idList[] = $this->pdo->lastInsertId();
    }
    return $idList;
  }
}
