<?php

namespace App\models;

use App\models\BaseModel;

class Kategori extends BaseModel
{
    public function getAllKategori()
    {
        $stmt = $this->db->prepare("SELECT * FROM kategori ORDER BY nama_kategori");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getKategoriById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
