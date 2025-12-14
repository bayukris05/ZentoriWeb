<?php

namespace App\controllers;

use App\config\View;
use App\models\Barang;
use App\models\Kategori;
use App\models\Stok;

class BarangController
{
    private $barangModel;
    private $kategoriModel;
    private $stockModel;

    public function __construct()
    {
        $this->barangModel = new Barang();
        $this->kategoriModel = new Kategori();
        $this->stockModel = new Stok();
    }

    public function index()
    {
        $barangData = $this->barangModel->getAllBarang();

        $barangWithStatus = array_map(function ($item) {
            $item['status_stok'] = $this->barangModel->getStatusStok($item['stok']);
            return $item;
        }, $barangData);

        $expiredSummary = $this->stockModel->getExpiredSummary();

        $data = [
            'barang' => $barangWithStatus,
            'kategori' => $this->kategoriModel->getAllKategori(),
            'expiredSummary' => $expiredSummary
        ];
        View::render("barang/index", $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_barang' => $_POST['nama_barang'] ?? '',
                'id_kategori' => $_POST['id_kategori'] ?? '',
                'satuan' => $_POST['satuan'] ?? '',
                'stok' => $_POST['stok'] ?? 0,
                'harga_beli' => $_POST['harga_beli'] ?? 0,
                'harga_jual' => $_POST['harga_jual'] ?? 0,
                'expired_date' => $_POST['expired_date'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if (empty($data['nama_barang']) || empty($data['id_kategori']) || empty($data['satuan'])) {
                echo json_encode(['success' => false, 'message' => 'Nama barang, kategori, dan satuan harus diisi']);
                return;
            }

            if ($data['harga_jual'] <= $data['harga_beli']) {
                echo json_encode(['success' => false, 'message' => 'Harga jual harus lebih besar dari harga beli']);
                return;
            }

            $result = $this->barangModel->createBarang($data);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Barang berhasil ditambahkan']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan barang']);
            }
        }
    }

    public function edit($id)
    {
        $barang = $this->barangModel->getBarangById($id);

        if ($barang) {
            $barang['status_stok'] = $this->barangModel->getStatusStok($barang['stok']);
            echo json_encode(['success' => true, 'data' => $barang]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Barang tidak ditemukan']);
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_barang' => $_POST['nama_barang'] ?? '',
                'id_kategori' => $_POST['id_kategori'] ?? '',
                'satuan' => $_POST['satuan'] ?? '',
                'stok' => $_POST['stok'] ?? 0,
                'harga_beli' => $_POST['harga_beli'] ?? 0,
                'harga_jual' => $_POST['harga_jual'] ?? 0,
                'expired_date' => $_POST['expired_date'] ?? null
            ];

            if (empty($data['nama_barang']) || empty($data['id_kategori']) || empty($data['satuan'])) {
                echo json_encode(['success' => false, 'message' => 'Nama barang, kategori, dan satuan harus diisi']);
                return;
            }

            if ($data['harga_jual'] <= $data['harga_beli']) {
                echo json_encode(['success' => false, 'message' => 'Harga jual harus lebih besar dari harga beli']);
                return;
            }

            $result = $this->barangModel->updateBarang($id, $data);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Barang berhasil diupdate']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mengupdate barang']);
            }
        }
    }

    public function delete($id)
    {
        $result = $this->barangModel->deleteBarang($id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Barang berhasil dinonaktifkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menonaktifkan barang']);
        }
    }

    public function activate($id)
    {
        $result = $this->barangModel->activateBarang($id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Barang berhasil diaktifkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengaktifkan barang']);
        }
    }

    public function getExpiredDetail($id)
    {
        $expiredInfo = $this->stockModel->getExpiredInfoByBarang($id);

        echo json_encode([
            'success' => true,
            'data' => $expiredInfo
        ]);
    }
}
