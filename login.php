<?php
$role = $_GET['role'] ?? 'student'; // default to student
$bgColor = match ($role) {
    'student' => 'bg-[#FFFFFF]',     // light blue
    'teacher' => 'bg-[#48483A]',     // light gray
    'admin'   => 'bg-[#48483A]',     // light yellow
    default   => 'bg-[#ffffff]',     // fallback white
  };
$txtColor = match ($role) {
    'student' => 'text-[#002D74]',     // light blue
    'teacher' => 'text-[#DFF8EB]',     // light gray
    'admin'   => 'text-[#DFF8EB]',     // light yellow
    default   => 'text-[#ffffff]',     // fallback white
  };

$imgRole = match ($role) {
    'student' => 'assets/student.png',     // light blue
    'teacher' => 'assets/prof.png',     // light gray
    'admin'   => 'assets/admin.png',     // light yellow
    default   => 'bg-[#ffffff]',     // fallback white
  };
?>
<script src="https://cdn.tailwindcss.com"></script>

<section class="min-h-screen flex box-border justify-center items-center " style="background: linear-gradient(to right,rgb(250, 218, 94),rgb(250, 227, 155), rgb(250, 222, 124));">
  <div class="<?= $bgColor ?> rounded-2xl flex max-w-3xl p-5 items-center">
    <div class="md:w-1/2 px-8">
      <h2 class="font-bold text-3xl <?= $txtColor ?>" id="loginTitle"><?= ucfirst($role) ?> Login</h2>

      <div id="alertBox" class="hidden mt-4 text-sm px-4 py-2 rounded-lg font-medium"></div>

      <form id="loginForm" class="flex flex-col gap-4 mt-4" data-role="<?= $role ?>">
        <?php if ($role === 'teacher'): ?>
          <input class="p-2 rounded-xl border" type="text" name="profNo" placeholder="Professor Number" maxlength="9" required>
        <?php elseif ($role === 'admin'): ?>
          <input class="p-2 rounded-xl border" type="text" name="adminNo" placeholder="Admin Number" maxlength="9" required>
        <?php else: ?>
          <input class="p-2 rounded-xl border" type="text" name="studNo" placeholder="Student Number" maxlength="9" required>
        <?php endif; ?>
        <input class="p-2 rounded-xl border" type="password" name="password" placeholder="Password" required>
        <button type="submit" class="bg-[#002D74] text-white py-2 rounded-xl hover:scale-105 duration-300 hover:bg-[#206ab1] font-medium">Login</button>
      </form>

      <?php if ($role !== 'admin'): ?>
        <div class="mt-4 text-sm flex justify-between items-center">
          <p class="<?= $txtColor ?>">If you don't have an account...</p>
          <a href="registration.php?role=<?= $role ?>" class="bg-[#002D74] text-white rounded-xl py-2 px-5 hover:scale-110 hover:bg-[#002c7424] font-semibold duration-300">
            Register
          </a>
        </div>
      <?php endif; ?>

    </div>

    <div class="md:block hidden w-1/2">
      <img class="rounded-2xl max-h-[1600px]" src="<?= $imgRole ?>" alt="login form image">
    </div>
  </div>

</section>

<script>
  document
    .getElementById("loginForm")
    .addEventListener("submit", async function(e) {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);

      const role = formData.has("adminNo") ?
        "admin" :
        formData.has("profNo") ?
        "teacher" :
        "student";

      formData.append("role", role);

      const alertBox = document.getElementById("alertBox");

      try {
        const response = await fetch("backend/login.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.text();

        if (result.trim() === "success") {
          console.log("Detected role:", role); // ✅ DEBUG: See if it's 'admin'
          alertBox.textContent = "Login successful! Redirecting...";
          alertBox.className =
            "mt-4 text-sm px-4 py-2 rounded-lg bg-green-500 text-white font-medium";

          setTimeout(() => {
            if (role === "teacher") {
              window.location.href = "teacher_dashboard.php";
            } else if (role === "admin") {
              window.location.href = "admin_dashboard.php";
            } else {
              window.location.href = "student_dashboard.php";
            }
          }, 1500);
        } else {
          alertBox.textContent = result;
          alertBox.className =
            "mt-4 text-sm px-4 py-2 rounded-lg bg-red-500 text-white font-medium";
        }

        alertBox.classList.remove("hidden");
      } catch (err) {
        alertBox.textContent = "Error occurred. Try again.";
        alertBox.className =
          "mt-4 text-sm px-4 py-2 rounded-lg bg-red-500 text-white font-medium";
        alertBox.classList.remove("hidden");
      }
    });
</script>