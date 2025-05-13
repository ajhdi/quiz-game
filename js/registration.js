document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("registerForm");

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(form);
    const role = formData.get("role"); // Get the selected role
    console.log(role);
    try {
      const response = await fetch("backend/registration.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.text();
      alert(result); // Optional: better to replace with a modal/toast

      if (result.toLowerCase().includes("successful")) {
        // Redirect with the correct role
        window.location.href = `login.php?role=${encodeURIComponent(role)}`;
      }
    } catch (error) {
      console.error("Error:", error);
      alert("An error occurred while registering.");
    }
  });
});
