<?php

namespace App\controllers;

use App\config\View;
use App\models\BaseModel;
use App\models\User;

class AuthController extends BaseModel
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        View::render("auth/login");
    }

    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Email dan password harus diisi']);
                return;
            }

            $user = $this->getUserByEmail($email);

            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Email tidak ditemukan']);
                return;
            }

            if ($user['status'] !== 'active') {
                echo json_encode(['success' => false, 'message' => 'Akun tidak aktif']);
                return;
            }
            if (!password_verify($password, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Password salah']);
                return;
            }

            $this->setUserSession($user);

            echo json_encode([
                'success' => true,
                'message' => 'Login berhasil!',
                'redirect' => '/dashboard'
            ]);
        } catch (\Exception $e) {
            error_log("Login error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function processRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $name = trim($_POST['name'] ?? '');
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';

            $validation = $this->validateRegistration($name, $email, $password, $confirmPassword);
            if (!$validation['success']) {
                echo json_encode($validation);
                return;
            }
            if ($this->userModel->checkEmailExists($email)) {
                echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar']);
                return;
            }

            $userData = [
                'nama' => $name,
                'email' => $email,
                'password' => $password,
                'role' => 'staff', 
                'status' => 'active'
            ];

            $result = $this->userModel->createUser($userData);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Registrasi berhasil! Silakan login dengan akun Anda.',
                    'redirect' => '/login' 
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal membuat akun']);
            }
        } catch (\Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function logout()
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        header('Location: /login');
        exit;
    }

    private function getUserByEmail($email)
    {
        try {
            $stmt = $this->userModel->db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Error getting user by email: " . $e->getMessage());
            return false;
        }
    }

    private function validateRegistration($name, $email, $password, $confirmPassword)
    {
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            return ['success' => false, 'message' => 'Semua field harus diisi'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter'];
        }

        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Password tidak cocok'];
        }

        if (strlen($name) < 2) {
            return ['success' => false, 'message' => 'Nama minimal 2 karakter'];
        }

        return ['success' => true];
    }

    private function setUserSession($user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['id_users']; 
        $_SESSION['user_name'] = $user['nama'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;

        error_log("Session set - User ID: " . $_SESSION['user_id'] . ", Role: " . $_SESSION['user_role']);
    }

    public static function checkAuth()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function getUserRole()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_role'] ?? null;
    }

    public static function getUserId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }
}
