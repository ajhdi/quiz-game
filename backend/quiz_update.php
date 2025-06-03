<?php
header('Content-Type: application/json');
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $quizID = $_POST['quizID'];
  $quizTitle = $_POST['quizTitle'];
  $subjectDesc = $_POST['subjectDesc'];
  $subjectCode = $_POST['subjectCode'];
  $courseCode = $_POST['courseCode'];
  $yearSection = $_POST['yearSection'];
  $timer = $_POST['timeLimit'];

  try {
    $stmt = $conn->prepare("
      UPDATE quiz_tbl 
      SET quizTitle = ?, subjectDesc = ?, subjectCode = ?, courseCode = ?, yearSection = ?, timer = ?
      WHERE quizID = ?
    ");
    $stmt->execute([$quizTitle, $subjectDesc, $subjectCode, $courseCode, $yearSection, $timer, $quizID]);

    echo json_encode(["success" => true]);
  } catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
  }
}
?>
