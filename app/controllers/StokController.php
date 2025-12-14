<?php

namespace App\controllers;

use App\config\View;
use App\models\Stok;
use App\models\Barang;
use App\models\StokKeluar;

class StokController
{
    private $stockInModel;
    private $barangModel;
    private $stockOutModel;

    public function __construct()
    {
        $this->stockInModel = new Stok();
        $this->barangModel = new Barang();
        $this->stockOutModel = new StokKeluar();
    }

    public function stockIn()
    {
        $search = $_GET['search'] ?? '';
        $period = $_GET['period'] ?? 'day';
        $filterDate = $_GET['date'] ?? date('Y-m-d');
        $filterMonth = $_GET['month'] ?? date('m');
        $filterYear = $_GET['year'] ?? date('Y');

        $stockInData = [];

        if (!empty($search)) {
            $stockInData = $this->stockInModel->searchStockIn($search);
        } else {
            switch ($period) {
                case 'day':
                    $stockInData = $this->stockInModel->getStockInByPeriod('day', $filterDate);
                    break;
                case 'month':
                    $stockInData = $this->stockInModel->getStockInByPeriod('month', [
                        'month' => $filterMonth,
                        'year' => $filterYear
                    ]);
                    break;
                case 'year':
                    $stockInData = $this->stockInModel->getStockInByPeriod('year', $filterYear);
                    break;
                default:
                    $stockInData = $this->stockInModel->getAllStockIn();
            }
        }

        $barangList = $this->stockInModel->getBarangList();
        $supplierList = $this->stockInModel->getSupplierList();

        $expiringItems = $this->stockInModel->getExpiringItems(7); 
        $expiredItems = $this->stockInModel->getExpiredItems();
        $expiredSummary = $this->stockInModel->getExpiredSummary();

        View::render("stok/stokin", [
            'stockInData' => $stockInData,
            'barangList' => $barangList,
            'supplierList' => $supplierList,
            'search' => $search,
            'period' => $period,
            'filterDate' => $filterDate,
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear,
            'expiringItems' => $expiringItems,
            'expiredItems' => $expiredItems,
            'expiredSummary' => $expiredSummary
        ]);
    }

    public function addStockIn()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_barang = trim($_POST['id_barang'] ?? '');
            $id_supplier = trim($_POST['id_supplier'] ?? '');
            $jumlah = intval($_POST['jumlah'] ?? 0);
            $harga_beli = intval(str_replace(['.', ','], '', $_POST['harga_beli'] ?? 0));
            $tanggal_masuk = trim($_POST['tanggal_masuk'] ?? '');
            $expired_date = !empty($_POST['expired_date']) ? $_POST['expired_date'] : null;

            if (empty($id_barang)) {
                $_SESSION['error_message'] = 'Pilih barang terlebih dahulu.';
                header('Location: /stokin');
                exit;
            }

            if (empty($id_supplier)) {
                $_SESSION['error_message'] = 'Pilih supplier terlebih dahulu.';
                header('Location: /stokin');
                exit;
            }

            if ($jumlah <= 0) {
                $_SESSION['error_message'] = 'Jumlah harus lebih dari 0.';
                header('Location: /stokin');
                exit;
            }

            if ($harga_beli <= 0) {
                $_SESSION['error_message'] = 'Harga beli harus lebih dari 0.';
                header('Location: /stokin');
                exit;
            }

            if (empty($tanggal_masuk)) {
                $_SESSION['error_message'] = 'Tanggal masuk harus diisi.';
                header('Location: /stokin');
                exit;
            }

            if ($expired_date && strtotime($expired_date) < strtotime($tanggal_masuk)) {
                $_SESSION['error_message'] = 'Tanggal kadaluarsa tidak boleh kurang dari tanggal masuk.';
                header('Location: /stokin');
                exit;
            }

            $tanggal_masuk = date('Y-m-d H:i:s', strtotime($tanggal_masuk));
            if ($expired_date) {
                $expired_date = date('Y-m-d', strtotime($expired_date));
            }

            $user_id = AuthController::getUserId();

