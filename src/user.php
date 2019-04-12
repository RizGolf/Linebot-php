<?php
 
namespace LineBotExamUser;

class User {

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
   * insert user
   */
  public function insertUser($token, $answer, $question) {
    // prepare statement for insert
    $sql = 'INSERT INTO public.users(token, answer, question) VALUES(:token, :answer, :question)';
    $stmt = $this->pdo->prepare($sql);
    
    // pass values to the statement
    $stmt->bindValue(':token', $token);
    $stmt->bindValue(':answer', $answer);
    $stmt->bindValue(':question', $question);
    
    // execute the insert statement
    $stmt->execute();
    
    // return generated id
    return $this->pdo->lastInsertId();
  }

  /**
   * delete user
   */
  public function deleteUser($userToken) {
    $sql = sprintf("DELETE FROM public.users WHERE token='%s' ", $userToken);
    $stmt = $this->pdo->query($sql);

    return $stmt;
  }

  /**
   * update user
   */
  public function updateUser($id, $answer) {
    $sql = sprintf("UPDATE public.users SET answer='%d' WHERE id='%d' ", $answer, $id);
    $stmt = $this->pdo->query($sql);

    return $stmt;
  }

  /**
   * Get last user
   */
  public function getLastUser($userToken) {
    $stmt = $this->pdo->prepare("SELECT * FROM public.users WHERE token = :userToken ORDER BY id DESC LIMIT 1");
    $stmt->bindValue(':userToken', $userToken);
    $stmt->execute();

    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    $lastUserData = array(
      "id" => $user['id'],
      "answer" => $user['answer'],
      "question" => $user['question']
    );

    return $lastUserData;
  }
}