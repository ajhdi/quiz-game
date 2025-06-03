  </main>
  <footer class="py-4 px-6 mt-auto" style="background-color: #FEE6A0;">
    <div class="container mx-auto text-center">
      <p class="text-sm text-[#C8C6D7]">&copy; <?= date('Y') ?> Your Company. All rights reserved.</p>
    </div>
  </footer>

  <script src="js/update_quiz.js"></script>
  <script>
    const btn = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');

    btn.addEventListener('click', () => {
      const isHidden = menu.classList.contains('hidden');

      if (isHidden) {
        menu.classList.remove('hidden');
        requestAnimationFrame(() => {
          menu.classList.remove('opacity-0', 'translate-y-[-10px]');
          menu.classList.add('opacity-70', 'translate-y-0');
        });
      } else {
        menu.classList.add('opacity-0', 'translate-y-[-10px]');
        menu.classList.remove('opacity-70', 'translate-y-0');

        // Delay hiding until after transition
        setTimeout(() => {
          menu.classList.add('hidden');
        }, 300);
      }
    });
    function showLogoutModal() {
      document.getElementById('logout-modal').classList.remove('hidden');
    }

    function hideLogoutModal() {
      document.getElementById('logout-modal').classList.add('hidden');
    }
    function showProfileModal(role) {
        if (role === 'student') {
          document.getElementById('studentProfileModal').classList.remove('hidden');
        } else if (role === 'teacher') {
          document.getElementById('professorProfileModal').classList.remove('hidden');
        }
      }

    function hideProfileModal() {
      const studentModal = document.getElementById('studentProfileModal');
      const professorModal = document.getElementById('professorProfileModal');

      if (studentModal) studentModal.classList.add('hidden');
      if (professorModal) professorModal.classList.add('hidden');
    }

  </script>

  </body>

  </html>