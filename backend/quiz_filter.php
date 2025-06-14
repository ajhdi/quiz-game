<?php
include_once 'conn.php';
// var_dump($_POST);

$profID = $_POST['profID'] ?? '';

$quizTitle = $_POST['quizTitle'] ?? '';
$subjectCode = $_POST['subjectCode'] ?? '';
$subjectDesc = $_POST['subjectDesc'] ?? '';
$courseCode = $_POST['courseCode'] ?? '';
$yearSection = $_POST['yearSection'] ?? '';
$year = $_POST['year'] ?? '';

$conditions = ["profID = :profID"];
$params = [':profID' => $profID];

if (!empty($quizTitle)) {
  $conditions[] = "quizTitle LIKE :quizTitle";
  $params[':quizTitle'] = "%$quizTitle%";
}
if (!empty($subjectCode)) {
  $conditions[] = "subjectCode LIKE :subjectCode";
  $params[':subjectCode'] = "%$subjectCode%";
}
if (!empty($subjectDesc)) {
  $conditions[] = "subjectDesc LIKE :subjectDesc";
  $params[':subjectDesc'] = "%$subjectDesc%";
}
if (!empty($courseCode)) {
  $conditions[] = "courseCode LIKE :courseCode";
  $params[':courseCode'] = "%$courseCode%";
}
if (!empty($yearSection)) {
  $conditions[] = "yearSection LIKE :yearSection";
  $params[':yearSection'] = "%$yearSection%";
}
if (!empty($year)) {
  $conditions[] = "YEAR(time_stamp) = :year";
  $params[':year'] = $year;
}

$sql = "SELECT * FROM quiz_tbl WHERE " . implode(' AND ', $conditions) . " ORDER BY time_stamp DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($results)) {
  echo "<p class='text-center text-gray-500 col-span-full'>No quizzes found.</p>";
} else {
  foreach ($results as $row) {
    extract($row);
    include('../components/card.php');
  }
}
?>
