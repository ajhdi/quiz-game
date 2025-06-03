<?php
session_start();
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

try {
    if (isset($_POST['studentID'])) {
        // Update student profile
        $stmt = $conn->prepare("
            UPDATE student_tbl SET
                studFname = :fname,
                studMname = :mname,
                studLname = :lname,
                email = :email,
                studNo = :studNo,
                courseCode = :courseCode,
                yearSection = :yearSection
            WHERE studentID = :id
        ");
        $stmt->execute([
            ':fname'       => $_POST['studFname'],
            ':mname'       => $_POST['studMname'],
            ':lname'       => $_POST['studLname'],
            ':email'       => $_POST['email'],
            ':studNo'      => $_POST['studNo'],
            ':courseCode'  => $_POST['courseCode'],
            ':yearSection' => $_POST['yearSection'],
            ':id'          => $_POST['studentID']
        ]);

    } elseif (isset($_POST['profID'])) {
        // Update professor profile
        $stmt = $conn->prepare("
            UPDATE prof_tbl SET
                proFname = :fname,
                proMname = :mname,
                proLname = :lname,
                email = :email,
                profNo = :profNo
            WHERE profID = :id
        ");
        $stmt->execute([
            ':fname'  => $_POST['proFname'],
            ':mname'  => $_POST['proMname'],
            ':lname'  => $_POST['proLname'],
            ':email'  => $_POST['email'],
            ':profNo' => $_POST['profNo'],
            ':id'     => $_POST['profID']
        ]);

    } else {
        throw new Exception('Missing user ID');
    }

    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Profile updated successfully.'];
    header("Location: ../" . ($_SESSION['profID'] ? 'teacher_dashboard.php' : 'student_dashboard.php'));
    exit;

} catch (Exception $e) {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Profile update failed: ' . $e->getMessage()];
    header("Location: ../" . ($_SESSION['profID'] ? 'teacher_dashboard.php' : 'student_dashboard.php'));
    exit;
}
