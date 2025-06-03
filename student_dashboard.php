<?php
include_once('components/header.php');
include_once('backend/conn.php');
?>

<div id="card" class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
  <?php
  if (isset($studentID)) {
    try {
      // Fetch courseCode and yearSection from student_tbl
      $studentStmt = $conn->prepare("SELECT courseCode, yearSection FROM student_tbl WHERE studentID = ?");
      $studentStmt->execute([$studentID]);
      $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

      if ($student) {
        $courseCode = $student['courseCode'];
        $yearSection = $student['yearSection'];

        // Now fetch quizzes based on courseCode and yearSection
        $quizStmt = $conn->prepare("
          SELECT quizID, quizTitle, subjectDesc, subjectCode, courseCode, yearSection, isActive, prof_tbl.proLname AS professorLastName 
          FROM quiz_tbl JOIN prof_tbl ON quiz_tbl.profID = prof_tbl.profID
          WHERE courseCode = ? AND yearSection = ? ORDER BY time_stamp DESC
        ");
        $quizStmt->execute([$courseCode, $yearSection]);

        while ($row = $quizStmt->fetch(PDO::FETCH_ASSOC)) {
          extract($row); // Makes $quizID, $quizTitle, etc. available
          $countStmt = $conn->prepare("SELECT COUNT(*) FROM question_tbl WHERE quizID = ?");
          $countStmt->execute([$quizID]);
          $questionCount = $countStmt->fetchColumn(); // returns the number directly

          include('components/student_card.php'); // You can now use $questionCount in this file
        }
      } else {
        echo "<p class='text-red-600'>Student not found.</p>";
      }
    } catch (PDOException $e) {
      echo "<p class='text-red-600'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
  } else {
    echo "<p class='text-red-600'>You must be logged in as a student to view quizzes.</p>";
  }
  ?>
</div>


<?php
include_once('components/footer.php');
?>