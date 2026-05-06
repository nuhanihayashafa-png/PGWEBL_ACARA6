<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\PolygonsController;
use App\Http\Controllers\PolylinesController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Halaman peta - pakai MapController
Route::get('/map', [MapController::class, 'map'])->name('map');

// API data untuk Leaflet
Route::get('/api/map-data', [MapController::class, 'getMapData'])->name('api.map.data');

// GeoJSON routes (untuk map.blade.php lama yang pakai $.getJSON)
Route::get('/geojson/points',   [MapController::class, 'geojsonPoints'])->name('geojson.points');
Route::get('/geojson/polylines',[MapController::class, 'geojsonPolylines'])->name('geojson.polylines');
Route::get('/geojson/polygons', [MapController::class, 'geojsonPolygons'])->name('geojson.polygons');

// Points
Route::post('/store-points',         [PointsController::class, 'store'])->name('points.store');
Route::delete('/delete-points/{id}', [PointsController::class, 'destroy'])->name('points.delete');

// Polylines
Route::post('/store-polylines',         [PolylinesController::class, 'store'])->name('polylines.store');
Route::delete('/delete-polylines/{id}', [PolylinesController::class, 'destroy'])->name('polylines.delete');

// Polygons
Route::post('/store-polygons',         [PolygonsController::class, 'store'])->name('polygons.store');
Route::delete('/delete-polygons/{id}', [PolygonsController::class, 'destroy'])->name('polygons.delete');

// Tabel
Route::get('/table', [MapController::class, 'table'])->name('table');

Route::get('/about', function () {
    return view('about', ['title' => 'Tentang']);
})->name('about');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

