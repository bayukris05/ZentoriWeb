<?php

namespace App\controllers;

use App\config\View;
use App\models\Supplier;

class SupplierController
{
    private $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new Supplier();
    }

    public function index()
    {
        $data = [
            'suppliers' => $this->supplierModel->getAllSupplier()
        ];
        View::render("supplier/index", $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_supplier' => $_POST['nama_supplier'] ?? '',
                'kontak' => $_POST['kontak'] ?? '',
                'email' => $_POST['email'] ?? '',
                'alamat' => $_POST['alamat'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ];

            if (empty($data['nama_supplier']) || empty($data['kontak']) || empty($data['email']) || empty($data['alamat'])) {
                echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
                return;
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
                return;
            }

            $result = $this->supplierModel->createSupplier($data);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Supplier berhasil ditambahkan']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan supplier']);
            }
        }
    }

    public function edit($id)
    {
        $supplier = $this->supplierModel->getSupplierById($id);

        if ($supplier) {
            echo json_encode(['success' => true, 'data' => $supplier]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Supplier tidak ditemukan']);
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_supplier' => $_POST['nama_supplier'] ?? '',
                'kontak' => $_POST['kontak'] ?? '',
                'email' => $_POST['email'] ?? '',
                'alamat' => $_POST['alamat'] ?? ''
            ];

            if (empty($data['nama_supplier']) || empty($data['kontak']) || empty($data['email']) || empty($data['alamat'])) {
                echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
                return;
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
                return;
            }

            $result = $this->supplierModel->updateSupplier($id, $data);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Supplier berhasil diupdate']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mengupdate supplier']);
            }
        }
    }

    public function delete($id)
    {
        $result = $this->supplierModel->deleteSupplier($id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Supplier berhasil dinonaktifkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menonaktifkan supplier']);
        }
    }

    public function activate($id)
    {
        $result = $this->supplierModel->activateSupplier($id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Supplier berhasil diaktifkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengaktifkan supplier']);
        }
    }
}