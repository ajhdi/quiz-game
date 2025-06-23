<?php
include_once('components/header.php');
include_once('components/toast.php');
include_once('backend/conn.php');
$quizID = $_GET['quizID'] ?? null;

if (!$quizID) {
    die("Quiz ID not provided.");
}

if (!$studentID || !$quizID) {
    // Redirect if not logged in or no quiz ID
    header("Location: student_dashboard.php");
    exit;
}
$checkStmt = $conn->prepare("SELECT 1 FROM result_tbl WHERE studentID = :studentID AND quizID = :quizID");
$checkStmt->bindParam(':studentID', $studentID, PDO::PARAM_INT);
$checkStmt->bindParam(':quizID', $quizID, PDO::PARAM_INT);
$checkStmt->execute();

if ($checkStmt->fetch()) {
    // Redirect if already taken
    header("Location: student_dashboard.php?message=already_taken");
    exit;
}

$quizID = intval($quizID); // sanitize

// Use PDO query & fetch
$stmt = $conn->query("SELECT quizID, quizTitle, subjectDesc, subjectCode, courseCode, yearSection, timer, isActive 
                      FROM quiz_tbl 
                      WHERE quizID = $quizID");

$quiz = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$quiz) {
    die("Quiz not found.");
}

$stmts = $conn->prepare("
    SELECT question_tbl.questionID, question_tbl.questionDesc, question_tbl.optionA, question_tbl.optionB, 
           question_tbl.optionC, question_tbl.optionD, question_tbl.correctAnswer, quiz_tbl.quizTitle
    FROM question_tbl
    JOIN quiz_tbl ON question_tbl.quizID = quiz_tbl.quizID
    WHERE question_tbl.quizID = :quizID
");

$stmts->bindParam(':quizID', $quizID, PDO::PARAM_INT);
$stmts->execute();
$questions = $stmts->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="css/student_question.css">
<div id="popup-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="relative p-4 w-full max-w-md">
        <div class="relative bg-white rounded-lg shadow-2xl ring-1 ring-gray-300">
            <button type="button"
                class="absolute top-3 right-3 text-gray-400 hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex items-center justify-center"
                onclick="hideModal()">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13" />
                </svg>
                <span class="sr-only">Close modal</span>
            </button>

            <div class="p-6 text-center">
                <h3 id="modal-title" class="mb-5 text-lg font-semibold text-gray-700">Game Over</h3>
                <button onclick="hideModal()" type="button"
                    class="py-2.5 px-5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-800 rounded-lg">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<body>
    <div class="game-container">
        <div class="game-card">
            <header>
                <h1><?= htmlspecialchars($quiz['subjectCode']) ?></h1>
            </header>

            <div class="game-content">
                <div class="progress-bar">
                    <div id="progress"></div>
                </div>

                <div class="question-container">
                    <p id="category" class="category-text"><?= htmlspecialchars($quiz['quizTitle']) ?></p>
                    <h2 id="question"></h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <button class="answer bg-yellow-200 hover:bg-yellow-300" data-id="1">Choice 1</button>
                    <button class="answer bg-yellow-200 hover:bg-yellow-300" data-id="2">Choice 2</button>
                    <button class="answer bg-yellow-200 hover:bg-yellow-300" data-id="3">Choice 3</button>
                    <button class="answer bg-yellow-200 hover:bg-yellow-300" data-id="4">Choice 4</button>
                </div>
                <div class="status-bar">
                    <p>Time Left: <span id="time"></span></p>
                </div>

                <?php if ((int) $quiz['timer'] > 0): ?>
                    <button id="start" class="my-2 px-4 py-2 bg-yellow-200 rounded hover:bg-yellow-300">Start Exam</button>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>
<script>
    const studentID = <?= json_encode($studentID) ?>;
    const quizID = <?= (int) $quiz['quizID'] ?>;

    const questions = <?= json_encode(array_map(function ($q) {
        return [
            'category' => $q['quizTitle'],
            'question' => $q['questionDesc'],
            'options' => [$q['optionA'], $q['optionB'], $q['optionC'], $q['optionD']],
            'answer' => ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3][substr($q['correctAnswer'], 0, 1)] ?? 0
        ];
    }, $questions), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    const timerSetting = <?= (int) $quiz['timer'] ?>;
    const hasTimer = timerSetting > 0;

    let shuffledQuestions = [];
    let currentQuestionIndex = 0;
    let score = 0;
    let progress = 0;
    let timeLeft = timerSetting * 60;
    let timerInterval;
    let quizStarted = false;

    const categoryElem = document.getElementById('category');
    const questionElem = document.getElementById('question');
    const answerButtons = document.querySelectorAll('.answer');
    const progressElem = document.getElementById('progress');
    const timeElem = document.getElementById('time');
    const timeContainer = timeElem?.parentElement;
    const startBtn = document.getElementById('start');
    const modal = document.getElementById('popup-modal');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const modalTitle = document.getElementById('modal-title');

    // Load saved state if it exists
    const savedState = localStorage.getItem(`quizState_${quizID}_${studentID}`);
    if (savedState) {
        const state = JSON.parse(savedState);
        currentQuestionIndex = state.currentQuestionIndex || 0;
        score = state.score || 0;
        timeLeft = state.timeLeft || timerSetting * 60;
        shuffledQuestions = state.shuffledQuestions || questions;
        quizStarted = state.started || false;
    } else {
        shuffledQuestions = [...questions];
        shuffle(shuffledQuestions);
    }

    if (!hasTimer && timeContainer) {
        timeContainer.style.display = 'none';
    }

    function loadQuestion() {
        const questionData = shuffledQuestions[currentQuestionIndex];
        categoryElem.innerText = `Category: ${questionData.category}`;
        questionElem.innerText = questionData.question;

        answerButtons.forEach((btn, index) => {
            btn.innerText = questionData.options[index];
            btn.disabled = false;
        });

        progress = ((currentQuestionIndex + 1) / shuffledQuestions.length) * 100;
        progressElem.style.width = `${progress}%`;

        saveState(); // Save after loading
    }

    function shuffle(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
    }

    function updateTimer() {
        timeLeft--;
        displayTime();

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            endGame();
        }

        saveState(); // Save every second
    }

    function displayTime() {
        if (!hasTimer) return;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timeElem.innerText = timeLeft > 60 ?
            `${minutes} minute${minutes > 1 ? 's' : ''} ${seconds} second${seconds !== 1 ? 's' : ''}` :
            `${timeLeft} second${timeLeft !== 1 ? 's' : ''}`;
    }

    function checkAnswer(selectedIndex) {
        if (!quizStarted) return;

        const questionData = shuffledQuestions[currentQuestionIndex];
        if (selectedIndex === questionData.answer) score++;

        answerButtons.forEach(btn => btn.disabled = true);
        setTimeout(nextQuestion, 1000);
    }

    function nextQuestion() {
        currentQuestionIndex++;
        if (currentQuestionIndex < shuffledQuestions.length) {
            loadQuestion();
        } else {
            endGame();
        }
    }

    function endGame() {
        if (hasTimer) clearInterval(timerInterval);
        localStorage.removeItem(`quizState_${quizID}_${studentID}`);
        saveScore(score, shuffledQuestions.length);
        showModal(score, shuffledQuestions.length);
    }

    function saveScore(score, total) {
        if (!studentID) return;
        fetch('backend/result.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ studentID, quizID, score, total })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') console.error('Failed to save score:', data.message);
            })
            .catch(err => console.error('Error saving score:', err));
    }

    function showModal(score, total) {
        modalTitle.innerText = `Your score: ${score}/${total}`;
        if (confirmBtn) confirmBtn.classList.add('hidden');
        modal.classList.remove('hidden');
    }

    function hideModal() {
        modal.classList.add('hidden');
        window.location.href = 'student_dashboard.php';
    }

    function saveState() {
        const state = {
            currentQuestionIndex,
            score,
            timeLeft,
            shuffledQuestions,
            started: quizStarted
        };
        localStorage.setItem(`quizState_${quizID}_${studentID}`, JSON.stringify(state));
    }

    // Start logic
    if (hasTimer && startBtn) {
        if (quizStarted) {
            startBtn.classList.add('hidden');
            quizStarted = true;
            answerButtons.forEach(btn => btn.disabled = false);
            displayTime();
            timerInterval = setInterval(updateTimer, 1000);
            loadQuestion();
        } else {
            answerButtons.forEach(btn => btn.disabled = true);
            startBtn.addEventListener('click', () => {
                quizStarted = true;
                startBtn.classList.add('hidden');
                answerButtons.forEach(btn => btn.disabled = false);
                displayTime();
                timerInterval = setInterval(updateTimer, 1000);
                loadQuestion();
            });
        }
    } else {
        // No timer mode or no start button
        quizStarted = true;
        answerButtons.forEach(btn => btn.disabled = false);
        loadQuestion();
    }

    answerButtons.forEach((btn, index) => {
        btn.addEventListener('click', () => checkAnswer(index));
    });

    // Warn before closing
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = 'You have an ongoing quiz. Are you sure you want to leave?';
    });
</script>


<?php
include_once('components/footer.php');
?>