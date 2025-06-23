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
<div class="w-full overflow-x-auto rounded-lg shadow-md mt-4">
  <table class="min-w-full border border-gray-200 text-sm text-left">
    <thead class="bg-yellow-100 text-gray-800">
      <tr>
        <th class="px-4 py-3 font-bold uppercase tracking-wider">Quiz Title</th>
        <th class="px-4 py-3 font-bold uppercase tracking-wider">Score</th>
        <th class="px-4 py-3 font-bold uppercase tracking-wider hidden md:table-cell">Subject Description</th>
        <th class="px-4 py-3 font-bold uppercase tracking-wider hidden md:table-cell">Professor</th>
        <th class="px-4 py-3 font-bold uppercase tracking-wider hidden md:table-cell">Date</th>
        <th class="px-4 py-3 md:hidden bg-yellow-100"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 bg-white">
      <?php foreach ($results as $index => $row): ?>
        <tr
          class="cursor-pointer <?= $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?> hover:bg-yellow-50 transition group"
          onclick="toggleRow(this)">
          <td class="px-4 py-3 text-gray-900"><?= htmlspecialchars($row['quizTitle']) ?></td>
          <td class="px-4 py-3 text-blue-700 font-semibold"><?= htmlspecialchars($row['scores']) . " / " . htmlspecialchars($row['totalScores']) ?></td>
          <td class="px-4 py-3 text-gray-700 hidden md:table-cell"><?= htmlspecialchars($row['subjectDesc']) ?></td>
          <td class="px-4 py-3 text-gray-700 hidden md:table-cell"><?= htmlspecialchars($row['proLname']) ?></td>
          <td class="px-4 py-3 text-gray-600 hidden md:table-cell"><?= date('m-d-Y', strtotime($row['date'])) ?></td>
          <td class="px-4 py-3 md:hidden text-right">
            <svg class="w-4 h-4 text-gray-500 transform transition-transform duration-200 group-[.expanded]:rotate-180 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </td>
        </tr>
        <!-- Expandable row -->
        <tr class="hidden md:hidden bg-gray-100 text-sm transition">
          <td colspan="6" class="px-4 py-3 space-y-2">
            <div><strong>Subject:</strong> <?= htmlspecialchars($row['subjectDesc']) ?></div>
            <div><strong>Professor:</strong> <?= htmlspecialchars($row['proLname']) ?></div>
            <div><strong>Date:</strong> <?= date('m-d-Y', strtotime($row['date'])) ?></div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
  $(document).ready(function() {
    $('#resultTable').DataTable();
  });

  function toggleRow(row) {
    const expandable = row.nextElementSibling;
    expandable.classList.toggle('hidden');
    row.classList.toggle('expanded');
  }
</script>
<?php
include_once('components/footer.php');
?>