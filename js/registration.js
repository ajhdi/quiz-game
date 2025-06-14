function showSuccessModal() {
  document.getElementById("successModal").classList.remove("hidden");
}

function closeSuccessModal() {
  document.getElementById("successModal").classList.add("hidden");
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
        alert(result.message); // Show error if registration fails
      }
    } catch (error) {
      console.error("Error:", error);
      alert("An error occurred while registering.");
    }
  });
});

