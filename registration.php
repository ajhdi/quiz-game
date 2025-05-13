<?php
$role = $_GET['role'] ?? 'student'; // default to student
?>
<script src="https://cdn.tailwindcss.com"></script>

<section class="min-h-screen flex box-border justify-center items-center" style="background: linear-gradient(to right, #0f2027, #203a43, #2c5364);">
  <div class="bg-[#dfa674] rounded-2xl flex max-w-3xl p-5 items-center shadow-xl">
    <div class="md:w-1/2 px-8">
      <h2 class="font-bold text-3xl text-[#002D74]"><?= ucfirst($role) ?> Registration</h2>
      <p class="text-sm mt-4 text-[#002D74]">Please fill out the form to create your account.</p>

      <div id="alertBox" class="hidden mt-4 text-sm px-4 py-2 rounded-lg font-medium"></div>

      <form id="registerForm" class="flex flex-col gap-4 mt-4" data-role="<?= $role ?>">
        <input type="hidden" name="role" value="<?= $role ?>">

        <input class="p-2 rounded-xl border" type="text" name="<?= $role == 'student' ? 'studFname' : 'proFname' ?>" placeholder="First Name" required>
        <input class="p-2 rounded-xl border" type="text" name="<?= $role == 'student' ? 'studLname' : 'proLname' ?>" placeholder="Last Name" required>
        <input class="p-2 rounded-xl border" type="text" name="<?= $role == 'student' ? 'studMname' : 'proMname' ?>" placeholder="Middle Name">
        <input class="p-2 rounded-xl border" type="text" name="<?= $role == 'student' ? 'studNo' : 'profNo' ?>" placeholder="<?= $role == 'student' ? 'Student Number' : 'Professor Number' ?>" required>

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
      <img class="rounded-2xl max-h-[1600px]" src="https://images.unsplash.com/photo-1552010099-5dc86fcfaa38?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NzEyNjZ8MHwxfHNlYXJjaHwxfHxmcmVzaHxlbnwwfDF8fHwxNzEyMTU4MDk0fDA&ixlib=rb-4.0.3&q=80&w=1080" alt="form image">
    </div>
  </div>
</section>

<!-- AJAX Script -->
<script src="js/registration.js"></script>
