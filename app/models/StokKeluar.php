<?php

namespace App\models;

use App\models\BaseModel;

class StokKeluar extends BaseModel
{
    private $table = "stok_keluar";

    public function getAllStockOut()
    {
        try {
            $query = "SELECT 
                        sk.id_stokout,
                        sk.tanggal_keluar,
                        sk.jumlah,
                        sk.tipe_keluar,
                        sk.keterangan,
                        sk.total_harga,
                        b.nama_barang,
                        b.id_barang,
                        u.nama as nama_user
                      FROM {$this->table} sk
                      LEFT JOIN barang b ON sk.id_barang = b.id_barang
                      LEFT JOIN users u ON sk.id_user = u.id_users
                      ORDER BY sk.tanggal_keluar DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getAllStockOut: " . $e->getMessage());
            return [];
        }
    }

    public function getStockOutByPeriod($period, $value)
    {
        try {
            $query = "SELECT 
                        sk.id_stokout,
                        sk.tanggal_keluar,
                        sk.jumlah,
                        sk.tipe_keluar,
                        sk.keterangan,
                        sk.total_harga,
                        b.nama_barang,
                        b.id_barang,
                        u.nama as nama_user
                      FROM {$this->table} sk
                      LEFT JOIN barang b ON sk.id_barang = b.id_barang
                      LEFT JOIN users u ON sk.id_user = u.id_users
                      WHERE ";

            switch ($period) {
                case 'day':
                    $query .= "DATE(sk.tanggal_keluar) = :date";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':date', $value);
                    break;
                case 'month':
                    $query .= "MONTH(sk.tanggal_keluar) = :month AND YEAR(sk.tanggal_keluar) = :year";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':month', $value['month']);
                    $stmt->bindParam(':year', $value['year']);
                    break;
                case 'year':
                    $query .= "YEAR(sk.tanggal_keluar) = :year";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':year', $value);
                    break;
                default:
                    return $this->getAllStockOut();
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getStockOutByPeriod: " . $e->getMessage());
            return [];
        }
    }

    public function searchStockOut($searchTerm)
    {
        try {
            $query = "SELECT 
                        sk.id_stokout,
                        sk.tanggal_keluar,
                        sk.jumlah,
                        sk.tipe_keluar,
                        sk.keterangan,
                        sk.total_harga,
                        b.nama_barang,
                        b.id_barang,
                        u.nama as nama_user
                      FROM {$this->table} sk
                      LEFT JOIN barang b ON sk.id_barang = b.id_barang
                      LEFT JOIN users u ON sk.id_user = u.id_users
                      WHERE b.nama_barang LIKE :search 
                         OR sk.keterangan LIKE :search
                         OR sk.id_stokout LIKE :search
                         OR sk.tipe_keluar LIKE :search
                         OR u.nama LIKE :search
                      ORDER BY sk.tanggal_keluar DESC";

            $stmt = $this->db->prepare($query);
            $searchTerm = "%{$searchTerm}%";
            $stmt->bindParam(':search', $searchTerm);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error searchStockOut: " . $e->getMessage());
            return [];
        }
    }

    public function addStockOut($data)
    {
        try {
            $idStokout = $this->generateStockOutId();

            $query = "INSERT INTO {$this->table} 
                      (id_stokout, id_barang, jumlah, tipe_keluar, keterangan, total_harga, id_user, tanggal_keluar) 
                      VALUES (:id_stokout, :id_barang, :jumlah, :tipe_keluar, :keterangan, :total_harga, :id_user, :tanggal_keluar)";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_stokout', $idStokout);
            $stmt->bindParam(':id_barang', $data['id_barang']);
            $stmt->bindParam(':jumlah', $data['jumlah'], \PDO::PARAM_INT);
            $stmt->bindParam(':tipe_keluar', $data['tipe_keluar']);
            $stmt->bindParam(':keterangan', $data['keterangan']);
            $stmt->bindParam(':total_harga', $data['total_harga'], \PDO::PARAM_INT);
            $stmt->bindParam(':id_user', $data['id_user']);
            $stmt->bindParam(':tanggal_keluar', $data['tanggal_keluar']);

            $result = $stmt->execute();

            if ($result) {
                error_log("Stock Out berhasil ditambahkan: " . $idStokout . " pada " . $data['tanggal_keluar']);
                return $idStokout;
            } else {
                error_log("Gagal menambahkan Stock Out");
                return false;
            }
        } catch (\PDOException $e) {
            error_log("Error addStockOut: " . $e->getMessage());
            error_log("Data yang gagal: " . print_r($data, true));
            return false;
        }
    }

    private function generateStockOutId()
    {
        try {
            $query = "SELECT id_stokout FROM {$this->table} ORDER BY id_stokout DESC LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $lastId = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($lastId && isset($lastId['id_stokout'])) {
                $lastNumber = (int) substr($lastId['id_stokout'], 3);
                $newNumber = $lastNumber + 1;
                return 'SO' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            }

            return 'SO001';
        } catch (\PDOException $e) {
            error_log("Error generateStockOutId: " . $e->getMessage());
            return 'SO001';
        }
    }

    public function getStockOutById($id_stokout)
    {
        try {
            $query = "SELECT 
                        sk.*,
                        b.nama_barang,
                        u.nama as nama_user
                      FROM {$this->table} sk
                      LEFT JOIN barang b ON sk.id_barang = b.id_barang
                      LEFT JOIN users u ON sk.id_user = u.id_users
                      WHERE sk.id_stokout = :id_stokout";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_stokout', $id_stokout);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getStockOutById: " . $e->getMessage());
            return null;
        }
    }

    public function deleteStockOut($id_stokout)
    {
        try {
            $this->db->beginTransaction();

            $stockOutData = $this->getStockOutById($id_stokout);

            if (!$stockOutData) {
                $this->db->rollBack();
                return false;
            }

            $query = "DELETE FROM {$this->table} WHERE id_stokout = :id_stokout";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_stokout', $id_stokout);
            $result = $stmt->execute();

            if ($result) {
                $barangModel = new Barang();
                $updateResult = $barangModel->updateStock(
                    $stockOutData['id_barang'],
                    $stockOutData['jumlah'],
                    'in'
                );

                if ($updateResult) {
                    $this->db->commit();
                    return true;
                } else {
                    $this->db->rollBack();
                    return false;
                }
            }

            $this->db->rollBack();
            return false;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error deleteStockOut: " . $e->getMessage());
            return false;
        }
    }
}
