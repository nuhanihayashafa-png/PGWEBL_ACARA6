<?php

use App\Http\Controllers\PointsController;
use App\Http\Controllers\PolygonsController;
use App\Http\Controllers\PolylinesController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Beranda & About — bebas diakses tanpa login
Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/about', function () {
    return view('about', ['title' => 'Tentang']);
})->name('about');

// Semua halaman berikut wajib login
Route::middleware(['auth'])->group(function () {

    Route::get('/map', [PageController::class, 'map'])->name('map');
    Route::get('/table', [PageController::class, 'table'])->name('table');

    Route::get('/geojson/points',    [PageController::class, 'geojsonPoints'])->name('geojson.points');
    Route::get('/geojson/polylines', [PageController::class, 'geojsonPolylines'])->name('geojson.polylines');
    Route::get('/geojson/polygons',  [PageController::class, 'geojsonPolygons'])->name('geojson.polygons');

    Route::get('/points/{id}/edit', [PointsController::class, 'edit'])->name('points.edit');
    Route::post('/store-points',    [PointsController::class, 'store'])->name('points.store');
    Route::get('/api/points/{id}',  [PointsController::class, 'show']);
    Route::put('/points/{id}',      [PointsController::class, 'update'])->name('points.update');
    Route::delete('/points/{id}',   [PointsController::class, 'destroy'])->name('points.destroy');

    Route::get('/polylines/{id}/edit', [PolylinesController::class, 'edit'])->name('polylines.edit');
    Route::post('/store-polylines',    [PolylinesController::class, 'store'])->name('polylines.store');
    Route::put('/polylines/{id}',      [PolylinesController::class, 'update'])->name('polylines.update');
    Route::delete('/polylines/{id}',   [PolylinesController::class, 'destroy'])->name('polylines.destroy');
    Route::get('/api/polylines/{id}',  [PolylinesController::class, 'show']);

    Route::get('/polygons/{id}/edit', [PolygonsController::class, 'edit'])->name('polygons.edit');
    Route::post('/store-polygons',    [PolygonsController::class, 'store'])->name('polygons.store');
    Route::put('/polygons/{id}',      [PolygonsController::class, 'update'])->name('polygons.update');
    Route::delete('/polygons/{id}',   [PolygonsController::class, 'destroy'])->name('polygons.destroy');
    Route::get('/api/polygons/{id}',  [PolygonsController::class, 'show']);

    // Profile bawaan Breeze
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::view('/dashboard', 'dashboard')->name('dashboard');

});

// Route auth bawaan Breeze (login, register, logout, dll)
require __DIR__.'/auth.php';
