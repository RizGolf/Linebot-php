<?php
 
namespace LineBotExamQuestion;

class Question {

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

public function getQuestion($userToken) {
    $randomQuestionId = rand(1, 10);
    error_log("Random question id: ".$randomQuestionId);
    error_log("Random question id with user: ".$userToken);

    $sql = sprintf("SELECT * FROM users WHERE question='%d' and token='%s' ", $randomQuestionId, $userToken);
    $userAlreadyAnswered = $this->pdo->query($sql);

    if($userAlreadyAnswered->rowCount() == 0) {
      $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE id = :randomQuestionId");
      $stmt->bindValue(':randomQuestionId', $randomQuestionId);
      $stmt->execute();

      $question = $stmt->fetch(\PDO::FETCH_ASSOC);

      if($question != false) {
        $questionData = array(
          "id" => $randomQuestionId,
          "title" => $question['title']
        );
        error_log("Send Question data with id: ".$questionData['id']);
        return $questionData;
      } else {
        error_log("Fail to send question data");
        return false;
      }
    } else {
      error_log("User already answer question witd id: ".$randomQuestionId. " so query find to next question 👉👉👉");
      return $this->getQuestion($userToken);
    }
  }
 
  public function getCorrectAnswer($questionNumber) {
    $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE id = :questionNumber");
    $stmt->bindValue(':questionNumber', $questionNumber);
    $stmt->execute();

    $correctAnswer = $stmt->fetch(\PDO::FETCH_ASSOC);
    $res = $correctAnswer['correct_answer'];

    return $res;
  }

  public function getUserAnswer($questionNumber, $userToken) {
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE question = :questionNumber and token = :userToken");
    $stmt->bindValue(':questionNumber', $questionNumber);
    $stmt->bindValue(':userToken', $userToken);
    $stmt->execute();

    $userAnswer = $stmt->fetch(\PDO::FETCH_ASSOC);
    $res = $userAnswer['answer'];
    
    return $res;
  }

  /**
   * calculate questions
   */
  public function calculateQuestions($userToken) {
    $score = 0;
    for ($i = 1; $i <= 10; $i++) {
      $correctAnswer = $this->getCorrectAnswer($i);
      $userAnswer = $this->getUserAnswer($i, $userToken);

      if ($correctAnswer == $userAnswer) {
        error_log("Correct answer: ".$correctAnswer." | User answer: ".$userAnswer." | Result: Correct ✅");
        $score++;
      } else {
        error_log("Correct answer: ".$correctAnswer." | User answer: ".$userAnswer." | Result: Wrong ❌");
      }
    } 
    
    error_log("<<< Total score = ".$score." >>>>");
    return $score;
  }
}
