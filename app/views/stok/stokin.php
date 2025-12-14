<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<main class="stockin-page-content">
    <div class="stockin-page-header">
        <div class="stockin-header-left">
            <h1 class="stockin-page-title">Stock In</h1>
            <p class="stockin-page-subtitle">Kelola barang masuk ke inventori</p>
        </div>
        <div class="stockin-header-actions">
            <a href="/stokin/expired-report" class="stockin-btn-warning me-2">
                <i class="bi bi-exclamation-triangle"></i> Laporan Expired
            </a>
            <button class="stockin-btn-add" data-bs-toggle="modal" data-bs-target="#addStockInModal">
                <i class="bi bi-plus"></i> Tambah Stock In
            </button>
        </div>
    </div>

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

    <div class="stockin-content-card">
        <div class="stockin-card-header">
            <h4 class="mt-2">Riwayat Stock In</h4>
            <div class="stockin-filter-container">
                <form method="GET" action="" class="d-flex align-items-center gap-3">
                    <div class="stockin-search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" class="stockin-search-input" placeholder="Cari riwayat..."
                            name="search" value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>

                    <div class="stockin-date-filter">
                        <div class="stockin-period-selector">
                            <button type="button" class="stockin-period-btn" id="periodDropdown">
                                <i class="bi bi-calendar-week"></i>
                                <span>
                                    <?= $period === 'day' ? 'Per Hari' : ($period === 'month' ? 'Per Bulan' : 'Per Tahun') ?>
                                </span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="stockin-period-dropdown" id="periodDropdownMenu">
                                <button type="button" class="stockin-period-option <?= $period === 'day' ? 'active' : '' ?>" data-period="day">
                                    <i class="bi bi-calendar-day"></i>
                                    Per Hari
                                </button>
                                <button type="button" class="stockin-period-option <?= $period === 'month' ? 'active' : '' ?>" data-period="month">
                                    <i class="bi bi-calendar-month"></i>
                                    Per Bulan
                                </button>
                                <button type="button" class="stockin-period-option <?= $period === 'year' ? 'active' : '' ?>" data-period="year">
                                    <i class="bi bi-calendar"></i>
                                    Per Tahun
                                </button>
                            </div>
                            <input type="hidden" name="period" id="selectedPeriod" value="<?= $period ?>">
                        </div>

                        <div class="stockin-date-inputs">
                            <div class="stockin-date-input-group <?= $period !== 'day' ? 'hidden' : '' ?>" id="dayInputs">
                                <input type="date" class="stockin-date-input" name="date"
                                    value="<?= $filterDate ?? date('Y-m-d') ?>">
                            </div>

                            <div class="stockin-date-input-group <?= $period !== 'month' ? 'hidden' : '' ?>" id="monthInputs">
                                <select class="stockin-month-select" name="month">
                                    <option value="">Pilih Bulan</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= sprintf('%02d', $i) ?>"
                                            <?= ($filterMonth ?? '') == sprintf('%02d', $i) ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <select class="stockin-year-select" name="year">
                                    <option value="">Pilih Tahun</option>
                                    <?php for ($year = date('Y'); $year >= 2020; $year--): ?>
                                        <option value="<?= $year ?>"
                                            <?= ($filterYear ?? '') == $year ? 'selected' : '' ?>>
                                            <?= $year ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="stockin-date-input-group <?= $period !== 'year' ? 'hidden' : '' ?>" id="yearInputs">
                                <select class="stockin-year-select" name="year">
                                    <option value="">Pilih Tahun</option>
                                    <?php for ($year = date('Y'); $year >= 2020; $year--): ?>
                                        <option value="<?= $year ?>"
                                            <?= ($filterYear ?? '') == $year ? 'selected' : '' ?>>
                                            <?= $year ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="stockin-filter-btn">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="stockin-table-wrapper">
            <table class="stockin-data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode Transaksi</th>
                        <th>Nama Barang</th>
                        <th>Supplier</th>
                        <th>Jumlah</th>
                        <th>Harga Beli</th>
                        <th>Total Harga</th>
                        <th>Expired Date</th>
                        <th>Status Expired</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stockInData)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">Tidak ada data stock in</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stockInData as $item): ?>
                            <?php
                            $expiredStatus = '';
                            $statusClass = '';
                            if ($item['expired_date']) {
                                $currentDate = date('Y-m-d');
                                $expiredDate = date('Y-m-d', strtotime($item['expired_date']));

                                if ($expiredDate < $currentDate) {
                                    $expiredStatus = 'Expired';
                                    $statusClass = 'stockin-status-expired';
                                } elseif ($expiredDate == $currentDate) {
                                    $expiredStatus = 'Expiring Today';
                                    $statusClass = 'stockin-status-expiring-today';
                                } elseif ((strtotime($expiredDate) - strtotime($currentDate)) / (60 * 60 * 24) <= 7) {
                                    $expiredStatus = 'Expiring Soon';
                                    $statusClass = 'stockin-status-expiring-soon';
                                } else {
                                    $expiredStatus = 'Aman';
                                    $statusClass = 'stockin-status-safe';
                                }
                            } else {
                                $expiredStatus = 'Tidak Ada';
                                $statusClass = 'stockin-status-none';
                            }
                            ?>
                            <tr>
                                <td class="stockin-date-cell">
                                    <?= date('Y-m-d H:i', strtotime($item['tanggal_masuk'])) ?>
                                </td>
                                <td class="stockin-code-cell">
                                    <strong><?= htmlspecialchars($item['id_stokin']) ?></strong>
                                </td>
                                <td class="stockin-item-cell">
                                    <?= htmlspecialchars($item['nama_barang']) ?>
                                </td>
                                <td class="stockin-supplier-cell">
                                    <?= htmlspecialchars($item['nama_supplier'] ?? '-') ?>
                                </td>
                                <td class="stockin-quantity-cell">
                                    <span class="stockin-quantity-positive">+<?= $item['jumlah'] ?> pcs</span>
                                </td>
                                <td class="stockin-price-cell">
                                    Rp <?= number_format($item['harga_beli'], 0, ',', '.') ?>
                                </td>
                                <td class="stockin-total-cell">
                                    Rp <?= number_format($item['total_harga'], 0, ',', '.') ?>
                                </td>
                                <td class="stockin-expired-cell">
                                    <?= $item['expired_date'] ? date('d/m/Y', strtotime($item['expired_date'])) : '-' ?>
                                </td>
                                <td class="stockin-status-cell">
                                    <span class="stockin-status-badge <?= $statusClass ?>">
                                        <?= $expiredStatus ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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

