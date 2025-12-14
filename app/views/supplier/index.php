<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>
<main class="supplier-page-content">
    <div class="supplier-page-header">
        <div class="supplier-header-left">
            <h1 class="supplier-page-title">Data Supplier</h1>
            <p class="supplier-page-subtitle">Kelola data supplier dan vendor</p>
        </div>
        <button class="supplier-btn-add" onclick="resetForm()">
            <i class="bi bi-plus"></i> Tambah Supplier
        </button>
    </div>

    <div class="supplier-content-card">
        <div class="supplier-card-header">
            <h4 class="mt-2">Daftar Supplier</h4>
            <div class="supplier-search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="supplier-search-input" placeholder="Cari supplier..." id="searchSupplier">
            </div>
        </div>

        <div class="supplier-table-wrapper">
            <table class="supplier-data-table">
                <thead>
                    <tr>
                        <th>ID Supplier</th>
                        <th>Nama Supplier</th>
                        <th>Kontak Person</th>
                        <th>Informasi</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="supplierTableBody">
                    <?php if (!empty($suppliers)): ?>
                        <?php foreach ($suppliers as $row): ?>
                            <?php if ($row['status'] === 'active'): ?>
                                <tr>
                                    <td class="supplier-id-cell"><?= htmlspecialchars($row['id_supplier']) ?></td>
                                    <td class="supplier-name-cell"><?= htmlspecialchars($row['nama_supplier']) ?></td>
                                    <td class="supplier-contact-cell"><?= htmlspecialchars($row['kontak']) ?></td>
                                    <td class="supplier-info-cell">
                                        <div class="supplier-phone-info">
                                            <i class="bi bi-telephone-fill supplier-info-icon"></i>
                                            <span class="supplier-phone-text"><?= htmlspecialchars($row['kontak']) ?></span>
                                        </div>
                                        <div class="supplier-email-info">
                                            <i class="bi bi-envelope-fill supplier-info-icon"></i>
                                            <span class="supplier-email-text"><?= htmlspecialchars($row['email']) ?></span>
                                        </div>
                                    </td>
                                    <td class="supplier-address-cell"><?= htmlspecialchars($row['alamat']) ?></td>
                                    <td>
                                        <div class="supplier-action-btns">
                                            <button class="supplier-btn-icon supplier-btn-edit" onclick="editSupplier('<?= $row['id_supplier']; ?>')" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="supplier-btn-icon supplier-btn-delete" onclick="confirmDelete('<?= $row['id_supplier']; ?>')" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Tidak ada data supplier</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="supplier-pagination-container">
            <div class="supplier-pagination-info" id="paginationInfo">
                Menampilkan <span id="showingStart">1</span>-<span id="showingEnd"><?= count($suppliers) ?></span> dari <span id="totalData"><?= count($suppliers) ?></span> data
            </div>
            <div class="supplier-pagination-controls">
                <button class="supplier-pagination-btn supplier-pagination-prev" id="prevBtn" onclick="changePage('prev')">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div id="paginationPages" class="d-flex flex-direction-row gap-2"></div>
                <button class="supplier-pagination-btn supplier-pagination-next" id="nextBtn" onclick="changePage('next')">
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

