<?php

namespace App\models;

use App\models\BaseModel;

class Supplier extends BaseModel
{
    public function getAllSupplier()
    {
        $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE status = 'active' ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSupplierById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE id_supplier = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createSupplier($data)
    {
        $lastId = $this->getLastSupplierId();
        $newId = $this->generateSupplierId($lastId);

        $sql = "INSERT INTO suppliers (id_supplier, nama_supplier, kontak, email, alamat, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $newId,
            $data['nama_supplier'],
            $data['kontak'],
            $data['email'],
            $data['alamat'],
            'active', 
            $data['created_at']
        ]);
    }

    public function updateSupplier($id, $data)
    {
        $sql = "UPDATE suppliers SET nama_supplier = ?, kontak = ?, email = ?, alamat = ? WHERE id_supplier = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nama_supplier'],
            $data['kontak'],
            $data['email'],
            $data['alamat'],
            $id
        ]);
    }

    public function deleteSupplier($id)
    {
        $sql = "UPDATE suppliers SET status = 'inactive' WHERE id_supplier = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function activateSupplier($id)
    {
        $sql = "UPDATE suppliers SET status = 'active' WHERE id_supplier = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    private function getLastSupplierId()
    {
        $stmt = $this->db->prepare("SELECT id_supplier FROM suppliers ORDER BY id_supplier DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['id_supplier'] : null;
    }

    private function generateSupplierId($lastId)
    {
        if (!$lastId) {
            return 'S001';
        }

        $number = (int) substr($lastId, 1);
        $number++;
        return 'S' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}