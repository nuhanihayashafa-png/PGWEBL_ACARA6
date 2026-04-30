<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PolylinesController;
use App\Http\Controllers\PolygonsController;
use App\Http\Controllers\PointsController;

// 1. Route untuk halaman utama (Home)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. Route untuk halaman Peta
Route::get('/map', [MapController::class, 'map'])->name('map');

// ==========================================
// RUTE API UNTUK MEMUAT DATA KE PETA LEAFLET
// ==========================================
Route::get('/api/map-data', [MapController::class, 'getMapData'])->name('api.map.data');


// ==========================================
// RUTE UNTUK MENYIMPAN DATA DARI FORM (POST)
// ==========================================
// Point - Rute untuk menyimpan data titik
Route::post('/store-points', [PointsController::class, 'store'])->name('store');

// Polylines - Rute untuk menyimpan data garis
Route::post('/store-polylines', [PolylinesController::class, 'store'])->name('polylines.store');

// Polygons - Rute untuk menyimpan data area/poligon
Route::post('/store-polygons', [PolygonsController::class, 'store'])->name('polygons.store');


// 3. Route untuk halaman Tabel
Route::get('/table', [MapController::class, 'table'])->name('table');

// Route bawaan jika kamu pakai Laravel Breeze/Jetstream
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
