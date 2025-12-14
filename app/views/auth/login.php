<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventori</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        .left-side {
            flex: 1;
            background: url('<?= BASE_URL ?>/assets/img/bg_login_inventori.jpg') center/cover no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .left-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }

        .left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            padding: 40px;
        }

        .icon-box {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 8px 32px rgba(251, 113, 133, 0.4);
        }

        .icon-box svg {
            width: 60px;
            height: 60px;
        }

        .left-content h1 {
            font-size: 3em;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .left-content p {
            font-size: 1.2em;
            opacity: 0.9;
            max-width: 400px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fdfdfd;
            padding: 40px;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 35px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(251, 113, 133, 0.18);
        }

        .form-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .form-header h2 {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #666;
            font-size: 0.9em;
        }

        .form-group {
            margin-bottom: 16px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #555;
            font-weight: 500;
            font-size: 0.875em;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper .input-icon {
            position: absolute;
            left: 15px;
            width: 20px;
            height: 20px;
            color: #999;
            pointer-events: none;
            z-index: 2;
        }

        .form-group input {
            width: 100%;
            padding: 12px 50px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 0.95em;
            transition: all 0.3s;
            background: #fafafa;
            height: 48px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #fb7185;
            background: white;
            box-shadow: 0 0 8px rgba(251, 113, 133, 0.25);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .submit-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 8px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 48px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(251, 113, 133, 0.35);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .switch-mode {
            text-align: center;
            margin-top: 18px;
            color: #666;
            font-size: 0.9em;
        }

        .switch-mode a {
            color: #fb7185;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .switch-mode a:hover {
            text-decoration: underline;
        }

        .hidden {
            display: none !important;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 0.9em;
        }

        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .left-side {
                display: none;
            }

            .right-side {
                flex: 1;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left-side">
            <div class="left-content">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1>Selamat Datang!</h1>
                <p>Masuk ke akun Anda dan nikmati berbagai fitur menarik yang telah kami siapkan untuk Anda.</p>
            </div>
        </div>

        <div class="right-side">
            <div class="form-container">
                <form id="loginForm">
                    <div class="form-header">
                        <h2>Masuk</h2>
                        <p>Masukkan kredensial Anda untuk melanjutkan</p>
                    </div>

                    <div id="loginAlert"></div>

                    <div class="form-group">
                        <label for="loginEmail">Email</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <input type="email" id="loginEmail" placeholder="nama@email.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input type="password" id="loginPassword" placeholder="Masukkan password" required>
                            <span class="password-toggle" onclick="togglePassword('loginPassword', this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="loginBtn">
                        <span id="loginText">Masuk</span>
                        <span id="loginLoading" class="hidden loading"></span>
                    </button>

                    <div class="switch-mode">
                        Belum punya akun? <a onclick="switchToSignup()">Daftar sekarang</a>
                    </div>
                </form>

                <form id="signupForm" class="hidden">
                    <div class="form-header">
                        <h2>Daftar</h2>
                        <p>Buat akun baru untuk memulai</p>
                    </div>

                    <div id="signupAlert"></div>

                    <div class="form-group">
                        <label for="signupName">Nama Lengkap</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <input type="text" id="signupName" placeholder="Nama lengkap Anda" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signupEmail">Email</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <input type="email" id="signupEmail" placeholder="nama@email.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signupPassword">Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input type="password" id="signupPassword" placeholder="Buat password" required>
                            <span class="password-toggle" onclick="togglePassword('signupPassword', this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input type="password" id="confirmPassword" placeholder="Konfirmasi password" required>
                            <span class="password-toggle" onclick="togglePassword('confirmPassword', this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="signupBtn">
                        <span id="signupText">Daftar</span>
                        <span id="signupLoading" class="hidden loading"></span>
                    </button>

                    <div class="switch-mode">
                        Sudah punya akun? <a onclick="switchToLogin()">Masuk di sini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('loginLoading').classList.add('hidden');
            document.getElementById('signupLoading').classList.add('hidden');
        });

        function switchToSignup() {
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('signupForm').classList.remove('hidden');
            clearAlerts();
        }

        function switchToLogin() {
            document.getElementById('signupForm').classList.add('hidden');
            document.getElementById('loginForm').classList.remove('hidden');
            clearAlerts();
        }

        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>`;
            }
        }

        function showAlert(formType, message, type = 'error') {
            const alertDiv = document.getElementById(`${formType}Alert`);
            alertDiv.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        }

        function clearAlerts() {
            document.getElementById('loginAlert').innerHTML = '';
            document.getElementById('signupAlert').innerHTML = '';
        }

        function setLoading(button, textElement, loadingElement, isLoading) {
            if (isLoading) {
                button.disabled = true;
                textElement.classList.add('hidden');
                loadingElement.classList.remove('hidden');
            } else {
                button.disabled = false;
                textElement.classList.remove('hidden');
                loadingElement.classList.add('hidden');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const loginBtn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loginLoading = document.getElementById('loginLoading');

            setLoading(loginBtn, loginText, loginLoading, true);
            clearAlerts();

            try {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('password', password);

                const response = await fetch('/auth/login/process', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('login', result.message, 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect || '/dashboard';
                    }, 1000);
                } else {
                    showAlert('login', result.message, 'error');
                }
            } catch (error) {
                console.error('Login error:', error);
                showAlert('login', 'Terjadi kesalahan jaringan', 'error');
            } finally {
                setLoading(loginBtn, loginText, loginLoading, false);
            }
        });

        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const name = document.getElementById('signupName').value;
            const email = document.getElementById('signupEmail').value;
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const signupBtn = document.getElementById('signupBtn');
            const signupText = document.getElementById('signupText');
            const signupLoading = document.getElementById('signupLoading');

            setLoading(signupBtn, signupText, signupLoading, true);
            clearAlerts();

            try {
                const formData = new FormData();
                formData.append('name', name);
                formData.append('email', email);
                formData.append('password', password);
                formData.append('confirmPassword', confirmPassword);

                const response = await fetch('/auth/register/process', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('signup', result.message, 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect || '/dashboard';
                    }, 1000);
                } else {
                    showAlert('signup', result.message, 'error');
                }
            } catch (error) {
                console.error('Signup error:', error);
                showAlert('signup', 'Terjadi kesalahan jaringan', 'error');
            } finally {
                setLoading(signupBtn, signupText, signupLoading, false);
            }
        });
    </script>
</body>

</html>