<?php
header('Content-Type: application/json');
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Read the raw input since you are sending JSON
  $data = json_decode(file_get_contents('php://input'), true);

  $quizID = $data['quizID'] ?? null;
  $questionDesc = $data['question'];
  $optionA = $data['optionA'];
  $optionB = $data['optionB'];
  $optionC = $data['optionC'];
  $optionD = $data['optionD'];
  $correctAnswer = $data['correctAnswer'];

  if (!$quizID) {
    echo json_encode(["success" => false, "message" => "Quiz ID is required"]);
    exit;
  }

  try {
    $stmt = $conn->prepare("INSERT INTO question_tbl (quizID, questionDesc, optionA, optionB, optionC, optionD, correctAnswer) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$quizID, $questionDesc, $optionA, $optionB, $optionC, $optionD, $correctAnswer]);

    echo json_encode(["success" => true]);
  } catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
  }
}
?>
