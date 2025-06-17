<?php
include_once('conn.php');
$data = json_decode(file_get_contents("php://input"), true);
$response = ['success' => false];

if (is_array($data)) {
    try {
        $conn->beginTransaction();
        foreach ($data as $id => $fields) {
            $set = [];
            $values = [];
            foreach ($fields as $field => $value) {
                $set[] = "$field = ?";
                $values[] = $value;
            }
            $values[] = $id;
            $stmt = $conn->prepare("UPDATE student_tbl SET " . implode(", ", $set) . " WHERE studNo = ?");
            $stmt->execute($values);
        }
        $conn->commit();
        $response['success'] = true;
    } catch (Exception $e) {
        $conn->rollBack();
    }
}

header("Content-Type: application/json");
echo json_encode($response);
