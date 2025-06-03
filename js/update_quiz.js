
  function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
  }

  function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
  }

function showToast(message = 'Quiz Updated Successfully', type = 'success') {
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
  }, 2000);
}




document.querySelectorAll('.update-quiz-form').forEach(form => {
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const quizID = this.dataset.id;
    const formData = new FormData(this);

    fetch('backend/quiz_update.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Close modal and show toast
        closeModal('modal-' + quizID);
        showToast();
      
        // Find the card corresponding to this quiz
        const card = document.querySelector(`#modal-${quizID}`).closest('.max-w-sm');
        if (!card) return;

        // Update quiz title
        const titleElem = card.querySelector('h5');
        if (titleElem) titleElem.textContent = formData.get('quizTitle');

        // Update Subject
        const subject = formData.get('subjectDesc');
        const subjectCode = formData.get('subjectCode');
        const subjectElem = card.querySelectorAll('p')[0];
        if (subjectElem) subjectElem.innerHTML = `<strong>Subject:</strong> ${subject} (${subjectCode})`;

        // Update Course
        const courseCode = formData.get('courseCode');
        const courseElem = card.querySelectorAll('p')[1];
        if (courseElem) courseElem.innerHTML = `<strong>Course:</strong> ${courseCode}`;

        // Update Section
        const yearSection = formData.get('yearSection');
        const sectionElem = card.querySelectorAll('p')[2];
        if (sectionElem) sectionElem.innerHTML = `<strong>Section:</strong> ${yearSection}`;

        // Update Timer
        const timeLimit = parseInt(formData.get('timeLimit'), 10);
        let timeLimitDisplay = card.querySelector('p')[3];
        const timerHTML = `<strong>Timer:</strong> ${timeLimit > 0 ? timeLimit + ' min' : 'No limit'}`;

        timeLimitDisplay.innerHTML = timerHTML;
        
      } else {
        alert('Failed to update quiz: ' + data.error);
      }
    });
  });
});


document.getElementById('create-quiz-form').addEventListener('submit', function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch('backend/quiz_create.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      closeModal('create-quiz-modal');
      showToast('Quiz created successfully');
      location.reload();
      // Optionally refresh quiz list or insert new card dynamically
    } else {
      alert('Failed to create quiz: ' + data.error);
    }
  });
});

  
