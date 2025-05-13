<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Select Role</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen" style="background: linear-gradient(to right, #0f2027, #203a43, #2c5364);">
  <div class="bg-white p-8 rounded-xl shadow-lg text-center space-y-6 w-80">
    <h1 class="text-2xl font-bold text-gray-700">Select Your Role</h1>
    <button onclick="selectRole('student')" class="w-full bg-blue-600 text-white py-2 rounded-xl hover:bg-blue-700 transition-all font-medium">
      Student
    </button>
    <button onclick="selectRole('teacher')" class="w-full bg-green-600 text-white py-2 rounded-xl hover:bg-green-700 transition-all font-medium">
      Teacher
    </button>
  </div>

  <script>
    function selectRole(role) {
      window.location.href = `login.php?role=${role}`;
    }
  </script>
</body>
</html>
