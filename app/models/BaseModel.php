<?php
namespace App\models;

use App\config\Database;

class BaseModel {
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect(); 
    }
}