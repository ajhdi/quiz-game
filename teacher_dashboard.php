<?php
include_once('components/header.php');
include_once('components/toast.php');
include_once('backend/conn.php');
?>
  <button onclick="openModal('create-quiz-modal')" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded mb-4">
    + Create Quiz
  </button>

  <div id="card" class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
    <?php
    try {
      $stmt = $conn->query("SELECT quizID, quizTitle, subjectDesc, subjectCode, courseCode, yearSection, isActive FROM quiz_tbl WHERE profID = $profID");
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row); // makes $quizTitle, $subjectDesc, etc. available
        include('components/card.php');
      }
    } catch (PDOException $e) {
      echo "<p class='text-red-600'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
  </div>
  <div id="create-quiz-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
      <h2 class="text-xl font-bold mb-4">Create New Quiz</h2>
      <form id="create-quiz-form">
        <input type="hidden" name="profID" value="<?= htmlspecialchars($profID) ?>">

        <div class="mb-4">
          <label class="block font-medium mb-1">Quiz Title</label>
          <input type="text" name="quizTitle" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-4">
          <label class="block font-medium mb-1">Subject</label>
          <input type="text" name="subjectDesc" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-4">
          <label class="block font-medium mb-1">Subject Code</label>
          <input type="text" name="subjectCode" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-4">
          <label class="block font-medium mb-1">Course Code</label>
          <input type="text" name="courseCode" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-4">
          <label class="block font-medium mb-1">Year & Section</label>
          <input type="text" name="yearSection" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-4">
          <label class="block font-medium mb-1">Is Active?</label>
          <select name="isActive" class="w-full border px-3 py-2 rounded">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" onclick="closeModal('create-quiz-modal')" class="bg-gray-300 hover:bg-gray-400 text-black py-2 px-4 rounded">Cancel</button>
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">Create</button>
        </div>
      </form>
    </div>
  </div>
<?php
include_once('components/footer.php');
?>
