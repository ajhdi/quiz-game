<?php
// Include necessary files (e.g., database connection)
include_once 'conn.php'; // Adjust according to your setup

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $quizTitle = $_POST['quizTitle'] ?? '';
    $subjectDesc = $_POST['subjectDesc'] ?? '';
    $subjectCode = $_POST['subjectCode'] ?? '';
    $courseCode = $_POST['courseCode'] ?? '';
    $yearSection = $_POST['yearSection'] ?? '';
    $isActive = $_POST['isActive'] ?? 0;
    $profID = $_POST['profID'] ?? ''; // Professor ID

    // Validate data
    if (empty($quizTitle) || empty($subjectDesc) || empty($subjectCode) || empty($courseCode) || empty($yearSection)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    // Prepare SQL query to insert quiz
    $stmt = $conn->prepare("INSERT INTO quiz_tbl (quizTitle, subjectDesc, subjectCode, courseCode, yearSection, isActive, profID) 
                            VALUES (:quizTitle, :subjectDesc, :subjectCode, :courseCode, :yearSection, :isActive, :profID)");

    // Bind values to the statement
    $stmt->bindParam(':quizTitle', $quizTitle);
    $stmt->bindParam(':subjectDesc', $subjectDesc);
    $stmt->bindParam(':subjectCode', $subjectCode);
    $stmt->bindParam(':courseCode', $courseCode);
    $stmt->bindParam(':yearSection', $yearSection);
    $stmt->bindParam(':isActive', $isActive, PDO::PARAM_INT);
    $stmt->bindParam(':profID', $profID, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to create quiz. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
