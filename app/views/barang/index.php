<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>
<main class="inventory-page-content">
    <div class="inventory-page-header">
        <div class="inventory-header-left">
            <h1 class="inventory-page-title">Data Barang</h1>
            <p class="inventory-page-subtitle">Kelola data barang inventori</p>
        </div>
        <div class="inventory-header-actions">
            <a href="/barang/expired-cleaned-history" class="inventory-btn-info me-2">
                <i class="bi bi-clock-history"></i> History Dibersihkan
            </a>
            <a href="/barang/expired-report" class="inventory-btn-warning me-2">
                <i class="bi bi-exclamation-triangle"></i> Laporan Expired
            </a>
            <button class="inventory-btn-add" onclick="resetForm()">
                <i class="bi bi-plus"></i> Tambah Barang
            </button>
        </div>
    </div>

    <!-- Notifikasi Barang Expired -->
    <?php if (!empty($expiredSummary) && ($expiredSummary['expired']['items'] > 0 || $expiredSummary['expiring_soon']['items'] > 0)): ?>
        <div class="alert alert-warning mb-3">
            <h6><i class="bi bi-exclamation-triangle"></i> Peringatan Kadaluarsa</h6>
            <?php if ($expiredSummary['expired']['items'] > 0): ?>
                <p class="mb-1 text-danger">
                    <i class="bi bi-x-circle"></i>
                    <?= $expiredSummary['expired']['items'] ?> batch barang (<?= $expiredSummary['expired']['quantity'] ?> pcs) sudah kadaluarsa
                </p>
            <?php endif; ?>
            <?php if ($expiredSummary['expiring_soon']['items'] > 0): ?>
                <p class="mb-0 text-warning">
                    <i class="bi bi-clock"></i>
                    <?= $expiredSummary['expiring_soon']['items'] ?> batch barang (<?= $expiredSummary['expiring_soon']['quantity'] ?> pcs) akan kadaluarsa dalam 7 hari
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="inventory-content-card">
        <div class="inventory-card-header">
            <h4 class="mt-2">Daftar Barang</h4>
            <div class="inventory-search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="inventory-search-input" placeholder="Cari barang..." id="searchInventory">
            </div>
        </div>

        <div class="inventory-table-wrapper">
            <table class="inventory-data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Status Stok</th>
                        <th>Batch Expired</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody">
                    <?php if (!empty($barang)): ?>
                        <?php foreach ($barang as $row): ?>
                            <?php if ($row['status'] === 'active'): ?>
                                <?php
                                $stockModel = new App\models\Stok();
                                $expiredInfo = $stockModel->getExpiredInfoByBarang($row['id_barang']);
                                $hasExpired = !empty($expiredInfo['expired_batches']);
                                $hasExpiringSoon = !empty($expiredInfo['expiring_soon_batches']);
                                $totalExpiredBatches = count($expiredInfo['expired_batches']);
                                $totalExpiringSoonBatches = count($expiredInfo['expiring_soon_batches']);
                                ?>
                                <tr>
                                    <td class="inventory-code-cell"><?= htmlspecialchars($row['id_barang']) ?></td>
                                    <td class="inventory-name-cell">
                                        <?= htmlspecialchars($row['nama_barang']) ?>
                                        <?php if ($hasExpired): ?>
                                            <span class="badge bg-danger ms-1" title="Ada batch expired">
                                                <i class="bi bi-exclamation-triangle"></i>
                                            </span>
                                        <?php elseif ($hasExpiringSoon): ?>
                                            <span class="badge bg-warning ms-1" title="Ada batch akan expired">
                                                <i class="bi bi-clock"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="inventory-category-cell"><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                                    <td class="inventory-stock-cell"><?= htmlspecialchars($row['stok']) ?> <?= htmlspecialchars($row['satuan']) ?></td>
                                    <td class="inventory-price-cell">Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                                    <td class="inventory-price-cell">Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="inventory-status-badge inventory-status-<?= $row['status_stok'] ?>">
                                            <?=
                                            $row['status_stok'] === 'available' ? 'Tersedia' : ($row['status_stok'] === 'low' ? 'Stok Rendah' : 'Habis')
                                            ?>
                                        </span>
                                    </td>
                                    <td class="inventory-expired-cell">
                                        <?php if ($hasExpired): ?>
                                            <span class="text-danger small">
                                                <i class="bi bi-x-circle"></i>
                                                <?= $totalExpiredBatches ?> batch expired
                                            </span>
                                        <?php elseif ($hasExpiringSoon): ?>
                                            <span class="text-warning small">
                                                <i class="bi bi-clock"></i>
                                                <?= $totalExpiringSoonBatches ?> batch akan expired
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="inventory-action-btns">
                                            <button class="inventory-btn-icon inventory-btn-edit" onclick="editBarang('<?= $row['id_barang']; ?>')" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="inventory-btn-icon inventory-btn-delete" onclick="confirmDelete('<?= $row['id_barang']; ?>')" title="Nonaktifkan">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php if ($hasExpired || $hasExpiringSoon): ?>
                                                <button class="inventory-btn-icon inventory-btn-delete" onclick="viewExpiredDetail('<?= $row['id_barang']; ?>')" title="Lihat Detail Expired">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center;">Tidak ada data barang</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="inventory-pagination-container">
            <div class="inventory-pagination-info" id="paginationInfo">
                Menampilkan <span id="showingStart">1</span>-<span id="showingEnd"><?= count($barang) ?></span> dari <span id="totalData"><?= count($barang) ?></span> data
            </div>
            <div class="inventory-pagination-controls">
                <button class="inventory-pagination-btn inventory-pagination-prev" id="prevBtn" onclick="changePage('prev')">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div id="paginationPages" class="d-flex flex-direction-row gap-2"></div>
                <button class="inventory-pagination-btn inventory-pagination-next" id="nextBtn" onclick="changePage('next')">
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

