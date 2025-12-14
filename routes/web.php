<?php

use App\config\Router;
use App\controllers\AuthController;
use App\controllers\BarangController;
use App\controllers\DashboardController;
use App\controllers\StokController;
use App\controllers\SupplierController;
use App\controllers\UserController;
use App\middleware\AuthMiddleware;

Router::add('GET', '/login', AuthController::class, 'login', [AuthMiddleware::class, 'redirectIfAuthenticated']);
Router::add('POST', '/auth/login/process', AuthController::class, 'processLogin', [AuthMiddleware::class, 'redirectIfAuthenticated']);
Router::add('POST', '/auth/register/process', AuthController::class, 'processRegister', [AuthMiddleware::class, 'redirectIfAuthenticated']);
Router::add('GET', '/auth/logout', AuthController::class, 'logout');

Router::add('GET', '/dashboard', DashboardController::class, 'index', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/users', UserController::class, 'index', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/users/create', UserController::class, 'create', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/users/edit/(.+)', UserController::class, 'edit', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/users/update/(.+)', UserController::class, 'update', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/users/delete/(.+)', UserController::class, 'delete', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/users/activate/(.+)', UserController::class, 'activate', [AuthMiddleware::class, 'checkAuth']);

Router::add('GET', '/supplier', SupplierController::class, 'index', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/supplier/create', SupplierController::class, 'create', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/supplier/edit/(.+)', SupplierController::class, 'edit', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/supplier/update/(.+)', SupplierController::class, 'update', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/supplier/delete/(.+)', SupplierController::class, 'delete', [AuthMiddleware::class, 'checkAuth']);

Router::add('GET', '/barang', BarangController::class, 'index', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/barang/create', BarangController::class, 'create', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/barang/edit/(.+)', BarangController::class, 'edit', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/barang/update/(.+)', BarangController::class, 'update', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/barang/delete/(.+)', BarangController::class, 'delete', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/barang/expired-detail/(.+)', BarangController::class, 'getExpiredDetail', [AuthMiddleware::class, 'checkAuth']);

Router::add('GET', '/stokin', StokController::class, 'stockIn', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/stokin/add', StokController::class, 'addStockIn', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/stokout', StokController::class, 'stockOut', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/stokout/add', StokController::class, 'addStockOut', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/history', StokController::class, 'history', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/history/export', StokController::class, 'exportHistory', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/barang/expired-report', StokController::class, 'expiredReport', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/stokin/expired-report', StokController::class, 'expiredReport', [AuthMiddleware::class, 'checkAuth']);
Router::add('POST', '/barang/mark-expired-cleaned', StokController::class, 'markExpiredCleaned', [AuthMiddleware::class, 'checkAuth']);
Router::add('GET', '/barang/expired-cleaned-history', StokController::class, 'expiredCleanedHistory', [AuthMiddleware::class, 'checkAuth']);

Router::add('GET', '/', function () {
    if (AuthController::checkAuth()) {
        header('Location: /dashboard');
    } else {
        header('Location: /login');
    }
    exit;
}, null, [AuthMiddleware::class, 'redirectIfAuthenticated']);
