function showSuccessModal() {
  document.getElementById("successModal").classList.remove("hidden");
}

function closeSuccessModal() {
  document.getElementById("successModal").classList.add("hidden");
}

function unSuccessModal(message) {
  const toast = document.getElementById("toast-failed");
  const toastMessage = toast.querySelector(".toast-message");

  toastMessage.textContent = message; // Set the result.message here
  toast.classList.remove("hidden");

  // Optionally auto-hide after a few seconds
  setTimeout(() => {
    toast.classList.add("hidden");
  }, 3000);
}

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("registerForm");

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(form);
    const role = formData.get("role");

    try {
      const response = await fetch("backend/registration.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        showSuccessModal();
        setTimeout(() => {
          window.location.href = `login.php?role=${encodeURIComponent(role)}`;
        }, 2000);
      } else {
        unSuccessModal(result.message); // Show error if registration fails
      }
    } catch (error) {
      console.error("Error:", error);
      unSuccessModal("An error occurred while registering.");
    }
  });
});

