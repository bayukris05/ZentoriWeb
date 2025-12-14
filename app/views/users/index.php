<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>
<main class="user-page-content">
    <div class="user-page-header">
        <div class="user-header-left">
            <h1 class="user-page-title">Data User</h1>
            <p class="user-page-subtitle">Kelola data pengguna sistem</p>
        </div>
        <button class="user-btn-add" onclick="resetForm()">
            <i class="bi bi-plus"></i> Tambah User
        </button>
    </div>

    <div class="user-content-card">
        <div class="user-card-header">
            <h4 class="mt-2">Daftar User</h4>
            <div class="user-search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="user-search-input" placeholder="Cari user..." id="searchUser">
            </div>
        </div>

        <div class="user-table-wrapper">
            <table class="user-data-table">
                <thead>
                    <tr>
                        <th>ID User</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <?php if (!empty($data)): ?>
                        <?php foreach ($data as $row): ?>
                            <?php if ($row['status'] === 'active'): ?>
                                <tr>
                                    <td class="user-id-cell"><?= htmlspecialchars($row['id_users']) ?></td>
                                    <td>
                                        <div class="user-info-cell">
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['nama']) ?>&background=F4A460&color=fff"
                                                alt="<?= htmlspecialchars($row['nama']) ?>" class="user-avatar-img">
                                            <span class="user-name-text"><?= htmlspecialchars($row['nama']) ?></span>
                                        </div>
                                    </td>
                                    <td class="user-email-text"><?= htmlspecialchars($row['email']) ?></td>
                                    <td class="user-role-text"><?= ucfirst(htmlspecialchars($row['role'])) ?></td>
                                    <td>
                                        <div class="user-action-btns">
                                            <button class="user-btn-icon user-btn-edit" onclick="editUser('<?= $row['id_users']; ?>')" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="user-btn-icon user-btn-delete" onclick="confirmDelete('<?= $row['id_users']; ?>')" title="Nonaktifkan">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Tidak ada data user</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="user-pagination-container">
            <div class="user-pagination-info" id="paginationInfo">
                Menampilkan <span id="showingStart">1</span>-<span id="showingEnd"><?= count($data) ?></span> dari <span id="totalData"><?= count($data) ?></span> data
            </div>
            <div class="user-pagination-controls">
                <button class="user-pagination-btn user-pagination-prev" id="prevBtn" onclick="changePage('prev')">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div id="paginationPages" class="d-flex flex-direction-row gap-2"></div>
                <button class="user-pagination-btn user-pagination-next" id="nextBtn" onclick="changePage('next')">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</main>

<div class="notification-toast" id="notificationToast">
    <div class="toast-content">
        <i class="toast-icon" id="toastIcon"></i>
        <div class="toast-message">
            <span class="toast-title" id="toastTitle"></span>
            <span class="toast-text" id="toastText"></span>
        </div>
        <button class="toast-close" onclick="hideNotification()">
            <i class="bi bi-x"></i>
        </button>
    </div>
    <div class="toast-progress" id="toastProgress"></div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content user-modal-content">
            <div class="modal-header user-modal-header">
                <h5 class="modal-title user-modal-title" id="modalTitle">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body user-modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="userId">
                    <input type="hidden" id="formAction" value="create">

                    <div class="mb-3">
                        <label class="user-form-label">Nama Lengkap</label>
                        <input type="text" class="user-form-input" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="user-form-label">Email</label>
                        <input type="email" class="user-form-input" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="user-form-label">Password <span id="passwordNote" style="display: none; font-size: 0.85em; color: #666;">(Kosongkan jika tidak ingin mengubah)</span></label>
                        <input type="password" class="user-form-input" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="user-form-label">Role</label>
                        <select class="user-form-select" id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="admin">Administrator</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer user-modal-footer">
                <button type="button" class="user-btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="user-btn-primary" onclick="saveUser()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content user-modal-content">
            <div class="modal-header user-modal-header">
                <h5 class="modal-title user-modal-title">Nonaktifkan User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body user-modal-body">
                <p style="font-size: 0.9rem; color: #4a5568; margin: 0;">
                    Apakah Anda yakin ingin menonaktifkan user ini? User yang dinonaktifkan tidak akan muncul dalam daftar.
                </p>
                <input type="hidden" id="deleteUserId">
            </div>

            <div class="modal-footer user-modal-footer">
                <button type="button" class="user-btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="user-btn-primary" onclick="deleteUser()">Nonaktifkan</button>
            </div>
        </div>
    </div>
