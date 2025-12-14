<?php

namespace App\Controllers;

use App\Models\BaseModel;
use PDO;
use Exception;

class SeederController extends BaseModel {

    public function run() {
        try {
            // Baca file database.sql dari root folder
            $sqlFile = __DIR__ . '/../../database.sql';
            
            if (!file_exists($sqlFile)) {
                throw new Exception("File database.sql tidak ditemukan di: " . $sqlFile);
            }

            $sql = file_get_contents($sqlFile);

            // Eksekusi query
            // Kita gunakan koneksi dari BaseModel
            $this->db->exec($sql);

            echo "<div style='background-color: #d4edda; color: #155724; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; font-family: sans-serif; margin: 20px;'>
                    <h3>✅ Berhasil!</h3>
                    <p>Database berhasil di-import dari file <b>database.sql</b>.</p>
                    <p>Silakan <a href='" . BASE_URL . "/login'>Login disini</a></p>
                  </div>";

        } catch (Exception $e) {
            echo "<div style='background-color: #f8d7da; color: #721c24; padding: 20px; border: 1px solid #f5c6cb; border-radius: 5px; font-family: sans-serif; margin: 20px;'>
                    <h3>❌ Gagal!</h3>
                    <p>Terjadi error saat import database:</p>
                    <pre>" . $e->getMessage() . "</pre>
                  </div>";
        }
    }
}
