<?php
include 'conn.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Extract and validate input
$studentID = isset($data['studentID']) ? (int) $data['studentID'] : null;
$quizID = isset($data['quizID']) ? (int) $data['quizID'] : null;
$score = isset($data['score']) ? (int) $data['score'] : 0;
$total = isset($data['total']) ? (int) $data['total'] : 0;

if (!$studentID || !$quizID || $total === 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing or invalid studentID, quizID, or total'
    ]);
    exit;
}



try {
    $stmt = $conn->prepare("INSERT INTO result_tbl (quizID, studentID, scores, totalScores, date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$quizID, $studentID, $score, $total]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