<div class="modal fade" id="addStockInModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content stockin-modal-content">
            <div class="modal-header stockin-modal-header">
                <h5 class="modal-title stockin-modal-title">Tambah Stock In</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/stokin/add" method="POST" id="addStockInForm">
                <div class="modal-body stockin-modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="stockin-form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="stockin-form-input" name="tanggal_masuk"
                                    required id="tanggal_masuk" value="<?= date('Y-m-d\TH:i') ?>">
                                <small class="form-text text-muted">Tanggal dan waktu barang masuk</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="stockin-form-label">Barang <span class="text-danger">*</span></label>
                                <select class="stockin-form-select" name="id_barang" required id="id_barang">
                                    <option value="">Pilih Barang</option>
                                    <?php if (!empty($barangList)): ?>
                                        <?php foreach ($barangList as $barang): ?>
                                            <option value="<?= $barang['id_barang'] ?>" data-stok="<?= $barang['stok'] ?? 0 ?>">
                                                <?= htmlspecialchars($barang['nama_barang']) ?>
                                                (Stok: <?= $barang['stok'] ?? 0 ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">-- Data barang tidak tersedia --</option>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">Stok saat ini: <span id="currentStock">0</span></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="stockin-form-label">Supplier <span class="text-danger">*</span></label>
                                <select class="stockin-form-select" name="id_supplier" required>
                                    <option value="">Pilih Supplier</option>
                                    <?php if (!empty($supplierList)): ?>
                                        <?php foreach ($supplierList as $supplier): ?>
                                            <option value="<?= $supplier['id_supplier'] ?>">
                                                <?= htmlspecialchars($supplier['nama_supplier']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">-- Data supplier tidak tersedia --</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="stockin-form-label">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" class="stockin-form-input" name="jumlah" min="1"
                                    required id="jumlah" placeholder="Masukkan jumlah barang">
                                <small class="form-text text-muted">Minimal 1 pcs</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="stockin-form-label">Harga Beli per Unit <span class="text-danger">*</span></label>
                                <input type="number" class="stockin-form-input" name="harga_beli" min="0"
                                    step="100" required id="harga_beli" placeholder="Masukkan harga beli">
                                <small class="form-text text-muted">Dalam Rupiah</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="stockin-form-label">Tanggal Kadaluarsa</label>
                                <input type="date" class="stockin-form-input" name="expired_date"
                                    id="expired_date" min="<?= date('Y-m-d') ?>">
                                <small class="form-text text-muted">Opsional - untuk barang yang memiliki masa kadaluarsa</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="stockin-form-label">Total Harga</label>
                                <input type="text" class="stockin-form-input" id="total_harga"
                                    readonly placeholder="Akan terhitung otomatis" style="background-color: #f8f9fa;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer stockin-modal-footer">
                    <button type="button" class="stockin-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="stockin-btn-primary" id="submitBtn">
                        <i class="bi bi-check-lg"></i> Simpan Stock In
                    </button>
                </div>
            </form>
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

    .stockin-status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: capitalize;
    }

    .stockin-status-expired {
        background: #fee2e2;
        color: #dc2626;
    }

    .stockin-status-expiring-today {
        background: #fef3c7;
        color: #d97706;
    }

    .stockin-status-expiring-soon {
        background: #fef3c7;
        color: #d97706;
    }

    .stockin-status-safe {
        background: #d1fae5;
        color: #065f46;
    }

    .stockin-status-none {
        background: #f3f4f6;
        color: #6b7280;
    }

    .stockin-btn-warning {
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

    .stockin-btn-warning:hover {
        background: #d97706;
        color: white;
    }

    .stockin-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .hidden {
        display: none !important;
    }
</style>

<script>
    let toastTimeout = null;

    document.addEventListener('DOMContentLoaded', function() {
        const periodDropdown = document.getElementById('periodDropdown');
        const periodDropdownMenu = document.getElementById('periodDropdownMenu');
        const periodOptions = document.querySelectorAll('.stockin-period-option');
        const selectedPeriod = document.getElementById('selectedPeriod');
        const dayInputs = document.getElementById('dayInputs');
        const monthInputs = document.getElementById('monthInputs');
        const yearInputs = document.getElementById('yearInputs');

        if (periodDropdown) {
            periodDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                periodDropdownMenu.classList.toggle('show');
            });

            document.addEventListener('click', function() {
                periodDropdownMenu.classList.remove('show');
            });

            periodOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const period = this.getAttribute('data-period');
                    periodOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');

                    const periodText = this.textContent.trim();
                    periodDropdown.querySelector('span').textContent = periodText;
                    selectedPeriod.value = period;

                    dayInputs.classList.add('hidden');
                    monthInputs.classList.add('hidden');
                    yearInputs.classList.add('hidden');

                    if (period === 'day') {
                        dayInputs.classList.remove('hidden');
                    } else if (period === 'month') {
                        monthInputs.classList.remove('hidden');
                    } else if (period === 'year') {
                        yearInputs.classList.remove('hidden');
                    }

                    periodDropdownMenu.classList.remove('show');
                });
            });
        }

        const jumlahInput = document.getElementById('jumlah');
        const hargaBeliInput = document.getElementById('harga_beli');
        const totalHargaInput = document.getElementById('total_harga');
        const barangSelect = document.getElementById('id_barang');
        const currentStockSpan = document.getElementById('currentStock');

        function calculateTotal() {
            const jumlah = parseInt(jumlahInput.value) || 0;
            const hargaBeli = parseInt(hargaBeliInput.value) || 0;
            const total = jumlah * hargaBeli;

            if (total > 0) {
                totalHargaInput.value = 'Rp ' + total.toLocaleString('id-ID');
            } else {
                totalHargaInput.value = '';
            }
        }

        function updateCurrentStock() {
            const selectedOption = barangSelect.options[barangSelect.selectedIndex];
            const currentStock = selectedOption.getAttribute('data-stok') || 0;
            currentStockSpan.textContent = currentStock;
        }

        if (jumlahInput && hargaBeliInput) {
            jumlahInput.addEventListener('input', calculateTotal);
            hargaBeliInput.addEventListener('input', calculateTotal);
        }

        if (barangSelect) {
            barangSelect.addEventListener('change', updateCurrentStock);
            updateCurrentStock();
        }

        const stockInForm = document.getElementById('addStockInForm');
        if (stockInForm) {
            stockInForm.addEventListener('submit', function(e) {
                const jumlah = parseInt(jumlahInput.value) || 0;
                const hargaBeli = parseInt(hargaBeliInput.value) || 0;
                const expiredDate = document.getElementById('expired_date').value;
                const tanggalMasuk = document.getElementById('tanggal_masuk').value;

                if (jumlah <= 0) {
                    e.preventDefault();
                    showNotification('error', 'Error', 'Jumlah harus lebih dari 0');
                    jumlahInput.focus();
                    return;
                }

                if (hargaBeli <= 0) {
                    e.preventDefault();
                    showNotification('error', 'Error', 'Harga beli harus lebih dari 0');
                    hargaBeliInput.focus();
                    return;
                }

                if (expiredDate && new Date(expiredDate) < new Date(tanggalMasuk)) {
                    e.preventDefault();
                    showNotification('error', 'Error', 'Tanggal kadaluarsa tidak boleh kurang dari tanggal masuk');
                    document.getElementById('expired_date').focus();
                    return;
                }

                const submitBtn = document.getElementById('submitBtn');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
                submitBtn.disabled = true;
            });
        }

        const addStockInModal = document.getElementById('addStockInModal');
        if (addStockInModal) {
            addStockInModal.addEventListener('hidden.bs.modal', function() {
                stockInForm.reset();
                document.getElementById('tanggal_masuk').value = '<?= date('Y-m-d\TH:i') ?>';
                updateCurrentStock();
                calculateTotal();

                const submitBtn = document.getElementById('submitBtn');
                submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan Stock In';
                submitBtn.disabled = false;
            });
        }

        <?php if (isset($_SESSION['success_message'])): ?>
            showNotification('success', 'Berhasil', '<?= addslashes($_SESSION['success_message']) ?>');
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            showNotification('error', 'Error', '<?= addslashes($_SESSION['error_message']) ?>');
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning_message'])): ?>
            showNotification('warning', 'Peringatan', '<?= addslashes($_SESSION['warning_message']) ?>');
            <?php unset($_SESSION['warning_message']); ?>
        <?php endif; ?>
    });

    function showNotification(type, title, message) {
        console.log('Showing notification:', type, title, message); 

        const toast = document.getElementById('notificationToast');
        const toastIcon = document.getElementById('toastIcon');
        const toastTitle = document.getElementById('toastTitle');
        const toastText = document.getElementById('toastText');
        const toastProgress = document.getElementById('toastProgress');

        if (!toast) {
            console.error('Toast element not found!');
            return;
        }

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

        toast.className = 'notification-toast';
        toast.classList.add(type);

        toast.classList.add('show');

        toastProgress.style.transform = 'scaleX(1)';
        setTimeout(() => {
            toastProgress.style.transform = 'scaleX(0)';
        }, 50);

        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }

        toastTimeout = setTimeout(() => {
            hideNotification();
        }, 5000);
    }

    function hideNotification() {
        const toast = document.getElementById('notificationToast');
        if (toast) {
            toast.classList.remove('show');
        }
        if (toastTimeout) {
            clearTimeout(toastTimeout);
            toastTimeout = null;
        }
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.toast-close')) {
            hideNotification();
        }
    });

    document.getElementById('notificationToast')?.addEventListener('click', function(e) {
        if (!e.target.closest('.toast-close')) {
            hideNotification();
        }
    });
</script>

<?php
include __DIR__ . '/../layouts/footer.php';
?>