<!-- Modal Detail Expired -->
<div class="modal fade" id="expiredDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content inventory-modal-content">
            <div class="modal-header inventory-modal-header">
                <h5 class="modal-title inventory-modal-title">Detail Batch Expired - <span id="modalBarangName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body inventory-modal-body">
                <div id="expiredDetailContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer inventory-modal-footer">
                <button type="button" class="inventory-btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Bersihkan Expired - DIPERBAIKI -->
<div class="modal fade" id="cleanExpiredModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content inventory-modal-content">
            <div class="modal-header inventory-modal-header">
                <h5 class="modal-title inventory-modal-title">Tandai Sudah Dibersihkan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cleanExpiredForm">
                <div class="modal-body inventory-modal-body">
                    <input type="hidden" id="clean_id_stokin" name="id_stokin">
                    <input type="hidden" id="clean_id_barang" name="id_barang">
                    <input type="hidden" id="clean_nama_barang" name="nama_barang">
                    <input type="hidden" id="clean_expired_date" name="expired_date">

                    <div class="mb-3">
                        <label class="inventory-form-label">Jumlah yang Dibersihkan <span class="text-danger">*</span></label>
                        <input type="number" class="inventory-form-input" name="jumlah"
                            id="clean_jumlah" min="1" required>
                        <small class="form-text text-muted">Masukkan jumlah yang akan ditandai sebagai sudah dibersihkan</small>
                    </div>

                    <div class="mb-3">
                        <label class="inventory-form-label">Tanggal Pembersihan <span class="text-danger">*</span></label>
                        <input type="date" class="inventory-form-input" name="tanggal_bersih"
                            id="tanggal_bersih" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="inventory-form-label">Alasan <span class="text-danger">*</span></label>
                        <select class="inventory-form-select" name="alasan" id="alasan" required>
                            <option value="dibuang">Dibuang</option>
                            <option value="dijual">Dijual (Diskon)</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="inventory-form-label">Keterangan</label>
                        <textarea class="inventory-form-input" name="keterangan" id="keterangan"
                            rows="3" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <small>
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Perhatian:</strong> Stok barang akan otomatis dikurangi sesuai jumlah yang dibersihkan. Tindakan ini tidak dapat dibatalkan.
                        </small>
                    </div>
                </div>
                <div class="modal-footer inventory-modal-footer">
                    <button type="button" class="inventory-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="inventory-btn-danger">
                        <i class="bi bi-check-circle"></i> Konfirmasi Dibersihkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content inventory-modal-content">
            <div class="modal-header inventory-modal-header">
                <h5 class="modal-title inventory-modal-title" id="modalTitle">Tambah Barang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body inventory-modal-body">
                <form id="inventoryForm">
                    <input type="hidden" id="barangId" name="barangId">
                    <input type="hidden" id="formAction" value="create">

                    <div class="mb-3">
                        <label class="inventory-form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="inventory-form-input" id="nama_barang" name="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label class="inventory-form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="inventory-form-select" id="id_kategori" name="id_kategori" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= htmlspecialchars($kat['id_kategori']) ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="inventory-form-label">Satuan <span class="text-danger">*</span></label>
                        <input type="text" class="inventory-form-input" id="satuan" name="satuan" placeholder="pcs, kg, unit, etc." required>
                    </div>
                    <div class="mb-3">
                        <label class="inventory-form-label">Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" class="inventory-form-input" id="stok" name="stok" value="0" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="inventory-form-label">Harga Beli <span class="text-danger">*</span></label>
                        <input type="number" class="inventory-form-input" id="harga_beli" name="harga_beli" value="0" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="inventory-form-label">Harga Jual <span class="text-danger">*</span></label>
                        <input type="number" class="inventory-form-input" id="harga_jual" name="harga_jual" value="0" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="inventory-form-label">Tanggal Kadaluarsa</label>
                        <input type="date" class="inventory-form-input" id="expired_date" name="expired_date">
                        <small class="form-text text-muted">Opsional - untuk barang yang memiliki masa kadaluarsa</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer inventory-modal-footer">
                <button type="button" class="inventory-btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="inventory-btn-primary" onclick="saveBarang()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteInventoryModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content inventory-modal-content">
            <div class="modal-header inventory-modal-header">
                <h5 class="modal-title inventory-modal-title">Nonaktifkan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body inventory-modal-body">
                <p style="font-size: 0.9rem; color: #4a5568; margin: 0;">
                    Apakah Anda yakin ingin menonaktifkan barang ini? Barang yang dinonaktifkan tidak akan muncul dalam daftar.
                </p>
                <input type="hidden" id="deleteBarangId">
            </div>

            <div class="modal-footer inventory-modal-footer">
                <button type="button" class="inventory-btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="inventory-btn-primary" onclick="deleteBarang()">Nonaktifkan</button>
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

    .inventory-status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: capitalize;
    }

    .inventory-status-available {
        background: #d1fae5;
        color: #065f46;
    }

    .inventory-status-low {
        background: #fef3c7;
        color: #92400e;
    }

    .inventory-status-empty {
        background: #fee2e2;
        color: #991b1b;
    }

    .inventory-btn-warning {
        background: #f59e0b;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.875rem;
        transition: background-color 0.2s;
    }

    .inventory-btn-warning:hover {
        background: #d97706;
        color: white;
    }

    .inventory-btn-info {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.875rem;
        transition: background-color 0.2s;
    }

    .inventory-btn-info:hover {
        background: #2563eb;
        color: white;
    }

    .inventory-btn-danger {
        background: #dc2626;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.875rem;
        transition: background-color 0.2s;
    }

    .inventory-btn-danger:hover {
        background: #b91c1c;
        color: white;
    }

    .inventory-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .batch-expired-item {
        border-left: 4px solid #dc2626;
        background: #fef2f2;
        padding: 12px;
        margin-bottom: 8px;
        border-radius: 4px;
    }

    .batch-expiring-soon-item {
        border-left: 4px solid #d97706;
        background: #fffbeb;
        padding: 12px;
        margin-bottom: 8px;
        border-radius: 4px;
    }

    .batch-safe-item {
        border-left: 4px solid #059669;
        background: #f0fdf4;
        padding: 12px;
        margin-bottom: 8px;
        border-radius: 4px;
    }

    .expired-section,
    .expiring-soon-section,
    .safe-section {
        border-left: 4px solid;
        padding-left: 15px;
        margin-bottom: 20px;
    }

    .expired-section {
        border-left-color: #dc2626;
    }

    .expiring-soon-section {
        border-left-color: #d97706;
    }

    .safe-section {
        border-left-color: #059669;
    }

    .btn-outline-danger {
        border-color: #dc2626;
        color: #dc2626;
    }

    .btn-outline-danger:hover {
        background-color: #dc2626;
        color: white;
    }

    .btn-outline-warning {
        border-color: #d97706;
        color: #d97706;
    }

    .btn-outline-warning:hover {
        background-color: #d97706;
        color: white;
    }
