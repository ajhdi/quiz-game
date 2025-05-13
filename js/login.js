document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const role = form.getAttribute('data-role');
    const formData = new FormData(form);
    formData.append('role', role);

    const alertBox = document.getElementById('alertBox');

    try {
        const response = await fetch('backend/login.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.text();

        if (result.trim() === 'success') {
            alertBox.textContent = 'Login successful! Redirecting...';
            alertBox.className = 'mt-4 text-sm px-4 py-2 rounded-lg bg-green-500 text-white font-medium';
            setTimeout(() => {
                window.location.href = role === 'teacher' ? 'teacher_dashboard.php' : 'student_dashboard.php';
            }, 1500);
        } else {
            alertBox.textContent = result;
            alertBox.className = 'mt-4 text-sm px-4 py-2 rounded-lg bg-red-500 text-white font-medium';
        }

        alertBox.classList.remove('hidden');
    } catch (err) {
        alertBox.textContent = 'Error occurred. Try again.';
        alertBox.className = 'mt-4 text-sm px-4 py-2 rounded-lg bg-red-500 text-white font-medium';
        alertBox.classList.remove('hidden');
    }
});
