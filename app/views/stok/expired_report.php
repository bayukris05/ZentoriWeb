<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>
<main class="inventory-page-content">
    <div class="inventory-page-header">
        <div class="inventory-header-left">
            <h1 class="inventory-page-title">Laporan Barang Expired</h1>
            <p class="inventory-page-subtitle">Monitor barang yang sudah dan akan kadaluarsa</p>
        </div>
        <div class="inventory-header-actions">
            <button class="inventory-btn-secondary" onclick="window.history.back()">
                <i class="bi bi-arrow-left"></i> Kembali
            </button>
        </div>
    </div>
    <div class="inventory-content-card">
        <div class="inventory-card-header">
            <h4 class="mt-2">Detail Batch Expired</h4>
        </div>

        <div class="inventory-table-wrapper">
            <table class="inventory-data-table">
                <thead>
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kode Batch</th>
                        <th>Tanggal Masuk</th>
                        <th>Expired Date</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Hari</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($expiredDetails)): ?>
                        <?php foreach ($expiredDetails as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['id_barang']) ?></td>
                                <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($item['id_stokin']) ?></td>
                                <td><?= date('d/m/Y', strtotime($item['tanggal_masuk'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($item['expired_date'])) ?></td>
                                <td><?= $item['batch_quantity'] ?> pcs</td>
                                <td>
                                    <span class="inventory-status-badge inventory-status-<?= $item['expired_status'] ?>">
                                        <?= 
                                            $item['expired_status'] === 'expired' ? 'Expired' : 
                                            ($item['expired_status'] === 'expiring_today' ? 'Expiring Today' : 
                                            ($item['expired_status'] === 'expiring_soon' ? 'Expiring Soon' : 'Aman'))
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['expired_status'] === 'expired'): ?>
                                        <span class="text-danger">
                                            +<?= $item['days_expired'] ?> hari
                                        </span>
                                    <?php elseif ($item['expired_status'] === 'expiring_today'): ?>
                                        <span class="text-warning">
                                            Hari ini
                                        </span>
                                    <?php elseif ($item['expired_status'] === 'expiring_soon'): ?>
                                        <span class="text-warning">
                                            <?= -$item['days_expired'] ?> hari lagi
                                        </span>
                                    <?php else: ?>
                                        <span class="text-success">
                                            <?= -$item['days_expired'] ?> hari
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">Tidak ada data barang expired</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<style>
.inventory-status-expired {
    background: #fee2e2;
    color: #dc2626;
}

.inventory-status-expiring-today {
    background: #fef3c7;
    color: #d97706;
}

.inventory-status-expiring-soon {
    background: #fef3c7;
    color: #d97706;
}

.inventory-status-safe {
    background: #d1fae5;
    color: #065f46;
}
</style>

<?php
include __DIR__ . '/../layouts/footer.php';