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
$stmt = $conn->query("SELECT quizID, quizTitle, subjectDesc, subjectCode, courseCode, yearSection, isActive 
                      FROM quiz_tbl 
                      WHERE quizID = $quizID");

$quiz = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$quiz) {
  die("Quiz not found.");
}

$stmts = $conn->prepare("SELECT questionID, questionDesc, optionA, optionB, optionC, optionD, correctAnswer FROM question_tbl WHERE quizID = :quizID");
$stmts->bindParam(':quizID', $quizID, PDO::PARAM_INT);
$stmts->execute();
$questions = $stmts->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
  #questionsTable td,
  #questionsTable th {
    word-wrap: break-word;
    white-space: normal;
  }
</style>


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
    <a href="question.php?quizID=<?= urlencode($quizID) ?>" class="bg-yellow-200 hover:bg-yellow-300 px-4 py-2 rounded-t-md text-black font-medium">Question</a>
    <a href="result.php?quizID=<?= urlencode($quizID) ?>" class="hover:bg-yellow-300 px-4 py-2 rounded-t-md text-black bg-gray-200 font-medium">Result</a>
  </div>
  <!-- Action buttons on the right -->
  <div class="flex gap-1">
    <button onclick="addModal()" class="bg-yellow-200 hover:bg-yellow-300 px-3 py-2 rounded flex items-center text-sm">
      <i class="fas fa-plus mr-2"></i> Add Question
    </button>
    <button id="importExcelBtn" class="bg-yellow-200 hover:bg-yellow-300 px-3 py-2 rounded flex items-center text-sm">
      <i class="fas fa-file-import mr-2"></i> Import Excel
    </button>

    <input type="file" id="importFileInput" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style="display:none" />
  </div>
</div>

<!-- Quiz Info Card -->
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

  <!-- Questions Table -->
  <div>
    <table id="questionsTable" class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
          <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Question</th>
          <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Option A</th>
          <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Option B</th>
          <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Option C</th>
          <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Option D</th>
          <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Correct Answer</th>
          <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Action</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <?php if (!empty($questions)) : ?>
          <?php $count = 1; ?>
          <?php foreach ($questions as $q) : ?>
            <tr class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"><?= $count++ ?></td>
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($q['questionDesc']) ?></td>
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($q['optionA']) ?></td>
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($q['optionB']) ?></td>
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($q['optionC']) ?></td>
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($q['optionD']) ?></td>
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($q['correctAnswer']) ?></td>
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900 space-x-3 flex">
                <i
                  class="fas fa-edit text-green-600 cursor-pointer editBtn"
                  title="Edit"
                  data-questionid="<?= htmlspecialchars($q['questionID']) ?>"
                  data-questiondesc="<?= htmlspecialchars($q['questionDesc']) ?>"
                  data-optiona="<?= htmlspecialchars($q['optionA']) ?>"
                  data-optionb="<?= htmlspecialchars($q['optionB']) ?>"
                  data-optionc="<?= htmlspecialchars($q['optionC']) ?>"
                  data-optiond="<?= htmlspecialchars($q['optionD']) ?>"
                  data-correctanswer="<?= htmlspecialchars($q['correctAnswer']) ?>"></i>
                <i
                  class="fas fa-trash-alt text-red-600 cursor-pointer delBtn"
                  title="Delete"
                  data-del-questionid="<?= htmlspecialchars($q['questionID']) ?>"></i>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>