</div>

<style>
    .notification-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 0;
        min-width: 300px;
        max-width: 400px;
        z-index: 9999;
        transform: translateX(400px);
        opacity: 0;
        transition: all 0.3s ease;
        border-left: 4px solid #4CAF50;
    }

    .notification-toast.show {
        transform: translateX(0);
        opacity: 1;
    }

    .notification-toast.success {
        border-left-color: #4CAF50;
    }

    .notification-toast.error {
        border-left-color: #f44336;
    }

    .notification-toast.warning {
        border-left-color: #ff9800;
    }

    .toast-content {
        display: flex;
        align-items: center;
        padding: 16px;
        gap: 12px;
    }

    .toast-icon {
        font-size: 20px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .success .toast-icon {
        color: #4CAF50;
    }

    .error .toast-icon {
        color: #f44336;
    }

    .warning .toast-icon {
        color: #ff9800;
    }

    .toast-message {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .toast-title {
        font-weight: 600;
        font-size: 14px;
        color: #1a202c;
    }

    .toast-text {
        font-size: 13px;
        color: #4a5568;
    }

    .toast-close {
        background: none;
        border: none;
        font-size: 16px;
        color: #718096;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .toast-close:hover {
        background-color: #f7fafc;
        color: #2d3748;
    }

    .toast-progress {
        height: 3px;
        background: #4CAF50;
        width: 100%;
        transform: scaleX(1);
        transform-origin: left;
        transition: transform 0.1s linear;
    }

    .success .toast-progress {
        background: #4CAF50;
    }

    .error .toast-progress {
        background: #f44336;
    }

    .warning .toast-progress {
        background: #ff9800;
    }
</style>

<script>
    let currentPage = 1;
    let rowsPerPage = 5;
    let allRows = [];
    let filteredRows = [];
    let toastTimeout;

    document.addEventListener('DOMContentLoaded', function() {
        allRows = Array.from(document.querySelectorAll('#userTableBody tr'));
        filteredRows = [...allRows];
        updatePagination();
        displayPage(currentPage);
    });

    function displayPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        allRows.forEach(row => row.style.display = 'none');

        filteredRows.slice(start, end).forEach(row => row.style.display = '');

        const totalRows = filteredRows.length;
        const showingStart = totalRows > 0 ? start + 1 : 0;
        const showingEnd = Math.min(end, totalRows);

        document.getElementById('showingStart').textContent = showingStart;
        document.getElementById('showingEnd').textContent = showingEnd;
        document.getElementById('totalData').textContent = totalRows;

        updatePaginationButtons(page);

        togglePaginationVisibility(totalRows);
    }

    function togglePaginationVisibility(totalRows) {
        const paginationContainer = document.querySelector('.user-pagination-container');
        if (totalRows > rowsPerPage) {
            paginationContainer.style.display = 'flex';
        } else {
            paginationContainer.style.display = 'none';
        }
    }

    function updatePaginationButtons(page) {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        const paginationPages = document.getElementById('paginationPages');
        paginationPages.innerHTML = '';

        let startPage = Math.max(1, page - 1);
        let endPage = Math.min(totalPages, page + 1);

        if (page === 1) {
            endPage = Math.min(3, totalPages);
        } else if (page === totalPages) {
            startPage = Math.max(1, totalPages - 2);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = 'user-pagination-page' + (i === page ? ' active' : '');
            pageBtn.textContent = i;
            pageBtn.onclick = () => goToPage(i);
            paginationPages.appendChild(pageBtn);
        }

        document.getElementById('prevBtn').disabled = page === 1;
        document.getElementById('nextBtn').disabled = page === totalPages || totalPages === 0;
    }

    function changePage(direction) {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);

        if (direction === 'prev' && currentPage > 1) {
            currentPage--;
        } else if (direction === 'next' && currentPage < totalPages) {
            currentPage++;
        }

        displayPage(currentPage);
    }

    function goToPage(page) {
        currentPage = page;
        displayPage(currentPage);
    }

    function updatePagination() {
        currentPage = 1;
        displayPage(currentPage);
    }

    document.getElementById('searchUser').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();

        if (searchValue === '') {
            filteredRows = [...allRows];
        } else {
            filteredRows = allRows.filter(row => {
                const text = row.textContent.toLowerCase();
                return text.includes(searchValue);
            });
        }

        updatePagination();
    });

    function resetForm() {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('formAction').value = 'create';
        document.getElementById('modalTitle').textContent = 'Tambah User Baru';
        document.getElementById('password').required = true;
        document.getElementById('passwordNote').style.display = 'none';

        const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
        modal.show();
    }

    function editUser(id) {
        fetch(`/users/edit/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    document.getElementById('userId').value = result.data.id_users;
                    document.getElementById('nama').value = result.data.nama;
                    document.getElementById('email').value = result.data.email;
                    document.getElementById('password').value = '';
                    document.getElementById('role').value = result.data.role;
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('modalTitle').textContent = 'Edit User';
                    document.getElementById('password').required = false;
                    document.getElementById('passwordNote').style.display = 'inline';

                    const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
                    modal.show();
                } else {
                    showNotification('error', 'Error', result.message || 'Gagal mengambil data user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Error', 'Terjadi kesalahan saat mengambil data user');
            });
    }

    function saveUser() {
        const form = document.getElementById('userForm');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const action = document.getElementById('formAction').value;
        const userId = document.getElementById('userId').value;

        let url = action === 'create' ?
            '/users/create' :
            `/users/update/${userId}`;

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                    showNotification('success', 'Berhasil', result.message);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showNotification('error', 'Error', result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Error', 'Terjadi kesalahan saat menyimpan data');
            });
    }

    function confirmDelete(id) {
        document.getElementById('deleteUserId').value = id;
        const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        modal.show();
    }

    function deleteUser() {
        const userId = document.getElementById('deleteUserId').value;

        fetch(`/users/delete/${userId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('deleteUserModal')).hide();
                    showNotification('success', 'Berhasil', result.message);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showNotification('error', 'Error', result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Error', 'Terjadi kesalahan saat menghapus data');
            });
    }

    function showNotification(type, title, message) {
        const toast = document.getElementById('notificationToast');
        const toastIcon = document.getElementById('toastIcon');
        const toastTitle = document.getElementById('toastTitle');
        const toastText = document.getElementById('toastText');
        const toastProgress = document.getElementById('toastProgress');

        let iconClass = '';
        switch (type) {
            case 'success':
                iconClass = 'bi bi-check-circle-fill';
                break;
            case 'error':
                iconClass = 'bi bi-x-circle-fill';
                break;
            case 'warning':
                iconClass = 'bi bi-exclamation-triangle-fill';
                break;
            default:
                iconClass = 'bi bi-info-circle-fill';
        }

        toastIcon.className = 'toast-icon ' + iconClass;
        toastTitle.textContent = title;
        toastText.textContent = message;

        toast.className = 'notification-toast ' + type;

        toast.classList.add('show');

        toastProgress.style.transform = 'scaleX(1)';
        setTimeout(() => {
            toastProgress.style.transform = 'scaleX(0)';
        }, 50);

        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            hideNotification();
        }, 5000);
    }

    function hideNotification() {
        const toast = document.getElementById('notificationToast');
        toast.classList.remove('show');
        clearTimeout(toastTimeout);
    }

    document.getElementById('notificationToast').addEventListener('click', function(e) {
        if (e.target.closest('.toast-close')) {
            hideNotification();
        }
    });
</script>

<?php
include __DIR__ . '/../layouts/footer.php';