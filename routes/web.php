<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'landing'])->name('landing');

// guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// auth only
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class)->except(['show']);
    Route::get('/customers/find-by-nik', [CustomerController::class, 'findByNik'])->name('customers.find-by-nik');
    Route::resource('customers', CustomerController::class);
    Route::resource('product-categories', ProductCategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/items', [OrderController::class, 'showItems'])->name('orders.items');
    Route::post('orders/{order}/items', [OrderController::class, 'storeItems'])->name('orders.storeItems');
    Route::get('orders/{order}/approve', [OrderController::class, 'showApproval'])->name('orders.approve');
    Route::post('orders/{order}/approve', [OrderController::class, 'processApproval'])->name('orders.processApproval');
    Route::get('orders/{order}/payment', [OrderController::class, 'showPayment'])->name('orders.payment');
    Route::post('orders/{order}/payment', [OrderController::class, 'processPayment'])->name('orders.processPayment');
});
