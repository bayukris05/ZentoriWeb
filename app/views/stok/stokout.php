<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>
<main class="stockout-page-content">
    <div class="stockout-page-header">
        <div class="stockout-header-left">
            <h1 class="stockout-page-title">Stock Out</h1>
            <p class="stockout-page-subtitle">Kelola barang keluar dari inventori</p>
        </div>
        <button class="stockout-btn-add" data-bs-toggle="modal" data-bs-target="#addStockOutModal">
            <i class="bi bi-plus"></i> Tambah Stock Out
        </button>
    </div>

    <div class="stockout-content-card">
        <div class="stockout-card-header">
            <h4 class="mt-2">Riwayat Stock Out</h4>
            <div class="stockout-filter-container">
                <form method="GET" action="" class="d-flex align-items-center gap-3">
                    <div class="stockout-search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="stockout-search-input" placeholder="Cari riwayat..." 
                               value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                    
                    <div class="stockout-date-filter">
                        <div class="stockout-period-selector">
                            <button type="button" class="stockout-period-btn" id="periodDropdown">
                                <i class="bi bi-calendar-week"></i>
                                <span>
                                    <?= match($period ?? 'day') {
                                        'day' => 'Per Hari',
                                        'month' => 'Per Bulan',
                                        'year' => 'Per Tahun',
                                        default => 'Pilih Periode'
                                    } ?>
                                </span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="stockout-period-dropdown" id="periodDropdownMenu">
                                <button type="button" class="stockout-period-option <?= ($period ?? 'day') === 'day' ? 'active' : '' ?>" data-period="day">
                                    <i class="bi bi-calendar-day"></i>
                                    Per Hari
                                </button>
                                <button type="button" class="stockout-period-option <?= ($period ?? 'day') === 'month' ? 'active' : '' ?>" data-period="month">
                                    <i class="bi bi-calendar-month"></i>
                                    Per Bulan
                                </button>
                                <button type="button" class="stockout-period-option <?= ($period ?? 'day') === 'year' ? 'active' : '' ?>" data-period="year">
                                    <i class="bi bi-calendar"></i>
                                    Per Tahun
                                </button>
                            </div>
                            <input type="hidden" name="period" id="periodInput" value="<?= $period ?? 'day' ?>">
                        </div>

                        <div class="stockout-date-inputs">
                            <div class="stockout-date-input-group <?= ($period ?? 'day') !== 'day' ? 'hidden' : '' ?>" id="dayInputs">
                                <input type="date" name="date" class="stockout-date-input" 
                                       value="<?= $filterDate ?? date('Y-m-d') ?>">
                            </div>

                            <div class="stockout-date-input-group <?= ($period ?? 'day') !== 'month' ? 'hidden' : '' ?>" id="monthInputs">
                                <select class="stockout-month-select" name="month">
                                    <option value="">Pilih Bulan</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= sprintf('%02d', $i) ?>" 
                                                <?= ($filterMonth ?? '') == sprintf('%02d', $i) ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <select class="stockout-year-select" name="year">
                                    <option value="">Pilih Tahun</option>
                                    <?php for ($year = date('Y'); $year >= 2020; $year--): ?>
                                        <option value="<?= $year ?>" 
                                                <?= ($filterYear ?? '') == $year ? 'selected' : '' ?>>
                                            <?= $year ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="stockout-date-input-group <?= ($period ?? 'day') !== 'year' ? 'hidden' : '' ?>" id="yearInputs">
                                <select class="stockout-year-select" name="year">
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

                        <button type="submit" class="stockout-filter-btn">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="stockout-table-wrapper">
            <table class="stockout-data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode Transaksi</th>
                        <th>Nama Barang</th>
                        <th>Tipe Keluar</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>Keterangan</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stockOutData)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">Tidak ada data stock out</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stockOutData as $item): ?>
                            <tr>
                                <td class="stockout-date-cell"><?= date('Y-m-d H:i', strtotime($item['tanggal_keluar'])) ?></td>
                                <td class="stockout-code-cell">
                                    <strong><?= htmlspecialchars($item['id_stokout']) ?></strong>
                                </td>
                                <td class="stockout-item-cell"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td class="stockout-type-cell">
                                    <span class="badge 
                                        <?= match($item['tipe_keluar']) {
                                            'penjualan' => 'bg-success',
                                            'pemakaian' => 'bg-primary',
                                            'retur' => 'bg-warning',
                                            default => 'bg-secondary'
                                        } ?>">
                                        <?= ucfirst($item['tipe_keluar']) ?>
                                    </span>
                                </td>
                                <td class="stockout-quantity-cell">
                                    <span class="stockout-quantity-negative">-<?= number_format($item['jumlah']) ?> pcs</span>
                                </td>
                                <td class="stockout-price-cell">
                                    Rp <?= number_format($item['total_harga'], 0, ',', '.') ?>
                                </td>
                                <td class="stockout-description-cell"><?= htmlspecialchars($item['keterangan']) ?></td>
                                <td class="stockout-user-cell"><?= htmlspecialchars($item['nama_user'] ?? 'System') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="stockout-pagination-container">
            <div class="stockout-pagination-info">
                Menampilkan <?= count($stockOutData) ?> data
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