<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content supplier-modal-content">
            <div class="modal-header supplier-modal-header">
                <h5 class="modal-title supplier-modal-title" id="modalTitle">Tambah Supplier Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body supplier-modal-body">
                <form id="supplierForm">
                    <input type="hidden" id="supplierId" name="supplierId">
                    <input type="hidden" id="formAction" value="create">

                    <div class="mb-3">
                        <label class="supplier-form-label">Nama Supplier</label>
                        <input type="text" class="supplier-form-input" id="nama_supplier" name="nama_supplier" required>
                    </div>
                    <div class="mb-3">
                        <label class="supplier-form-label">Kontak Person</label>
                        <input type="text" class="supplier-form-input" id="kontak" name="kontak" required>
                    </div>
                    <div class="mb-3">
                        <label class="supplier-form-label">Email</label>
                        <input type="email" class="supplier-form-input" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="supplier-form-label">Alamat</label>
                        <textarea class="supplier-form-input" id="alamat" name="alamat" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer supplier-modal-footer">
                <button type="button" class="supplier-btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="supplier-btn-primary" onclick="saveSupplier()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content supplier-modal-content">
            <div class="modal-header supplier-modal-header">
                <h5 class="modal-title supplier-modal-title">Nonaktifkan Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body supplier-modal-body">
                <p style="font-size: 0.9rem; color: #4a5568; margin: 0;">
                    Apakah Anda yakin ingin menonaktifkan supplier ini? Supplier yang dinonaktifkan tidak akan muncul dalam daftar.
                </p>
                <input type="hidden" id="deleteSupplierId">
            </div>

            <div class="modal-footer supplier-modal-footer">
                <button type="button" class="supplier-btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="supplier-btn-primary" onclick="deleteSupplier()">Nonaktifkan</button>
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

    .supplier-id-cell {
        font-weight: 600;
        color: #4a5568;
    }
</style>

<script>
    let currentPage = 1;
    let rowsPerPage = 5;
    let allRows = [];
    let filteredRows = [];
    let toastTimeout;

    document.addEventListener('DOMContentLoaded', function() {
        allRows = Array.from(document.querySelectorAll('#supplierTableBody tr'));
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
        const paginationContainer = document.querySelector('.supplier-pagination-container');
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
            pageBtn.className = 'supplier-pagination-page' + (i === page ? ' active' : '');
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

    document.getElementById('searchSupplier').addEventListener('keyup', function() {
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
        document.getElementById('supplierForm').reset();
        document.getElementById('supplierId').value = '';
        document.getElementById('formAction').value = 'create';
        document.getElementById('modalTitle').textContent = 'Tambah Supplier Baru';

        const modal = new bootstrap.Modal(document.getElementById('addSupplierModal'));
        modal.show();
    }

    function editSupplier(id) {
        fetch(`/supplier/edit/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    document.getElementById('supplierId').value = result.data.id_supplier;
                    document.getElementById('nama_supplier').value = result.data.nama_supplier;
                    document.getElementById('kontak').value = result.data.kontak;
                    document.getElementById('email').value = result.data.email;
                    document.getElementById('alamat').value = result.data.alamat;
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('modalTitle').textContent = 'Edit Supplier';

                    const modal = new bootstrap.Modal(document.getElementById('addSupplierModal'));
                    modal.show();
                } else {
                    showNotification('error', 'Error', result.message || 'Gagal mengambil data supplier');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Error', 'Terjadi kesalahan saat mengambil data supplier');
            });
    }

    function saveSupplier() {
        const form = document.getElementById('supplierForm');
        const formData = new FormData(form);

        const namaSupplier = document.getElementById('nama_supplier').value;
        const kontak = document.getElementById('kontak').value;
        const email = document.getElementById('email').value;
        const alamat = document.getElementById('alamat').value;

        if (!namaSupplier || !kontak || !email || !alamat) {
            showNotification('error', 'Error', 'Semua field harus diisi');
            return;
        }

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const action = document.getElementById('formAction').value;
        const supplierId = document.getElementById('supplierId').value;

        let url = action === 'create' ? '/supplier/create' : `/supplier/update/${supplierId}`;

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addSupplierModal'));
                    modal.hide();
                    showNotification('success', 'Berhasil', result.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
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
        document.getElementById('deleteSupplierId').value = id;
        const modal = new bootstrap.Modal(document.getElementById('deleteSupplierModal'));
        modal.show();
    }

    function deleteSupplier() {
        const supplierId = document.getElementById('deleteSupplierId').value;

        fetch(`/supplier/delete/${supplierId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteSupplierModal'));
                    modal.hide();
                    showNotification('success', 'Berhasil', result.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
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
?>