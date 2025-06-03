<?php
include_once('components/header.php');
include_once('components/toast.php');
include_once('backend/conn.php');

$quizID = $_GET['quizID'] ?? null;

if (!$quizID) {
    die("Quiz ID not provided.");
}

$quizID = intval($quizID); // sanitize

// Use PDO query & fetch
$stmt = $conn->prepare("
  SELECT r.resultID, r.quizID, r.studentID, r.scores, r.totalScores, r.date, 
         s.studNo, s.studLname, s.studFname
  FROM result_tbl r
  JOIN student_tbl s ON r.studentID = s.studentID
  WHERE r.quizID = :quizID
");
$stmt->execute([':quizID' => $quizID]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $conn->query("SELECT quizID, quizTitle, subjectDesc, subjectCode, courseCode, yearSection, isActive 
                      FROM quiz_tbl 
                      WHERE quizID = $quizID");

$quiz = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$quiz) {
    die("Quiz not found.");
}

?>
<!-- Breadcrumb -->
<nav class="flex px-5 pb-4 text-gray-700" aria-label="Breadcrumb">
  <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
    <li class="inline-flex items-center">
      <a href="teacher_dashboard.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-yellow-300 dark:text-gray-400 dark:hover:text-white">
        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
        </svg>
        Dashboard
      </a>
    </li>
    <li>
      <div class="flex items-center">
        <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
        </svg>
        <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-yellow-300 md:ms-2 dark:text-gray-400 dark:hover:text-white"><?= htmlspecialchars($quiz['quizTitle']) ?></a>
      </div>
    </li>
  </ol>
</nav>
<!-- Tabs + Info Card Wrapper -->
<div class="flex justify-between items-center ">
    <!-- Tabs on the left -->
    <div class="flex">
        <a href="question.php?quizID=<?= urlencode($quizID) ?>" class="px-4 py-2 hover:bg-yellow-300 rounded-t-md text-black font-medium">Question</a>
        <a href="result.php?quizID=<?= urlencode($quizID) ?>" class="bg-yellow-200 hover:bg-yellow-300 px-4 py-2 rounded-t-md text-black bg-gray-200 font-medium">Result</a>
    </div>
</div>
<div class="bg-white p-6 shadow-md rounded-md mb-6">
    <!-- Quiz Info Row -->
    <div class="flex flex-col md:flex-row items-center justify-between text-gray-800 text-sm md:text-base font-medium mb-6">
        <!-- Left: Subject -->
        <div class="mb-3 md:mb-0 text-left w-full md:w-1/3">
            <span class="text-gray-500">Subject:</span>
            <span class="text-black font-semibold"><?= htmlspecialchars($quiz['subjectCode']) ?>:</span>
            <span><?= htmlspecialchars($quiz['subjectDesc']) ?></span>
        </div>

        <!-- Center: Title -->
        <div class="mb-3 md:mb-0 text-center w-full md:w-1/3">
            <span class="text-blue-600 text-xl font-bold"><?= htmlspecialchars($quiz['quizTitle']) ?></span>
        </div>

        <!-- Right: Course & Section -->
        <div class="text-right w-full md:w-1/3">
            <span class="text-gray-500">Course:</span>
            <span class="text-black font-semibold"><?= htmlspecialchars($quiz['courseCode']) ?></span>
            <span class="mx-2 text-gray-400">|</span>
            <span class="text-gray-500">Section:</span>
            <span class="text-black font-semibold"><?= htmlspecialchars($quiz['yearSection']) ?></span>
        </div>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto">
        <table id="resultsTable" class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No.</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Student No.</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Student Name</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Scores / Total Scores</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($results as $index => $row): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= $index + 1 ?></td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row['studNo']) ?></td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row['studLname'] . ', ' . $row['studFname']) ?></td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row['scores']) ?> / <?= htmlspecialchars($row['totalScores']) ?></td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars(date('Y-m-d', strtotime($row['date']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#resultsTable').DataTable({
            // Optional configs
            order: [
                [0, 'asc']
            ], // Order by No.
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
        });
    });
</script>
<?php
include_once('components/footer.php');
?>