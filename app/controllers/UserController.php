<?php

namespace App\controllers;

use App\models\User;
use App\config\View;

class UserController
{
    private $user;

    public function __construct()
    {
        if (strtolower(AuthController::getUserRole() ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        $this->user = new User();
    }

    public function index()
    {
        $data = $this->user->getAllUser();
        View::render("users/index", ['data' => $data]);
    }

    public function create()
    {
        header('Content-Type: application/json');

        try {
            if (empty($_POST['nama']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Semua field harus diisi'
                ]);
                return;
            }

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Format email tidak valid'
                ]);
                return;
            }

            if ($this->user->checkEmailExists($_POST['email'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email sudah terdaftar'
                ]);
                return;
            }

            $data = [
                'nama' => htmlspecialchars($_POST['nama']),
                'email' => htmlspecialchars($_POST['email']),
                'password' => $_POST['password'],
                'role' => htmlspecialchars($_POST['role']),
                'status' => 'active' 
            ];

            if ($this->user->createUser($data)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User berhasil ditambahkan'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menambahkan user'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        header('Content-Type: application/json');

        try {
            if (empty($id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID user tidak valid'
                ]);
                return;
            }

            $user = $this->user->getUserById($id);

            if ($user) {
                $responseData = [
                    'id_users' => $user['id_users'],
                    'nama' => $user['nama'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'status' => $user['status']
                ];

                echo json_encode([
                    'success' => true,
                    'data' => $responseData
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');

        try {
            if (empty($_POST['nama']) || empty($_POST['email']) || empty($_POST['role'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Nama, email, dan role harus diisi'
                ]);
                return;
            }

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Format email tidak valid'
                ]);
                return;
            }

            if ($this->user->checkEmailExists($_POST['email'], $id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email sudah digunakan oleh user lain'
                ]);
                return;
            }

            $data = [
                'nama' => htmlspecialchars($_POST['nama']),
                'email' => htmlspecialchars($_POST['email']),
                'role' => htmlspecialchars($_POST['role'])
            ];

            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }

            if ($this->user->updateUser($id, $data)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User berhasil diupdate'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal mengupdate user'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        header('Content-Type: application/json');

        try {
            if ($this->user->deleteUser($id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User berhasil dinonaktifkan'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menonaktifkan user'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function activate($id)
    {
        header('Content-Type: application/json');

        try {
            if ($this->user->activateUser($id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User berhasil diaktifkan'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal mengaktifkan user'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}