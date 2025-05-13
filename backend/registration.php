<?php
session_start();
include 'conn.php';

$role = $_POST['role'] ?? 'student';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    if ($role === 'student') {
        $stmt = $conn->prepare("INSERT INTO student_tbl (studFname, studLname, studMname, studNo, email, password, courseCode, yearSection)
                                VALUES (:fname, :lname, :mname, :studNo, :email, :password, :courseCode, :yearSection)");
        $stmt->execute([
            ':fname' => $_POST['studFname'],
            ':lname' => $_POST['studLname'],
            ':mname' => $_POST['studMname'],
            ':studNo' => $_POST['studNo'],
            ':email' => $email,
            ':password' => $hashedPassword,
            ':courseCode' => $_POST['courseCode'],
            ':yearSection' => $_POST['yearSection']
        ]);
    } elseif ($role === 'teacher') {
        $stmt = $conn->prepare("INSERT INTO prof_tbl (proFname, proLname, proMname, profNo, email, password)
                                VALUES (:fname, :lname, :mname, :profNo, :email, :password)");
        $stmt->execute([
            ':fname' => $_POST['proFname'],
            ':lname' => $_POST['proLname'],
            ':mname' => $_POST['proMname'],
            ':profNo' => $_POST['profNo'],
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
    }

    echo "Registration successful!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
