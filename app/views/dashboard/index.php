<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';

function timeAgo($datetime) {
    if (empty($datetime)) return 'Tidak diketahui';
    
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) {
        return 'Baru saja';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' menit lalu';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' jam lalu';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' hari lalu';
    } else {
        return date('d M Y', $time);
    }
}
?>

<main class="dashboard-content">
    <div class="page-title mb-4">
        <h1>Dashboard</h1>
        <p>Overview sistem inventori dan keuangan Anda</p>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-4">
                    <div class="title-card d-flex justify-content-between align-items-center mb-1">
                        <h6 class="text-muted mb-2">Total Barang</h6>
                        <i class="bi bi-box"></i>
                    </div>
                    <h2 class="mb-0" style="font-size: 24px;"><?= isset($stats['total_barang']) ? $stats['total_barang'] : 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-4">
                    <div class="title-card d-flex justify-content-between align-items-center mb-1">
                        <h6 class="text-muted mb-2">Total Supplier</h6>
                        <i class="bi bi-people"></i>
                    </div>
                    <h2 class="mb-0 text-success" style="font-size: 24px;"><?= isset($stats['total_supplier']) ? $stats['total_supplier'] : 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-4">
                    <div class="title-card d-flex justify-content-between align-items-center mb-1">
                        <h6 class="text-muted mb-2">Total Pemasukan</h6>
                        <i class="bi bi-arrow-down-left text-success"></i>
                    </div>
                    <h2 class="mb-0 text-success" style="font-size: 24px;">Rp <?= number_format($stats['total_pemasukan'] ?? 0, 0, ',', '.') ?></h2>
                    <small class="text-muted">Bulan ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-4">
                    <div class="title-card d-flex justify-content-between align-items-center mb-1">
                        <h6 class="text-muted mb-2">Total Pengeluaran</h6>
                        <i class="bi bi-arrow-up-right text-danger"></i>
                    </div>
                    <h2 class="mb-0 text-danger" style="font-size: 24px;">Rp <?= number_format($stats['total_pengeluaran'] ?? 0, 0, ',', '.') ?></h2>
                    <small class="text-muted">Bulan ini</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="title-card d-flex justify-content-between align-items-center">
                        <h5>Stok Menipis</h5>
                        <span class="badge bg-warning"><?= count($lowStock ?? []) ?> items</span>
                    </div>
                    
                    <?php if (!empty($lowStock)): ?>
                        <?php foreach ($lowStock as $item): ?>
                            <div class="card-content d-flex mt-4 justify-content-between align-items-center border-bottom pb-2">
                                <div class="left-part">
                                    <p class="nama-barang mb-0 fw-semibold"><?= htmlspecialchars($item->nama_barang ?? 'Unknown') ?></p>
                                    <p class="detail-stok mb-0 text-muted">Min. Stock: <?= $item->min_stok ?? 10 ?></p>
                                    <small class="text-muted">Kode: <?= $item->id_barang ?? 'N/A' ?></small>
                                </div>
                                <div class="right-part">
                                    <div class="badge bg-danger text-white px-3 py-2 rounded">
                                        <p class="mb-0 fw-semibold"><?= $item->stok ?? 0 ?> unit</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <p class="text-muted mt-2">Semua stok dalam kondisi aman</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="title-card d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Transaksi Terbaru</h5>
                        <span class="badge bg-primary"><?= count($recentTransactions ?? []) ?> transaksi</span>
                    </div>
                    
                    <?php if (!empty($recentTransactions)): ?>
                        <div class="transaction-list">
                            <?php foreach ($recentTransactions as $index => $transaction): ?>
                                <div class="card-content d-flex justify-content-between align-items-start border-bottom pb-3 <?= $index > 0 ? 'mt-3' : '' ?>">
                                    <div class="left-part flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <?php if ($transaction->jenis_transaksi == 'pemasukan'): ?>
                                                <i class="bi bi-arrow-down-circle-fill text-success me-2"></i>
                                            <?php else: ?>
                                                <i class="bi bi-arrow-up-circle-fill text-danger me-2"></i>
                                            <?php endif; ?>
                                            <p class="nama-barang mb-0 fw-semibold">
                                                <?= htmlspecialchars($transaction->nama_barang) ?>
                                            </p>
                                        </div>
                                        
                                        <p class="detail-stok mb-1 text-muted small">
                                            <?= $transaction->jumlah ?> unit
                                        </p>
                                        
                                        <div class="transaction-details">
                                            <?php if ($transaction->jenis_transaksi == 'pemasukan' && !empty($transaction->supplier)): ?>
                                                <small class="text-muted">
                                                    <i class="bi bi-truck"></i> <?= htmlspecialchars($transaction->supplier) ?>
                                                </small>
                                            <?php elseif ($transaction->jenis_transaksi == 'pengeluaran' && !empty($transaction->tipe_keluar)): ?>
                                                <small class="text-muted">
                                                    <i class="bi bi-tag"></i> 
                                                    <?php
                                                        $tipe = $transaction->tipe_keluar;
                                                        echo $tipe == 'penjualan' ? 'Penjualan' : 
                                                             ($tipe == 'pemakaian' ? 'Pemakaian' : 
                                                             ($tipe == 'retur' ? 'Retur' : ucfirst($tipe)));
                                                    ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="right-part text-end ms-3">
                                        <p class="mb-1 fw-bold <?= $transaction->jenis_transaksi == 'pemasukan' ? 'text-success' : 'text-danger' ?>">
                                            <?= $transaction->jenis_transaksi == 'pemasukan' ? '+' : '-' ?> 
                                            Rp <?= number_format($transaction->total_harga, 0, ',', '.') ?>
                                        </p>
                                        <small class="text-muted d-block">
                                            <?= timeAgo($transaction->tanggal_transaksi) ?>
                                        </small>
                                        <span class="badge <?= $transaction->jenis_transaksi == 'pemasukan' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> mt-1">
                                            <?= $transaction->jenis_transaksi == 'pemasukan' ? 'Masuk' : 'Keluar' ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-receipt text-muted fs-1"></i>
                            <p class="text-muted mt-3 mb-0">Belum ada transaksi</p>
                            <small class="text-muted">Transaksi akan muncul di sini</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include __DIR__ . '/../layouts/footer.php';
?>