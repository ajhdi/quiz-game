<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
include_once('backend/conn.php'); // Ensure DB connection

$profID = $_SESSION['profID'] ?? null;
$studentID = $_SESSION['studentID'] ?? null;

$role = $profID ? 'teacher' : ($studentID ? 'student' : null);
if (!$role) {
  header("Location: index.php");
  exit;
}
$dashboardUrl = $role === 'teacher' ? 'teacher_dashboard.php' : 'student_dashboard.php';

// Fetch user data
$studentData = null;
$professorData = null;

if ($studentID) {
  $stmt = $conn->prepare("SELECT * FROM student_tbl WHERE studentID = ?");
  $stmt->execute([$studentID]);
  $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($profID) {
  $stmt = $conn->prepare("SELECT * FROM prof_tbl WHERE profID = ?");
  $stmt->execute([$profID]);
  $professorData = $stmt->fetch(PDO::FETCH_ASSOC);
}
$toast = null;
if (isset($_SESSION['toast'])) {
  $toast = $_SESSION['toast'];
  unset($_SESSION['toast']);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Online Quiz System</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <!-- Tippy.js for tooltips -->
  <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://unpkg.com/tippy.js@6"></script>
</head>

<body class="min-h-screen flex flex-col" style="background-color: #F2F2F2;">
  <!-- Header -->
  <header class="relative py-4 px-6 shadow-md" style="background-color: #FEE6A0;">
    <div class="container mx-auto flex justify-between items-center">
      <!-- Logo -->
      <a href="<?= htmlspecialchars($dashboardUrl) ?>" class="flex items-center space-x-2 hover:opacity-90 transition-all">
        <img src="assets/logo.png" alt="Logo" class="h-10 w-auto">
        <span class="text-2xl font-extrabold text-gray-900">QUIZIX</span>
      </a>

      <!-- Navigation -->
      <nav class="hidden md:flex space-x-8">
        <a href="<?= htmlspecialchars($dashboardUrl) ?>" class="text-gray-900 font-medium hover:text-yellow-700 transition-colors">Dashboard</a>
        <button onclick="showProfileModal('<?= $role ?>')" class="text-gray-900 font-medium hover:text-yellow-700 transition-colors">User</button> <!-- Button that shows the logout modal -->
        <button onclick="showLogoutModal()"
          class="block text-gray-900 font-semibold hover:text-red-600 transition-colors">
          Logout
        </button>
      </nav>

      <!-- Mobile Menu Icon -->
      <div class="md:hidden">
        <button id="mobile-menu-button" class="text-gray-800 focus:outline-none">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
      <nav id="mobile-menu"
        class="hidden absolute top-full left-0 right-0 z-50 flex-col space-y-3 px-4 py-4 shadow-md rounded-b-md transition-all duration-300 ease-in-out opacity-0 translate-y-[-10px] md:hidden"
        style="background-color: rgba(254, 230, 160, 0.95);">
        <a href="<?= htmlspecialchars($dashboardUrl) ?>"
          class="block text-gray-900 font-semibold hover:text-yellow-700 transition-colors">
          Dashboard
        </a>
        <a href="#"
          class="block text-gray-900 font-semibold hover:text-yellow-700 transition-colors">
          User
        </a>
        <!-- Button that shows the logout modal -->
        <button onclick="showLogoutModal()"
          class="block text-gray-900 font-semibold hover:text-red-600 transition-colors">
          Logout
        </button>

      </nav>
    </div>


  </header>
  <!-- Logout Confirmation Modal -->
  <div id="logout-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="relative p-4 w-full max-w-md">
      <div class="relative bg-white rounded-lg shadow-2xl ring-1 ring-gray-300">

        <!-- Close Button -->
        <button type="button"
          class="absolute top-3 right-3 text-gray-400 hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex items-center justify-center"
          onclick="hideLogoutModal()">
          <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13" />
          </svg>
          <span class="sr-only">Close modal</span>
        </button>

        <!-- Modal Content -->
        <div class="p-6 text-center">
          <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
          <h3 class="mb-5 text-lg font-semibold text-gray-700">Are you sure you want to logout?</h3>
          <a href="backend/logout.php"
            class="text-white bg-red-600 hover:bg-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5">
            Yes, log me out
          </a>
          <button onclick="hideLogoutModal()" type="button"
            class="ml-3 py-2.5 px-5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-blue-700">
            No, cancel
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Student Modal -->
  <?php if ($studentData): ?>
    <div id="studentProfileModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 animate-fade-in scale-95
              mx-4 sm:mx-auto
              max-h-[90vh] overflow-y-auto">
        <div class="mb-6 text-center">
          <h2 class="text-3xl font-bold text-gray-800">Student Profile</h2>
          <p class="text-sm text-gray-500">Update your account information below</p>
        </div>

        <form action="backend/user_profile_update.php" method="POST" class="space-y-5">
          <input type="hidden" name="studentID" value="<?= $studentData['studentID'] ?>">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" name="studFname" placeholder="First Name" value="<?= htmlspecialchars($studentData['studFname']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition" required>
            <input type="text" name="studMname" placeholder="Middle Name" value="<?= htmlspecialchars($studentData['studMname']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition">
            <input type="text" name="studLname" placeholder="Last Name" value="<?= htmlspecialchars($studentData['studLname']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($studentData['email']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition" required>
            <input type="text" name="studNo" readonly placeholder="Student Number" value="<?= htmlspecialchars($studentData['studNo']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition" required>
            <input type="text" name="courseCode" placeholder="Course" value="<?= htmlspecialchars($studentData['courseCode']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition" required>
            <input type="text" name="yearSection" placeholder="Year & Section" value="<?= htmlspecialchars($studentData['yearSection']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition" required>
          </div>

          <div class="flex justify-end space-x-4 pt-4">
            <button type="button" onclick="hideProfileModal()" class="text-gray-600 hover:text-gray-800 font-medium">Cancel</button>
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg transition">Save Changes</button>
          </div>
        </form>
      </div>
    </div>

  <?php endif; ?>

  <!-- Professor Modal -->
  <?php if ($professorData): ?>
    <div id="professorProfileModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 animate-fade-in scale-95
              mx-4 sm:mx-auto
              max-h-[90vh] overflow-y-auto">
        <div class="mb-6 text-center">
          <h2 class="text-3xl font-bold text-gray-800">Professor Profile</h2>
          <p class="text-sm text-gray-500">Update your account information below</p>
        </div>

        <form action="backend/user_profile_update.php" method="POST" class="space-y-5">
          <input type="hidden" name="profID" value="<?= $professorData['profID'] ?>">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" name="proFname" placeholder="First Name" value="<?= htmlspecialchars($professorData['proFname']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition" required>
            <input type="text" name="proMname" placeholder="Middle Name" value="<?= htmlspecialchars($professorData['proMname']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition">
            <input type="text" name="proLname" placeholder="Last Name" value="<?= htmlspecialchars($professorData['proLname']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($professorData['email']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition" required>
            <input type="text" name="profNo" placeholder="Professor Number" value="<?= htmlspecialchars($professorData['profNo']) ?>" class="w-full border-b-2 border-black focus:border-yellow-500 focus:outline-none px-2 py-2 bg-transparent placeholder-gray-400 transition" required>
          </div>

          <div class="flex justify-end space-x-4 pt-4">
            <button type="button" onclick="hideProfileModal()" class="text-gray-600 hover:text-gray-800 font-medium">Cancel</button>
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg transition">Save Changes</button>
          </div>
        </form>
      </div>
    </div>

  <?php endif; ?>

  <?php if ($toast): ?>
    <?php include 'components/toast.php'; ?>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const toast = document.getElementById('toast-<?= $toast['type'] ?>');
        if (!toast) return;

        // Set the toast message dynamically
        toast.querySelector('.toast-message').textContent = "<?= addslashes($toast['message']) ?>";

        toast.classList.remove('hidden');

        // Auto hide after 3 seconds (adjust as needed)
        setTimeout(() => {
          toast.classList.add('hidden');
        }, 3000);
      });
    </script>
  <?php endif; ?>


  <!-- Main content -->
  <main class="flex-grow py-6 px-12">