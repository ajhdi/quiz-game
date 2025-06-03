<?php
require 'conn.php'; // your DB connection

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quizID'])) {
    $quizID = intval($_POST['quizID']);

    // Get current status
    $stmt = $conn->prepare("SELECT isActive FROM quiz_tbl WHERE quizID = ?");
    $stmt->execute([$quizID]);
    $current = $stmt->fetchColumn();

    if ($current !== false) {
        $newStatus = $current ? 0 : 1;
        $update = $conn->prepare("UPDATE quiz_tbl SET isActive = ? WHERE quizID = ?");
        $update->execute([$newStatus, $quizID]);

        echo json_encode(['success' => true, 'newStatus' => $newStatus]);
        exit;
    }
}

echo json_encode(['success' => false]);
