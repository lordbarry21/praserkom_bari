<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 1. Rute Halaman Utama & Katalog Pelanggan (Customer UI)
// Menjadikan halaman utama (http://127.0.0.1:8000/) langsung menampilkan katalog pemesanan tiket customer
Route::get('/', [OrderController::class, 'index'])->name('customer.index');
Route::get('/customer', [OrderController::class, 'index']);
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// CONTOH: Saat pelanggan mengirim pesanan tiket konser, data dikirim lewat POST ke rute ini untuk diproses simpan ke DB.
Route::post('/checkout', [OrderController::class, 'store'])->name('customer.checkout');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// 2. Rute Dashboard Breeze
// CONTOH: Mengarahkan URL /dashboard ke rekap pesanan admin. Middleware 'auth' memastikan hanya pengguna yang sudah login yang bisa melihat.
Route::get('/dashboard', [OrderController::class, 'adminDashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 3. Rute Grup Terproteksi Admin (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CONTOH: Saat admin mengganti dropdown status pesanan dari 'Pending' ke 'Confirmed' / 'Canceled', form mengirim request PATCH ke rute ini.
    Route::patch('/admin/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Route Resource:
    // CONTOH: Otomatis membangkitkan rute CRUD sekaligus (/events, /events/create, /events/{id}/edit, dll) untuk mempermudah kelola master data konser.
    Route::resource('events', EventController::class);
    Route::resource('/admin/events', EventController::class);
});

// Memanggil rute otentikasi login & logout bawaan Laravel Breeze (/login, /logout, /register)
require __DIR__.'/auth.php';
