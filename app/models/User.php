<?php

namespace App\models;

use App\models\BaseModel;

class User extends BaseModel
{
    public function getAllUser()
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE status = 'active' ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id_users = :id");
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch();

            if (!$user) {
                return false;
            }

            return $user;
        } catch (\PDOException $e) {
            error_log("Error getting user by ID: " . $e->getMessage());
            return false;
        }
    }

    public function createUser($data)
    {
        $newId = $this->generateUserId();

        $role = strtolower($data['role']);

        $sql = "INSERT INTO users (id_users, nama, email, password, role, status, created_at) 
                VALUES (:id_users, :nama, :email, :password, :role, :status, :created_at)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_users' => $newId,
            ':nama' => $data['nama'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role' => $role,
            ':status' => $data['status'],
            ':created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function updateUser($id, $data)
    {
        if (isset($data['role'])) {
            $data['role'] = strtolower($data['role']);
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        $setPart = "";
        foreach ($data as $key => $value) {
            $setPart .= "$key = :$key, ";
        }

        $setPart = rtrim($setPart, ", ");

        $sql = "UPDATE users SET $setPart WHERE id_users = :id";

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->bindValue(":id", $id);

        return $stmt->execute();
    }

    public function deleteUser($id)
    {
        $sql = "UPDATE users SET status = 'inactive' WHERE id_users = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);

        return $stmt->execute();
    }

    public function activateUser($id)
    {
        $sql = "UPDATE users SET status = 'active' WHERE id_users = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);

        return $stmt->execute();
    }

    public function checkEmailExists($email, $excludeId = null)
    {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id_users != :id");
            $stmt->execute([':email' => $email, ':id' => $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
        }
        return $stmt->fetchColumn() > 0;
    }

    private function generateUserId()
    {
        $stmt = $this->db->prepare("SELECT id_users FROM users ORDER BY id_users DESC LIMIT 1");
        $stmt->execute();
        $lastId = $stmt->fetchColumn();

        if ($lastId) {
            $number = intval(substr($lastId, 1));
            $newNumber = $number + 1;
        } else {
            $newNumber = 1;
        }

        return 'U' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}