<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>
<main class="history-page-content">
    <div class="history-page-header">
        <div class="history-header-left">
            <h1 class="history-page-title">History</h1>
            <p class="history-page-subtitle">Riwayat semua transaksi stok</p>
        </div>
        <button class="history-btn-add" onclick="exportHistory()">
            <i class="bi bi-download"></i> Export
        </button>
    </div>

    <div class="history-content-card">
        <div class="history-card-header">
            <h4 class="mt-2">Semua Transaksi</h4>
            <div class="history-filter-container">
                <form method="GET" action="/history" class="history-search-box" id="searchForm">
                    <i class="bi bi-search"></i>
                    <input type="text" class="history-search-input" placeholder="Cari riwayat..."
                        id="searchHistory" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="period" value="<?= $period ?>">
                    <input type="hidden" name="date" value="<?= $filterDate ?>">
                    <input type="hidden" name="month" value="<?= $filterMonth ?>">
                    <input type="hidden" name="year" value="<?= $filterYear ?>">
                </form>

                <form method="GET" action="/history" class="history-date-filter" id="filterForm">
                    <div class="history-period-selector">
                        <button type="button" class="history-period-btn" id="periodDropdown">
                            <i class="bi bi-calendar-week"></i>
                            <span>
                                <?= $period === 'day' ? 'Per Hari' : ($period === 'month' ? 'Per Bulan' : 'Per Tahun') ?>
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="history-period-dropdown" id="periodDropdownMenu">
                            <button type="button" class="history-period-option <?= $period === 'day' ? 'active' : '' ?>" data-period="day">
                                <i class="bi bi-calendar-day"></i>
                                Per Hari
                            </button>
                            <button type="button" class="history-period-option <?= $period === 'month' ? 'active' : '' ?>" data-period="month">
                                <i class="bi bi-calendar-month"></i>
                                Per Bulan
                            </button>
                            <button type="button" class="history-period-option <?= $period === 'year' ? 'active' : '' ?>" data-period="year">
                                <i class="bi bi-calendar"></i>
                                Per Tahun
                            </button>
                        </div>
                        <input type="hidden" name="period" id="periodInput" value="<?= $period ?>">
                    </div>

                    <div class="history-date-inputs">
                        <div class="history-date-input-group <?= $period !== 'day' ? 'hidden' : '' ?>" id="dayInputs">
                            <input type="date" class="history-date-input" name="date"
                                id="filterDateDay" value="<?= $filterDate ?>">
                        </div>

                        <div class="history-date-input-group <?= $period !== 'month' ? 'hidden' : '' ?>" id="monthInputs">
                            <select class="history-month-select" name="month" id="filterMonth">
                                <option value="">Pilih Bulan</option>
                                <?php
                                $months = [
                                    '01' => 'Januari',
                                    '02' => 'Februari',
                                    '03' => 'Maret',
                                    '04' => 'April',
                                    '05' => 'Mei',
                                    '06' => 'Juni',
                                    '07' => 'Juli',
                                    '08' => 'Agustus',
                                    '09' => 'September',
                                    '10' => 'Oktober',
                                    '11' => 'November',
                                    '12' => 'Desember'
                                ];
                                foreach ($months as $value => $name): ?>
                                    <option value="<?= $value ?>" <?= $filterMonth == $value ? 'selected' : '' ?>>
                                        <?= $name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select class="history-year-select" name="year" id="filterYearMonth">
                                <option value="">Pilih Tahun</option>
                                <?php for ($year = date('Y'); $year >= 2020; $year--): ?>
                                    <option value="<?= $year ?>" <?= $filterYear == $year ? 'selected' : '' ?>>
                                        <?= $year ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="history-date-input-group <?= $period !== 'year' ? 'hidden' : '' ?>" id="yearInputs">
                            <select class="history-year-select" name="year" id="filterYear">
                                <option value="">Pilih Tahun</option>
                                <?php for ($year = date('Y'); $year >= 2020; $year--): ?>
                                    <option value="<?= $year ?>" <?= $filterYear == $year ? 'selected' : '' ?>>
                                        <?= $year ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="search" id="searchInput" value="<?= htmlspecialchars($search) ?>">

                    <button type="submit" class="history-filter-btn">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </form>
            </div>
        </div>

        <div class="history-table-wrapper">
            <table class="history-data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historyData)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="history-empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>Tidak ada data transaksi</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historyData as $transaction): ?>
                            <tr>
                                <td class="history-date-cell">
                                    <?= date('Y-m-d', strtotime($transaction['tanggal'])) ?>
                                </td>
                                <td class="history-type-cell">
                                    <?php if ($transaction['tipe'] === 'in'): ?>
                                        <span class="history-type-in">
                                            <i class="bi bi-arrow-down-left"></i> Stock In
                                        </span>
                                    <?php else: ?>
                                        <span class="history-type-out">
                                            <i class="bi bi-arrow-up-right"></i> Stock Out
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="history-code-cell">
                                    <strong><?= htmlspecialchars($transaction['kode']) ?></strong>
                                </td>
                                <td class="history-item-cell">
                                    <?= htmlspecialchars($transaction['nama_barang']) ?>
                                </td>
                                <td class="history-quantity-cell">
                                    <?php if ($transaction['tipe'] === 'in'): ?>
                                        <span class="history-quantity-positive">
                                            +<?= number_format($transaction['jumlah']) ?> pcs
                                        </span>
                                    <?php else: ?>
                                        <span class="history-quantity-negative">
                                            -<?= number_format($transaction['jumlah']) ?> pcs
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="history-user-cell">
                                    <?= htmlspecialchars($transaction['user']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="history-pagination-container">
                <div class="history-pagination-info">
                    Menampilkan <?= $startIndex ?>-<?= $endIndex ?> dari <?= $totalData ?> data
                </div>
                <div class="history-pagination-controls">
                    <a href="?<?=
                                http_build_query(array_merge($_GET, ['page' => max(1, $currentPage - 1)]))
                                ?>"
                        class="history-pagination-btn history-pagination-prev <?= $currentPage == 1 ? 'disabled' : '' ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" style="text-decoration: none;"
                                class="history-pagination-page <?= $i == $currentPage ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php elseif ($i == $currentPage - 3 || $i == $currentPage + 3): ?>
                            <span class="history-pagination-ellipsis">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <a href="?<?=
                                http_build_query(array_merge($_GET, ['page' => min($totalPages, $currentPage + 1)]))
                                ?>"
                        class="history-pagination-btn history-pagination-next <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<form id="exportForm" action="/history/export" method="GET" style="display: none;">
    <input type="hidden" name="search" id="exportSearch" value="<?= htmlspecialchars($search) ?>">
    <input type="hidden" name="period" id="exportPeriod" value="<?= $period ?>">
    <input type="hidden" name="date" id="exportDate" value="<?= $filterDate ?>">
    <input type="hidden" name="month" id="exportMonth" value="<?= $filterMonth ?>">
    <input type="hidden" name="year" id="exportYear" value="<?= $filterYear ?>">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodDropdown = document.getElementById('periodDropdown');
        const periodDropdownMenu = document.getElementById('periodDropdownMenu');
        const periodOptions = document.querySelectorAll('.history-period-option');
        const periodInput = document.getElementById('periodInput');
        const dayInputs = document.getElementById('dayInputs');
        const monthInputs = document.getElementById('monthInputs');
        const yearInputs = document.getElementById('yearInputs');
        const searchInput = document.getElementById('searchHistory');
        const searchForm = document.getElementById('searchForm');
        const filterForm = document.getElementById('filterForm');

        let searchTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                document.getElementById('searchInput').value = this.value;

                searchForm.submit();
            }, 800); 
        });

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
                periodInput.value = period;

                const searchFormPeriod = searchForm.querySelector('input[name="period"]');
                if (searchFormPeriod) {
                    searchFormPeriod.value = period;
                }

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

                filterForm.submit();
            });
        });

        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.getElementById('filterDateDay');
        if (dateInput && !dateInput.value) {
            dateInput.value = today;
        }

        const autoSubmitElements = document.querySelectorAll('#filterMonth, #filterYearMonth, #filterYear, #filterDateDay');
        autoSubmitElements.forEach(element => {
            element.addEventListener('change', function() {
                const name = this.getAttribute('name');
                const value = this.value;
                const searchFormInput = searchForm.querySelector(`input[name="${name}"]`);
                if (searchFormInput) {
                    searchFormInput.value = value;
                }

                filterForm.submit();
            });
        });

        const paginationLinks = document.querySelectorAll('.history-pagination-page, .history-pagination-prev, .history-pagination-next');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.classList.contains('disabled')) {
                    e.preventDefault();
                    return;
                }
            });
        });
    });

    function exportHistory() {
        document.getElementById('exportSearch').value = document.getElementById('searchHistory').value;
        document.getElementById('exportPeriod').value = document.getElementById('periodInput').value;
        document.getElementById('exportDate').value = document.getElementById('filterDateDay').value;
        document.getElementById('exportMonth').value = document.getElementById('filterMonth').value;

        const period = document.getElementById('periodInput').value;
        if (period === 'month') {
            document.getElementById('exportYear').value = document.getElementById('filterYearMonth').value;
        } else if (period === 'year') {
            document.getElementById('exportYear').value = document.getElementById('filterYear').value;
        } else {
            document.getElementById('exportYear').value = new Date().getFullYear().toString();
        }

        document.getElementById('exportForm').submit();
    }

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            exportHistory();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            document.getElementById('searchHistory').focus();
        }
    });
</script>

<style>
    .history-empty-state {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }

    .history-empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .hidden {
        display: none !important;
    }

    .history-btn-add {
        background: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: background-color 0.3s;
    }

    .history-btn-add:hover {
        background: #218838;
    }
</style>

<?php
include __DIR__ . '/../layouts/footer.php';
?>