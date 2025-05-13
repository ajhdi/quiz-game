<?php
session_start();
include 'conn.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'student';

if (!$email || !$password) {
    echo "Please enter both email and password.";
    exit;
}

try {
    if ($role === 'teacher') {
        $stmt = $conn->prepare("SELECT profID, password FROM prof_tbl WHERE email = :email");
    } else {
        $stmt = $conn->prepare("SELECT studentID, password FROM student_tbl WHERE email = :email");
    }

    $stmt->bindParam(':email', $email);
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
        echo "Invalid email or password.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