            if (!$user_id) {
                $_SESSION['error_message'] = 'User tidak terautentikasi. Silakan login kembali.';
                header('Location: /stokin');
                exit;
            }

            error_log("Adding stock in - User ID: " . $user_id . ", Barang: " . $id_barang);

            $data = [
                'id_barang' => $id_barang,
                'id_supplier' => $id_supplier,
                'jumlah' => $jumlah,
                'harga_beli' => $harga_beli,
                'tanggal_masuk' => $tanggal_masuk,
                'expired_date' => $expired_date,
                'id_user' => $user_id 
            ];

            $result = $this->stockInModel->addStockIn($data);

            if ($result) {
                $updateResult = $this->barangModel->updateStock($id_barang, $jumlah, 'in');

                if ($updateResult) {
                    $_SESSION['success_message'] = 'Stock In berhasil ditambahkan dan stok barang diperbarui.';
                } else {
                    $_SESSION['warning_message'] = 'Stock In berhasil ditambahkan tetapi gagal update stok barang.';
                }
            } else {
                $_SESSION['error_message'] = 'Gagal menambahkan Stock In. Silakan coba lagi.';
            }

            header('Location: /stokin');
            exit;
        }
    }

    public function markExpiredCleaned()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $debug_messages = [];

            $debug_messages[] = "=== DEBUG markExpiredCleaned Controller ===";
            $debug_messages[] = "POST Data: " . print_r($_POST, true);

            $id_stokin = $_POST['id_stokin'] ?? '';
            $id_barang = $_POST['id_barang'] ?? '';
            $nama_barang = $_POST['nama_barang'] ?? '';
            $jumlah = intval($_POST['jumlah'] ?? 0);
            $expired_date = $_POST['expired_date'] ?? '';
            $tanggal_bersih = $_POST['tanggal_bersih'] ?? date('Y-m-d');
            $alasan = $_POST['alasan'] ?? 'dibuang';
            $keterangan = $_POST['keterangan'] ?? '';

            $debug_messages[] = "Validating data...";

            if (empty($id_stokin)) {
                $debug_messages[] = "ERROR: id_stokin kosong";
                echo json_encode([
                    'success' => false,
                    'message' => 'ID Stock In tidak valid'
                ]);
                return;
            }

            if (empty($id_barang) || $id_barang === 'undefined') {
                $debug_messages[] = "ERROR: id_barang invalid: " . $id_barang;

                $correct_id_barang = $this->stockInModel->getBarangIdFromStokIn($id_stokin);
                if ($correct_id_barang) {
                    $id_barang = $correct_id_barang;
                    $debug_messages[] = "Recovered id_barang from database: " . $id_barang;
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'ID Barang tidak valid',
                        'debug' => $debug_messages
                    ]);
                    return;
                }
            }

            if ($jumlah <= 0) {
                $debug_messages[] = "ERROR: jumlah <= 0: $jumlah";
                echo json_encode([
                    'success' => false,
                    'message' => 'Jumlah harus lebih dari 0'
                ]);
                return;
            }

            if (empty($expired_date)) {
                $debug_messages[] = "ERROR: expired_date kosong";
                echo json_encode([
                    'success' => false,
                    'message' => 'Tanggal expired tidak valid'
                ]);
                return;
            }

            $user_id = AuthController::getUserId();
            if (!$user_id) {
                $debug_messages[] = "ERROR: user_id tidak ditemukan di session";
                echo json_encode([
                    'success' => false,
                    'message' => 'User tidak terautentikasi. Silakan login kembali.'
                ]);
                return;
            }

            $debug_messages[] = "Data validation passed";

            $data = [
                'id_stokin' => $id_stokin,
                'id_barang' => $id_barang,
                'nama_barang' => $nama_barang,
                'jumlah' => $jumlah,
                'expired_date' => $expired_date,
                'tanggal_bersih' => $tanggal_bersih,
                'alasan' => $alasan,
                'keterangan' => $keterangan,
                'id_user' => $user_id 
            ];

            $debug_messages[] = "Calling model with data: " . print_r($data, true);

            $result = $this->stockInModel->markExpiredCleaned($data);

            if ($result['success']) {
                $debug_messages[] = "SUCCESS: markExpiredCleaned berhasil";
                echo json_encode([
                    'success' => true,
                    'message' => 'Barang expired berhasil ditandai sudah dibersihkan'
                ]);
            } else {
                $debug_messages[] = "FAILED: markExpiredCleaned gagal";
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menandai barang expired sudah dibersihkan. ' .
                        ($result['message'] ?? 'Periksa stok atau koneksi database.')
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
        }
    }

    public function expiredCleanedHistory()
    {
        $cleanedHistory = $this->stockInModel->getExpiredCleanedHistory();

        View::render("stok/expired_cleaned_history", [
            'cleanedHistory' => $cleanedHistory
        ]);
    }

    public function expiredReport()
    {
        $expiredDetails = $this->stockInModel->getExpiredItemsDetailed();
        $expiredSummary = $this->stockInModel->getExpiredSummary();

        View::render("stok/expired_report", [
            'expiredDetails' => $expiredDetails,
            'expiredSummary' => $expiredSummary
        ]);
    }

    public function stockOut()
    {
        $search = $_GET['search'] ?? '';
        $period = $_GET['period'] ?? 'day';
        $filterDate = $_GET['date'] ?? date('Y-m-d');
        $filterMonth = $_GET['month'] ?? date('m');
        $filterYear = $_GET['year'] ?? date('Y');

        $stockOutData = [];

        if (!empty($search)) {
            $stockOutData = $this->stockOutModel->searchStockOut($search);
        } else {
            switch ($period) {
                case 'day':
                    $stockOutData = $this->stockOutModel->getStockOutByPeriod('day', $filterDate);
                    break;
                case 'month':
                    $stockOutData = $this->stockOutModel->getStockOutByPeriod('month', [
                        'month' => $filterMonth,
                        'year' => $filterYear
                    ]);
                    break;
                case 'year':
                    $stockOutData = $this->stockOutModel->getStockOutByPeriod('year', $filterYear);
                    break;
                default:
                    $stockOutData = $this->stockOutModel->getAllStockOut();
            }
        }

        $barangList = $this->barangModel->getActiveBarang();

        View::render("stok/stokout", [
            'stockOutData' => $stockOutData,
            'barangList' => $barangList,
            'search' => $search,
            'period' => $period,
            'filterDate' => $filterDate,
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear
        ]);
    }

    public function addStockOut()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_barang = trim($_POST['id_barang'] ?? '');
            $jumlah = intval($_POST['jumlah'] ?? 0);
            $tipe_keluar = trim($_POST['tipe_keluar'] ?? '');
            $keterangan = trim($_POST['keterangan'] ?? '');
            $total_harga = intval(str_replace(['.', ','], '', $_POST['total_harga'] ?? 0));
            $tanggal_keluar = trim($_POST['tanggal_keluar'] ?? '');

            if (empty($id_barang)) {
                $_SESSION['error_message'] = 'Pilih barang terlebih dahulu.';
                header('Location: /stokout');
                exit;
            }

            if ($jumlah <= 0) {
                $_SESSION['error_message'] = 'Jumlah harus lebih dari 0.';
                header('Location: /stokout');
                exit;
            }

            if (empty($tipe_keluar)) {
                $_SESSION['error_message'] = 'Pilih tipe keluar terlebih dahulu.';
                header('Location: /stokout');
                exit;
            }

            if (empty($keterangan)) {
                $_SESSION['error_message'] = 'Keterangan harus diisi.';
                header('Location: /stokout');
                exit;
            }

            if (empty($tanggal_keluar)) {
                $_SESSION['error_message'] = 'Tanggal keluar harus diisi.';
                header('Location: /stokout');
                exit;
            }

            $currentStock = $this->barangModel->getStock($id_barang);
            if ($currentStock < $jumlah) {
                $_SESSION['error_message'] = 'Stok tidak mencukupi. Stok tersedia: ' . $currentStock;
                header('Location: /stokout');
                exit;
            }

            $user_id = AuthController::getUserId();
            if (!$user_id) {
                $_SESSION['error_message'] = 'User tidak terautentikasi. Silakan login kembali.';
                header('Location: /stokout');
                exit;
            }

            $data = [
                'id_barang' => $id_barang,
                'jumlah' => $jumlah,
                'tipe_keluar' => $tipe_keluar,
                'keterangan' => $keterangan,
                'total_harga' => $total_harga,
                'tanggal_keluar' => $tanggal_keluar,
                'id_user' => $user_id 
            ];

            $result = $this->stockOutModel->addStockOut($data);

            if ($result) {
                $updateResult = $this->barangModel->updateStock($id_barang, $jumlah, 'out');

                if ($updateResult) {
                    $_SESSION['success_message'] = 'Stock Out berhasil ditambahkan dan stok barang diperbarui.';
                } else {
                    $_SESSION['warning_message'] = 'Stock Out berhasil ditambahkan tetapi gagal update stok barang.';
                }
            } else {
                $_SESSION['error_message'] = 'Gagal menambahkan Stock Out. Silakan coba lagi.';
            }

            header('Location: /stokout');
            exit;
        }
    }


    public function history()
    {
        $search = $_GET['search'] ?? '';
        $period = $_GET['period'] ?? 'day';
        $filterDate = $_GET['date'] ?? date('Y-m-d');
        $filterMonth = $_GET['month'] ?? date('m');
        $filterYear = $_GET['year'] ?? date('Y');

        $historyData = [];

        if (!empty($search)) {
            $historyData = $this->stockInModel->searchStockHistory($search);
        } else {
            switch ($period) {
                case 'day':
                    $historyData = $this->stockInModel->getStockHistoryByPeriod('day', $filterDate);
                    break;
                case 'month':
                    $historyData = $this->stockInModel->getStockHistoryByPeriod('month', [
                        'month' => $filterMonth,
                        'year' => $filterYear
                    ]);
                    break;
                case 'year':
                    $historyData = $this->stockInModel->getStockHistoryByPeriod('year', $filterYear);
                    break;
                default:
                    $historyData = $this->stockInModel->getAllStockHistory();
            }
        }

        $totalData = count($historyData);
        $currentPage = $_GET['page'] ?? 1;
        $perPage = 10;
        $totalPages = ceil($totalData / $perPage);

        $startIndex = ($currentPage - 1) * $perPage;
        $paginatedData = array_slice($historyData, $startIndex, $perPage);

        View::render("stok/history", [
            'historyData' => $paginatedData,
            'search' => $search,
            'period' => $period,
            'filterDate' => $filterDate,
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalData' => $totalData,
            'startIndex' => $startIndex + 1,
            'endIndex' => min($startIndex + $perPage, $totalData)
        ]);
    }

    public function exportHistory()
    {
        try {
            $search = $_GET['search'] ?? '';
            $period = $_GET['period'] ?? 'day';
            $filterDate = $_GET['date'] ?? date('Y-m-d');
            $filterMonth = $_GET['month'] ?? date('m');
            $filterYear = $_GET['year'] ?? date('Y');

            if (!empty($search)) {
                $exportData = $this->stockInModel->searchStockHistory($search);
            } else {
                switch ($period) {
                    case 'day':
                        $exportData = $this->stockInModel->getStockHistoryByPeriod('day', $filterDate);
                        break;
                    case 'month':
                        $exportData = $this->stockInModel->getStockHistoryByPeriod('month', [
                            'month' => $filterMonth,
                            'year' => $filterYear
                        ]);
                        break;
                    case 'year':
                        $exportData = $this->stockInModel->getStockHistoryByPeriod('year', $filterYear);
                        break;
                    default:
                        $exportData = $this->stockInModel->getAllStockHistory();
                }
            }

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="history_transaksi_' . date('Y-m-d_H-i-s') . '.xls"');
            header('Cache-Control: max-age=0');

            $this->generateExcelContent($exportData, $period, $filterDate, $filterMonth, $filterYear, $search);
            exit;
        } catch (\Exception $e) {
            error_log("Error exportHistory: " . $e->getMessage());
            $_SESSION['error_message'] = 'Gagal mengekspor data: ' . $e->getMessage();
            header('Location: /history');
            exit;
        }
    }

    private function generateExcelContent($data, $period, $filterDate, $filterMonth, $filterYear, $search)
    {
        echo '<html>';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        echo 'th { background-color: #f2f2f2; font-weight: bold; }';
        echo '.positive { color: #28a745; }';
        echo '.negative { color: #dc3545; }';
        echo '.summary { background-color: #f8f9fa; font-weight: bold; }';
        echo 'h2 { color: #333; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';

        echo '<h2>LAPORAN HISTORY TRANSAKSI STOK</h2>';

        echo '<div style="margin-bottom: 20px;">';
        echo '<p><strong>Periode:</strong> ';
        switch ($period) {
            case 'day':
                echo 'Harian - ' . date('d F Y', strtotime($filterDate));
                break;
            case 'month':
                $monthNames = [
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
                echo 'Bulanan - ' . $monthNames[$filterMonth] . ' ' . $filterYear;
                break;
            case 'year':
                echo 'Tahunan - ' . $filterYear;
                break;
            default:
                echo 'Semua Periode';
        }
        echo '</p>';

        if (!empty($search)) {
            echo '<p><strong>Pencarian:</strong> ' . htmlspecialchars($search) . '</p>';
        }

        echo '<p><strong>Tanggal Export:</strong> ' . date('d F Y H:i:s') . '</p>';
        echo '<p><strong>Total Data:</strong> ' . count($data) . ' transaksi</p>';
        echo '</div>';

        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>Tanggal</th>';
        echo '<th>Tipe</th>';
        echo '<th>Kode Transaksi</th>';
        echo '<th>Nama Barang</th>';
        echo '<th>Jumlah</th>';
        echo '<th>User</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        if (empty($data)) {
            echo '<tr>';
            echo '<td colspan="7" style="text-align: center;">Tidak ada data transaksi</td>';
            echo '</tr>';
        } else {
            $no = 1;
            $totalIn = 0;
            $totalOut = 0;

            foreach ($data as $transaction) {
                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($transaction['tanggal'])) . '</td>';
                echo '<td>' . ($transaction['tipe'] === 'in' ? 'STOCK IN' : 'STOCK OUT') . '</td>';
                echo '<td>' . htmlspecialchars($transaction['kode']) . '</td>';
                echo '<td>' . htmlspecialchars($transaction['nama_barang']) . '</td>';

                if ($transaction['tipe'] === 'in') {
                    echo '<td style="color: #28a745;">+' . number_format($transaction['jumlah']) . ' pcs</td>';
                    $totalIn += $transaction['jumlah'];
                } else {
                    echo '<td style="color: #dc3545;">-' . number_format($transaction['jumlah']) . ' pcs</td>';
                    $totalOut += $transaction['jumlah'];
                }

                echo '<td>' . htmlspecialchars($transaction['user']) . '</td>';
                echo '</tr>';
            }

            echo '<tr class="summary">';
            echo '<td colspan="4" style="text-align: right;">TOTAL STOCK IN:</td>';
            echo '<td style="color: #28a745;">+' . number_format($totalIn) . ' pcs</td>';
            echo '<td colspan="2"></td>';
            echo '</tr>';

            echo '<tr class="summary">';
            echo '<td colspan="4" style="text-align: right;">TOTAL STOCK OUT:</td>';
            echo '<td style="color: #dc3545;">-' . number_format($totalOut) . ' pcs</td>';
            echo '<td colspan="2"></td>';
            echo '</tr>';

            echo '<tr class="summary">';
            echo '<td colspan="4" style="text-align: right;">NET:</td>';
            echo '<td style="font-weight: bold;">' . ($totalIn - $totalOut >= 0 ? '+' : '') . number_format($totalIn - $totalOut) . ' pcs</td>';
            echo '<td colspan="2"></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';

        echo '</body>';
        echo '</html>';
    }
}
