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
<!-- Tabs + Info Card Wrapper -->
<div class="flex justify-between items-center ">
  <!-- Tabs on the left -->
  <div class="flex">
    <button class="tab-btn px-4 py-2 rounded-t-md text-black bg-gray-200 font-medium" id="tab-question">Question</button>
    <button class="tab-btn px-4 py-2 rounded-t-md text-black bg-gray-200 font-medium" id="tab-result">Result</button>
  </div>

  <!-- Action buttons on the right -->
  <div class="flex gap-2">
    <button onclick="addModal()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded flex items-center text-sm">
      <i class="fas fa-plus mr-2"></i> Add Question
    </button>
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded flex items-center text-sm">
      <i class="fas fa-file-import mr-2"></i> Import Excel
    </button>
  </div>
</div>

<div class="bg-white p-4 shadow-md rounded-md ">
  <!-- Quiz Info Row -->
  <div class="flex flex-col md:flex-row items-center justify-between text-gray-800 text-sm md:text-base font-medium">

    <!-- Left: Subject -->
    <div class="mb-2 md:mb-0 text-left w-full md:w-1/3">
      <span class="text-gray-500">Subject:</span>
      <span class="text-black"><?= htmlspecialchars($quiz['subjectCode']) ?>:</span>
      <?= htmlspecialchars($quiz['subjectDesc']) ?>
    </div>

    <!-- Center: Title -->
    <div class="mb-2 md:mb-0 text-center w-full md:w-1/3">
      <span class="text-blue-600 text-lg font-semibold"><?= htmlspecialchars($quiz['quizTitle']) ?></span>
    </div>

    <!-- Right: Course & Section -->
    <div class="text-right w-full md:w-1/3">
      <span class="text-gray-500">Course:</span>
      <span class="text-black"><?= htmlspecialchars($quiz['courseCode']) ?></span>
      <span class="mx-1">|</span>
      <span class="text-gray-500">Section:</span>
      <span class="text-black"><?= htmlspecialchars($quiz['yearSection']) ?></span>
    </div>
  </div>
</div>


<div id="questionTableContainer"></div>

<!-- Modal: now with form -->
<div id="questionModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
    <button onclick="closeModalQ()" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">&times;</button>
    <h2 class="text-xl font-semibold mb-4">Edit Question</h2>

    <form id="updateQuestionForm" class="space-y-3 text-sm">
      <input type="hidden" id="modalQuestionID">

      <div>
        <label class="block font-medium">Question</label>
        <textarea id="modalQuestionDesc" class="w-full border border-gray-300 rounded p-2" rows="2" required></textarea>
      </div>
      <div>
        <label class="block font-medium mb-1 text-center">Correct Answer</label>
        <div class="flex justify-center gap-6">
            <label class="flex items-center gap-1">
            A:
            <input type="radio" name="modalCorrectAnswer" value="A" required>
            </label>
            <label class="flex items-center gap-1">
            B:
            <input type="radio" name="modalCorrectAnswer" value="B">
            </label>
            <label class="flex items-center gap-1">
            C:
            <input type="radio" name="modalCorrectAnswer" value="C">
            </label>
            <label class="flex items-center gap-1">
            D:
            <input type="radio" name="modalCorrectAnswer" value="D">
            </label>
        </div>
    </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-medium">Option A</label>
          <input type="text" id="modalOptionA" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <div>
          <label class="block font-medium">Option B</label>
          <input type="text" id="modalOptionB" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <div>
          <label class="block font-medium">Option C</label>
          <input type="text" id="modalOptionC" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <div>
          <label class="block font-medium">Option D</label>
          <input type="text" id="modalOptionD" class="w-full border border-gray-300 rounded p-2" required>
        </div>
      </div>
      <div>
      

      <div class="text-right pt-4">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Add Question Modal -->
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
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
          Add Question
        </button>
      </div>
    </form>
  </div>
</div>



