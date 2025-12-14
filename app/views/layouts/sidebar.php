<?php
$current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$userName = $_SESSION['user_name'] ?? 'Admin User';
$userRole = $_SESSION['user_role'] ?? 'Administrator';

// Helper function to check active state
$isActive = function($path) use ($current) {
    if ($path === BASE_URL . '/dashboard') {
        return $current === '/dashboard' ? 'active' : '';
    }
    // Remove BASE_URL from path for comparison if needed, or just compare segments
    // Assuming $path passed to function is full URL like BASE_URL . '/users'
    // But for simplicity let's pass the relative path segment to the function or handle it smartly.
    
    // Better approach matching the existing code style:
    // The existing code manually constructed links: BASE_URL . '/page'
    // Let's modify the usage.
    return ''; 
};

// Re-evaluating the replacement strategy to be cleaner and less prone to errors with the tool.
// I will implement the logic directly in the view or define the closure properly.

// Helper function to check active state
$isActive = function($path) use ($current) {
    // Normalize paths by removing trailing slashes
    $current = rtrim($current, '/');
    $path = rtrim($path, '/');

    // Exact match
    if ($current === $path) {
        return 'active';
    }

    // Check if it's a sub-path, BUT ensure we are matching a complete segment.
    // e.g. /barang matches /barang/add but NOT /barang-stok
    if (strpos($current, $path . '/') === 0) {
        return 'active';
    }

    return '';
};
?>

<div class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
    <i class="bi bi-list"></i>
</div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="bi bi-box-seam"></i>
        <span>Zentori</span>
    </div>
    <div class="sidebar-content">
        <div class="menu-item <?= $isActive('/dashboard') ?>" data-tooltip="Dashboard" onclick="window.location.href='<?= BASE_URL ?>/dashboard'">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </div>

        <p class="title-item">Data Master</p>
        <?php if (strtolower($userRole) === 'admin') : ?>
        <div class="menu-item <?= $isActive('/users') ?>" data-tooltip="Data User" onclick="window.location.href='<?= BASE_URL ?>/users'">
            <i class="bi bi-people"></i>
            <span>Data User</span>
        </div>
        <?php endif; ?>
        <div class="menu-item <?= $isActive('/supplier') ?>" data-tooltip="Data Supplier" onclick="window.location.href='<?= BASE_URL ?>/supplier'">
            <i class="bi bi-truck"></i>
            <span>Data Supplier</span>
        </div>
        <div class="menu-item <?= $isActive('/barang') ?>" data-tooltip="Data Barang" onclick="window.location.href='<?= BASE_URL ?>/barang'">
            <i class="bi bi-box"></i>
            <span>Data Barang</span>
        </div>

        <p class="title-item">Manajemen Stok</p>
        <div class="menu-item <?= $isActive('/stokin') ?>" data-tooltip="Stock In" onclick="window.location.href='<?= BASE_URL ?>/stokin'">
            <i class="bi bi-arrow-down-left"></i>
            <span>Stock In</span>
        </div>
        <div class="menu-item <?= $isActive('/stokout') ?>" data-tooltip="Stock Out" onclick="window.location.href='<?= BASE_URL ?>/stokout'">
            <i class="bi bi-arrow-up-right"></i>
            <span>Stock Out</span>
        </div>
        <div class="menu-item <?= $isActive('/history') ?>" data-tooltip="History" onclick="window.location.href='<?= BASE_URL ?>/history'">
            <i class="bi bi-clock-history"></i>
            <span>History</span>
        </div>
    </div>
</nav>

<!-- Modal Konfirmasi Logout - CLASS NAME DIPERBAIKI -->
<div class="logout-modal-overlay" id="logoutModal">
    <div class="logout-modal-content">
        <div class="logout-modal-header">
            <h3>Konfirmasi Logout</h3>
            <button type="button" class="logout-modal-close" onclick="closeLogoutModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="logout-modal-body">
            <div class="logout-modal-icon">
                <i class="bi bi-question-circle"></i>
            </div>
            <p>Apakah Anda yakin ingin logout dari sistem?</p>
        </div>
        <div class="logout-modal-footer">
            <button type="button" class="logout-btn-secondary" onclick="closeLogoutModal()">Batal</button>
            <button type="button" class="logout-btn-primary" onclick="confirmLogout()">Ya, Logout</button>
        </div>
    </div>
</div>

<div class="main-content" id="mainContent">
    <header>
        <div></div>
        <div class="d-flex align-items-center">
            <div class="profile-part" onclick="toggleDropdown()">
                <div class="avatar-profile">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?= urlencode($userName) ?>" alt="foto-profile">
                </div>
                <div class="avatar-name">
                    <h3><?= htmlspecialchars($userName) ?></h3>
                    <p><?= htmlspecialchars($userRole) ?></p>
                </div>
                <i class="bi bi-chevron-down" id="panah-bawah"></i>

                <!-- Dropdown Menu -->
                <div class="profile-dropdown-menu" id="dropdownMenu">
                    <div class="profile-dropdown-item" onclick="showLogoutModal()">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <script>
        function toggleDropdown() {
            const dropdownMenu = document.getElementById('dropdownMenu');
            const arrow = document.getElementById('panah-bawah');

            dropdownMenu.classList.toggle('show');
            arrow.classList.toggle('rotated');
        }

        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'flex';
            document.getElementById('dropdownMenu').classList.remove('show');
            document.getElementById('panah-bawah').classList.remove('rotated');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function confirmLogout() {
            window.location.href = '<?= BASE_URL ?>/auth/logout';
        }

        document.addEventListener('click', function(event) {
            const dropdownMenu = document.getElementById('dropdownMenu');
            const profilePart = document.querySelector('.profile-part');

            if (!profilePart.contains(event.target)) {
                dropdownMenu.classList.remove('show');
                document.getElementById('panah-bawah').classList.remove('rotated');
            }
        });

        document.addEventListener('click', function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                closeLogoutModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeLogoutModal();
            }
        });
    </script>

    <style>
        .profile-part {
            position: relative;
            cursor: pointer;
        }

        .profile-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            min-width: 150px;
            padding: 8px 0;
            display: none;
            z-index: 1000;
        }

        .profile-dropdown-menu.show {
            display: block;
        }

        .profile-dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .profile-dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .profile-dropdown-item i {
            color: #202020ff;
        }

        #panah-bawah {
            transition: transform 0.3s;
        }

        #panah-bawah.rotated {
            transform: rotate(180deg);
        }

        .logout-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .logout-modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .logout-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .logout-modal-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .logout-modal-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            color: #6c757d;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }

        .logout-modal-close:hover {
            background-color: #f8f9fa;
            color: #495057;
        }

        .logout-modal-body {
            padding: 24px 20px;
            text-align: center;
        }

        .logout-modal-icon {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 16px;
        }

        .logout-modal-body p {
            margin: 0;
            font-size: 1rem;
            color: #495057;
        }

        .logout-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 20px;
            border-top: 1px solid #e9ecef;
        }

        .logout-btn-secondary,
        .logout-btn-primary {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .logout-btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .logout-btn-secondary:hover {
            background-color: #5a6268;
        }

        .logout-btn-primary {
            background-color: #dc3545;
            color: white;
        }

        .logout-btn-primary:hover {
            background-color: #c82333;
        }
    </style>