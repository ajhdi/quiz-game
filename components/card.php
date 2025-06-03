<div class="relative max-w-sm p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700
         transition-transform duration-300 ease-in-out hover:scale-[1.03] hover:shadow-lg hover:border-yellow-400">
  <!-- Edit button (ellipsis) -->
  <button onclick="openModal('modal-<?= $quizID ?>')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-xl font-bold">⋯</button>

  <!-- Card Content -->
  <a href="question.php?quizID=<?= urlencode($quizID) ?>">
    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white truncate">
      <?= htmlspecialchars($quizTitle) ?>
    </h5>
    <p class="mb-1 text-gray-700 dark:text-gray-400 truncate">
      <strong>Subject:</strong> <?= htmlspecialchars($subjectDesc) ?> (<?= htmlspecialchars($subjectCode) ?>)
    </p>
    <p class="mb-1 text-gray-700 dark:text-gray-400">
      <strong>Course:</strong> <?= htmlspecialchars($courseCode) ?>
    </p>
    <p class="mb-1 text-gray-700 dark:text-gray-400">
      <strong>Section:</strong> <?= htmlspecialchars($yearSection) ?>
    </p>
    <p class="mb-3 text-gray-700 dark:text-gray-400">
      <strong>Timer:</strong>
      <?= ($timer === null || $timer == 0) ? 'No time limit' : htmlspecialchars($timer) . ':00 min' ?>
    </p>
  </a>
  <div class="flex justify-center">
    <button
      onclick="toggleStatus(<?= $quizID ?>)"
      id="statusBtn-<?= $quizID ?>"
      class="mt-2 inline-block px-3 py-1 text-sm rounded-full font-semibold transition-all duration-300 
        <?= $isActive ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-red-500 text-white hover:bg-red-600' ?>">
      <?= $isActive ? 'Active' : 'Inactive' ?>
    </button>
  </div>
</div>
<!--Edit Modal -->
<div id="modal-<?= $quizID ?>" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-4 relative">
    <button onclick="closeModal('modal-<?= $quizID ?>')" class="absolute top-4 right-4 text-gray-500 hover:text-black text-2xl leading-none">&times;</button>

    <h2 class="text-2xl font-semibold text-gray-800">✏️ Edit Quiz</h2>

    <form class="update-quiz-form space-y-4" data-id="<?= $quizID ?>">
      <input type="hidden" name="quizID" value="<?= $quizID ?>">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Title</label>
        <input type="text" name="quizTitle" value="<?= htmlspecialchars($quizTitle) ?>" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
          <input type="text" name="subjectDesc" value="<?= htmlspecialchars($subjectDesc) ?>" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Subject Code</label>
          <input type="text" name="subjectCode" value="<?= htmlspecialchars($subjectCode) ?>" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Course Code</label>
          <input type="text" name="courseCode" value="<?= htmlspecialchars($courseCode) ?>" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Year & Section</label>
          <input type="text" name="yearSection" value="<?= htmlspecialchars($yearSection) ?>" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Time Limit <span class="text-xs text-gray-500">(in minutes, 0 = no limit)</span></label>
        <input type="number" name="timeLimit" min="0" value="<?= (int) $timer ?>" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
      </div>

      <div class="flex justify-end gap-3 pt-4">
        <button type="button" onclick="closeModal('modal-<?= $quizID ?>')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-5 rounded-lg transition">Cancel</button>
        <button type="submit" class="bg-yellow-200 hover:bg-yellow-300 font-medium py-2 px-6 rounded-lg transition">Update</button>
      </div>
    </form>
  </div>
</div>
<script>
  function toggleStatus(quizID) {
    fetch('backend/toggle_status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'quizID=' + encodeURIComponent(quizID)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const btn = document.getElementById('statusBtn-' + quizID);
          const isActive = parseInt(data.newStatus) === 1;

          btn.textContent = isActive ? 'Active' : 'Inactive';
          btn.className = 'mt-2 inline-block px-3 py-1 text-sm rounded-full font-semibold transition-all duration-300 ' +
            (isActive ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-red-500 text-white hover:bg-red-600');
        } else {
          alert('Failed to update status');
        }
      });
  }
</script>