</style>

<script>
    let currentPage = 1;
    let rowsPerPage = 5;
    let allRows = [];
    let filteredRows = [];
    let toastTimeout;

    document.addEventListener('DOMContentLoaded', function() {
        allRows = Array.from(document.querySelectorAll('#inventoryTableBody tr'));
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
        const paginationContainer = document.querySelector('.inventory-pagination-container');
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
            pageBtn.className = 'inventory-pagination-page' + (i === page ? ' active' : '');
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

    document.getElementById('searchInventory').addEventListener('keyup', function() {
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
        document.getElementById('inventoryForm').reset();
        document.getElementById('barangId').value = '';
        document.getElementById('formAction').value = 'create';
        document.getElementById('modalTitle').textContent = 'Tambah Barang Baru';

        const modal = new bootstrap.Modal(document.getElementById('addInventoryModal'));
        modal.show();
    }

    function editBarang(id) {
        fetch(`/barang/edit/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    document.getElementById('barangId').value = result.data.id_barang;
                    document.getElementById('nama_barang').value = result.data.nama_barang;
                    document.getElementById('id_kategori').value = result.data.id_kategori;
                    document.getElementById('satuan').value = result.data.satuan;
                    document.getElementById('stok').value = result.data.stok;
                    document.getElementById('harga_beli').value = result.data.harga_beli;
                    document.getElementById('harga_jual').value = result.data.harga_jual;
                    document.getElementById('expired_date').value = result.data.expired_date;
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('modalTitle').textContent = 'Edit Barang';

                    const modal = new bootstrap.Modal(document.getElementById('addInventoryModal'));
                    modal.show();
                } else {
                    showNotification('error', 'Error', result.message || 'Gagal mengambil data barang');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Error', 'Terjadi kesalahan saat mengambil data barang');
            });
    }

    function viewExpiredDetail(id) {
        console.log('Loading expired detail for barang:', id);

        const content = document.getElementById('expiredDetailContent');
        content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat data expired...</p>
        </div>
    `;

        const modal = new bootstrap.Modal(document.getElementById('expiredDetailModal'));
        modal.show();

        fetch(`/barang/expired-detail/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('Expired detail result:', result);

                if (result.success) {
                    let html = '<div class="expired-detail-container">';

                    if (result.data.expired_batches && result.data.expired_batches.length > 0) {
                        html += `
                        <div class="expired-section">
                            <h6 class="text-danger mb-3">
                                <i class="bi bi-x-circle"></i> Batch Expired
                                <span class="badge bg-danger">${result.data.expired_batches.length}</span>
                            </h6>
                    `;

                        result.data.expired_batches.forEach(batch => {
                            const tanggalMasuk = new Date(batch.tanggal_masuk).toLocaleDateString('id-ID');
                            const expiredDate = new Date(batch.expired_date).toLocaleDateString('id-ID');
                            const daysExpired = Math.floor((new Date() - new Date(batch.expired_date)) / (1000 * 60 * 60 * 24));

                            html += `
                            <div class="batch-expired-item mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="text-danger">Batch: ${batch.id_stokin}</strong>
                                        <br>
                                        <small class="text-muted">Expired ${daysExpired} hari yang lalu</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="showCleanModal(
                                                '${batch.id_stokin}', 
                                                '${batch.id_barang}', 
                                                '${batch.nama_barang.replace(/'/g, "\\'")}', 
                                                ${batch.sisa_stok}, 
                                                '${batch.expired_date}',
                                                ${batch.sisa_stok}
                                            )">
                                        <i class="bi bi-trash"></i> Tandai Dibersihkan
                                    </button>
                                </div>
                                <div class="row small">
                                    <div class="col-md-4">
                                        <strong>Tanggal Masuk:</strong> ${tanggalMasuk}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Expired Date:</strong> ${expiredDate}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Sisa Stok:</strong> ${batch.sisa_stok} pcs
                                    </div>
                                    <div class="col-md-12 mt-1">
                                        <small class="text-muted">
                                            Jumlah Awal: ${batch.jumlah_awal} pcs | 
                                            Sudah Dibersihkan: ${batch.jumlah_sudah_dibersihkan} pcs
                                        </small>
                                    </div>
                                </div>
                            </div>
                        `;
                        });

                        html += `</div>`;
                    }

                    if (result.data.expiring_soon_batches && result.data.expiring_soon_batches.length > 0) {
                        html += `
                        <div class="expiring-soon-section mt-4">
                            <h6 class="text-warning mb-3">
                                <i class="bi bi-clock"></i> Batch Akan Expired
                                <span class="badge bg-warning">${result.data.expiring_soon_batches.length}</span>
                            </h6>
                    `;

                        result.data.expiring_soon_batches.forEach(batch => {
                            const tanggalMasuk = new Date(batch.tanggal_masuk).toLocaleDateString('id-ID');
                            const expiredDate = new Date(batch.expired_date).toLocaleDateString('id-ID');
                            const daysUntil = Math.floor((new Date(batch.expired_date) - new Date()) / (1000 * 60 * 60 * 24));

                            html += `
                            <div class="batch-expiring-soon-item mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="text-warning">Batch: ${batch.id_stokin}</strong>
                                        <br>
                                        <small class="text-muted">Akan expired dalam ${daysUntil} hari</small>
                                    </div>
                                    <span class="badge bg-warning">Masih Berlaku</span>
                                </div>
                                <div class="row small">
                                    <div class="col-md-4">
                                        <strong>Tanggal Masuk:</strong> ${tanggalMasuk}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Expired Date:</strong> ${expiredDate}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Sisa Stok:</strong> ${batch.sisa_stok} pcs
                                    </div>
                                    <div class="col-md-12 mt-1">
                                        <small class="text-muted">
                                            Jumlah Awal: ${batch.jumlah_awal} pcs | 
                                            Sudah Dibersihkan: ${batch.jumlah_sudah_dibersihkan} pcs
                                        </small>
                                    </div>
                                </div>
                            </div>
                        `;
                        });

                        html += `</div>`;
                    }

                    if (result.data.safe_batches && result.data.safe_batches.length > 0) {
                        html += `
                        <div class="safe-section mt-4">
                            <h6 class="text-success mb-3">
                                <i class="bi bi-check-circle"></i> Batch Aman
                                <span class="badge bg-success">${result.data.safe_batches.length}</span>
                            </h6>
                    `;

                        result.data.safe_batches.forEach(batch => {
                            const tanggalMasuk = new Date(batch.tanggal_masuk).toLocaleDateString('id-ID');
                            const expiredDate = new Date(batch.expired_date).toLocaleDateString('id-ID');
                            const daysUntil = Math.floor((new Date(batch.expired_date) - new Date()) / (1000 * 60 * 60 * 24));

                            html += `
                            <div class="batch-safe-item mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="text-success">Batch: ${batch.id_stokin}</strong>
                                        <br>
                                        <small class="text-muted">Masih ${daysUntil} hari lagi</small>
                                    </div>
                                    <span class="badge bg-success">Aman</span>
                                </div>
                                <div class="row small">
                                    <div class="col-md-4">
                                        <strong>Tanggal Masuk:</strong> ${tanggalMasuk}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Expired Date:</strong> ${expiredDate}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Sisa Stok:</strong> ${batch.sisa_stok} pcs
                                    </div>
                                    <div class="col-md-12 mt-1">
                                        <small class="text-muted">
                                            Jumlah Awal: ${batch.jumlah_awal} pcs | 
                                            Sudah Dibersihkan: ${batch.jumlah_sudah_dibersihkan} pcs
                                        </small>
                                    </div>
                                </div>
                            </div>
                        `;
                        });

                        html += `</div>`;
                    }

                    if ((!result.data.expired_batches || result.data.expired_batches.length === 0) &&
                        (!result.data.expiring_soon_batches || result.data.expiring_soon_batches.length === 0) &&
                        (!result.data.safe_batches || result.data.safe_batches.length === 0)) {
                        html = `
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle text-success display-4"></i>
                            <h5 class="mt-3 text-success">Tidak Ada Batch Expired</h5>
                            <p class="text-muted">Semua batch barang dalam kondisi aman.</p>
                        </div>
                    `;
                    }

                    html += `</div>`;
                    content.innerHTML = html;

                    if (result.data.expired_batches && result.data.expired_batches.length > 0) {
                        document.getElementById('modalBarangName').textContent = result.data.expired_batches[0].nama_barang;
                    } else if (result.data.expiring_soon_batches && result.data.expiring_soon_batches.length > 0) {
                        document.getElementById('modalBarangName').textContent = result.data.expiring_soon_batches[0].nama_barang;
                    }

                } else {
                    content.innerHTML = `<p class="text-danger">Error: ${result.message || 'Gagal mengambil data expired'}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching expired detail:', error);
                content.innerHTML = `
                <p class="text-danger">
                    Terjadi kesalahan saat mengambil data expired: ${error.message}
                </p>
            `;
            });
    }

    function showCleanModal(idStokin, idBarang, namaBarang, jumlah, expiredDate, maxJumlah) {
        console.log('Show clean modal parameters:', {
            idStokin,
            idBarang,
            namaBarang,
            jumlah,
            expiredDate,
            maxJumlah
        });

        if (!idBarang || idBarang === 'undefined') {
            console.error('Error: idBarang is undefined!');
            showNotification('error', 'Error', 'ID Barang tidak valid');
            return;
        }

        document.getElementById('clean_id_stokin').value = idStokin;
        document.getElementById('clean_id_barang').value = idBarang;
        document.getElementById('clean_nama_barang').value = namaBarang;
        document.getElementById('clean_jumlah').value = jumlah;
        document.getElementById('clean_expired_date').value = expiredDate;

        document.getElementById('clean_jumlah').setAttribute('max', maxJumlah);

        document.getElementById('tanggal_bersih').value = '<?= date('Y-m-d') ?>';
        document.getElementById('alasan').value = 'dibuang';
        document.getElementById('keterangan').value = '';

        let jumlahInfo = document.getElementById('maxJumlahInfo');
        if (!jumlahInfo) {
            jumlahInfo = document.createElement('div');
            jumlahInfo.id = 'maxJumlahInfo';
            jumlahInfo.className = 'alert alert-info mt-2';
            document.querySelector('input[name="jumlah"]').parentNode.appendChild(jumlahInfo);
        }
        jumlahInfo.innerHTML = `<small><i class="bi bi-info-circle"></i> Jumlah maksimal yang dapat dibersihkan: <strong>${maxJumlah} pcs</strong></small>`;

        const modal = new bootstrap.Modal(document.getElementById('cleanExpiredModal'));
        modal.show();
    }

    document.getElementById('cleanExpiredForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');

        const tanggalBersih = document.getElementById('tanggal_bersih').value;
        const alasan = document.getElementById('alasan').value;
        const jumlah = document.getElementById('clean_jumlah').value;

        if (!tanggalBersih) {
            showNotification('error', 'Error', 'Tanggal pembersihan harus diisi');
            return;
        }

        if (!alasan) {
            showNotification('error', 'Error', 'Alasan harus dipilih');
            return;
        }

        if (!jumlah || jumlah <= 0) {
            showNotification('error', 'Error', 'Jumlah harus lebih dari 0');
            return;
        }

        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
        submitBtn.disabled = true;

        fetch('/stokin/mark-expired-cleaned', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('Response from server:', result); 

                if (result.success) {
                    showNotification('success', 'Berhasil', result.message);

                    const cleanModal = bootstrap.Modal.getInstance(document.getElementById('cleanExpiredModal'));
                    if (cleanModal) cleanModal.hide();

                    const detailModal = bootstrap.Modal.getInstance(document.getElementById('expiredDetailModal'));
                    if (detailModal) detailModal.hide();

                    setTimeout(() => {
                        window.location.href = '/barang/expired-cleaned-history';
                    }, 2000);

                } else {
                    if (result.debug) {
                        console.error('Debug info:', result.debug);
                    }
                    showNotification('error', 'Error', result.message || 'Gagal memproses data');
                    submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Konfirmasi Dibersihkan';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Error', 'Terjadi kesalahan saat memproses');
                submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Konfirmasi Dibersihkan';
                submitBtn.disabled = false;
            });
    });

    function saveBarang() {
        const form = document.getElementById('inventoryForm');
        const formData = new FormData(form);

        const namaBarang = document.getElementById('nama_barang').value;
        const idKategori = document.getElementById('id_kategori').value;
        const satuan = document.getElementById('satuan').value;
        const hargaBeli = parseFloat(document.getElementById('harga_beli').value);
        const hargaJual = parseFloat(document.getElementById('harga_jual').value);

        if (!namaBarang || !idKategori || !satuan) {
            showNotification('error', 'Error', 'Nama barang, kategori, dan satuan harus diisi');
            return;
        }

        if (hargaJual <= hargaBeli) {
            showNotification('error', 'Error', 'Harga jual harus lebih besar dari harga beli');
            return;
        }

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const action = document.getElementById('formAction').value;
        const barangId = document.getElementById('barangId').value;

        let url = action === 'create' ? '/barang/create' : `/barang/update/${barangId}`;

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addInventoryModal'));
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
        document.getElementById('deleteBarangId').value = id;
        const modal = new bootstrap.Modal(document.getElementById('deleteInventoryModal'));
        modal.show();
    }

    function deleteBarang() {
        const barangId = document.getElementById('deleteBarangId').value;

        fetch(`/barang/delete/${barangId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteInventoryModal'));
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