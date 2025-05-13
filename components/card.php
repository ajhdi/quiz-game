<div class="relative max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
  <!-- Edit button (ellipsis) -->
  <button onclick="openModal('modal-<?= $quizID ?>')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-xl font-bold">⋯</button>

  <!-- Card Content -->
  <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white truncate">
    <?= htmlspecialchars($quizTitle) ?>
  </h5>
  <p class="mb-1 text-gray-700 dark:text-gray-400 truncate"><strong>Subject:</strong> <?= htmlspecialchars($subjectDesc) ?> (<?= htmlspecialchars($subjectCode) ?>)</p>
  <p class="mb-1 text-gray-700 dark:text-gray-400"><strong>Course:</strong> <?= htmlspecialchars($courseCode) ?></p>
  <p class="mb-3 text-gray-700 dark:text-gray-400"><strong>Section:</strong> <?= htmlspecialchars($yearSection) ?></p>
  <span class="inline-block px-3 py-1 text-sm rounded-full <?= $isActive ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">
    <?= $isActive ? 'Active' : 'Inactive' ?>
  </span>

  <!-- Modal -->
  <div id="modal-<?= $quizID ?>" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
      <button onclick="closeModal('modal-<?= $quizID ?>')" class="absolute top-2 right-2 text-gray-600 hover:text-black">&times;</button>
      <h3 class="text-xl font-semibold mb-4">Edit Quiz</h3>
      <form class="update-quiz-form flex flex-col gap-3" data-id="<?= $quizID ?>">
        <input type="hidden" name="quizID" value="<?= $quizID ?>">

        <label class="text-sm font-medium">Quiz Title</label>
        <input type="text" name="quizTitle" value="<?= htmlspecialchars($quizTitle) ?>" class="p-2 border rounded" required>

        <label class="text-sm font-medium">Subject Description</label>
        <input type="text" name="subjectDesc" value="<?= htmlspecialchars($subjectDesc) ?>" class="p-2 border rounded" required>

        <label class="text-sm font-medium">Subject Code</label>
        <input type="text" name="subjectCode" value="<?= htmlspecialchars($subjectCode) ?>" class="p-2 border rounded" required>

        <label class="text-sm font-medium">Course Code</label>
        <input type="text" name="courseCode" value="<?= htmlspecialchars($courseCode) ?>" class="p-2 border rounded" required>

        <label class="text-sm font-medium">Year Section</label>
        <input type="text" name="yearSection" value="<?= htmlspecialchars($yearSection) ?>" class="p-2 border rounded" required>

        <label class="text-sm font-medium">Status</label>
        <select name="isActive" class="p-2 border rounded">
          <option value="1" <?= $isActive ? 'selected' : '' ?>>Active</option>
          <option value="0" <?= !$isActive ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
      </form>
    </div>
  </div>
</div>
