<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_FILES['importFile'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
    exit;
  }

  $quizID = $_POST['quizID'] ?? null;
  if (!$quizID) {
    echo json_encode(['success' => false, 'message' => 'Quiz ID is required.']);
    exit;
  }

  $file = $_FILES['importFile']['tmp_name'];
  $ext = pathinfo($_FILES['importFile']['name'], PATHINFO_EXTENSION);

  if (!in_array(strtolower($ext), ['csv', 'xls', 'xlsx'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid file format.']);
    exit;
  }

  if (strtolower($ext) === 'csv') {
    if (($handle = fopen($file, 'r')) !== false) {
      // Skip header
      fgetcsv($handle);

      while (($row = fgetcsv($handle, 1000, ',')) !== false) {
        $questionDesc = trim($row[0]);
        $optionA = formatOption('A', $row[1]);
        $optionB = formatOption('B', $row[2]);
        $optionC = formatOption('C', $row[3]);
        $optionD = formatOption('D', $row[4]);

        $correctLetter = strtoupper(trim($row[5]));
        $correctMap = [
        'A' => $optionA,
        'B' => $optionB,
        'C' => $optionC,
        'D' => $optionD
        ];

$correctAnswer = $correctMap[$correctLetter] ?? '';

        if (!$correctAnswer) continue; // skip invalid rows

        $stmt = $conn->prepare("INSERT INTO question_tbl (quizID, questionDesc, optionA, optionB, optionC, optionD, correctAnswer) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$quizID, $questionDesc, $optionA, $optionB, $optionC, $optionD, $correctAnswer]);
      }
      fclose($handle);

      echo json_encode(['success' => true]);
      exit;
    }
  }

  echo json_encode(['success' => false, 'message' => 'File processing not implemented for this format.']);
}
function formatOption($letter, $text) {
  $cleanText = preg_replace('/^[A-D][\.\)]?\s*/i', '', $text); // Remove A., A), A
  return $letter . '. ' . trim($cleanText);
}
?>
