<?php
$resultCheckStmt = $conn->prepare("SELECT scores, totalScores FROM result_tbl WHERE studentID = ? AND quizID = ?");
$resultCheckStmt->execute([$studentID, $quizID]);
$result = $resultCheckStmt->fetch(PDO::FETCH_ASSOC);
$hasTakenQuiz = $result !== false;

$escapedQuizTitle = htmlspecialchars(addslashes($quizTitle));
$escapedSubjectDesc = htmlspecialchars(addslashes($subjectDesc));
$score = $hasTakenQuiz ? (int)$result['scores'] : 0;
$totalScore = $hasTakenQuiz ? (int)$result['totalScores'] : 0;
$quizIsActive = (bool)$isActive;

$onclick = "";
if ($hasTakenQuiz) {
  // Show result modal if already taken, regardless of active status
  $onclick = "showResultModal('{$escapedQuizTitle}', '{$escapedSubjectDesc}', {$score}, {$totalScore})";
} elseif (!$quizIsActive) {
  // Show toast if inactive and not taken
  $onclick = "showToast('This quiz is currently inactive.')";
} else {
  // Navigate to quiz if active and not taken
  $url = "student_question.php?quizID=" . urlencode($quizID);
  $onclick = "window.location.href='{$url}'";
}

?>

<div
  onclick="<?= $onclick ?>"
  class="cursor-pointer p-6 bg-white border border-gray-300 rounded-xl shadow-md dark:bg-gray-900 dark:border-gray-700 hover:shadow-lg hover:bg-gray-50 dark:hover:bg-gray-800 transform transition-transform duration-300 ease-in-out hover:scale-105 select-none"
  role="button"
  tabindex="0"
  onkeypress="if(event.key === 'Enter' || event.key === ' ') { <?= $onclick ?> }">
  <div class="flex justify-between items-start">
    <h5 class="text-2xl font-semibold text-gray-900 dark:text-white truncate max-w-[70%]">
      <?= htmlspecialchars($quizTitle) ?>
    </h5>
    <?php if ($hasTakenQuiz): ?>
      <span class="ml-4 inline-block px-3 py-1 text-xs font-semibold text-white bg-green-600 rounded-full select-none">
        Already Taken
      </span>
    <?php endif; ?>
  </div>

  <div class="mt-3 space-y-1 text-gray-700 dark:text-gray-300 text-sm">
    <p class="truncate">
      <strong>Subject:</strong> <?= htmlspecialchars($subjectDesc) ?> (<?= htmlspecialchars($subjectCode) ?>)
    </p>
    
    <p>
      <strong>Professor:</strong> <?= htmlspecialchars($professorLastName) ?>
    </p>
    <p>
      <strong>No. of Questions:</strong> <?= htmlspecialchars($questionCount) ?>
    </p>
    <p class="flex items-center space-x-2">
      <strong>Status:</strong>
      <?php if ($quizIsActive): ?>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M5 13l4 4L19 7" />
          </svg>
          Active
        </span>
      <?php else: ?>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
          Inactive
        </span>
      <?php endif; ?>
    </p>
  </div>
</div>

<div id="resultModal" class="hidden fixed inset-0 z-50 flex items-start justify-center pt-20 bg-black bg-opacity-60 backdrop-blur-sm transition-opacity duration-300">
  <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl p-6 max-w-sm w-full relative transform scale-95 opacity-0 transition-transform duration-300 ease-out text-center"
    style="will-change: transform, opacity;">
    <button
      onclick="hideResultModal()"
      class="absolute top-3 right-3 text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
      aria-label="Close modal">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
    <h2 id="resultQuizTitle" class="text-xl font-bold text-gray-800 dark:text-gray-100 truncate tracking-tight"></h2>
    <p id="resultSubject" class="text-lg text-gray-600 dark:text-gray-400 text-base font-semibold leading-relaxed"></p>
    <p id="resultScore" class="text-lg font-semibold text-green-700 dark:text-green-300 tracking-wide"></p>
  </div>
</div>

<div class="fixed top-4 inset-x-0 flex justify-center z-50">
  <!-- Toast Success -->
  <div id="toast-success" class="hidden flex items-center w-full max-w-2xl px-6 py-4 text-red-800 bg-red-100 rounded-lg shadow-md dark:bg-red-900 dark:text-red-200" role="alert">
    <!-- Icon -->
    <div class="inline-flex items-center justify-center w-8 h-8 bg-red-200 rounded-full dark:bg-red-700">
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
      </svg>
    </div>
    <!-- Message -->
    <div class="ms-3 text-sm font-medium toast-message">Success!</div>
  </div>
</div>

<script>
  function showResultModal(title, subject, score, total) {
    const modal = document.getElementById('resultModal');
    const modalContent = modal.firstElementChild;

    document.getElementById('resultQuizTitle').innerText = title;
    document.getElementById('resultSubject').innerText = `Subject: ${subject}`;
    document.getElementById('resultScore').innerText = `Score: ${score} / ${total}`;

    modal.classList.remove('hidden');
    // Animate modal scale & opacity in
    requestAnimationFrame(() => {
      modalContent.classList.remove('scale-95', 'opacity-0');
      modalContent.classList.add('scale-100', 'opacity-100');
    });
  }

  function hideResultModal() {
    const modal = document.getElementById('resultModal');
    const modalContent = modal.firstElementChild;

    // Animate modal scale & opacity out
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    // Wait for animation to finish before hiding
    modalContent.addEventListener('transitionend', () => {
      modal.classList.add('hidden');
    }, {
      once: true
    });
  }

  function showToast(message, duration = 3000) {
    const toastWrapper = document.getElementById('toast-wrapper');
    const toast = document.getElementById('toast-success');
    const toastMessage = toast.querySelector('.toast-message');


    toastMessage.innerText = message;
    toast.classList.remove('hidden');
    toastWrapper.style.pointerEvents = 'auto';

    setTimeout(() => {
      toast.classList.add('hidden');
      toastWrapper.style.pointerEvents = 'none';
    }, duration);
  }
</script>