<div class="modal fade" id="addStockOutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content stockout-modal-content">
            <div class="modal-header stockout-modal-header">
                <h5 class="modal-title stockout-modal-title">Tambah Stock Out</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/stokout/add" id="addStockOutForm">
                <div class="modal-body stockout-modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="stockout-form-label">Tanggal Keluar *</label>
                                <input type="datetime-local" name="tanggal_keluar" class="stockout-form-input" 
                                       value="<?= date('Y-m-d\TH:i') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="stockout-form-label">Tipe Keluar *</label>
                                <select name="tipe_keluar" class="stockout-form-select" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="penjualan">Penjualan</option>
                                    <option value="pemakaian">Pemakaian Internal</option>
                                    <option value="retur">Retur</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="stockout-form-label">Barang *</label>
                        <select name="id_barang" class="stockout-form-select" required id="barangSelect">
                            <option value="">Pilih Barang</option>
                            <?php foreach ($barangList as $barang): ?>
                                <option value="<?= $barang['id_barang'] ?>" 
                                        data-stock="<?= $barang['stok'] ?>"
                                        data-harga="<?= $barang['harga_jual'] ?? 0 ?>">
                                    <?= htmlspecialchars($barang['nama_barang']) ?> (Stok: <?= $barang['stok'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="stockout-form-label">Jumlah *</label>
                                <input type="number" name="jumlah" class="stockout-form-input" 
                                       min="1" required id="jumlahInput" placeholder="0">
                                <small class="text-muted" id="stockInfo">Stok tersedia: 0</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="stockout-form-label">Total Harga</label>
                                <input type="text" name="total_harga" class="stockout-form-input" 
                                       placeholder="0" id="totalHargaInput" readonly>
                                <small class="text-muted">Akan terisi otomatis berdasarkan harga barang</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="stockout-form-label">Keterangan *</label>
                        <textarea name="keterangan" class="stockout-form-input" rows="3" 
                                  placeholder="Masukkan keterangan stock out..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer stockout-modal-footer">
                    <button type="button" class="stockout-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="stockout-btn-primary" id="submitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let toastTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    const periodDropdown = document.getElementById('periodDropdown');
    const periodDropdownMenu = document.getElementById('periodDropdownMenu');
    const periodOptions = document.querySelectorAll('.stockout-period-option');
    const periodInput = document.getElementById('periodInput');
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
                periodInput.value = period;
                
                periodOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');

                const periodText = this.textContent.trim();
                periodDropdown.querySelector('span').textContent = periodText;

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

    const barangSelect = document.getElementById('barangSelect');
    const jumlahInput = document.getElementById('jumlahInput');
    const totalHargaInput = document.getElementById('totalHargaInput');
    const stockInfo = document.getElementById('stockInfo');
    const submitBtn = document.getElementById('submitBtn');
    const stockOutForm = document.getElementById('addStockOutForm');

    function updateStockInfo() {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        const stock = selectedOption.getAttribute('data-stock') || 0;
        const harga = selectedOption.getAttribute('data-harga') || 0;
        
        stockInfo.textContent = `Stok tersedia: ${stock}`;
        jumlahInput.max = stock;
        
        if (parseInt(jumlahInput.value) > parseInt(stock)) {
            jumlahInput.value = '';
            totalHargaInput.value = '0';
        }
        
        updateTotalHarga();
    }

    function updateTotalHarga() {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        const harga = parseInt(selectedOption.getAttribute('data-harga')) || 0;
        const jumlah = parseInt(jumlahInput.value) || 0;
        const total = harga * jumlah;
        
        totalHargaInput.value = total.toLocaleString('id-ID');
    }

    function validateForm() {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
        const jumlah = parseInt(jumlahInput.value) || 0;
        
        if (jumlah > stock) {
            submitBtn.disabled = true;
            stockInfo.innerHTML = `<span class="text-danger">Stok tidak mencukupi! Stok tersedia: ${stock}</span>`;
        } else {
            submitBtn.disabled = false;
            stockInfo.textContent = `Stok tersedia: ${stock}`;
        }
    }

    if (barangSelect && jumlahInput) {
        barangSelect.addEventListener('change', updateStockInfo);
        jumlahInput.addEventListener('input', function() {
            updateTotalHarga();
            validateForm();
        });
    }

    if (stockOutForm) {
        stockOutForm.addEventListener('submit', function(e) {
            const jumlah = parseInt(jumlahInput.value) || 0;
            const selectedOption = barangSelect.options[barangSelect.selectedIndex];
            const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
            
            if (jumlah <= 0) {
                e.preventDefault();
                showNotification('error', 'Error', 'Jumlah harus lebih dari 0');
                jumlahInput.focus();
                return;
            }
            
            if (jumlah > stock) {
                e.preventDefault();
                showNotification('error', 'Error', 'Stok tidak mencukupi! Stok tersedia: ' + stock);
                jumlahInput.focus();
                return;
            }
            
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }

    const addStockOutModal = document.getElementById('addStockOutModal');
    if (addStockOutModal) {
        addStockOutModal.addEventListener('hidden.bs.modal', function() {
            stockOutForm.reset();
            document.querySelector('input[name="tanggal_keluar"]').value = '<?= date('Y-m-d\TH:i') ?>';
            updateStockInfo();
            validateForm();
            
            submitBtn.innerHTML = 'Simpan';
            submitBtn.disabled = false;
        });
    }

    updateStockInfo();
    validateForm();

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

<style>
.stockout-price-cell {
    font-weight: 500;
    color: #28a745;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

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

<?php
include __DIR__ . '/../layouts/footer.php';
?>