<?php
session_start();
include 'conn.php';

$studNo = $_POST['studNo'] ?? '';
$profNo = $_POST['profNo'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'student';

if ($role === 'teacher') {
    if (!$profNo || !$password) {
        echo "Please enter both professor number and password.";
        exit;
    }
} else {
    if (!$studNo || !$password) {
        echo "Please enter both student number and password.";
        exit;
    }
}

try {
    if ($role === 'teacher') {
        $stmt = $conn->prepare("SELECT profID, password FROM prof_tbl WHERE profNo = :profNo");
        $stmt->bindParam(':profNo', $profNo);
    } else {
        $stmt = $conn->prepare("SELECT studentID, password FROM student_tbl WHERE studNo = :studNo");
        $stmt->bindParam(':studNo', $studNo);
    }

    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        if ($role === 'teacher') {
            $_SESSION['profID'] = $user['profID'];
        } else {
            $_SESSION['studentID'] = $user['studentID'];
        }
        echo "success";
    } else {
        echo "Invalid credentials.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
