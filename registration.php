<?php
$role = $_GET['role'] ?? 'student'; // default to student
include_once('components/toast_failed.php');
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
<body>
<section class="min-h-screen flex box-border justify-center items-center" style="background: linear-gradient(to right,rgb(250, 218, 94),rgb(250, 227, 155), rgb(250, 222, 124));">
  <div class="<?= $bgColor ?> rounded-2xl flex max-w-3xl p-5 items-center shadow-xl">
    <div class="md:w-1/2 px-8">
      <h2 class="font-bold text-3xl <?= $txtColor ?>"><?= ucfirst($role) ?> Registration</h2>
      <p class="text-sm mt-4 <?= $txtColor ?>">Please fill out the form to create your account.</p>

      <div id="alertBox" class="hidden mt-4 text-sm px-4 py-2 rounded-lg font-medium"></div>

      <form id="registerForm" class="flex flex-col gap-4 mt-4" data-role="<?= $role ?>">
        <input type="hidden" name="role" value="<?= $role ?>">

        <input class="p-2 rounded-xl border" type="text" name="<?= $role == 'student' ? 'studFname' : 'proFname' ?>" placeholder="First Name" required>
        <input class="p-2 rounded-xl border" type="text" name="<?= $role == 'student' ? 'studLname' : 'proLname' ?>" placeholder="Last Name" required>
        <input class="p-2 rounded-xl border" type="text" name="<?= $role == 'student' ? 'studMname' : 'proMname' ?>" placeholder="Middle Name">
        <input class="p-2 rounded-xl border" type="text" name="<?= $role == 'student' ? 'studNo' : 'profNo' ?>" placeholder="<?= $role == 'student' ? 'Student Number' : 'Professor Number' ?>" maxlength="9" required>

        <?php if ($role == 'student'): ?>
          <input class="p-2 rounded-xl border" type="text" name="courseCode" placeholder="Course Code" required>
          <input class="p-2 rounded-xl border" type="text" name="yearSection" placeholder="Year & Section" required>
        <?php endif; ?>

        <input class="p-2 rounded-xl border" type="email" name="email" placeholder="Email" required>
        <input class="p-2 rounded-xl border" type="password" name="password" placeholder="Password" required>

        <button type="submit" class="bg-[#002D74] text-white py-2 rounded-xl hover:scale-105 duration-300 hover:bg-[#206ab1] font-medium">
          Register
        </button>
      </form>
    </div>

    <div class="md:block hidden w-1/2">
      <img class="rounded-2xl max-h-[1600px]" src="<?= $imgRole ?>" alt="form image">
    </div>
  </div>
</section>
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white p-6 rounded shadow-lg text-center max-w-sm w-full">
    <h2 class="text-xl font-semibold mb-4 text-green-600">Registration Successful</h2>
    <p class="mb-4">You will be redirected shortly.</p>
    <button onclick="closeSuccessModal()" class="bg-blue-600 text-white px-4 py-2 rounded">OK</button>
  </div>
</div>
</body>
<!-- AJAX Script -->
 <script src="https://cdn.tailwindcss.com"></script>
<script src="js/registration.js"></script>
