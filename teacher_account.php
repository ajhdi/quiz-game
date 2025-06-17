<?php
include_once('components/header.php');
include_once('components/toast.php');
include_once('components/toast_failed.php');
include_once('backend/conn.php');
$stmts = $conn->prepare("SELECT * FROM prof_tbl");
$stmts->execute();
$prof = $stmts->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="flex justify-between items-center ">
    <!-- Tabs on the left -->
    <div class="flex">
        <a href="admin_dashboard.php" class=" hover:bg-yellow-300 px-4 py-2 rounded-t-md text-black font-medium">Student</a>
        <a href="teacher_account.php" class="bg-yellow-200 hover:bg-yellow-300 px-4 py-2 rounded-t-md text-black bg-gray-200 font-medium">Professor</a>
    </div>
    <div class="flex gap-1">
        <button id="updateBtn" class="bg-green-800 hover:bg-green-900 px-3 py-2 rounded flex items-center text-sm text-white">
            Update Mode
        </button>
        <button id="saveBtn" class="hidden bg-green-800 hover:bg-green-700 px-3 py-2 rounded flex items-center text-sm text-white">
            <i class="fas fa-file-import mr-2"></i> Save
        </button>
        <button id="cancelBtn" class="hidden bg-red-600 hover:bg-red-800 px-3 py-2 rounded flex items-center text-sm text-white">
            <i class="fas fa-file-export mr-2"></i> Cancel
        </button>
    </div>

</div>
<div class="bg-white p-6 shadow-md rounded-md mb-6">
    <div>
        <table id="questionsTable" class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Professor #</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Professor First Name</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Professor Last Name</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Professor Middle Name</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Professor Email</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($prof)) : ?>
                    <?php $count = 1; ?>
                    <?php foreach ($prof as $s) : ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="editable px-5 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"
                                contenteditable="false"
                                data-id="<?= htmlspecialchars($s['profNo']) ?>"
                                data-field="profNo">
                                <?= htmlspecialchars($s['profNo']) ?>
                            </td>
                            <td class="editable px-5 py-4 whitespace-nowrap text-sm text-gray-900"
                                contenteditable="false"
                                data-id="<?= htmlspecialchars($s['profNo']) ?>"
                                data-field="proFname">
                                <?= htmlspecialchars($s['proFname']) ?>
                            </td>
                            <td class="editable px-5 py-4 whitespace-nowrap text-sm text-gray-900"
                                contenteditable="false"
                                data-id="<?= htmlspecialchars($s['profNo']) ?>"
                                data-field="proLname">
                                <?= htmlspecialchars($s['proLname']) ?>
                            </td>
                            <td class="editable px-5 py-4 whitespace-nowrap text-sm text-gray-900"
                                contenteditable="false"
                                data-id="<?= htmlspecialchars($s['profNo']) ?>"
                                data-field="proMname">
                                <?= htmlspecialchars($s['proMname']) ?>
                            </td>
                            <td class="editable px-5 py-4 whitespace-nowrap text-sm text-gray-900"
                                contenteditable="false"
                                data-id="<?= htmlspecialchars($s['profNo']) ?>"
                                data-field="email">
                                <?= htmlspecialchars($s['email']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>

                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const updateBtn = document.getElementById("updateBtn");
        const saveBtn = document.getElementById("saveBtn");
        const cancelBtn = document.getElementById("cancelBtn");

        let changes = {};

        updateBtn.addEventListener("click", () => {
            updateBtn.classList.add("hidden");
            saveBtn.classList.remove("hidden");
            cancelBtn.classList.remove("hidden");

            document.querySelectorAll("td.editable").forEach(cell => {
                const field = cell.dataset.field;

                cell.contentEditable = true;
                cell.classList.add("bg-green-200");

                // Capture original value
                const original = cell.textContent.trim();

                // Enforce max length for profNo
                if (field === "profNo") {
                    cell.addEventListener("input", () => {
                        if (cell.textContent.length > 9) {
                            cell.textContent = cell.textContent.slice(0, 9); // cut to 9 chars
                            placeCursorAtEnd(cell); // helper to keep cursor at end
                        }
                    });
                }

                // Track changes
                cell.addEventListener("blur", () => {
                    const newValue = cell.textContent.trim();
                    const id = cell.dataset.id;

                    if (newValue !== original) {
                        if (!changes[id]) changes[id] = {};
                        changes[id][field] = newValue;
                    }
                });
            });

            // Helper to place cursor at the end of a content
            function placeCursorAtEnd(el) {
                const range = document.createRange();
                const sel = window.getSelection();
                range.selectNodeContents(el);
                range.collapse(false);
                sel.removeAllRanges();
                sel.addRange(range);
            }
        });

        cancelBtn.addEventListener("click", () => {
            location.reload(); // Reset everything
        });

        saveBtn.addEventListener("click", () => {
            if (Object.keys(changes).length === 0) {
                showToast("No changes made!", false);
                return;
            }

            let invalid = false;

            Object.entries(changes).forEach(([id, fields]) => {
                for (const [field, value] of Object.entries(fields)) {
                    if (field !== "proMname" && value.trim() === "") {
                        invalid = true;
                        break;
                    }
                }
            });

            if (invalid) {
                showToast("Fields cannot be empty (except Middle Name)", false);
                return;
            }

            fetch("backend/save_prof_batch.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(changes)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast("Saved successfully!", true);

                        updateBtn.classList.remove("hidden");
                        saveBtn.classList.add("hidden");
                        cancelBtn.classList.add("hidden");

                        document.querySelectorAll("td.editable").forEach(cell => {
                            cell.contentEditable = false;
                            cell.classList.remove("bg-green-200");
                        });

                        changes = {}; // Clear saved changes
                    } else {
                        showToast("Save failed!", false);
                    }
                })
                .catch(() => showToast("Network error!", false));
        });


        function showToast(message, success = true) {
            const toast = document.getElementById("toast-success");
            toast.querySelector(".toast-message").textContent = message;
            toast.classList.remove("hidden");

            toast.classList.toggle("bg-green-100", success);
            toast.classList.toggle("text-green-800", success);
            toast.classList.toggle("bg-red-100", !success);
            toast.classList.toggle("text-red-800", !success);

            setTimeout(() => toast.classList.add("hidden"), 3000);
        }
    });
</script>



<?php
include_once('components/footer.php');
?>