<div id="addQuestionModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
    <button onclick="closeAddModal()" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">&times;</button>
    <h2 class="text-xl font-semibold mb-4">Add Question</h2>

    <form id="addQuestionForm" class="space-y-3 text-sm">
      <!-- No hidden ID needed here since it's a new question -->

      <div>
        <label class="block font-medium">Question</label>
        <textarea id="addQuestionDesc" class="w-full border border-gray-300 rounded p-2" rows="2" required></textarea>
      </div>
      <div>
        <label class="block font-medium mb-1 text-center">Correct Answer</label>
        <div class="flex justify-center gap-6">
          <label class="flex items-center gap-1">
            A:
            <input type="radio" name="addCorrectAnswer" value="A" required>
          </label>
          <label class="flex items-center gap-1">
            B:
            <input type="radio" name="addCorrectAnswer" value="B">
          </label>
          <label class="flex items-center gap-1">
            C:
            <input type="radio" name="addCorrectAnswer" value="C">
          </label>
          <label class="flex items-center gap-1">
            D:
            <input type="radio" name="addCorrectAnswer" value="D">
          </label>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-medium">Option A</label>
          <input type="text" id="addOptionA" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <div>
          <label class="block font-medium">Option B</label>
          <input type="text" id="addOptionB" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <div>
          <label class="block font-medium">Option C</label>
          <input type="text" id="addOptionC" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <div>
          <label class="block font-medium">Option D</label>
          <input type="text" id="addOptionD" class="w-full border border-gray-300 rounded p-2" required>
        </div>
      </div>

      <div class="text-right pt-4">
        <button type="submit" class="bg-yellow-200 hover:bg-yello-300 px-4 py-2 rounded">
          Add Question
        </button>
      </div>
    </form>
  </div>
</div>

<div id="editQuestionModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
    <button onclick="closeEditModal()" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">&times;</button>
    <h2 class="text-xl font-semibold mb-4">Edit Question</h2>

    <form id="editQuestionForm" class="space-y-3 text-sm">
      <!-- Hidden question ID -->
      <input type="hidden" id="editQuestionID" name="questionID" />

      <div>
        <label for="editQuestionDesc" class="block font-medium">Question</label>
        <textarea id="editQuestionDesc" name="questionDesc" rows="2" class="w-full border border-gray-300 rounded p-2" required></textarea>
      </div>

      <div>
        <label class="block font-medium mb-1 text-center">Correct Answer</label>
        <div class="flex justify-center gap-6">
          <label class="flex items-center gap-1">
            A:
            <input type="radio" name="editCorrectAnswer" value="A" required>
          </label>
          <label class="flex items-center gap-1">
            B:
            <input type="radio" name="editCorrectAnswer" value="B">
          </label>
          <label class="flex items-center gap-1">
            C:
            <input type="radio" name="editCorrectAnswer" value="C">
          </label>
          <label class="flex items-center gap-1">
            D:
            <input type="radio" name="editCorrectAnswer" value="D">
          </label>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label for="editOptionA" class="block font-medium">Option A</label>
          <input type="text" id="editOptionA" name="optionA" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <div>
          <label for="editOptionB" class="block font-medium">Option B</label>
          <input type="text" id="editOptionB" name="optionB" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <div>
          <label for="editOptionC" class="block font-medium">Option C</label>
          <input type="text" id="editOptionC" name="optionC" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <div>
          <label for="editOptionD" class="block font-medium">Option D</label>
          <input type="text" id="editOptionD" name="optionD" class="w-full border border-gray-300 rounded p-2" required>
        </div>
      </div>

      <div class="text-right pt-4">
        <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>
<!-- Delete modal -->
<div id="popup-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
  <div class="relative p-4 w-full max-w-md">
    <div class="relative bg-white rounded-lg shadow-2xl ring-1 ring-gray-300">

      <!-- Close Button -->
      <button type="button"
        class="absolute top-3 right-3 text-gray-400 hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex items-center justify-center"
        onclick="hideModal()">
        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13" />
        </svg>
        <span class="sr-only">Close modal</span>
      </button>

      <!-- Modal Content -->
      <div class="p-6 text-center">
        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" fill="none" viewBox="0 0 20 20">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <h3 class="mb-5 text-lg font-semibold text-gray-700">Are you sure you want to delete this question?</h3>
        <button id="confirmDeleteBtn" type="button"
          class= bg-red-600 hover:bg-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5">
          Yes, I'm sure
        </button>
        <button onclick="hideModal()" type="button"
          class="ml-3 py-2.5 px-5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-blue-700">
          No, cancel
        </button>
      </div>
    </div>
  </div>
</div>


