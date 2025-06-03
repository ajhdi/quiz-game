<?php
// Include necessary files (e.g., database connection)
include_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quizTitle = $_POST['quizTitle'] ?? '';
    $subjectDesc = $_POST['subjectDesc'] ?? '';
    $subjectCode = $_POST['subjectCode'] ?? '';
    $courseCode = $_POST['courseCode'] ?? '';
    $yearSection = $_POST['yearSection'] ?? '';
    $timer = $_POST['timeLimit'] ?? '';
    $isActive = 0;
    $profID = $_POST['profID'] ?? '';

    // Validate
    if (
        empty($quizTitle) || empty($subjectDesc) || empty($subjectCode) ||
        empty($courseCode) || empty($yearSection) || !is_numeric($timer)
    ) {
        echo json_encode(['success' => false, 'error' => 'All fields are required and time limit must be a number.']);
        exit;
    }

    // Insert into quiz_tbl (include timer)
    $stmt = $conn->prepare("INSERT INTO quiz_tbl 
        (quizTitle, subjectDesc, subjectCode, courseCode, yearSection, timer, isActive, profID) 
        VALUES 
        (:quizTitle, :subjectDesc, :subjectCode, :courseCode, :yearSection, :timer, :isActive, :profID)");

    $stmt->bindParam(':quizTitle', $quizTitle);
    $stmt->bindParam(':subjectDesc', $subjectDesc);
    $stmt->bindParam(':subjectCode', $subjectCode);
    $stmt->bindParam(':courseCode', $courseCode);
    $stmt->bindParam(':yearSection', $yearSection);
    $stmt->bindParam(':timer', $timer, PDO::PARAM_INT);
    $stmt->bindParam(':isActive', $isActive, PDO::PARAM_INT);
    $stmt->bindParam(':profID', $profID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to create quiz.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

?>
