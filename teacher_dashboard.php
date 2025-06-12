<?php
include_once('components/header.php');
include_once('components/toast.php');
include_once('backend/conn.php');
?>
<button onclick="openModal('create-quiz-modal')" class="bg-yellow-200 hover:bg-yellow-300 font-semibold py-2 px-4 rounded-lg mb-4">
  + Create Quiz
</button>
<!-- Filter Modal Trigger -->
<!-- Filter Icon Button with Tooltip using Tippy.js -->
<button
  onclick="openFilterModal('filterModal')"
  class="text-yellow-600 hover:text-yellow-800 p-2 text-xl"
  data-tippy-content="Filter Quizzes">
  <i class="fas fa-filter"></i>
</button>



<div id="card" class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
  <?php
  try {
    $stmt = $conn->query("SELECT quizID, quizTitle, subjectDesc, subjectCode, courseCode, yearSection, isActive, timer FROM quiz_tbl WHERE profID = $profID ORDER BY time_stamp DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      extract($row); // makes $quizTitle, $subjectDesc, etc. available
      include('components/card.php');
    }
  } catch (PDOException $e) {
    echo "<p class='text-red-600'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
  }
  ?>
</div>
<!-- create modal -->
<div id="create-quiz-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-4">
    <h2 class="text-2xl font-semibold text-gray-800">📝 Create New Quiz</h2>

    <form id="create-quiz-form" class="space-y-4">
      <input type="hidden" name="profID" value="<?= htmlspecialchars($profID) ?>">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Title</label>
        <input type="text" name="quizTitle" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
          <input type="text" name="subjectDesc" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Subject Code</label>
          <input type="text" name="subjectCode" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Course Code</label>
          <input type="text" name="courseCode" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Year & Section</label>
          <input type="text" name="yearSection" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Time Limit <span class="text-xs text-gray-500">(in minutes, 0 = no limit)</span></label>
        <input type="number" name="timeLimit" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" min="0" value="0">
      </div>

      <div class="flex justify-end gap-3 pt-4">
        <button type="button" onclick="closeFilterModal('create-quiz-modal')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-5 rounded-lg transition">Cancel</button>
        <button type="submit" class="bg-yellow-200 hover:bg-yellow-300 font-medium py-2 px-6 rounded-lg transition">Create Quiz</button>
      </div>
    </form>
  </div>
</div>


<!-- Modal -->

<div id="filterModal" class="fixed inset-0 z-50 hidden justify-center items-center bg-black bg-opacity-50">
  <div class="bg-white p-6 rounded shadow-lg w-full max-w-md relative">
    <h2 class="text-xl font-semibold mb-4">Filter Quizzes</h2>

    <form id="filterForm" class="space-y-4">
      <!-- Hidden input for profID -->
      <input type="hidden" name="profID" value="<?= htmlspecialchars($profID) ?>">

      <input type="text" name="quizTitle" placeholder="Quiz Title" class="w-full border rounded px-3 py-2" />
      <input type="text" name="subjectCode" placeholder="Subject Code" class="w-full border rounded px-3 py-2" />
      <input type="text" name="subjectDesc" placeholder="Subject Description" class="w-full border rounded px-3 py-2" />
      <input type="text" name="courseCode" placeholder="Course Code" class="w-full border rounded px-3 py-2" />
      <input type="text" name="yearSection" placeholder="Year & Section" class="w-full border rounded px-3 py-2" />
      <input type="number" name="year" placeholder="Year (from time_stamp)" class="w-full border rounded px-3 py-2" />

      <div class="flex justify-end gap-2">
        <button type="button" onclick="closeModal('filterModal')" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Apply</button>
      </div>
    </form>
  </div>
</div>

<script>
  tippy('[data-tippy-content]');

  function openFilterModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
  }

  function closeFilterModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
  }

  document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('backend/quiz_filter.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(html => {
        document.getElementById('card').innerHTML = html;
        closeModal('filterModal');
      });
  });
</script>
<?php
include_once('components/footer.php');
?>