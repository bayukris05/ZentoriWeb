<?php

namespace App\controllers;

use App\config\View;
use App\config\Database;

class DashboardController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index()
    {
        $stats = $this->getDashboardStats();

        $lowStock = $this->getLowStockItems();

        $recentTransactions = $this->getRecentTransactions();
        
        View::render("dashboard/index", [
            'stats' => $stats,
            'lowStock' => $lowStock,
            'recentTransactions' => $recentTransactions
        ]);
    }

    private function getDashboardStats()
    {
        $stats = [];

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM barang");
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stats['total_barang'] = $result ? $result['total'] : 0;

            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM suppliers WHERE status = 'active'");
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stats['total_supplier'] = $result ? $result['total'] : 0;

            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(total_harga), 0) as total 
                FROM stok_masuk 
                WHERE MONTH(tanggal_masuk) = MONTH(CURRENT_DATE())
                AND YEAR(tanggal_masuk) = YEAR(CURRENT_DATE())
            ");
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stats['total_pemasukan'] = $result ? $result['total'] : 0;

            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(total_harga), 0) as total 
                FROM stok_keluar 
                WHERE MONTH(tanggal_keluar) = MONTH(CURRENT_DATE())
                AND YEAR(tanggal_keluar) = YEAR(CURRENT_DATE())
            ");
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stats['total_pengeluaran'] = $result ? $result['total'] : 0;
        } catch (\Exception $e) {
            error_log("Dashboard Error: " . $e->getMessage());
            $stats['total_barang'] = 0;
            $stats['total_supplier'] = 0;
            $stats['total_pemasukan'] = 0;
            $stats['total_pengeluaran'] = 0;
        }

        return $stats;
    }

    private function getLowStockItems()
    {
        try {
            $checkColumn = $this->db->prepare("SHOW COLUMNS FROM barang LIKE 'min_stok'");
            $checkColumn->execute();
            $columnExists = $checkColumn->fetch();

            if ($columnExists) {
                $stmt = $this->db->prepare("
                    SELECT 
                        id_barang,
                        nama_barang, 
                        stok,
                        COALESCE(min_stok, 10) as min_stok
                    FROM barang 
                    WHERE stok <= COALESCE(min_stok, 10)
                    AND stok > 0
                    ORDER BY stok ASC 
                    LIMIT 5
                ");
            } else {
                $stmt = $this->db->prepare("
                    SELECT 
                        id_barang,
                        nama_barang, 
                        stok,
                        10 as min_stok
                    FROM barang 
                    WHERE stok <= 10
                    AND stok > 0 AND status = 'active'
                    ORDER BY stok ASC 
                    LIMIT 5
                ");
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            error_log("Low Stock Error: " . $e->getMessage());
            return [];
        }
    }

    private function getRecentTransactions()
    {
        try {
            $transactions = [];
            
            $queryKeluar = "
                SELECT 
                    sk.id_stokout,
                    sk.id_barang,
                    b.nama_barang,
                    sk.jumlah,
                    sk.total_harga,
                    sk.tanggal_keluar,
                    sk.tipe_keluar
                FROM stok_keluar sk
                INNER JOIN barang b ON sk.id_barang = b.id_barang
                ORDER BY sk.tanggal_keluar DESC
                LIMIT 10
            ";
            
            $stmt = $this->db->query($queryKeluar);
            $resultsKeluar = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($resultsKeluar as $row) {
                $obj = new \stdClass();
                $obj->jenis_transaksi = 'pemasukan';
                $obj->total_harga = $row['total_harga'];
                $obj->tanggal_transaksi = $row['tanggal_keluar'];
                $obj->nama_barang = $row['nama_barang'];
                $obj->jumlah = $row['jumlah'];
                $obj->id_transaksi = $row['id_stokout'];
                $obj->supplier = '';
                $obj->tipe_keluar = $row['tipe_keluar'] ?? '';
                
                $transactions[] = $obj;
            }

            $queryMasuk = "
                SELECT 
                    sm.id_stokin,
                    sm.id_barang,
                    b.nama_barang,
                    sm.jumlah,
                    sm.total_harga,
                    sm.tanggal_masuk,
                    s.nama_supplier
                FROM stok_masuk sm
                INNER JOIN barang b ON sm.id_barang = b.id_barang
                LEFT JOIN suppliers s ON sm.id_supplier = s.id_supplier
                ORDER BY sm.tanggal_masuk DESC
                LIMIT 10
            ";
            
            $stmt = $this->db->query($queryMasuk);
            $resultsMasuk = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($resultsMasuk as $row) {
                $obj = new \stdClass();
                $obj->jenis_transaksi = 'pengeluaran';
                $obj->total_harga = $row['total_harga'];
                $obj->tanggal_transaksi = $row['tanggal_masuk'];
                $obj->nama_barang = $row['nama_barang'];
                $obj->jumlah = $row['jumlah'];
                $obj->id_transaksi = $row['id_stokin'];
                $obj->supplier = $row['nama_supplier'] ?? '';
                $obj->tipe_keluar = '';
                
                $transactions[] = $obj;
            }
            usort($transactions, function($a, $b) {
                return strtotime($b->tanggal_transaksi) - strtotime($a->tanggal_transaksi);
            });
            
            $final = array_slice($transactions, 0, 3);
            return $final;
            
        } catch (\Exception $e) {
            return [];
        }
    }
}