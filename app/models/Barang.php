<?php

namespace App\models;

use App\models\BaseModel;

class Barang extends BaseModel
{
    private $table = "barang";

    public function getAllBarang()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, k.nama_kategori 
                FROM {$this->table} b 
                LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                WHERE b.status = 'active'
                ORDER BY b.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getAllBarang: " . $e->getMessage());
            return [];
        }
    }

    public function getBarangById($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, k.nama_kategori 
                FROM {$this->table} b 
                LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                WHERE b.id_barang = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getBarangById: " . $e->getMessage());
            return null;
        }
    }

    public function createBarang($data)
    {
        try {
            $lastId = $this->getLastBarangId();
            $newId = $this->generateBarangId($lastId);

            $sql = "INSERT INTO {$this->table} 
                    (id_barang, nama_barang, id_kategori, satuan, stok, harga_beli, harga_jual, expired_date, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $newId,
                $data['nama_barang'],
                $data['id_kategori'],
                $data['satuan'],
                $data['stok'],
                $data['harga_beli'],
                $data['harga_jual'],
                $data['expired_date'],
                'active',
                $data['created_at']
            ]);
        } catch (\PDOException $e) {
            error_log("Error createBarang: " . $e->getMessage());
            return false;
        }
    }

    public function updateBarang($id, $data)
    {
        try {
            $sql = "UPDATE {$this->table} 
                    SET nama_barang = ?, id_kategori = ?, satuan = ?, stok = ?, harga_beli = ?, harga_jual = ?, expired_date = ? 
                    WHERE id_barang = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['nama_barang'],
                $data['id_kategori'],
                $data['satuan'],
                $data['stok'],
                $data['harga_beli'],
                $data['harga_jual'],
                $data['expired_date'],
                $id
            ]);
        } catch (\PDOException $e) {
            error_log("Error updateBarang: " . $e->getMessage());
            return false;
        }
    }

    public function deleteBarang($id)
    {
        try {
            $sql = "UPDATE {$this->table} SET status = 'inactive' WHERE id_barang = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error deleteBarang: " . $e->getMessage());
            return false;
        }
    }

    public function activateBarang($id)
    {
        try {
            $sql = "UPDATE {$this->table} SET status = 'active' WHERE id_barang = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error activateBarang: " . $e->getMessage());
            return false;
        }
    }

    private function getLastBarangId()
    {
        try {
            $stmt = $this->db->prepare("SELECT id_barang FROM {$this->table} ORDER BY id_barang DESC LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result['id_barang'] : null;
        } catch (\PDOException $e) {
            error_log("Error getLastBarangId: " . $e->getMessage());
            return null;
        }
    }

    private function generateBarangId($lastId)
    {
        if (!$lastId) {
            return 'B001';
        }

        $number = (int) substr($lastId, 1);
        $number++;
        return 'B' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusStok($stok)
    {
        if ($stok == 0) {
            return 'empty';
        } elseif ($stok <= 5) {
            return 'low';
        } else {
            return 'available';
        }
    }

    public function updateStock($idBarang, $jumlah, $type = 'in')
    {
        try {
            if ($type === 'in') {
                $query = "UPDATE {$this->table} 
                         SET stok = stok + :jumlah 
                         WHERE id_barang = :id_barang";
            } else {
                $query = "UPDATE {$this->table} 
                         SET stok = stok - :jumlah 
                         WHERE id_barang = :id_barang AND stok >= :jumlah";
            }

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':jumlah', $jumlah, \PDO::PARAM_INT);
            $stmt->bindParam(':id_barang', $idBarang);

            $result = $stmt->execute();

            if ($result && $type === 'out') {
                $affectedRows = $stmt->rowCount();
                if ($affectedRows === 0) {
                    error_log("Gagal update stok: stok tidak mencukupi untuk barang " . $idBarang);
                    return false;
                }
            }

            return $result;
        } catch (\PDOException $e) {
            error_log("Error updateStock: " . $e->getMessage());
            return false;
        }
    }

    public function getExpiredStatus($expiredDate)
    {
        if (!$expiredDate) {
            return 'none';
        }
        
        $currentDate = date('Y-m-d');
        $expiredDate = date('Y-m-d', strtotime($expiredDate));
        
        if ($expiredDate < $currentDate) {
            return 'expired';
        } elseif ($expiredDate == $currentDate) {
            return 'expiring_today';
        } elseif ((strtotime($expiredDate) - strtotime($currentDate)) / (60 * 60 * 24) <= 7) {
            return 'expiring_soon';
        } else {
            return 'safe';
        }
    }

    public function getActiveBarang()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id_barang, nama_barang, stok, harga_jual, harga_beli, satuan
                FROM {$this->table} 
                WHERE status = 'active' 
                ORDER BY nama_barang
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getActiveBarang: " . $e->getMessage());
            return [];
        }
    }

    public function getStock($id_barang)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT stok FROM {$this->table} 
                WHERE id_barang = :id_barang AND status = 'active'
            ");
            $stmt->bindParam(':id_barang', $id_barang);
            $stmt->execute();
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result['stok'] : 0;
        } catch (\PDOException $e) {
            error_log("Error getStock: " . $e->getMessage());
            return 0;
        }
    }

    public function getLowStockItems($threshold = 5)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, k.nama_kategori 
                FROM {$this->table} b 
                LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                WHERE b.status = 'active' AND b.stok <= :threshold
                ORDER BY b.stok ASC
            ");
            $stmt->bindParam(':threshold', $threshold, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getLowStockItems: " . $e->getMessage());
            return [];
        }
    }

    public function getExpiringItems($days = 7)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, k.nama_kategori 
                FROM {$this->table} b 
                LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                WHERE b.status = 'active' 
                AND b.expired_date IS NOT NULL
                AND b.expired_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                ORDER BY b.expired_date ASC
            ");
            $stmt->bindParam(':days', $days, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getExpiringItems: " . $e->getMessage());
            return [];
        }
    }

    public function searchBarang($searchTerm)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, k.nama_kategori 
                FROM {$this->table} b 
                LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                WHERE b.status = 'active' 
                AND (b.nama_barang LIKE :search OR b.id_barang LIKE :search OR k.nama_kategori LIKE :search)
                ORDER BY b.nama_barang
            ");
            $searchTerm = "%{$searchTerm}%";
            $stmt->bindParam(':search', $searchTerm);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error searchBarang: " . $e->getMessage());
            return [];
        }
    }

    public function getBarangByKategori($id_kategori)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, k.nama_kategori 
                FROM {$this->table} b 
                LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                WHERE b.status = 'active' AND b.id_kategori = :id_kategori
                ORDER BY b.nama_barang
            ");
            $stmt->bindParam(':id_kategori', $id_kategori);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getBarangByKategori: " . $e->getMessage());
            return [];
        }
    }

    public function updateHarga($id_barang, $harga_beli, $harga_jual)
    {
        try {
            $sql = "UPDATE {$this->table} 
                    SET harga_beli = :harga_beli, harga_jual = :harga_jual 
                    WHERE id_barang = :id_barang";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':harga_beli', $harga_beli, \PDO::PARAM_INT);
            $stmt->bindParam(':harga_jual', $harga_jual, \PDO::PARAM_INT);
            $stmt->bindParam(':id_barang', $id_barang);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updateHarga: " . $e->getMessage());
            return false;
        }
    }

    public function getTotalBarang()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'active'
            ");
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result['total'] : 0;
        } catch (\PDOException $e) {
            error_log("Error getTotalBarang: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalStokValue()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT SUM(stok * harga_beli) as total_value 
                FROM {$this->table} 
                WHERE status = 'active'
            ");
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result['total_value'] : 0;
        } catch (\PDOException $e) {
            error_log("Error getTotalStokValue: " . $e->getMessage());
            return 0;
        }
    }

    public function getBarangStats()
    {
        try {
            $stats = [
                'total_barang' => $this->getTotalBarang(),
                'total_stok_value' => $this->getTotalStokValue(),
                'low_stock_items' => count($this->getLowStockItems()),
                'expiring_items' => count($this->getExpiringItems())
            ];

            return $stats;
        } catch (\PDOException $e) {
            error_log("Error getBarangStats: " . $e->getMessage());
            return [
                'total_barang' => 0,
                'total_stok_value' => 0,
                'low_stock_items' => 0,
                'expiring_items' => 0
            ];
        }
    }
}