<?php
include_once('components/header.php');
include_once('components/toast.php');
include_once('backend/conn.php');
$stmt = $conn->prepare("
  SELECT 
    q.quizTitle,
    q.subjectDesc,
    p.proLname,
    r.scores,
    r.totalScores,
    r.date
  FROM result_tbl r
  JOIN quiz_tbl q ON r.quizID = q.quizID
  JOIN prof_tbl p ON q.profID = p.profID
  WHERE r.studentID = ?
");
$stmt->execute([$studentID]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="overflow-x-auto">
  <table id="resultTable" class="min-w-full border border-gray-200 shadow-md rounded-lg overflow-hidden">
    <thead class="bg-yellow-100 text-gray-800">
      <tr>
        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Quiz Title</th>
        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Subject Description</th>
        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Professor</th>
        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Score</th>
        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Date</th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-100">
      <?php foreach ($results as $index => $row): ?>
        <tr class="<?= $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?> hover:bg-yellow-50 transition">
          <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($row['quizTitle']) ?></td>
          <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($row['subjectDesc']) ?></td>
          <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($row['proLname']) ?></td>
          <td class="px-6 py-4 text-sm text-blue-700 font-semibold"><?= htmlspecialchars($row['scores']) . " / " . htmlspecialchars($row['totalScores']) ?></td>
          <td class="px-6 py-4 text-sm text-gray-600"><?= date('m-d-Y', strtotime($row['date'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
    $(document).ready(function() {
        $('#resultTable').DataTable();
    });
</script>
<?php
include_once('components/footer.php');
?>