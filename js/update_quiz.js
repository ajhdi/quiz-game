
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
  }, 3000);
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
        // Close modal
        closeModal('modal-' + quizID);
        showToast();
        // Update card content (basic approach)
        const card = document.querySelector(`#modal-${quizID}`).closest('.max-w-sm');
        card.querySelector('h5').textContent = formData.get('quizTitle');
        card.querySelectorAll('p')[0].innerHTML = `<strong>Subject:</strong> ${formData.get('subjectDesc')} (${formData.get('subjectCode')})`;
        card.querySelectorAll('p')[1].innerHTML = `<strong>Course:</strong> ${formData.get('courseCode')}`;
        card.querySelectorAll('p')[2].innerHTML = `<strong>Section:</strong> ${formData.get('yearSection')}`;

        const badge = card.querySelector('span');
        const active = formData.get('isActive') === "1";
        badge.textContent = active ? 'Active' : 'Inactive';
        badge.className = `inline-block px-3 py-1 text-sm rounded-full ${active ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
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