<script>
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

  const btnQuestion = document.getElementById('tab-question');
  const btnResult = document.getElementById('tab-result');
  const tabContent = document.getElementById('tab-content');

  // Injected from PHP below
  const questionTableHTML = `<?= json_encode($questions, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>`;
  const questionData = JSON.parse(questionTableHTML);

  function renderQuestionTable() {
    if (questionData.length === 0) {
      return "<p class='text-gray-600'>No questions found for this quiz.</p>";
    }

    let rows = questionData.map((q, index) => `
      <tr class="border-b bg-white">
        <td class="px-4 py-2 font-medium text-gray-800">${index + 1}</td>
        <td class="px-4 py-2">${q.questionDesc}</td>
        <td class="px-4 py-2">${q.optionA}</td>
        <td class="px-4 py-2">${q.optionB}</td>
        <td class="px-4 py-2">${q.optionC}</td>
        <td class="px-4 py-2">${q.optionD}</td>
        <td class="px-4 py-2 text-green-700 font-bold">${q.correctAnswer}</td>
        <td class="px-4 py-2">
            <button class="edit-btn bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm"
              data-question='${JSON.stringify(q).replace(/'/g, "&#39;")}'
            >
              Edit
            </button>

        </td>
      </tr>
    `).join('');

    return `
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto border text-sm text-left text-gray-700">
          <thead class="bg-white text-gray-700 uppercase">
            <tr>
              <th class="px-4 py-2">#</th>
              <th class="px-4 py-2">Question</th>
              <th class="px-4 py-2">A</th>
              <th class="px-4 py-2">B</th>
              <th class="px-4 py-2">C</th>
              <th class="px-4 py-2">D</th>
              <th class="px-4 py-2">Answer</th>
              <th class="px-4 py-2">Action</th>
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
      </div>
    `;
  }

  document.querySelectorAll('.edit-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const question = JSON.parse(btn.getAttribute('data-question'));
    openModalQ(question); // assuming openModalQ accepts a JS object
  });
});


  function openModalQ(q) {
  document.getElementById('modalQuestionID').value = q.questionID;
  document.getElementById('modalQuestionDesc').value = q.questionDesc;
  document.getElementById('modalOptionA').value = q.optionA;
  document.getElementById('modalOptionB').value = q.optionB;
  document.getElementById('modalOptionC').value = q.optionC;
  document.getElementById('modalOptionD').value = q.optionD;

  // Parse correct answer letter from "C. Apple"
  const correctLetter = q.correctAnswer?.trim().charAt(0).toUpperCase();

  // Set the correct radio button
    const radios = document.getElementsByName('modalCorrectAnswer');
    radios.forEach(radio => {
        radio.checked = radio.value === correctLetter;
    });

    document.getElementById('questionModal').classList.remove('hidden');
    }
    

  function closeModalQ() {
    document.getElementById('questionModal').classList.add('hidden');
  }

  function renderAndAttach() {
      document.getElementById('questionTableContainer').innerHTML = renderQuestionTable();

      // Attach event listeners AFTER the buttons exist
      document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          const question = JSON.parse(btn.getAttribute('data-question'));
          openModalQ(question);
        });
      });
    }

    // Call this function after page load or after questions data is ready
    renderAndAttach();


  document.getElementById('updateQuestionForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const correctRadio = document.querySelector('input[name="modalCorrectAnswer"]:checked');
        if (!correctRadio) {
            alert('Please select the correct answer.');
            return;
        }

        const correctLetter = correctRadio.value;
        const optionMap = {
            A: document.getElementById('modalOptionA').value,
            B: document.getElementById('modalOptionB').value,
            C: document.getElementById('modalOptionC').value,
            D: document.getElementById('modalOptionD').value
        };
        const correctAnswer = `${optionMap[correctLetter]}`;

        const formData = new FormData();
        formData.append('questionID', document.getElementById('modalQuestionID').value);
        formData.append('questionDesc', document.getElementById('modalQuestionDesc').value);
        formData.append('optionA', optionMap.A);
        formData.append('optionB', optionMap.B);
        formData.append('optionC', optionMap.C);
        formData.append('optionD', optionMap.D);
        formData.append('correctAnswer', correctAnswer);

            try {
                const response = await fetch('backend/question_update.php', {
                method: 'POST',
                body: formData
                });
                const result = await response.text();
                showToasts(); // Show your toast notification
                closeModalQ(); // Close modal
                // Optionally, refresh your table or list here
            } catch (err) {
                console.error(err);
                alert('Failed to update question.');
            }
  });


  function addModal() {
  // Reset the add form fields
  document.getElementById('addQuestionForm').reset();

    // Show the Add Question modal
    document.getElementById('addQuestionModal').classList.remove('hidden');
  }

  // Close Add Modal
  function closeAddModal() {
    document.getElementById('addQuestionModal').classList.add('hidden');
  }

// Handle Add Question form submit with AJAX
  document.getElementById('addQuestionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Prepare form data
    const formData = {
      question: document.getElementById('addQuestionDesc').value,
      correctAnswer: document.querySelector('input[name="addCorrectAnswer"]:checked').value,
      optionA: document.getElementById('addOptionA').value,
      optionB: document.getElementById('addOptionB').value,
      optionC: document.getElementById('addOptionC').value,
      optionD: document.getElementById('addOptionD').value,
    };

    // Example AJAX using fetch (change URL to your endpoint)
    fetch('/your-add-question-endpoint', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
      if(data.success) {
        alert('Question added successfully!');
        closeAddModal();
        // Optionally refresh question list or UI here
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(err => {
      alert('AJAX error: ' + err.message);
    });
  });


  const actionButtons = document.getElementById("actionButtons");

    function setActiveTab(activeBtn, inactiveBtn, contentHTML, showActions = true) {
    activeBtn.style.backgroundColor = "#FEE6A0";
    activeBtn.classList.remove("text-yellow-700", "bg-gray-100");
    activeBtn.classList.add("text-black");

    inactiveBtn.style.backgroundColor = "";
    inactiveBtn.classList.remove("text-black");
    inactiveBtn.classList.add("text-yellow-700", "bg-gray-100");

    tabContent.innerHTML = contentHTML;

    // Show or hide the action buttons
    if (showActions) {
        actionButtons.classList.remove("hidden");
    } else {
        actionButtons.classList.add("hidden");
    }
    }

    btnQuestion.addEventListener("click", () => {
    setActiveTab(btnQuestion, btnResult, renderQuestionTable(), true);
    });

    btnResult.addEventListener("click", () => {
    setActiveTab(btnResult, btnQuestion, "<p>Result content here...</p>", false);
    });

    // Load questions by default on page load
    window.addEventListener("DOMContentLoaded", () => {
    setActiveTab(btnQuestion, btnResult, renderQuestionTable(), true);
    });

</script>
<?php
include_once('components/footer.php');
?>