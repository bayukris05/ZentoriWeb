<?php

namespace App\models;

use App\models\BaseModel;

class Stok extends BaseModel
{
    private $table = "stok_masuk";

    public function getAllStockIn()
    {
        try {
            $query = "SELECT 
                        si.id_stokin,
                        si.tanggal_masuk,
                        si.jumlah,
                        si.harga_beli,
                        si.total_harga,
                        si.expired_date,
                        b.nama_barang,
                        b.id_barang,
                        s.nama_supplier,
                        u.nama as nama_user
                      FROM {$this->table} si
                      LEFT JOIN barang b ON si.id_barang = b.id_barang
                      LEFT JOIN suppliers s ON si.id_supplier = s.id_supplier
                      LEFT JOIN users u ON si.id_user = u.id_users
                      ORDER BY si.tanggal_masuk DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getAllStockIn: " . $e->getMessage());
            return [];
        }
    }

    public function getStockInByPeriod($period, $value)
    {
        try {
            $query = "SELECT 
                        si.id_stokin,
                        si.tanggal_masuk,
                        si.jumlah,
                        si.harga_beli,
                        si.total_harga,
                        si.expired_date,
                        b.nama_barang,
                        b.id_barang,
                        s.nama_supplier,
                        u.nama as nama_user
                      FROM {$this->table} si
                      LEFT JOIN barang b ON si.id_barang = b.id_barang
                      LEFT JOIN suppliers s ON si.id_supplier = s.id_supplier
                      LEFT JOIN users u ON si.id_user = u.id_users
                      WHERE ";

            switch ($period) {
                case 'day':
                    $query .= "DATE(si.tanggal_masuk) = :date";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':date', $value);
                    break;
                case 'month':
                    $query .= "MONTH(si.tanggal_masuk) = :month AND YEAR(si.tanggal_masuk) = :year";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':month', $value['month']);
                    $stmt->bindParam(':year', $value['year']);
                    break;
                case 'year':
                    $query .= "YEAR(si.tanggal_masuk) = :year";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':year', $value);
                    break;
                default:
                    return $this->getAllStockIn();
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getStockInByPeriod: " . $e->getMessage());
            return [];
        }
    }

    public function searchStockIn($searchTerm)
    {
        try {
            $query = "SELECT 
                        si.id_stokin,
                        si.tanggal_masuk,
                        si.jumlah,
                        si.harga_beli,
                        si.total_harga,
                        si.expired_date,
                        b.nama_barang,
                        b.id_barang,
                        s.nama_supplier,
                        u.nama as nama_user
                      FROM {$this->table} si
                      LEFT JOIN barang b ON si.id_barang = b.id_barang
                      LEFT JOIN suppliers s ON si.id_supplier = s.id_supplier
                      LEFT JOIN users u ON si.id_user = u.id_users
                      WHERE b.nama_barang LIKE :search 
                         OR s.nama_supplier LIKE :search
                         OR si.id_stokin LIKE :search
                         OR si.tanggal_masuk LIKE :search
                      ORDER BY si.tanggal_masuk DESC";

            $stmt = $this->db->prepare($query);
            $searchTerm = "%{$searchTerm}%";
            $stmt->bindParam(':search', $searchTerm);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error searchStockIn: " . $e->getMessage());
            return [];
        }
    }

    public function addStockIn($data)
    {
        try {
            $idStokin = $this->generateStockInId();

            $total_harga = $data['jumlah'] * $data['harga_beli'];

            $query = "INSERT INTO {$this->table} 
                      (id_stokin, id_barang, id_supplier, jumlah, harga_beli, total_harga, id_user, tanggal_masuk, expired_date) 
                      VALUES (:id_stokin, :id_barang, :id_supplier, :jumlah, :harga_beli, :total_harga, :id_user, :tanggal_masuk, :expired_date)";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_stokin', $idStokin);
            $stmt->bindParam(':id_barang', $data['id_barang']);
            $stmt->bindParam(':id_supplier', $data['id_supplier']);
            $stmt->bindParam(':jumlah', $data['jumlah'], \PDO::PARAM_INT);
            $stmt->bindParam(':harga_beli', $data['harga_beli'], \PDO::PARAM_INT);
            $stmt->bindParam(':total_harga', $total_harga, \PDO::PARAM_INT);
            $stmt->bindParam(':id_user', $data['id_user']);
            $stmt->bindParam(':tanggal_masuk', $data['tanggal_masuk']);
            $stmt->bindParam(':expired_date', $data['expired_date']);

            $result = $stmt->execute();

            if ($result) {
                error_log("Stock In berhasil ditambahkan: " . $idStokin . " pada " . $data['tanggal_masuk']);
                return $idStokin;
            } else {
                error_log("Gagal menambahkan Stock In");
                return false;
            }
        } catch (\PDOException $e) {
            error_log("Error addStockIn: " . $e->getMessage());
            error_log("Data yang gagal: " . print_r($data, true));
            return false;
        }
    }

    private function generateStockInId()
    {
        try {
            $query = "SELECT id_stokin FROM {$this->table} ORDER BY id_stokin DESC LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $lastId = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($lastId && isset($lastId['id_stokin'])) {
                $lastNumber = (int) substr($lastId['id_stokin'], 3);
                $newNumber = $lastNumber + 1;
                return 'SM' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            }

            return 'SM001';
        } catch (\PDOException $e) {
            error_log("Error generateStockInId: " . $e->getMessage());
            return 'SM001';
        }
    }

    public function getBarangList()
    {
        try {
            $query = "SELECT id_barang, nama_barang, stok FROM barang WHERE status = 'active' ORDER BY nama_barang";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getBarangList: " . $e->getMessage());
            return [];
        }
    }

    public function getSupplierList()
    {
        try {
            $query = "SELECT id_supplier, nama_supplier FROM suppliers WHERE status = 'active' ORDER BY nama_supplier";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getSupplierList: " . $e->getMessage());
            return [];
        }
    }

    public function getExpiringItems($days = 7)
    {
        try {
            $query = "SELECT 
                        si.id_stokin,
                        si.id_barang,
                        b.nama_barang,
                        si.jumlah as sisa_stok,
                        si.expired_date,
                        si.tanggal_masuk,
                        DATEDIFF(si.expired_date, CURDATE()) as days_until_expired,
                        'stok_masuk' as source
                      FROM {$this->table} si
                      LEFT JOIN barang b ON si.id_barang = b.id_barang
                      WHERE si.expired_date IS NOT NULL 
                        AND si.expired_date >= CURDATE()
                        AND DATEDIFF(si.expired_date, CURDATE()) <= :days
                        AND b.status = 'active'
                      ORDER BY si.expired_date ASC";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':days', $days, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getExpiringItems: " . $e->getMessage());
            return [];
        }
    }

    public function getExpiredItems()
    {
        try {
            $query = "SELECT 
                        si.id_stokin,
                        si.id_barang,
                        b.nama_barang,
                        si.jumlah as sisa_stok,
                        si.expired_date,
                        si.tanggal_masuk,
                        DATEDIFF(CURDATE(), si.expired_date) as days_expired,
                        'stok_masuk' as source
                      FROM {$this->table} si
                      LEFT JOIN barang b ON si.id_barang = b.id_barang
                      WHERE si.expired_date IS NOT NULL 
                        AND si.expired_date < CURDATE()
                        AND b.status = 'active'
                      ORDER BY si.expired_date DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getExpiredItems: " . $e->getMessage());
            return [];
        }
    }

    public function getExpiredSummary()
    {
        try {
            $query = "SELECT 
                    COUNT(*) as total_items,
                    SUM(sm.jumlah - COALESCE(SUM(le.jumlah), 0)) as total_quantity,
                    'expired' as status
                  FROM stok_masuk sm
                  LEFT JOIN barang b ON sm.id_barang = b.id_barang
                  LEFT JOIN log_expired le ON sm.id_stokin = le.id_stokin
                  WHERE sm.expired_date IS NOT NULL 
                    AND sm.expired_date < CURDATE()
                    AND b.status = 'active'
                  GROUP BY sm.id_stokin
                  HAVING total_quantity > 0
                  
                  UNION ALL
                  
                  SELECT 
                    COUNT(*) as total_items,
                    SUM(sm.jumlah - COALESCE(SUM(le.jumlah), 0)) as total_quantity,
                    'expiring_soon' as status
                  FROM stok_masuk sm
                  LEFT JOIN barang b ON sm.id_barang = b.id_barang
                  LEFT JOIN log_expired le ON sm.id_stokin = le.id_stokin
                  WHERE sm.expired_date IS NOT NULL 
                    AND sm.expired_date >= CURDATE()
                    AND DATEDIFF(sm.expired_date, CURDATE()) <= 7
                    AND b.status = 'active'
                  GROUP BY sm.id_stokin
                  HAVING total_quantity > 0";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $summary = [
                'expired' => ['items' => 0, 'quantity' => 0],
                'expiring_soon' => ['items' => 0, 'quantity' => 0]
            ];

            foreach ($result as $row) {
                if ($row['status'] === 'expired') {
                    $summary['expired']['items'] += $row['total_items'];
                    $summary['expired']['quantity'] += $row['total_quantity'];
                } elseif ($row['status'] === 'expiring_soon') {
                    $summary['expiring_soon']['items'] += $row['total_items'];
                    $summary['expiring_soon']['quantity'] += $row['total_quantity'];
                }
            }

            return $summary;
        } catch (\PDOException $e) {
            error_log("Error getExpiredSummary: " . $e->getMessage());
            return ['expired' => ['items' => 0, 'quantity' => 0], 'expiring_soon' => ['items' => 0, 'quantity' => 0]];
        }
    }

    public function getExpiredItemsDetailed()
    {
        try {
            $query = "SELECT 
                    b.id_barang,
                    b.nama_barang,
                    b.stok as current_stock,
                    si.id_stokin,
                    si.tanggal_masuk,
                    si.expired_date,
                    si.jumlah as batch_quantity,
                    DATEDIFF(CURDATE(), si.expired_date) as days_expired,
                    CASE 
                        WHEN si.expired_date < CURDATE() THEN 'expired'
                        WHEN DATEDIFF(si.expired_date, CURDATE()) = 0 THEN 'expiring_today'
                        WHEN DATEDIFF(si.expired_date, CURDATE()) <= 7 THEN 'expiring_soon'
                        ELSE 'safe'
                    END as expired_status
                  FROM {$this->table} si
                  LEFT JOIN barang b ON si.id_barang = b.id_barang
                  WHERE si.expired_date IS NOT NULL 
                    AND b.status = 'active'
                  ORDER BY 
                    CASE 
                        WHEN si.expired_date < CURDATE() THEN 1
                        WHEN DATEDIFF(si.expired_date, CURDATE()) = 0 THEN 2
                        WHEN DATEDIFF(si.expired_date, CURDATE()) <= 7 THEN 3
                        ELSE 4
                    END,
                    si.expired_date ASC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getExpiredItemsDetailed: " . $e->getMessage());
            return [];
        }
    }

    public function getExpiredInfoByBarang($id_barang)
    {
        try {
            $query = "SELECT 
                    sm.id_stokin,
                    sm.tanggal_masuk,
                    sm.expired_date,
                    sm.jumlah as jumlah_awal,
                    COALESCE(SUM(le.jumlah), 0) as jumlah_sudah_dibersihkan,
                    (sm.jumlah - COALESCE(SUM(le.jumlah), 0)) as sisa_stok,
                    b.nama_barang,
                    b.id_barang,
                    CASE 
                        WHEN sm.expired_date < CURDATE() THEN 'expired'
                        WHEN DATEDIFF(sm.expired_date, CURDATE()) <= 7 THEN 'expiring_soon'
                        ELSE 'safe'
                    END as status
                  FROM stok_masuk sm
                  LEFT JOIN barang b ON sm.id_barang = b.id_barang
                  LEFT JOIN log_expired le ON sm.id_stokin = le.id_stokin
                  WHERE sm.id_barang = :id_barang 
                    AND sm.expired_date IS NOT NULL
                    AND b.status = 'active'
                  GROUP BY sm.id_stokin, sm.tanggal_masuk, sm.expired_date, sm.jumlah, b.nama_barang, b.id_barang
                  HAVING sisa_stok > 0
                  ORDER BY sm.expired_date ASC";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_barang', $id_barang);
            $stmt->execute();

            $batches = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $result = [
                'expired_batches' => [],
                'expiring_soon_batches' => [],
                'safe_batches' => []
            ];

            foreach ($batches as $batch) {
                if ($batch['sisa_stok'] > 0) {
                    if ($batch['status'] === 'expired') {
                        $result['expired_batches'][] = $batch;
                    } elseif ($batch['status'] === 'expiring_soon') {
                        $result['expiring_soon_batches'][] = $batch;
                    } else {
                        $result['safe_batches'][] = $batch;
                    }
                }
            }

            return $result;
        } catch (\PDOException $e) {
            error_log("Error getExpiredInfoByBarang: " . $e->getMessage());
            return ['expired_batches' => [], 'expiring_soon_batches' => [], 'safe_batches' => []];
        }
    }

    public function markExpiredCleaned($data)
    {
        try {
            $this->db->beginTransaction();

            $debug_messages = [];
            $debug_messages[] = "=== DEBUG markExpiredCleaned Model ===";
            $debug_messages[] = "Data received: " . print_r($data, true);

            if (empty($data['id_barang']) || $data['id_barang'] === 'undefined') {
                $debug_messages[] = "ERROR: id_barang is undefined in model";
                $this->db->rollBack();
                return ['success' => false, 'message' => 'ID Barang tidak valid', 'debug' => $debug_messages];
            }

            $queryCheckBarang = "SELECT id_barang, stok FROM barang WHERE id_barang = :id_barang AND status = 'active'";
            $stmtCheckBarang = $this->db->prepare($queryCheckBarang);
            $stmtCheckBarang->bindParam(':id_barang', $data['id_barang']);
            $stmtCheckBarang->execute();
            $barangResult = $stmtCheckBarang->fetch(\PDO::FETCH_ASSOC);

            if (!$barangResult) {
                $debug_messages[] = "ERROR: Barang tidak ditemukan atau tidak active";
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Barang tidak ditemukan', 'debug' => $debug_messages];
            }

            $queryCheck = "SELECT 
                        sm.id_stokin, 
                        sm.jumlah as jumlah_awal,
                        sm.id_barang,
                        b.nama_barang,
                        b.stok as stok_barang_sekarang
                      FROM stok_masuk sm
                      LEFT JOIN barang b ON sm.id_barang = b.id_barang
                      WHERE sm.id_stokin = :id_stokin";

            $stmtCheck = $this->db->prepare($queryCheck);
            $stmtCheck->bindParam(':id_stokin', $data['id_stokin']);
            $stmtCheck->execute();
            $checkResult = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

            $debug_messages[] = "Check result: " . print_r($checkResult, true);

            if (!$checkResult) {
                $this->db->rollBack();
                $debug_messages[] = "ERROR: Batch tidak ditemukan";
                error_log("Batch tidak ditemukan: " . $data['id_stokin']);
                return ['success' => false, 'message' => 'Batch stok tidak ditemukan', 'debug' => $debug_messages];
            }

            $queryCleaned = "SELECT COALESCE(SUM(jumlah), 0) as total_cleaned 
                            FROM log_expired 
                            WHERE id_stokin = :id_stokin";

            $stmtCleaned = $this->db->prepare($queryCleaned);
            $stmtCleaned->bindParam(':id_stokin', $data['id_stokin']);
            $stmtCleaned->execute();
            $cleanedResult = $stmtCleaned->fetch(\PDO::FETCH_ASSOC);

            $totalCleaned = $cleanedResult['total_cleaned'] ?? 0;
            $sisaStok = $checkResult['jumlah_awal'] - $totalCleaned;
            $jumlahYangAkanDibersihkan = $data['jumlah'];

            $debug_messages[] = "Jumlah awal: " . $checkResult['jumlah_awal'];
            $debug_messages[] = "Total sudah dibersihkan: " . $totalCleaned;
            $debug_messages[] = "Sisa stok batch: " . $sisaStok;
            $debug_messages[] = "Jumlah yang akan dibersihkan: " . $jumlahYangAkanDibersihkan;

            if ($jumlahYangAkanDibersihkan > $sisaStok) {
                $this->db->rollBack();
                $debug_messages[] = "ERROR: Jumlah melebihi sisa stok batch";
                error_log("Jumlah melebihi sisa stok batch: requested $jumlahYangAkanDibersihkan, available $sisaStok");
                return ['success' => false, 'message' => 'Jumlah melebihi sisa stok batch', 'debug' => $debug_messages];
            }

            $currentStock = $checkResult['stok_barang_sekarang'];
            if ($jumlahYangAkanDibersihkan > $currentStock) {
                $this->db->rollBack();
                $debug_messages[] = "ERROR: Jumlah melebihi stok barang saat ini";
                error_log("Jumlah melebihi stok barang: requested $jumlahYangAkanDibersihkan, available $currentStock");
                return ['success' => false, 'message' => 'Jumlah melebihi stok barang saat ini', 'debug' => $debug_messages];
            }

            $queryLog = "INSERT INTO log_expired 
                    (id_stokin, id_barang, nama_barang, jumlah, expired_date, tanggal_bersih, alasan, keterangan, id_user) 
                    VALUES (:id_stokin, :id_barang, :nama_barang, :jumlah, :expired_date, :tanggal_bersih, :alasan, :keterangan, :id_user)";

            $stmtLog = $this->db->prepare($queryLog);
            $stmtLog->bindParam(':id_stokin', $data['id_stokin']);
            $stmtLog->bindParam(':id_barang', $data['id_barang']);
            $stmtLog->bindParam(':nama_barang', $data['nama_barang']);
            $stmtLog->bindParam(':jumlah', $data['jumlah'], \PDO::PARAM_INT);
            $stmtLog->bindParam(':expired_date', $data['expired_date']);
            $stmtLog->bindParam(':tanggal_bersih', $data['tanggal_bersih']);
            $stmtLog->bindParam(':alasan', $data['alasan']);
            $stmtLog->bindParam(':keterangan', $data['keterangan']);
            $stmtLog->bindParam(':id_user', $data['id_user']);

            $resultLog = $stmtLog->execute();

            if (!$resultLog) {
                $errorInfo = $stmtLog->errorInfo();
                $debug_messages[] = "ERROR: Gagal insert ke log_expired: " . $errorInfo[2];
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Gagal menyimpan data pembersihan', 'debug' => $debug_messages];
            }

            $debug_messages[] = "SUCCESS: Data berhasil dimasukkan ke log_expired";

            $queryUpdateStok = "UPDATE barang 
                           SET stok = stok - :jumlah 
                           WHERE id_barang = :id_barang AND stok >= :jumlah";

            $stmtUpdate = $this->db->prepare($queryUpdateStok);
            $stmtUpdate->bindParam(':jumlah', $data['jumlah'], \PDO::PARAM_INT);
            $stmtUpdate->bindParam(':id_barang', $data['id_barang']);
            $resultUpdate = $stmtUpdate->execute();

            if (!$resultUpdate) {
                $errorInfo = $stmtUpdate->errorInfo();
                $debug_messages[] = "ERROR: Gagal update stok barang: " . $errorInfo[2];
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Gagal update stok barang', 'debug' => $debug_messages];
            }

            $affectedRows = $stmtUpdate->rowCount();
            $debug_messages[] = "Rows affected by stock update: " . $affectedRows;

            if ($affectedRows === 0) {
                $debug_messages[] = "ERROR: Stok tidak cukup atau barang tidak ditemukan";
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Stok tidak cukup untuk melakukan pembersihan', 'debug' => $debug_messages];
            }

            $debug_messages[] = "SUCCESS: Stok barang berhasil diupdate";

            $this->db->commit();
            $debug_messages[] = "=== TRANSACTION COMMITTED ===";

            error_log("Berhasil menandai expired cleaned: " . $data['id_stokin'] . " - Jumlah: " . $data['jumlah']);
            return ['success' => true, 'message' => 'Barang expired berhasil ditandai sudah dibersihkan', 'debug' => $debug_messages];
        } catch (\PDOException $e) {
            $this->db->rollBack();
            $debug_messages[] = "EXCEPTION: " . $e->getMessage();

            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                $debug_messages[] = "FOREIGN KEY VIOLATION: id_barang mungkin tidak valid";
                return ['success' => false, 'message' => 'Data barang tidak valid', 'debug' => $debug_messages];
            }

            error_log("Error markExpiredCleaned: " . $e->getMessage());
            error_log("Data: " . print_r($data, true));
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage(), 'debug' => $debug_messages];
        }
    }

    public function getExpiredCleanedHistory()
    {
        try {
            $query = "SELECT 
                    ec.*,
                    u.nama as username,
                    b.nama_barang
                  FROM log_expired ec
                  LEFT JOIN users u ON ec.id_user = u.id_users
                  LEFT JOIN barang b ON ec.id_barang = b.id_barang
                  ORDER BY ec.tanggal_bersih DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error getting expired cleaned history: " . $e->getMessage());
            return [];
        }
    }

    public function getBarangIdFromStokIn($id_stokin)
    {
        try {
            $query = "SELECT id_barang FROM stok_masuk WHERE id_stokin = :id_stokin";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_stokin', $id_stokin);
            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['id_barang'] ?? null;
        } catch (\PDOException $e) {
            error_log("Error getBarangIdFromStokIn: " . $e->getMessage());
            return null;
        }
    }

    public function getAllStockHistory()
    {
        try {
            $query = "SELECT 
                    'in' as tipe,
                    sm.id_stokin as kode,
                    sm.tanggal_masuk as tanggal,
                    b.nama_barang,
                    sm.jumlah,
                    u.nama as user,
                    'Stock In' as tipe_label
                  FROM stok_masuk sm
                  LEFT JOIN barang b ON sm.id_barang = b.id_barang
                  LEFT JOIN users u ON sm.id_user = u.id_users
                  
                  UNION ALL
                  
                  SELECT 
                    'out' as tipe,
                    sk.id_stokout as kode,
                    sk.tanggal_keluar as tanggal,
                    b.nama_barang,
                    sk.jumlah,
                    u.nama as user,
                    'Stock Out' as tipe_label
                  FROM stok_keluar sk
                  LEFT JOIN barang b ON sk.id_barang = b.id_barang
                  LEFT JOIN users u ON sk.id_user = u.id_users
                  
                  ORDER BY tanggal DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getAllStockHistory: " . $e->getMessage());
            return [];
        }
    }

    public function getStockHistoryByPeriod($period, $value)
    {
        try {
            $query = "SELECT 
                    'in' as tipe,
                    sm.id_stokin as kode,
                    sm.tanggal_masuk as tanggal,
                    b.nama_barang,
                    sm.jumlah,
                    u.nama as user,
                    'Stock In' as tipe_label
                  FROM stok_masuk sm
                  LEFT JOIN barang b ON sm.id_barang = b.id_barang
                  LEFT JOIN users u ON sm.id_user = u.id_users
                  WHERE ";

            switch ($period) {
                case 'day':
                    $query .= "DATE(sm.tanggal_masuk) = :date";
                    break;
                case 'month':
                    $query .= "MONTH(sm.tanggal_masuk) = :month AND YEAR(sm.tanggal_masuk) = :year";
                    break;
                case 'year':
                    $query .= "YEAR(sm.tanggal_masuk) = :year";
                    break;
                default:
                    return $this->getAllStockHistory();
            }

            $query .= " UNION ALL
                  SELECT 
                    'out' as tipe,
                    sk.id_stokout as kode,
                    sk.tanggal_keluar as tanggal,
                    b.nama_barang,
                    sk.jumlah,
                    u.nama as user,
                    'Stock Out' as tipe_label
                  FROM stok_keluar sk
                  LEFT JOIN barang b ON sk.id_barang = b.id_barang
                  LEFT JOIN users u ON sk.id_user = u.id_users
                  WHERE ";

            switch ($period) {
                case 'day':
                    $query .= "DATE(sk.tanggal_keluar) = :date";
                    break;
                case 'month':
                    $query .= "MONTH(sk.tanggal_keluar) = :month AND YEAR(sk.tanggal_keluar) = :year";
                    break;
                case 'year':
                    $query .= "YEAR(sk.tanggal_keluar) = :year";
                    break;
            }

            $query .= " ORDER BY tanggal DESC";

            $stmt = $this->db->prepare($query);

            switch ($period) {
                case 'day':
                    $stmt->bindParam(':date', $value);
                    break;
                case 'month':
                    $stmt->bindParam(':month', $value['month']);
                    $stmt->bindParam(':year', $value['year']);
                    break;
                case 'year':
                    $stmt->bindParam(':year', $value);
                    break;
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getStockHistoryByPeriod: " . $e->getMessage());
            return [];
        }
    }

    public function searchStockHistory($searchTerm)
    {
        try {
            $query = "SELECT 
                    'in' as tipe,
                    sm.id_stokin as kode,
                    sm.tanggal_masuk as tanggal,
                    b.nama_barang,
                    sm.jumlah,
                    u.nama as user,
                    'Stock In' as tipe_label
                  FROM stok_masuk sm
                  LEFT JOIN barang b ON sm.id_barang = b.id_barang
                  LEFT JOIN users u ON sm.id_user = u.id_users
                  WHERE b.nama_barang LIKE :search 
                     OR sm.id_stokin LIKE :search
                     OR u.nama LIKE :search
                     
                  UNION ALL
                  
                  SELECT 
                    'out' as tipe,
                    sk.id_stokout as kode,
                    sk.tanggal_keluar as tanggal,
                    b.nama_barang,
                    sk.jumlah,
                    u.nama as user,
                    'Stock Out' as tipe_label
                  FROM stok_keluar sk
                  LEFT JOIN barang b ON sk.id_barang = b.id_barang
                  LEFT JOIN users u ON sk.id_user = u.id_users
                  WHERE b.nama_barang LIKE :search 
                     OR sk.id_stokout LIKE :search
                     OR u.nama LIKE :search
                     
                  ORDER BY tanggal DESC";

            $stmt = $this->db->prepare($query);
            $searchTerm = "%{$searchTerm}%";
            $stmt->bindParam(':search', $searchTerm);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error searchStockHistory: " . $e->getMessage());
            return [];
        }
    }
}
