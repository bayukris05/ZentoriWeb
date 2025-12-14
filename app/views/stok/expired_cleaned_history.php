<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>
<main class="inventory-page-content">
    <div class="inventory-page-header">
        <div class="inventory-header-left">
            <h1 class="inventory-page-title">History Barang Expired Dibersihkan</h1>
            <p class="inventory-page-subtitle">Riwayat barang expired yang sudah ditandai dibersihkan</p>
        </div>
        <div class="inventory-header-actions">
            <a href="/barang" class="inventory-btn-secondary me-2" style="text-decoration: none;">
                <i class="bi bi-arrow-left"></i> Kembali ke Data Barang
            </a>
            <a href="/barang/expired-report" class="inventory-btn-warning me-2">
                <i class="bi bi-exclamation-triangle"></i> Laporan Expired
            </a>
        </div>
    </div>

    <div class="inventory-content-card">
        <div class="inventory-card-header">
            <h4 class="mt-2">Riwayat Pembersihan Expired</h4>
        </div>

        <div class="inventory-table-wrapper">
            <table class="inventory-data-table">
                <thead>
                    <tr>
                        <th>Tanggal Bersih</th>
                        <th>Kode Batch</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal Expired</th>
                        <th>Alasan</th>
                        <th>Keterangan</th>
                        <th>Ditangani Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cleanedHistory)): ?>
                        <?php foreach ($cleanedHistory as $history): ?>
                            <tr>
                                <td class="inventory-date-cell">
                                    <?= date('d/m/Y', strtotime($history['tanggal_bersih'])) ?>
                                </td>
                                <td class="inventory-code-cell">
                                    <?= htmlspecialchars($history['id_stokin']) ?>
                                </td>
                                <td class="inventory-name-cell">
                                    <?= htmlspecialchars($history['nama_barang']) ?>
                                </td>
                                <td class="inventory-stock-cell">
                                    <span class="text-danger">-<?= $history['jumlah'] ?> pcs</span>
                                </td>
                                <td class="inventory-date-cell">
                                    <?= date('d/m/Y', strtotime($history['expired_date'])) ?>
                                </td>
                                <td>
                                    <?php
                                    $alasanText = '';
                                    $badgeClass = '';
                                    switch ($history['alasan']) {
                                        case 'dibuang':
                                            $alasanText = 'Dibuang';
                                            $badgeClass = 'bg-danger';
                                            break;
                                        case 'dijual':
                                            $alasanText = 'Dijual (Diskon)';
                                            $badgeClass = 'bg-warning';
                                            break;
                                        case 'lainnya':
                                            $alasanText = 'Lainnya';
                                            $badgeClass = 'bg-secondary';
                                            break;
                                        default:
                                            $alasanText = $history['alasan'];
                                            $badgeClass = 'bg-secondary';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $alasanText ?></span>
                                </td>
                                <td class="inventory-description-cell">
                                    <?= !empty($history['keterangan']) ? htmlspecialchars($history['keterangan']) : '-' ?>
                                </td>
                                <td class="inventory-user-cell">
                                    <?= htmlspecialchars($history['username'] ?? 'System') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4"></i>
                                    <h5 class="mt-3">Tidak ada data history</h5>
                                    <p>Belum ada barang expired yang ditandai sudah dibersihkan</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<style>
    .inventory-date-cell {
        white-space: nowrap;
    }

    .inventory-description-cell {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
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
</style>

<?php
include __DIR__ . '/../layouts/footer.php';
?>