<script>
  $(document).ready(function() {
    $('#questionsTable').DataTable({
      pageLength: 10,

      searching: true,
      ordering: true,
      columnDefs: [{
          orderable: false,
          targets: 7
        } // Disable ordering on "Action" column
      ],
      language: {
        search: "Filter questions:",
        emptyTable: "No questions available"
      },
      lengthMenu: [5, 10, 25, 50],

    });
  });

  function showToasts(message = 'Question Updated Successfully', type = 'success') {
    const toastContainer = document.getElementById(`toast-${type}`);
    if (!toastContainer) return;

    const textContainer = toastContainer.querySelector('.toast-message');
    if (textContainer) {
      textContainer.textContent = message;
    }

    toastContainer.classList.remove('hidden');

    setTimeout(() => {
      toastContainer.classList.add('hidden');
      location.reload();
    }, 1000);
  }

  function getQuizIDFromURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get('quizID');
  }

  function addModal() {
    document.getElementById('addQuestionForm').reset();
    document.getElementById('addQuestionModal').classList.remove('hidden');
  }
  // Close Add Modal
  function closeAddModal() {
    document.getElementById('addQuestionModal').classList.add('hidden');
  }

  // Handle Add Question form submit with AJAX
  document.getElementById('addQuestionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const quizID = getQuizIDFromURL();
    if (!quizID) {
      alert('Quiz ID not found in URL!');
      return;
    }

    // Get raw values
    const optionAVal = document.getElementById('addOptionA').value.trim();
    const optionBVal = document.getElementById('addOptionB').value.trim();
    const optionCVal = document.getElementById('addOptionC').value.trim();
    const optionDVal = document.getElementById('addOptionD').value.trim();

    // Prefix with letter + dot + space
    const optionA = `A. ${optionAVal}`;
    const optionB = `B. ${optionBVal}`;
    const optionC = `C. ${optionCVal}`;
    const optionD = `D. ${optionDVal}`;

    // Get the selected correct answer letter (e.g., 'A', 'B', 'C', or 'D')
    const correctLetter = document.querySelector('input[name="addCorrectAnswer"]:checked').value;

    // Map letter to full correct answer with letter + dot prefix
    const correctAnswerMap = {
      A: optionA,
      B: optionB,
      C: optionC,
      D: optionD
    };
    const correctAnswer = correctAnswerMap[correctLetter];

    const formData = {
      quizID: quizID,
      question: document.getElementById('addQuestionDesc').value,
      correctAnswer: correctAnswer,
      optionA: optionA,
      optionB: optionB,
      optionC: optionC,
      optionD: optionD,
    };

    fetch('backend/question_create.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showToasts();
          closeAddModal();
          // Refresh UI or question list if needed
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        alert('AJAX error: ' + err.message);
      });
  });

  // Show the modal and populate data
  function stripPrefix(text) {
    return text.replace(/^[A-D]\.\s*/, ''); // Remove letter + dot + space at start if exists
  }

  document.querySelectorAll('.editBtn').forEach(button => {
    button.addEventListener('click', () => {
      const qID = button.dataset.questionid;
      const qDesc = button.dataset.questiondesc;
      const optionA = button.dataset.optiona;
      const optionB = button.dataset.optionb;
      const optionC = button.dataset.optionc;
      const optionD = button.dataset.optiond;
      const correctAnswerFull = button.dataset.correctanswer; // e.g. "A. 15"

      // Fill form fields - strip prefix before putting in inputs
      document.getElementById('editQuestionID').value = qID;
      document.getElementById('editQuestionDesc').value = qDesc;
      document.getElementById('editOptionA').value = stripPrefix(optionA);
      document.getElementById('editOptionB').value = stripPrefix(optionB);
      document.getElementById('editOptionC').value = stripPrefix(optionC);
      document.getElementById('editOptionD').value = stripPrefix(optionD);

      // Extract the letter from correctAnswer (e.g. "A. 15" -> "A")
      const correctLetter = correctAnswerFull.trim().charAt(0).toUpperCase();

      // Select the correct radio
      const radios = document.getElementsByName('editCorrectAnswer');
      radios.forEach(radio => {
        radio.checked = (radio.value === correctLetter);
      });

      // Show modal
      document.getElementById('editQuestionModal').classList.remove('hidden');
    });
  });

  function closeEditModal() {
    document.getElementById('editQuestionModal').classList.add('hidden');
  }

  document.getElementById('editQuestionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const qID = document.getElementById('editQuestionID').value;
    const qDesc = document.getElementById('editQuestionDesc').value.trim();

    // Strip prefix before prefixing again
    const optionA = stripPrefix(document.getElementById('editOptionA').value.trim());
    const optionB = stripPrefix(document.getElementById('editOptionB').value.trim());
    const optionC = stripPrefix(document.getElementById('editOptionC').value.trim());
    const optionD = stripPrefix(document.getElementById('editOptionD').value.trim());

    // Find selected correct answer letter
    const correctAnswer = [...document.getElementsByName('editCorrectAnswer')]
      .find(radio => radio.checked)?.value;

    if (!correctAnswer) {
      alert('Please select the correct answer.');
      return;
    }

    // Prefix options and correct answer letter + dot + space (same as add)
    const formattedOptionA = `A. ${optionA}`;
    const formattedOptionB = `B. ${optionB}`;
    const formattedOptionC = `C. ${optionC}`;
    const formattedOptionD = `D. ${optionD}`;

    const correctAnswerMap = {
      A: formattedOptionA,
      B: formattedOptionB,
      C: formattedOptionC,
      D: formattedOptionD
    };

    const formattedCorrectAnswer = correctAnswerMap[correctAnswer];

    // Send AJAX update request
    fetch('backend/question_update.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          questionID: qID,
          questionDesc: qDesc,
          optionA: formattedOptionA,
          optionB: formattedOptionB,
          optionC: formattedOptionC,
          optionD: formattedOptionD,
          correctAnswer: formattedCorrectAnswer
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showToasts();
          closeEditModal();
          // Refresh your question list if needed
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => alert('AJAX error: ' + err.message));
  });


  document.getElementById('importExcelBtn').addEventListener('click', () => {
    document.getElementById('importFileInput').click();
  });

  const importFileInput = document.getElementById('importFileInput');

  importFileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const quizID = getQuizIDFromURL();
    if (!quizID) {
      alert('Quiz ID not found in URL.');
      return;
    }

    const allowedTypes = [
      'text/csv',
      'application/vnd.ms-excel',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (!allowedTypes.includes(file.type)) {
      alert('Please upload a valid CSV or Excel file.');
      return;
    }

    const formData = new FormData();
    formData.append('importFile', file);
    formData.append('quizID', quizID); // <-- Use quizID from URL

    fetch('backend/import_questions.php', {
        method: 'POST',
        body: formData,
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showToasts();
          // Optionally refresh your question list here
        } else {
          alert('Import failed: ' + data.message);
        }
      })
      .catch(err => alert('Upload error: ' + err.message));
  });

  let questionToDelete = null;

  function showModal() {
    document.getElementById('popup-modal').classList.remove('hidden');
    document.getElementById('popup-modal').classList.add('flex');
  }

  function hideModal() {
    document.getElementById('popup-modal').classList.add('hidden');
    document.getElementById('popup-modal').classList.remove('flex');
    questionToDelete = null;
  }

  document.querySelectorAll('.delBtn').forEach(button => {
    button.addEventListener('click', () => {
      questionToDelete = button.dataset.delQuestionid;
      showModal();
    });
  });

  document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
    if (!questionToDelete) return;

    fetch('backend/question_delete.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          questionID: questionToDelete
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showToasts();
          // Remove the corresponding row
          const row = document.querySelector(`button[data-del-questionid="${questionToDelete}"]`)?.closest('tr');
          if (row) row.remove();
        } else {
          alert('Failed to delete: ' + data.message);
        }
        hideModal();
      })
      .catch(err => {
        alert('AJAX error: ' + err.message);
        hideModal();
      });
  });
</script>
<?php
include_once('components/footer.php');
?>