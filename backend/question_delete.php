<?php
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  $questionID = $input['questionID'] ?? null;

  if (!$questionID) {
    echo json_encode(['success' => false, 'message' => 'Missing question ID.']);
    exit;
  }

  $stmt = $conn->prepare("DELETE FROM question_tbl WHERE questionID = ?");
  $success = $stmt->execute([$questionID]);

  if ($success) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete question.']);
  }
}
