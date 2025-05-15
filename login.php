<?php
$role = $_GET['role'] ?? 'student'; // default to student
?>
<script src="https://cdn.tailwindcss.com"></script>

<section class="min-h-screen flex box-border justify-center items-center" style="background: linear-gradient(to right,rgb(250, 218, 94),rgb(250, 227, 155), rgb(250, 222, 124));">
  <div class="bg-[#FFFFFF] rounded-2xl flex max-w-3xl p-5 items-center">
    <div class="md:w-1/2 px-8">
      <h2 class="font-bold text-3xl text-[#002D74]" id="loginTitle"><?= ucfirst($role) ?> Login</h2>
      <p class="text-sm mt-4 text-[#002D74]">If you're already a member, log in now.</p>

      <div id="alertBox" class="hidden mt-4 text-sm px-4 py-2 rounded-lg font-medium"></div>

      <form id="loginForm" class="flex flex-col gap-4 mt-4" data-role="<?= $role ?>">
        <input class="p-2 rounded-xl border" type="email" name="email" placeholder="Email" required>
        <input class="p-2 rounded-xl border" type="password" name="password" placeholder="Password" required>
        <button type="submit" class="bg-[#002D74] text-white py-2 rounded-xl hover:scale-105 duration-300 hover:bg-[#206ab1] font-medium">Login</button>
      </form>

      <div class="mt-10 text-sm border-b border-gray-500 py-5">Forgot password?</div>

      <div class="mt-4 text-sm flex justify-between items-center">
        <p>If you don't have an account...</p>
        <a href="registration.php?role=<?= $role ?>" class="bg-[#002D74] text-white rounded-xl py-2 px-5 hover:scale-110 hover:bg-[#002c7424] font-semibold duration-300">
            Register
        </a>
      </div>

    </div>

    <div class="md:block hidden w-1/2">
      <img class="rounded-2xl max-h-[1600px]" src="https://images.unsplash.com/photo-1552010099-5dc86fcfaa38?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NzEyNjZ8MHwxfHNlYXJjaHwxfHxmcmVzaHxlbnwwfDF8fHwxNzEyMTU4MDk0fDA&ixlib=rb-4.0.3&q=80&w=1080" alt="login form image">
    </div>
  </div>
</section>

<script src="js/login.js"></script>
