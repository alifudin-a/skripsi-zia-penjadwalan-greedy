<?php

use App\Http\Controllers\JadwalPublicController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', '/admin/login');

// Halaman publik untuk murid — lihat jadwal tanpa login.
Route::get('/jadwal', [JadwalPublicController::class, 'index'])->name('public.jadwal');