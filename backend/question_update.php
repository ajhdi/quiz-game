<?php
include 'conn.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
    $questionID    = $input['questionID'] ?? '';
    $questionDesc  = $input['questionDesc'] ?? '';
    $optionA       = $input['optionA'] ?? '';
    $optionB       = $input['optionB'] ?? '';
    $optionC       = $input['optionC'] ?? '';
    $optionD       = $input['optionD'] ?? '';
    $correctAnswer = $input['correctAnswer'] ?? '';

    if (!$questionID) {
        echo json_encode(["success" => false, "message" => "Invalid question ID."]);
        exit;
    }

    try {
        $stmt = $conn->prepare("UPDATE question_tbl 
            SET questionDesc = :desc, optionA = :a, optionB = :b, optionC = :c, optionD = :d, correctAnswer = :ans 
            WHERE questionID = :id");

        $stmt->bindParam(':desc', $questionDesc);
        $stmt->bindParam(':a', $optionA);
        $stmt->bindParam(':b', $optionB);
        $stmt->bindParam(':c', $optionC);
        $stmt->bindParam(':d', $optionD);
        $stmt->bindParam(':ans', $correctAnswer);
        $stmt->bindParam(':id', $questionID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update question."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
